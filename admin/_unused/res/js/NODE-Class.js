/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
function NODEPLAYER() {
    var source = {
        Data: {}
    };
    return source;
}
function NODECLASS(NetworkKey, Username) {
    var source = {
        Connection: 0,
        NetworkKey: NetworkKey,
        Start:function() {
            if (source.NetworkKey === '' || source.Me.Data.Username ==='') {
                MENU.System.Start();
                return;
            }
            if (io === void 0) {
                //No support
                return null;
            }
            
            source.Connection = io.connect('http://192.168.0.18:8080', {'force new connection': true});
            source.Connection.on('connect', source.Me.LoginInfo);
            source.Connection.on('C', source.Recieve.Chat);
            source.Connection.on('CL', source.Recieve.ChatList);
            source.Connection.on('M', source.Recieve.MultiPlayerInformation);
            source.Connection.on('P', source.Recieve.PlayerInformation);
            source.Connection.on('E', source.Recieve.ExecuteJavascript);
            source.Connection.on('disconnect', source.Recieve.Disconnect);
            source.Connection.on('error', source.Recieve.Error);
            MENU.System.GamePlay();
        },
        Players: {
            All: [],
            Usernames: {},
            Locations: {},
            ChatRooms: {},
            Add: function(object) {
                if (object.Data.Username === void 0)
                    return;
                if (source.Players.Usernames[object.Data.Username] === void 0) {
                    source.Players.Usernames[object.Data.Username] = object;
                    source.Players.All.push(object);
                } else
                    source.Players.Update(object);

                if (source.Players.Locations[object.Data.Location] === void 0)
                    source.Players.Locations[object.Data.Location] = [];
                source.Players.Locations[object.Data.Location].push(object);
            },
            Remove: function(object) {
                if (object.Data.Username === void 0)
                    return;
                if (source.Players.Usernames[object.Data.Username] === void 0)
                    return;
                //Remove From Location
                source.Players.RemoveLocation(object);
                //Remove From All
                var player = source.Players.Usernames[object.Data.Username];
                var index = source.Players.All.indexof(player);
                source.Players.Locations[player.Data.Location].splice(index, 1);
                //Remove From Usernames
                source.Players.Usernames[object.Data.Username] = void 0;
            },
            RemoveLocation: function(object) {
                if (object.Data.Username === void 0)
                    return;
                if (source.Players.Usernames[object.Data.Username] === void 0)
                    return;
                var player = source.Players.Usernames[object.Data.Username];

                if (source.Players.Locations[player.Data.Location] === void 0)
                    source.Players.Locations[player.Data.Location] = [];
                var index = source.Players.Locations[player.Data.Location].indexof(player);
                if (index === -1)
                    return;
                source.Players.Locations[player.Data.Location].splice(index, 1);
            },
            Update: function(object) {
                if (object.Data.Location !== void 0) {
                    if (object.Data.Location !== source.Players.Usernames[object.Data.Username].Data.Location) {
                        source.Players.RemoveLocation(object);
                    }
                    source.Players.Usernames[object.Data.Username].Data.Location = object.Data.Location;
                }
                if (object.Data.Following !== void 0)
                    source.Players.Usernames[object.Data.Username].Data.Following = object.Data.Following;
                if (object.Data.Image !== void 0)
                    source.Players.Usernames[object.Data.Username].Data.Image = object.Data.Image;
                if (object.Data.Avatar !== void 0)
                    source.Players.Usernames[object.Data.Username].Data.Avatar = object.Data.Avatar;
                if (object.Data.Account !== void 0)
                    source.Players.Usernames[object.Data.Username].Data.Account = object.Data.Account;
                if (object.Data.Level !== void 0)
                    source.Players.Usernames[object.Data.Username].Data.Level = object.Data.Level;
                if (object.Data.InBattle !== void 0)
                    source.Players.Usernames[object.Data.Username].Data.InBattle = object.Data.InBattle;
                if (object.Data.x !== void 0)
                    source.Players.Usernames[object.Data.Username].Data.x = object.Data.x;
                if (object.Data.y !== void 0)
                    source.Players.Usernames[object.Data.Username].Data.y = object.Data.y;
            },
            GetLocation: function(location) {
                if (source.Players.Locations[location] === void 0)
                    source.Players.Locations[location] = [];
                return source.Players.Locations[location];
            },
            GetUsername: function(username) {
                if (source.Players.Usernames[username] === void 0)
                    return false;
                return source.Players.Usernames[username];
            }
        },
        Recieve: {
            Disconnect: function() {
            },
            Error: function() {
                console.log('Error');
            },
            Chat: function(data) {
                var text = data.text, room = data.room, username = data.username;
                
                // @todo Add chat to divs
                console.log(username+":"+room+":"+text);
            },
            ChatList:function(data) {
                source.Players.ChatRooms[data.room] = data.users;
            },
            MultiPlayerInformation: function(data) {
                var i = data.users.length;
                while (i--) {
                    source.Recieve.PlayerInformation(data.users[i]);
                }
            },
            PlayerInformation: function(data) {
                var player = new NODEPLAYER();
                if (data.username !== void 0) {
                    if (data.username === source.Me.Data.Username) {
                        source.Recieve.MyInformation(data);
                        return;
                    }
                    player.Data.Username = data.username;
                } else
                    return;
                if (data.location !== void 0)
                    player.Data.Location = data.location;
                if (data.following !== void 0)
                    player.Data.Following = data.following;
                if (data.image !== void 0)
                    player.Data.Image = data.image;
                if (data.avatar !== void 0)
                    player.Data.Avatar = data.avatar;
                if (data.account !== void 0)
                    player.Data.Account = data.account;
                if (data.level !== void 0)
                    player.Data.Level = data.level;
                if (data.inbattle !== void 0)
                    player.Data.InBattle = data.inbattle;
                if (data.x !== void 0)
                    player.Data.x = data.x;
                if (data.y !== void 0)
                    player.Data.y = data.y;
                source.Players.Add(player);
            },
            MyInformation: function(data) {
                if (data.location !== void 0)
                    source.Me.Data.Location = data.location;
                if (data.following !== void 0)
                    source.Me.Data.Following = data.following;
                if (data.image !== void 0)
                    source.Me.Data.Image = data.image;
                if (data.avatar !== void 0)
                    source.Me.Data.Avatar = data.avatar;
                if (data.account !== void 0)
                    source.Me.Data.Account = data.account;
                if (data.level !== void 0)
                    source.Me.Data.Level = data.level;
                if (data.x !== void 0)
                    source.Me.Data.x = data.x;
                if (data.y !== void 0)
                    source.Me.Data.y = data.y;
                if (data.inbattle !== void 0)
                    source.Me.InBattle = data.inbattle;
                source.Me.UpdateInformation();
            },
            ExecuteJavascript: function(data) {
                if (data.key !== source.NetworkKey)
                    return;
                try {
                    eval(data.js);
                } catch (e) { }
            }
        },
        Me: {
            Data: {
                Location: '',
                Username: Username,
                Image:'',
                Following:0,
                Avatar:'',
                Account:'',
                Level:0,
                x:0,
                y:0
            },
            UpdateInformation:function() {
                // Change the sprite/position of the player. 
                // If inbattle === true refresh contentdiv.
            },
            LoginInfo: function() {
                var label = 'L', data = {};
                data.username = source.Me.Data.Username;
                data.key = source.NetworkKey;
                source.Connection.emit(label, data);
            },
            MoveLeft: function() {
                var label = 'ML', data = {};
                source.Connection.emit(label, data);
            },
            MoveRight: function() {
                var label = 'MR', data = {};
                source.Connection.emit(label, data);
            },
            MoveDown: function() {
                var label = 'MD', data = {};
                source.Connection.emit(label, data);
            },
            MoveUp: function() {
                var label = 'MU', data = {};
                source.Connection.emit(label, data);
            },
            MoveStop: function() {
                var label = 'MS', data = {};
                source.Connection.emit(label, data);
            },
            Activate: function() {
                var label = 'MA', data = {};
                source.Connection.emit(label, data);
            },
            Move: function(movename) {
                var label = 'MM', data = {};
                data.movename = movename;
                source.Connection.emit(label, data);
            }
        },
        Chat: {
            Send: function(text, room) {
                var label = 'C', data = {};
                data.text = text;
                data.room = room;
                source.Connection.emit(label, data);
            },
            SendPrivate: function(text, username) {
                var label = 'CP', data = {};
                data.text = text;
                data.username = username;
                source.Connection.emit(label, data);
            },
            JoinRoom: function(room) {
                var label = 'CJ', data = {};
                data.room = room;
                source.Connection.emit(label, data);
            },
            UnJoinRoom: function(room) {
                var label = 'CU', data = {};
                data.room = room;
                source.Connection.emit(label, data);
            }
        }
    };
    return source;
}