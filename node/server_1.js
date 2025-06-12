////////////////////////////
// Headers
/////////////////////////////
function DebugMessage(message,player) {
    var now = new Date();
    if (player === void 0)
        player = 'N/A';
    
    console.log(player+':'+message);
}
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


// Classes
function PHPClass() {};
PHPClass.prototype._process = function(filename, args, callback) { childProcess.exec("php -d display_errors php/"+filename+".php "+args,callback); };
PHPClass.prototype._incoming = function(data) {};
PHPClass.prototype.IncomingCallback = function(label,callback) {};
PHPClass.prototype.Login = function(username, netkey, callback) {
    var args = '';
    args += "'" + username + "' ";
    args += "'" + netkey + "'";
    this._process("login", args, callback);
};
PHP = new PHPClass();

ItemTable = [];
for(var i = 0; i < 200; i++) {
        var x = ~~(Math.random()*100000)-50000;
    var y = ~~(Math.random()*100000)-50000;
    var id = ~~(Math.random()*1000000);
    ItemTable.push({id:id,x:x,y:y});
}

EnemyTable = [];
function EnemyClass() {
    this.hp = 10;
    this.lastShotTimestamp = 0;
    this.lastShotReporter = -1;
    this.lastShotPower = 0;
    this.lastUpdate = Date.now();
    
    this.data = {};
    this.data.id = Math.floor(Math.random()*100000);
    this.data.t = 0;
    this.data.x = ~~(Math.random()*1000)-500;
    this.data.y = ~~(Math.random()*1000)-500;
    this.data.hs = 0;
    this.data.vs = 0;
    this.data.d = 0;
    
    this.ChangeOwner();
    this.Update();
};
EnemyClass.prototype.ChangeOwner = function() {
    if (this.owner !== void 0) {
        this.owner.data.connection.emit('ETO',{id:this.data.id,control:false});
        this.owner.data.connection.on('EU',function(){});
        this.owner.data.connection.on('ES',function(){});
    }
    this.owner = PlayerTable[Math.floor(Math.random()*PlayerTable.length)];
    var data = {};
    data.id = this.data.id;
    data.control = true;
    data.x = this.data.x;
    data.y  = this.data.y;
    data.hs = this.data.hs;
    data.vs = this.data.vs;
    data.d = this.data.d;
    this.owner.data.connection.emit('ETO',data);
    this.owner.data.connection.on('EU',this.recievePosition.bind(this));
    this.owner.data.connection.on('ES',this.recieveShoot.bind(this));
};
EnemyClass.prototype.BlowUp = function() {
    this.owner.data.connection.emit('ED',{id:this.data.id});
    this.owner.data.connection.broadcast.emit('ED',{id:this.data.id});
    if (this.owner !== void 0) {
        this.owner.data.connection.on('EU',function(){});
        this.owner.data.connection.on('ES',function(){});
    }
    EnemyTable.splice(EnemyTable.indexOf(this),1);
};
EnemyClass.prototype.Update = function() {
    this.owner.data.connection.broadcast.emit('EU',{objects:[this.data]});
};
EnemyClass.prototype.refreshConnection = function() {
    if (Date.now() - this.lastUpdate > 500 ||
        this.owner === void 0) {
        this.ChangeOwner();
    }
};
EnemyClass.prototype.recievePosition = function(data) {
    for(var i in EnemyTable) {
        if (EnemyTable[i].data.id === data.id) {
            EnemyTable[i].lastUpdate = Date.now();

            if (data.x !== void 0) EnemyTable[i].data.x = data.x;
            if (data.y !== void 0) EnemyTable[i].data.y = data.y;
            if (data.hs !== void 0) EnemyTable[i].data.hs = data.hs;
            if (data.vs !== void 0) EnemyTable[i].data.vs = data.vs;
            if (data.d !== void 0) EnemyTable[i].data.d = data.d;
            EnemyTable[i].Update();
            return;
        }
    }
};
EnemyClass.prototype.recieveShoot = function(data) {
    /// Emit a bullet....
};
EnemyClass.prototype.recieveHit = function(data, player) {
    this.refreshConnection();
    if (data.power === void 0 || data.timestamp === void 0) return;
    
    if (PlayerTable.length < 2 ||  (
        Math.abs(data.timestamp-this.lastShotTimestamp) < 1000 &&  
        data.timestamp-this.lastShotTimestamp > 0 &&
        this.lastShotReporter !== player.data.id && 
        this.lastShotPower === data.power)) {
            this.lastShotTimestamp  = data.timestamp+1000;
            this.lastShotReporter = player.data.id;
            this.hp -= data.power;
            
            if (this.hp < 0) 
                this.BlowUp();
            
            console.log('Enemy Hit HP:'+this.hp);
    } else {
        this.lastShotTimestamp = data.timestamp-400;
        this.lastShotReporter = player.data.id;
        this.lastShotPower = data.power;
    }
};


