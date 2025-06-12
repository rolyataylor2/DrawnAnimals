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
function PHPCLASS() {
    var source = {
        Recieve: {
            Process: function(data) {
                switch (data.action) {
                    case 'PlayerUpdateInformation':
                        var player = PLAYERS.Get.Username(data.username);
                        if (player === void 0)
                            return;
                        
                        player.Set.UserInformation(data);
                        break;
                }
            }
        },
        Send: {
            Process: function(filename, args, callback) {
                childProcess.exec("php -d display_errors php/"+filename+".php "+args,callback);
            },
            LoginProcess: function(username, netkey, callback) {
                var args = '';
                args += "'" + username + "' ";
                args += "'" + netkey + "'";
                source.Send.Process("login", args, callback);
            },
            Chat:function(uid, text, room, callback) {
                var args = '';
                args += "'" + uid + "' ";
                args += "'" + text.replace(/'/g,"&apos;") + "' ";
                args += "'" + room + "'";
                source.Send.Process("save_chat", args, callback);
            },
            getMap:function(mapX,mapY,callback) {
                var arguments = '';
                arguments += "'" + mapX + "' ";
                arguments += "'" + mapY + "'";
                source.Send.Process("getMap", arguments, callback);
            },
            initWildBattle:function(userId,tileType,grassType,callback) {
                var arguments = '';
                arguments += "'" + userId + "' ";
                arguments += "'" + tileType + "' ";
                arguments += "'" + grassType + "'";
                source.Send.Process("initWildBattle", arguments, callback);
            },
            addLocationObject:function(userId,itemId) {
                
            }
            
            
        }
    };
    return source;
}
PHP = new PHPCLASS();

function OBJECTCLASS() {

}
OBJECTCLASS.prototype.loadProperties = function(objectnumber) {};
OBJECTCLASS.prototype.loadPropertiesCallback = function(phpErr,phpOut,err) {
    this.id = data.id;
    this.uid = data.uid;
    this.name = data.name;
    this.type = data.type;
    this.hpmax = data.hp;
    this.hp = data.hp;
    this.width = data.width;
    this.height = data.height;
    this.collision = data.collision;
    this.regenerate = data.regenerate;
};
OBJECTCLASS.prototype.damage = function(movetype, movepower) {};
OBJECTCLASS.prototype.regen = function() {};
////

function LOCATIONCLASS() {
    this.data = {
        tiles:{},
        height:{},
        objects:{},
        objectsSpread:{},
        hp:{},
        collision:{},
        water:{}
    };
    this.loadedMaps = {};
}
LOCATIONCLASS.prototype.loadMap = function(xindex,yindex) {
    xindex = Math.floor (xindex / 64);
    yindex = Math.floor (yindex / 64);
    if (this.loadedMaps[xindex.toString()] !== void 0 && this.loadedMaps[xindex.toString()][yindex.toString()] === true) return false;
    if (this.loadedMaps[xindex.toString()] === void 0) this.loadedMaps[xindex.toString()] = [];
    this.loadedMaps[xindex.toString()][yindex.toString()] = true;
    PHP.Send.getMap(xindex,yindex,this.loadMapCallback.bind(this));
    return true;
};
LOCATIONCLASS.prototype.loadMapCallback = function(err, PhpOut, PhpErr) {
    var map = JSON.parse(PhpOut);
    console.log('Loaded Map {'+map.xindex+':'+map.yindex+'}');

    if (this.data.tiles[map.xindex.toString()] === void 0) 
        this.data.tiles[map.xindex.toString()] = {};
    this.data.tiles[map.xindex.toString()][map.yindex.toString()] = map.data.tiles.tiles;
    
    if (this.data.height[map.xindex.toString()] === void 0) 
        this.data.height[map.xindex.toString()] = {};
    this.data.height[map.xindex.toString()][map.yindex.toString()] = map.data.tiles.height;
    
    if (this.data.objects[map.xindex.toString()] === void 0) 
        this.data.objects[map.xindex.toString()] = {};
    this.data.objects[map.xindex.toString()][map.yindex.toString()] = map.data.tiles.objects;
    
    if (this.data.water[map.xindex.toString()] === void 0) 
        this.data.water[map.xindex.toString()] = {};
    this.data.water[map.xindex.toString()][map.yindex.toString()] = map.data.tiles.water;
};
LOCATIONCLASS.prototype.loadMapCollision = function(x,y,width,height) { 
    /// check all objects for square
    for(var i=x; i<x+width; i++) {
        for(var ii=y; ii < y+height; ii++) {
            var object = this.Object(i,ii);
            if (object === void 0) continue;
            // Set the collision map and square hp
            this.Hp(i,ii,object.hp);
            for(var ioff=0; ioff<object.width; ioff++) {
                for(var iioff=0; iioff<object.height; iioff++) {
                    if (object.collision[ioff] === void 0 || object.collision[ioff][iioff] === void 0) continue;
                    this.Collision(i+ioff,ii+iioff,object.collision[ioff][iioff]);
                }
            }
        }
    }
};

LOCATIONCLASS.prototype.broadcastLocationData = function(x,y,width,height) { 
      var data = this.getLocationData(x,y,width,height);
      data.width = width;
      data.height = height;
      data.x = x;
      data.y = y;
      for(var i in PlayerTable) PlayerTable[i].sendLocationData(data);
};
LOCATIONCLASS.prototype.getLocationData = function(xIndex,yIndex,width,height) { 
    var data = {
        tiles:[],
        height:[],
        object:[],
        hp:[],
        water:[],
        x:xIndex,
        y:yIndex,
        w:width,
        h:height
    };
    for(var i=xIndex; i<=xIndex+width; i++) {
        data.tiles[i-xIndex] = [];
        data.height[i-xIndex] = [];
        data.object[i-xIndex] = [];
        data.hp[i-xIndex] = [];
        data.water[i-xIndex] = [];
        for(var ii=yIndex; ii<=yIndex+height; ii++) {
            if (this.loadMap(i,ii)) {
                data = {};
                data.notloaded = true;
                return data;
            };
            data.tiles[i-xIndex][ii-yIndex] = this.Tile(i,ii);
            data.height[i-xIndex][ii-yIndex] = this.Height(i,ii);
            data.object[i-xIndex][ii-yIndex] = this.Object(i,ii);
            data.hp[i-xIndex][ii-yIndex] = this.Hp(i,ii);
            data.water[i-xIndex][ii-yIndex] = this.Water(i,ii);
        }
    }
    return data;
};

LOCATIONCLASS.prototype.AddObject = function(user,itemid) {
    PHP.addLocationObject(user.data.id,itemid,this.AddObjectCallback.bind(this));
};

LOCATIONCLASS.prototype.Map = function(x,y,array) {
    if (this.data[array] === void 0) this.data[array] = {};
    if (this.data[array][Math.floor (x / 64).toString()] === void 0) 
        this.data[array][Math.floor (x / 64).toString()] = {};
    if (this.data[array][Math.floor (x / 64).toString()][Math.floor (y / 64).toString()] === void 0) 
        this.data[array][Math.floor (x / 64).toString()][Math.floor (y / 64).toString()] = {};
    return this.data[array][Math.floor (x / 64).toString()][Math.floor (y / 64).toString()];
};
LOCATIONCLASS.prototype.RelPos = function(x,y) {
    return {'x':x-(Math.floor (x / 64)*64),
            'y':y-(Math.floor (x / 64)*64)};
};
LOCATIONCLASS.prototype.Tile = function(x,y,set) {
    var map = this.Map(x,y,'tile');
    var relative = this.RelPos(x,y);
    if (map[relative.x] === void 0) map[relative.x] = {};
    if (map[relative.x][relative.y] === void 0) map[relative.x][relative.y] = -1;
    if (set !== void 0) map[relative.x][relative.y] = set;
    return map[relative.x][relative.y];
};
LOCATIONCLASS.prototype.Height = function(x,y,set) {
    var map = this.Map(x,y,'height');
    var relative = this.RelPos(x,y);
    if (map[relative.x] === void 0) map[relative.x] = {};
    if (map[relative.x][relative.y] === void 0) map[relative.x][relative.y] = -1;
    if (set !== void 0) map[relative.x][relative.y] = set;
    return map[relative.x][relative.y];
};
LOCATIONCLASS.prototype.Hp = function(x,y,set) {
    var map = this.Map(x,y,'hp');
    var relative = this.RelPos(x,y);
    if (map[relative.x] === void 0) map[relative.x] = {};
    if (map[relative.x][relative.y] === void 0) map[relative.x][relative.y] = -1;
    if (set !== void 0) map[relative.x][relative.y] = set;
    return map[relative.x][relative.y];
};
LOCATIONCLASS.prototype.Object = function(x,y,set) {
    var map = this.Map(x,y,'object');
    var relative = this.RelPos(x,y);
    if (map[relative.x] === void 0) map[relative.x] = {};
    if (map[relative.x][relative.y] === void 0) map[relative.x][relative.y] = -1;
    if (set !== void 0) map[relative.x][relative.y] = set;
    return map[relative.x][relative.y];
};
LOCATIONCLASS.prototype.Water = function(x,y,set) {
    var map = this.Map(x,y,'water');
    var relative = this.RelPos(x,y);
    if (map[relative.x] === void 0) map[relative.x] = {};
    if (map[relative.x][relative.y] === void 0) map[relative.x][relative.y] = -1;
    if (set !== void 0) map[relative.x][relative.y] = set;
    return map[relative.x][relative.y];
};
LOCATIONCLASS.prototype.Collision = function(x,y,set) {
    var map = this.Map(x,y,'collision');
    var relative = this.RelPos(x,y);
    if (map[relative.x] === void 0) map[relative.x] = {};
    if (map[relative.x][relative.y] === void 0) map[relative.x][relative.y] = -1;
    if (set !== void 0) map[relative.x][relative.y] = set;
    return map[relative.x][relative.y];
};

LOCATIONCLASS.prototype.getPlaceFree = function(xindex,yindex) { 
    if (this.loadMap(xindex,yindex)) return true;
    return true;
    return (this.getGrass(xindex,yindex) === 0);
};
LOCATIONCLASS.prototype.getGrass = function(x,y) {
    //if (this.getWater(xIndex,yIndex) !== 0) return 2;
    var obj = this.Object(x,y);
    return (obj===1?true:false);
};
LOCATION = new LOCATIONCLASS();


/**
 * Manage Players
 */
ItemTable = [];
for(var i = 0; i < 200; i++) {
        var x = ~~(Math.random()*100000)-50000;
    var y = ~~(Math.random()*100000)-50000;
    var id = ~~(Math.random()*1000000);
    ItemTable.push({id:id,x:x,y:y});
}
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
    connection.on('M', this._GetMap.bind(this));
    connection.on('MA', this._Activate.bind(this));
    connection.on('MU', this._MoveUp.bind(this));
    connection.on('MD', this._MoveDown.bind(this));
    connection.on('ML', this._MoveLeft.bind(this));
    connection.on('MR', this._MoveRight.bind(this));
    connection.on('MS', this._MoveDone.bind(this));
    
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
    PHP.Send.LoginProcess(data.username,
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
        PlayerTableById[data.id] = this;
        PlayerTableByUsername[data.username] = this;
        this.sendPlayerList();
        this.sendPlayerInfo();
        this.broadcastPlayerInfo();
        this._SpaceSpawnItem();
        this._SpaceGetAllItems();
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
PlayerClass.prototype._MoveUp = function(data) {
    if (LOCATION.getPlaceFree(this.data.x, this.data.y-1)) {
        this.data.y -= 1;
        this.data.direction = 0;
        this.sendBattleInit();
        this.broadcastPosition();
    }
};
PlayerClass.prototype._MoveLeft = function(data) {
    if (LOCATION.getPlaceFree(this.data.x-1, this.data.y)) {
        this.data.x -= 1;
        this.data.direction = 2;
        this.sendBattleInit();
        this.broadcastPosition();
    }
};
PlayerClass.prototype._MoveRight = function(data) {
    if (LOCATION.getPlaceFree(this.data.x+1, this.data.y)) {
        this.data.x += 1;
        this.data.direction = 3;
        this.sendBattleInit();
        this.broadcastPosition();
    }
};
PlayerClass.prototype._MoveDown = function(data) {
    if (LOCATION.getPlaceFree(this.data.x, this.data.y+1)) {
        this.data.y += 1;
        this.data.direction = 1;
        this.sendBattleInit();
        this.broadcastPosition();
    }
};
PlayerClass.prototype._MoveDone = function(data) {
    this.data.direction = data.d;
    this.sendConfirmPosition();
    this.broadcastDirection();
};

PlayerClass.prototype.sendBattleInit = function() {
    var grass = LOCATION.getGrass(this.data.x,this.data.y);
    if (grass !== false) {
        if (Math.floor(Math.random()*18) === 1) {
            
            var tile = LOCATION.Tile(this.data.x,this.data.y);
            PHP.Send.initWildBattle(this.data.id,tile,grass,function(err, PhpOut, PhpErr) {
                if (PhpOut !== '') {
                    this.data.battle_id = PhpOut;
                    this.sendServerCommand('MENU.Game.Play();'+
                                           'GAME.Sound.addBGM("http://img.drawnimals.com/sfx/m/wildBattle.mp3");'+
                                           'GAME.Sound.play("http://img.drawnimals.com/sfx/m/wildBattle.mp3");');
                    
                }
                console.log(err+' - '+PhpOut+' - '+PhpErr);
            }.bind(this));
        }
    }
};
PlayerClass.prototype.broadcastBattle = function() {
    var label = 'P', data = {};
    data.id = this.data.id;
    data.battle_id = this.data.battle_id;
    //@todo change to in the same location
    this.data.connection.broadcast.emit(label, data);
    this.data.connection.emit(label, data);
    
};
PlayerClass.prototype.broadcastPosition = function() {
    var label = 'P', data = {};
    data.id = this.data.id;
    data.x = this.data.x;
    data.y = this.data.y;
    //@todo change to in the same location
    this.data.connection.broadcast.emit(label, data);
};
PlayerClass.prototype.broadcastDirection = function() {
    var label = 'P', data = {};
    data.id = this.data.id;
    data.d = this.data.direction;
    //@todo change to in the same location
    this.data.connection.broadcast.emit(label, data);
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
PlayerClass.prototype._SpaceShoot = function(data) {
    if (Date.now() < this.nextBullet) return;
    this.nextBullet = Date.now()+100;
    var label = 'S';
    this.data.connection.broadcast.emit(label, data);
};
PlayerClass.prototype._SpaceTattle = function(data) {
    if (data.timestamp === void 0 ||  PlayerTableById[data.id] === void 0) {
        console.log('cannot find user '+data.id);
        return;
    }
    if (Math.abs(data.timestamp-PlayerTableById[data.id].lastShotTimestamp) < 1000 && 
        data.timestamp-PlayerTableById[data.id].lastShotTimestamp > 0 &&
        PlayerTableById[data.id].lastShotReporter !== -1) {
        console.log(Math.abs(data.timestamp-PlayerTableById[data.id].lastShotTimestamp))
        PlayerTableById[data.id].lastShotTimestamp  += 1000;
        PlayerTableById[data.id].lastShotReporter = -1;
        PlayerTableById[data.id].data.hp -= 0.5;
        PlayerTableById[data.id].broadcastHp();
    } else {
        PlayerTableById[data.id].lastShotTimestamp = data.timestamp-400;
        PlayerTableById[data.id].lastShotReporter = this.data.id;
    }
    
    
};
PlayerClass.prototype.broadcastHp = function() {
    var label = 'P', data = {};
    data.id = this.data.id;
    data.hp = this.data.hp;
    //@todo change to in the same location
    this.data.connection.emit(label, data);
    this.data.connection.broadcast.emit(label, data);
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


PlayerClass.prototype.sendConfirmPosition = function() {
    var label = 'P', data = {};
    data.id = this.data.id;
    data.x = this.data.x;
    data.y = this.data.y;
    data.d = this.data.direction;
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
        PHP.Send.Chat(this.data.id, data.text,data.room,this.chatCallback.bind(this));
    }
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

PlayerClass.prototype._GetMap = function(data) {
    var x = data.x, y = data.y, width = data.w, height = data.h;
    var map = LOCATION.getLocationData(x,y,width,height);
    this.sendLocationData(map);
};
PlayerClass.prototype.sendLocationData = function(data) {
    var label = 'M';
    this.data.connection.emit(label, data);
};
PlayerClass.prototype._Activate = function(data) {

};
PlayerClass.prototype._Action = function(data) {};
PlayerClass.prototype._UseMove = function(data) {};

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