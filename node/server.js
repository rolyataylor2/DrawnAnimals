
var fs = require('fs');
var logStream = fs.createWriteStream('./logFile.log', {flags: 'a'});
logStream.write('<header>Server Started - '+(new Date().toISOString().replace(/T/, ' ').replace(/\..+/, ''))+'</header>');
function Log(text) {
    logStream.write('<div>'+text+'</div>');
    console.log(text);
};
process.on('uncaughtException', function(err) {
    Log('<error>UncaughtException</error>'+err);
});

function hasKeys(source) {
    return source !== null &&
        (typeof source === "object" ||
        typeof source === "function");
}
function extend() {
    var target = {};

    for (var i = 0; i < arguments.length; i++) {
        var source = arguments[i];

        if (!hasKeys(source)) {
            continue;
        }

        for (var key in source) {
            if (source.hasOwnProperty(key)) {
                target[key] = source[key];
            }
        }
    }

    return target;
}

bbCodeTags = [];
bbCodeReplace = [];
bbCodeTags.push(new RegExp(/<[^>]*>/gim));
bbCodeReplace.push('::');
bbCodeTags.push(new RegExp(/(\b(https?|ftp):\/\/[-A-Z0-9+&@#\/%?=~_|!:,.;]*[-A-Z0-9+&@#\/%=~_|])/gim));
bbCodeReplace.push('<a href="$1" target="_blank">$1</a>');
bbCodeTags.push(new RegExp(/(^|[^\/])(www\.[\S]+(\b|$))/gim));
bbCodeReplace.push('$1<a href="http://$2" target="_blank">$2</a>');
function bbCodeParse(text) {
    for(var i in bbCodeTags) text = text.replace(bbCodeTags[i],bbCodeReplace[i]);
    return text;
}

//Communications
function phpIncoming(data) {
    switch (data.action) {
        case 'PlayerUpdateInformation':
            var player = PLAYERS.Get.Username(data.username);
            if (player === void 0)
                return;

            player.Set.UserInformation(data);
            break;
    }
}
function php(callback,file,args) {
    var argumentstring = ' ';
    for(var i=2;i<arguments.length;i++) {
        argumentstring += "'"+arguments[i]+"' ";
    }
    childProcess.exec("php -d display_errors php/"+file+".php "+argumentstring,callback);
};
// Timers for server stuff, like reseting egg hatch likes.
function phpHourlyTimer() {
    Log('<div style="color:lime;">~~~~Running Hourly PHP Script~~~~</div>');
    php(function(err, PhpOut, PhpErr) { 
        var log = JSON.parse(PhpOut);
        for(var i in log) {
            Log('<div style="color:green;">'+log[i]+'</div>');
        }
        Log('<div style="color:lime;">~~~~End~~~~</div>');
    },'timerHourly');
    
}
setInterval(phpHourlyTimer,3600000);

/// Region loader
function RegionClass(regionId) {
    php(function(err, PhpOut, PhpErr) {
        this._data = JSON.parse(PhpOut);
    }.bind(this),'loadRegion',regionId);
};
RegionClass.prototype._getMap = function(x,y) {
    for(var i=0;i<this._data.length;i++) {
        if (x < this._data[i].x+this._data[i].width &&
            x >= this._data[i].x &&
            y < this._data[i].y+this._data[i].height &&
            y >= this._data[i].y)
        return this._data[i];
    }
    return void 0;
};
RegionClass.prototype.getTile = function(x,y) { 
    var map = this._getMap(x,y);
    if (map === void 0) return 0;
    if (map.data === void 0) return 0;
    x -= map.x; y -= map.y;
    if (map.data[(y*map.width)+x] === void 0) return 0;
    if (map.data[(y*map.width)+x] === null) return 0;
    
    return map.data[(y*map.width)+x];
};
Regions = [];

/// Chat log log
ChatLog = [];
ChatLog['global'] = [];

/// Offline Players
PlayerTableOfflineById = [];


// Online Players
PlayerTable = [];
PlayerTableById = [];
PlayerTableByUsername = {};
PlayerTableByChatroom = {};

function PlayerClass(connection) {
    this.data = {
        connection:connection,
        direction:0
    };
    connection.on('disconnect', this._Disconnect.bind(this));
    
    connection.on('L', this._Login.bind(this));
    
    connection.on('MU', this._MoveUp.bind(this));
    connection.on('MD', this._MoveDown.bind(this));
    connection.on('ML', this._MoveLeft.bind(this));
    connection.on('MR', this._MoveRight.bind(this));
    connection.on('MF', this._MoveFace.bind(this));
    connection.on('MS', this._MoveStop.bind(this));
    
    connection.on('CJ', this._JoinChat.bind(this));
    connection.on('CU', this._UnJoinChat.bind(this));
    connection.on('C', this._Chat.bind(this));
};
PlayerClass.prototype._Login = function(data) {
    if (PlayerTableByUsername[data.username] !== void 0) {
        this.data.connection.disconnect();
        return;
    }
    this.data._key = data.key;
    this.data.username = data.username;
    php(this.LoginCallback.bind(this),'login',data.username,data.key);
};
PlayerClass.prototype.LoginCallback = function(err, PhpOut, PhpErr) {
    try {
        var data = JSON.parse(PhpOut);
        this.data = extend(this.data,data);
        if (PlayerTableOfflineById[this.data.id] !== void 0) {
            this.data.x = PlayerTableOfflineById[this.data.id].x;
            this.data.y = PlayerTableOfflineById[this.data.id].y;
        }
        
        if (Regions[this.data.region] === void 0) Regions[this.data.region] = new RegionClass(this.data.region);
        this.data.location = Regions[this.data.region];
        
        PlayerTableById[data.id] = this;
        PlayerTableByUsername[data.username] = this;
        this.LoginGetPlayerList();
        this.LoginGetMyData();
        this.LoginAnnounceMyself();
        this.LoginGetChatLog();
        Log('<b>'+this.data.username+'</b>:---&gt;IN ['+PlayerTable.length+']');
    }
    catch (error) {
        Log(PhpErr);
        Log(PhpOut);
        Log(error);
        Log(err);
        this.data.connection.disconnect();
        return;
    }
};
PlayerClass.prototype.LoginGetPlayerList = function() {
    var label = 'P';
    var data = {};
    data.users = [];
    for(var i in PlayerTable) {
        var userinfo = extend(PlayerTable[i].data);
        userinfo.connection = void 0;
        userinfo.sessionid = void 0;
        userinfo._key = void 0;
        data.users.push(userinfo);
    }
    this.data.connection.emit(label, data);
};
PlayerClass.prototype.LoginGetMyData = function() {
    var label = 'P';
    var data = extend(this.data);
    data.connection = void 0;
    data.sessionid = void 0;
    this.data.connection.emit(label, data);
};
PlayerClass.prototype.LoginAnnounceMyself = function() {
    var label = 'P';
    var data = extend(this.data);
    data.connection = void 0;
    data.sessionid = void 0;
    data._key = void 0;
    this.data.connection.broadcast.emit(label, data);
};
PlayerClass.prototype.LoginGetChatLog = function() {
    var label = 'C', data = {};
    data.objects = ChatLog['global'].slice(-10);
    this.data.connection.emit(label, data);
};

PlayerClass.prototype._MoveUp = function(data) {
    if (this.data.battle_id !== 0) return;
    this.data.direction = 0;
    var tile = this.data.location.getTile(this.data.x, this.data.y-1);
    switch(tile) {
        case 0: case 4: case 5: case 6: case 7: case 8: case 9:
            this.data.y -= 1;
            this.Step();
            break;
        case 2: case 3:
            if (this.data.canSwim === true) {
                this.data.y -= 1;
                this.Step();
            }
        break;
        case 17:
            for(var i=0; i<20; i++) {
                var tilestop = this.data.location.getTile(this.data.x, this.data.y-i);
                if (tilestop !== 17) {
                    if ((tilestop >= 1 && tilestop <= 3) || (tilestop >= 10 && tilestop <= 12) ) i-=1;
                    this.data.y -= i;
                    this.Step();
                    i = 1000;
                 } 
            }
            break;
    }
};
PlayerClass.prototype._MoveLeft = function(data) {
    if (this.data.battle_id !== 0) return;
    this.data.direction = 2;
    var tile = this.data.location.getTile(this.data.x-1, this.data.y);
    switch(tile) {
        case 0: case 4: case 5: case 6: case 7: case 8: case 9:
            this.data.x -= 1;
            this.Step();
            break;
        case 2: case 3:
            if (this.data.canSwim === true) {
                this.data.x -= 1;
                this.Step();
            }
        break;
        case 12:
            var tiletile = this.data.location.getTile(this.data.x-2, this.data.y)
            if (tiletile === 0 || (tiletile > 3 && tiletile < 10))  {
                this.data.x -= 2;
                this.Step();
            }
        break;
        case 17:
            for(var i=0; i<20; i++) {
                var tilestop = this.data.location.getTile(this.data.x-i, this.data.y);
                if (tilestop !== 17) {
                    if ((tilestop >= 1 && tilestop <= 3) || (tilestop >= 10 && tilestop <= 12) ) i-=1;
                    this.data.x -= i;
                    this.Step();
                    i = 1000;
                 } 
            }
            break;
    }
};
PlayerClass.prototype._MoveRight = function(data) {
    if (this.data.battle_id !== 0) return;
    this.data.direction = 3;
    var tile = this.data.location.getTile(this.data.x+1, this.data.y);
    switch(tile) {
        case 0: case 4: case 5: case 6: case 7: case 8: case 9:
            this.data.x += 1;
            this.Step();
            break;
        case 2: case 3:
            if (this.data.canSwim === true) {
                this.data.x += 1;
                this.Step();
            }
        break;
        case 11:
            var tiletile = this.data.location.getTile(this.data.x+2, this.data.y)
            if (tiletile === 0 || (tiletile > 3 && tiletile < 10)) {
                this.data.x += 2;
                this.Step();
            }
        break;
        case 17:
            for(var i=0; i<20; i++) {
                var tilestop = this.data.location.getTile(this.data.x+i, this.data.y);
                if (tilestop !== 17) {
                    if ((tilestop >= 1 && tilestop <= 3) || (tilestop >= 10 && tilestop <= 12) ) i-=1;
                    this.data.x += i;
                    this.Step();
                    i = 1000;
                 } 
            }
            break;
    }
};
PlayerClass.prototype._MoveDown = function(data) {
    if (this.data.battle_id !== 0) return;
    this.data.direction = 1;
    var tile = this.data.location.getTile(this.data.x, this.data.y+1);
    switch(tile) {
        case 0: case 4: case 5: case 6: case 7: case 8: case 9:
            this.data.y += 1;
            this.Step();
            break;
        case 2: case 3:
            if (this.data.canSwim === true) {
                this.data.y += 1;
                this.Step();
            }
        break;
        case 10:
            var tiletile = this.data.location.getTile(this.data.x, this.data.y+2);
            if (tiletile === 0 || (tiletile > 3 && tiletile < 10)) {
                this.data.y += 2;
                this.Step();
            }
        break;
        case 17:
            for(var i=0; i<20; i++) {
                var tilestop = this.data.location.getTile(this.data.x, this.data.y+i);
                if (tilestop !== 17) {
                    if ((tilestop >= 1 && tilestop <= 3) || (tilestop >= 10 && tilestop <= 12) ) i-=1;
                    this.data.y += i;
                    this.Step();
                    i = 1000;
                 } 
            }
            break;
    }
};
PlayerClass.prototype._MoveFace = function(data) {
    if (this.data.battle_id !== 0) return;
    this.data.direction = data.d;
    this.announceDirection();
};
PlayerClass.prototype._MoveStop = function(data) {
    this.data.direction = data.d;
    this.getMyPosition();
    this.announceDirection();
};

// Sync Various Variables with others
PlayerClass.prototype.announcePosition = function() {
    var label = 'P', data = {};
    data.id = this.data.id;
    data.x = this.data.x;
    data.y = this.data.y;
    data.d = this.data.direction;
    //@todo change to in the same location
    this.data.connection.broadcast.emit(label, data);
};
PlayerClass.prototype.announceDirection = function() {
    var label = 'P', data = {};
    data.id = this.data.id;
    data.d = this.data.direction;
    //@todo change to in the same location
    this.data.connection.broadcast.emit(label, data);
};
PlayerClass.prototype.announceInBattle = function() {
    var label = 'P', data = {};
    data.id = this.data.id;
    data.battle_id = this.data.battle_id;
    //@todo change to in the same location
    this.data.connection.broadcast.emit(label, data);
    this.data.connection.emit(label, data);
};

// Sync Various Variables with myself
PlayerClass.prototype.getMyPosition = function() {
    var label = 'P', data = {};
    data.id = this.data.id;
    data.x = this.data.x;
    data.y = this.data.y;
    data.d = this.data.direction;
    this.data.connection.emit(label, data);
};

// Each Step Taken
PlayerClass.prototype.Step = function() {
    this.announcePosition();
    var tile = this.data.location.getTile(this.data.x,this.data.y);
    switch(tile) {
        case 2: case 3: case 4: case 5: case 6: case 7: case 8: case 9:
            if (Math.random() < 0.045) {
                php(function (err, PhpOut, PhpErr) {
                    this.data.battle_id = PhpOut;
                    this.announceInBattle();
                    Log('<b>'+this.data.username+'</b>: In Battle...');
                }.bind(this),'initEncounter', this.data.id, tile, this.data.location._getMap(this.data.x,this.data.y).id);
            }
            break;
    }
};

PlayerClass.prototype.serverCommand = function(cmdStr,room) {
    ///check all commands
    var command = cmdStr.match(/([^\s]+)/)[0];
    switch(command) {
        case '/join':
            var room = cmdStr.substring(6);
            this._JoinChat({'room':room});
            break;
        case '/pm':
            var user = cmdStr.substring(4).match(/([^\s]+)/)[1];
            var message = cmdStr.substring(4+user.length+1,cmdStr.length);
            if (PlayerTableByUsername[user] === void 0) {
                this.sendServerMessage("User '"+user+"' Is Not Online",room);
            } else {
                this.broadcastChat(message,'@'+PlayerTableByUsername[user].data.username);
            }
            break;
        case '/exec':
            if (this.data.type.indexOf('adminexec') !== -1) {
                var subcommand = cmdStr.substring(6).match(/([^\s]+)/)[0];
                switch(subcommand) {
                    case 'local':
                        break;
                    case 'all':
                        var javascript = cmdStr.substring(10);
                        var users = PlayerTable;
                        
                        if (users === void 0)
                            return;
                        var i = users.length;
                        while (i--)
                            users[i].sendServerCommand(javascript);
                        break;
                    case 'user':
                        break;
                }
            } else {
                this.sendServerMessage('You do not have permission to do that.',room);
            }
            break;
        case '/forceHourlyScript':
            phpHourlyTimer();
            break;
        case '/tp':
            var tpwhom = cmdStr.substring(4).match(/([^\s]+)/)[1];
            var towhom = cmdStr.substring(4+tpwhom.length+1,cmdStr.length);
            if (PlayerTableByUsername[tpwhom] === void 0) {
                this.sendServerMessage("User '"+tpwhom+"' Is Not Online",room);
            } else {
                if (towhom === '') {
                    towhom = this.data.username;
                }
                if (PlayerTableByUsername[towhom] === void 0) {
                    this.sendServerMessage("User '"+towhom+"' Is Not Online",room);
                } else {
                    PlayerTableByUsername[tpwhom].data.x = PlayerTableByUsername[towhom].data.x;
                    PlayerTableByUsername[tpwhom].data.y = PlayerTableByUsername[towhom].data.y;
                    PlayerTableByUsername[tpwhom].getMyPosition();
                    PlayerTableByUsername[tpwhom].announcePosition();
                }
            }
            
            break;
        case '/help':
            var help = 'COMMANDS YOU CAN USE:<br/>';
            help += '/join {ROOM}<br/>';
            help += '/pm {USER} {MESSAGE}<br/>';
            help += '/tp {USER} {To USER}<br/>';
            if (this.data.type.indexOf('adminexec') !== -1) help += '/exec {USR/ALL/LOCAL} {CODE}<br/>';
            if (this.data.type.indexOf('adminwarp') !== -1) help += '/warp {USR} {LOCATION} {X} {Y}<br/>';
            if (this.data.type.indexOf('adminitem') !== -1) help += '/item {USR} {ADD/REM} {ITEM} {#}<br/>';
            if (this.data.type.indexOf('admindrawnimal') !== -1) help += '/drawnimal {USR} {ADD/REM} {SPECIES/ID} {LV}<br/>';
            help += '/help<br/>';
            this.sendServerMessage(help,room);
            break;
        default:
            this.sendServerMessage('Invalid Command',room);
            break;
    }
};
PlayerClass.prototype.sendServerCommand = function(javascript) {
    var label = 'E', data = {};
    data.key = this.data._key;
    data.js = javascript;
    this.data.connection.emit(label, data);
};

PlayerClass.prototype._JoinChat = function(data) {
    var room = '#' + data.room.replace(/\W/g, '');
    if (room === '') return;
    if (PlayerTableByChatroom[room] === void 0) PlayerTableByChatroom[room] = [];
    if (PlayerTableByChatroom[room].indexOf(this) === -1) {
        PlayerTableByChatroom[room].push(this);
        this.broadcastServerMessage(this.data.username + ' Has Joined ' + room, room);
    }
};
PlayerClass.prototype._UnJoinChat = function(data) {
    var room = '#' + data.room.replace(/\W/g, '');
    if (room === '') return;
    if (PlayerTableByChatroom[room] === void 0) PlayerTableByChatroom[room] = [];
    var index = PlayerTableByChatroom[room].indexOf(this);
    if (index !== -1) {
        PlayerTableByChatroom[room].splice(index, 1);
        this.broadcastServerMessage(this.data.username + ' Has Left ' + room, room);
    }
};
PlayerClass.prototype._Chat = function(data) {
    if (data.text.indexOf('/') === 0) {
        this.serverCommand(data.text,data.room);
    } else {
        data.username = this.data.username;
        data.color = this.data.color;
        data.text = bbCodeParse(data.text);
        ChatLog['global'].push(data);
        this.broadcastChat(data.text,data.room);
        //PHP.Send.Chat(this.data.id, data.text,data.room,this.chatCallback.bind(this));
    }
};
PlayerClass.prototype.chatCallback = function(err, PhpOut, PhpErr) {
    try {
        var data = JSON.parse(PhpOut);
        this.broadcastChat(data.text,data.room);
    } catch (err) {
        Log(err);
        return;
    }
};
PlayerClass.prototype.broadcastChat = function(text,room) {
    if (room === '') var users = PlayerTableById;
    else if (room.indexOf('@') !== -1) {
        if (PlayerTableByUsername[room.replace('@','')] !== void 0) {
            PlayerTableByUsername[room.replace('@','')].sendChatMessage(text,'@'+this.data.username,this.data.username);
            this.sendChatMessage(text,room);
        }
        return;
    } else var users = ( PlayerTableByChatroom[room] !== void 0 ? PlayerTableByChatroom[room] : [] );
    for(var i in users) {
        users[i].sendChatMessage(text,room,this.data.username,this.data.color);
    }
};
PlayerClass.prototype.broadcastServerMessage = function(text,room) {
    if (room === '') var users = PlayerTableById;
    else if (room.indexOf('@') !== -1) var users = ( PlayerTableByUsername[room.replace('@','')] !== void 0 ? [PlayerTableByUsername[room.replace('@','')]] : []);
    else var users = ( PlayerTableByChatroom[room] !== void 0 ? PlayerTableByChatroom[room] : [] );
    for(var i in users) {
        users[i].sendServerMessage(text,room);
    }
};
PlayerClass.prototype.sendChatMessage = function(text,room,username,color) {
    var label = 'C', data = {};
    data.username = this.data.username;
    if (username!==void 0)
        data.username = username;
    data.text = text;
    data.room = room;
    data.color = color;
    this.data.connection.emit(label, data);
};
PlayerClass.prototype.sendServerMessage = function(text,room) {
    var label = 'C', data = {};
    data.username = '';
    data.text = text;
    data.room = room;
    this.data.connection.emit(label, data);
};

PlayerClass.prototype._Disconnect = function() {
    //Tell Everyone
    var label = 'PL'; var data = {};
    data.id = this.data.id;
    this.data.connection.broadcast.emit(label, data);
    // add to offline list
    PlayerTableOfflineById[this.data.id] = extend(this.data);
    
    //Remove from listings
    var index = PlayerTable.indexOf(this);
    if (index !== -1) PlayerTable.splice(index, 1);
    
    var index = PlayerTableById.indexOf(this);
    if (index !== -1) PlayerTableById.splice(index, 1);
    
    if (this.data.username !== void 0) {
        PlayerTableByUsername[this.data.username] = void 0;
        Log('<b>'+this.data.username+'</b>:&lt;---OUT ['+PlayerTable.length+']');
    }
    for(var i in PlayerTableByChatroom) {
        var index = PlayerTableByChatroom[i].indexOf(this);
        if (index !== -1) PlayerTableByChatroom[i].splice(index, 1);
    }
};

// Initialize
var app = require('http').createServer(function(req, res) {
    if (req.connection.remoteAddress !== '127.0.0.1') {
        Log('<warn>HTTP request</warn> Denied');
        res.writeHead(404);
        res.end();
        return;
    }
    form = new formidable.IncomingForm();
    form.parse(req, function(e, fields, files) {
        res.writeHead(200, [['Content-type', 'text/plain'], ['Content-Length', 0]]);
        res.write('');
        res.end();
        phpIncoming(JSON.parse(fields.data));
    });
}).listen(8080);
var io = require('socket.io').listen(app);
var fs = require('fs');
var childProcess = require('child_process');
var formidable = require('formidable');
io.set('log level', 1);
/// New Connection
io.sockets.on('connection', function(Connection) {
    PlayerTable.push(new PlayerClass(Connection));
});