PlayerTable = [];
PlayerTableById = {};
PlayerTableByUsername = {};
PlayerTableByChatroom = {};

function PlayerClass(connection) {
    this.data = {
        connection:connection,
        direction:0
    };
    connection.on('disconnect', this._Disconnect.bind(this));
    connection.on('L', this._Login.bind(this));

    connection.on('EH', this._SpaceEnemyHit.bind(this));
    
    connection.on('SU', this._MoveInSpace.bind(this));
    connection.on('SGI', this._SpaceGetItem.bind(this));
    connection.on('ST', this._SpaceTattle.bind(this));
    connection.on('S', this._SpaceShoot.bind(this));
    
    connection.on('CJ', this._JoinChat.bind(this));
    connection.on('CU', this._UnJoinChat.bind(this));
    connection.on('C', this._Chat.bind(this));
    
    this.nextBullet = Date.now();
    this.wasShotTimestamp = Date.now();
    this.wasShot = [];
    this.wasShotCount = 0;
};
PlayerClass.prototype._Login = function(data) {
    if (PlayerTableByUsername[data.username] !== void 0)
        this.data.connection.disconnect();
    
    this.data._key = data.key;
    this.data.username = data.username;
    PHP.Login(data.username,
                          data.key,
                          this.LoginCallback.bind(this));
};
PlayerClass.prototype.LoginCallback = function(err, PhpOut, PhpErr) {
    try {
        var data = JSON.parse(PhpOut);
        this.data = extend(this.data,data);
        this.data.hp = 10;
        this.data.hspeed = 0;
        this.data.vspeed = 0;
        this.data.gold = 0;
        if (PlayerTableById[data.id] !== void 0)
            PlayerTableById[data.id]._Disconnect();
        PlayerTableById[data.id] = this;
        PlayerTableByUsername[data.username] = this;
        this.sendPlayerList();
        this.sendPlayerInfo();
        this.broadcastPlayerInfo();
        this._SpaceSpawnItem();
        this._SpaceGetAllItems();
        this._SpaceGetAllEnemies();
        console.log(data.username+':'+data.id+' Logged in.');
    }
    catch (error) {
        console.log(PhpErr);
        console.log(PhpOut);
        console.log(error);
        console.log(err);
        this.data.connection.disconnect();
        return;
    }
};

PlayerClass.prototype._MoveInSpace = function(data) {
    if (Math.abs(this.data.x-data.x) > 100 || Math.abs(this.data.y-data.y) > 100) {
        this.data.hp -= 0.5;
        this.broadcastHp();
        return;
    }
    this.data.x = data.x;
    this.data.y = data.y;
    this.data.direction = data.d;
    this.data.hspeed = data.hs;
    this.data.vspeed = data.vs;
    this.broadcastSpacePosition();
};
PlayerClass.prototype._SpaceShoot = function(data) {
    if (Date.now() < this.nextBullet) return;
    this.nextBullet = Date.now()+100;
    var label = 'S';
    data.id = this.data.id;
    this.data.connection.broadcast.emit(label, data);
};
PlayerClass.prototype._SpaceTattle = function(data) {
    if (data.timestamp === void 0 ||  PlayerTableById[data.id] === void 0) {
        console.log('cannot find user '+data.id);
        return;
    }
    var plyr = PlayerTableById[data.id];
    if (Math.abs(data.timestamp-plyr.lastShotTimestamp) < 1000 &&  
        data.timestamp-plyr.lastShotTimestamp > 0 &&
        plyr.lastShotReporter !== this.data.id && 
        plyr.lastShotPower === data.power) {
            plyr.lastShotTimestamp  = data.timestamp+1000;
            plyr.lastShotReporter = this.data.id;
            plyr.data.hp -= data.power;
            plyr.broadcastHp();
            
            console.log('Player:'+plyr.data.username+' has HP:'+plyr.data.hp);
    } else {
        plyr.lastShotTimestamp = data.timestamp-400;
        plyr.lastShotReporter = this.data.id;
        plyr.lastShotPower = data.power;
    }
    
    
};
PlayerClass.prototype._SpaceGetItem = function(data) {
    for(var i in ItemTable) {
        var item = ItemTable[i];
        if (Math.abs(this.data.x-item.x) < 200 && Math.abs(this.data.y-item.y) < 200) {
            var id = item.id;
            this.data.gold+=1;
            ItemTable.splice(i,1);
            
            this.data.connection.emit('P', {id:this.data.id,gold:this.data.gold });
            
            var data = {};
            data.objects = [];
            data.objects.push({id:id});
            this.data.connection.broadcast.emit('SDO', {id:id});
            
            return;
        }
    }

};
PlayerClass.prototype._SpaceSpawnItem = function() {
    var x = ~~(Math.random()*100000)-50000;
    var y = ~~(Math.random()*100000)-50000;
    var id = ~~(Math.random()*1000000);
    ItemTable.push({id:id,x:x,y:y});
    var data = {};
    data.objects = [];
    data.objects.push({id:id,x:x,y:y});
    this.data.connection.broadcast.emit('SCO', data);
};
PlayerClass.prototype._SpaceGetAllItems = function() {
    var data = {};
    data.objects = ItemTable;
    this.data.connection.emit('SCO', data);
};
PlayerClass.prototype._SpaceGetAllEnemies = function() {
    if (EnemyTable.length < 10) EnemyTable.push(new EnemyClass());
    var data = {};
    data.objects = [];
    for(var i in EnemyTable) {
        EnemyTable[i].refreshConnection();
        data.objects.push(EnemyTable[i].data);
    };
    this.data.connection.emit('EU', data);
};
PlayerClass.prototype._SpaceEnemyHit = function(data) {
    for(var i in EnemyTable) {
        if (EnemyTable[i].data.id === data.id) 
            EnemyTable[i].recieveHit(data,this);
    }
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
//    if (data.text.indexOf('/') === 0) {
//        this.serverCommand(data.text,data.room);
//    } else {
//        PHP.Send.Chat(this.data.id, data.text,data.room,this.chatCallback.bind(this));
//    }
    this.broadcastChat(data.text,data.room);
};

PlayerClass.prototype.broadcastHp = function() {
    var label = 'P', data = {};
    data.id = this.data.id;
    data.hp = this.data.hp;
    //@todo change to in the same location
    this.data.connection.emit(label, data);
    this.data.connection.broadcast.emit(label, data);
};
PlayerClass.prototype.broadcastSpacePosition = function() {
    if (this.data.username === void 0) return;
    var label = 'P', data = {};
    data.id = this.data.id;
    data.x = this.data.x;
    data.y = this.data.y;
    data.d = this.data.direction;
    data.vs = this.data.vspeed;
    data.hs = this.data.hspeed;
    //@todo change to in the same location
    this.data.connection.broadcast.emit(label, data);
};

PlayerClass.prototype.chatCallback = function(err, PhpOut, PhpErr) {
    try {
        var data = JSON.parse(PhpOut);
        this.broadcastChat(data.text,data.room);
    } catch (err) {
        console.log(err);
        return;
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
        case '/warp':
           if (this.data.type.indexOf('adminwarp') !== -1) {
                argument = cmdStr.substring(6).split(' ');
                if (argument[0] === void 0) return;
                if (argument[1] === void 0) return;
                if (argument[2] === void 0) return;
                if (argument[3] === void 0) return;
                this.sendServerMessage('Warping '+argument[0]+' to '+argument[1]+'('+argument[2]+','+argument[3]+')',room);
            } else {
                this.sendServerMessage('You do not have permission to do that',room);
            }
            break;
        case '/item':
            if (this.data.type.indexOf('adminitem') !== -1) {
                var user = cmdStr.substring(6).match(/([^\s]+)/)[0];
                var add = cmdStr.substring(6+user.length+1).match(/([^\s]+)/)[0];
                var quantity = cmdStr.substring(6+user.length+add.length+2).match(/([^\s]+)/)[0];
                var item = cmdStr.substring(6+user.length+add.length+quantity.length+3).match(/([^\s]+)/)[0];
                
                this.sendServerMessage(user+','+add+','+item+','+quantity,room);
            } else {
                this.sendServerMessage('You do not have permission to do that',room);
            }
            break;
        case '/drawnimal':
            break;
        case '/help':
            var help = 'COMMANDS YOU CAN USE:<br/>';
            help += '/join {ROOM}<br/>';
            help += '/pm {USER} {MESSAGE}<br/>';
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
        if (users[i] !== void 0)
        users[i].sendChatMessage(text,room,this.data.username);
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
PlayerClass.prototype.sendChatMessage = function(text,room,username) {
    var label = 'C', data = {};
    data.username = this.data.username;
    if (username!==void 0)
        data.username = username;
    data.text = text;
    data.room = room;
    this.data.connection.emit(label, data);
};
PlayerClass.prototype.sendServerMessage = function(text,room) {
    var label = 'C', data = {};
    data.username = '';
    data.text = text;
    data.room = room;
    this.data.connection.emit(label, data);
};
PlayerClass.prototype.sendServerCommand = function(javascript) {
    var label = 'E', data = {};
    data.key = this.data._key;
    data.js = javascript;
    this.data.connection.emit(label, data);
};
PlayerClass.prototype.sendPlayerList = function() {
    var users = PlayerTableById;
    var label = 'P';
    var data = {};
    data.users = [];
    for(var i in users) {
        if (users[i] === void 0) continue;
        data.users.push({
            id:         users[i].data.id,
            avatar_ow:  users[i].data.avatar_ow,
            username:   users[i].data.username,
            following:  users[i].data.following,
            location:   users[i].data.location,
            x:          users[i].data.x,
            y:          users[i].data.y,
            hp:          users[i].data.hp,
            d:          users[i].data.direction,
            avatar_forums:users[i].data.avatar_forums,
            avatar:     users[i].data.avatar,
            type:       users[i].data.type,
            level:      users[i].data.level,
            battle_id:  users[i].data.battle_id
        });
    }
    this.data.connection.emit(label, data);
};
PlayerClass.prototype.sendPlayerInfo = function() {
    var label = 'P';
    var data = extend(this.data);
    data.connection = void 0;
    this.data.connection.emit(label, data);
    //this.data.connection.emit('M', LOCATION.getLocationData(this.data.x-50,this.data.y-50,100,100));
};
PlayerClass.prototype.broadcastPlayerInfo = function() {
    var label = 'P', data = {
            id:         this.data.id,
            avatar_ow:  this.data.avatar_ow,
            username:   this.data.username,
            following:  this.data.following,
            location:   this.data.location,
            x:          this.data.x,
            y:          this.data.y,
            hp:         this.data.hp,
            d:          this.data.direction,
            avatar_forums:this.data.avatar_forums,
            avatar:     this.data.avatar,
            type:       this.data.type,
            level:      this.data.level,
            battle_id:  this.data.battle_id
            
        };
    this.data.connection.broadcast.emit(label, data);
};

PlayerClass.prototype._Disconnect = function() {
    //Tell Everyone
    var label = 'PL'; var data = {};
    data.id = this.data.id;
    this.data.connection.broadcast.emit(label, data);
    
    //Remove from listings
    var index = PlayerTable.indexOf(this);
    if (index !== -1) PlayerTable.splice(index, 1);
    
    if (this.data.id !== void 0) {
        PlayerTableById[this.data.id] = void 0;
    }
    
    if (this.data.username !== void 0) {
        PlayerTableByUsername[this.data.username] = void 0;
    }
    for(var i in PlayerTableByChatroom) {
        var index = PlayerTableByChatroom[i].indexOf(this);
        if (index !== -1) PlayerTableByChatroom[i].splice(index, 1);
    }
};

// Initialize
var app = require('http').createServer(function(req, res) {
    if (req.connection.remoteAddress !== '127.0.0.1') {
        DebugMessage('HTTP request: Denied');
        res.writeHead(404);
        res.end();
        return;
    }
    form = new formidable.IncomingForm();
    form.parse(req, function(e, fields, files) {
        res.writeHead(200, [['Content-type', 'text/plain'], ['Content-Length', 0]]);
        res.write('');
        res.end();
        PHP.Recieve.Process(JSON.parse(fields.data));
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