/////////////////////////////////////////////////////////////////////////
// Extend Pixi to do dynamic canvas's
//
PIXI.CanvasSprite = function() {
    PIXI.Sprite.apply(this, arguments);

    this._dirtyTexture = false;
};
PIXI.CanvasSprite.prototype = Object.create(PIXI.Sprite.prototype);
PIXI.CanvasSprite.prototype.constructor = PIXI.CanvasSprite;
PIXI.CanvasSprite.prototype._renderWebGL = function(renderSession) {
    if (this._dirtyTexture) {
        this._dirtyTexture = false;
        PIXI.updateWebGLTexture(this.texture.baseTexture, renderSession.gl);
    }

    PIXI.Sprite.prototype._renderWebGL.call(this, renderSession);
};

gameInitFunction = void 0;
function gameSystemQuadTree(level,x,y,width,height) {
    this.level = level;
    
    this.y = y;
    this.x = x;
    this.height = height;
    this.width = width;
    
    this.objects = [];
    this.nodes = [];
}
gameSystemQuadTree.prototype.split = function() {
    var newWidth = this.width/2, newHeight = this.height/2;
    this.nodes[0] = new gameSystemQuadTree(this.level+1, this.x,            this.y,             newWidth,newHeight);
    this.nodes[1] = new gameSystemQuadTree(this.level+1, this.x+newWidth,   this.y,             newWidth,newHeight);
    this.nodes[2] = new gameSystemQuadTree(this.level+1, this.x+newWidth,   this.y+newHeight,   newWidth,newHeight);
    this.nodes[3] = new gameSystemQuadTree(this.level+1, this.x,            this.y+newHeight,   newWidth,newHeight);
};
gameSystemQuadTree.prototype.getIndex = function(x,y,width,height) {
    var index = -1;
    if (x+width < (this.x+(this.width/2))) {
        if (y+height < (this.y+(this.height/2)))
            index = 0;
        if (y > (this.y+(this.height/2)))
            index = 3;
    } 
    if (x > (this.x+this.width/2)){
        if (y+height < (this.y+(this.height/2)))
            index = 1;
        if (y > (this.y+(this.height/2)))
            index = 2;
    }
        
    return index; // if it cant fit into a corner;
};
gameSystemQuadTree.prototype.add = function(objRef) {
    if (objRef === void 0 || objRef.position === void 0 || objRef.position.collision === void 0) return;
    if (this.nodes[0] !== void 0) {
        var index = this.getIndex(objRef.position.x,objRef.position.y,objRef.position.width,objRef.position.height);
        if (index !== -1) {
            this.nodes[index].add(objRef);
            return;
        }
    }
    
    this.objects.push(objRef);
    
    if (this.objects.length > 10 && this.level < 4) {
        if (this.nodes[0] === void 0) this.split();
        
        for (var i=0,l=this.objects.length;i<l;i++) {
            if (this.objects[i] === void 0 || this.objects[i].position === void 0) continue;
            var index = this.getIndex(this.objects[i].position.x,
                                    this.objects[i].position.y,
                                    this.objects[i].position.width,
                                    this.objects[i].position.height);
            if (index !== -1) {
                this.nodes[index].add(this.objects.splice(i,1)[0]);
                i-=1;
            }
        }
    }
};
gameSystemQuadTree.prototype.get = function(x,y,width,height) {
    var results = [];
    var index = this.getIndex(x,y,width,height);
    if (this.nodes[0] !== void 0) {
        if (index !== -1) {
            results = results.concat(this.nodes[index].get(x,y,width,height));
            results = results.concat(this.objects);
            return results;
        } else {
            results = results.concat(this.nodes[0].get(x,y,width,height));
            results = results.concat(this.nodes[1].get(x,y,width,height));
            results = results.concat(this.nodes[2].get(x,y,width,height));
            results = results.concat(this.nodes[3].get(x,y,width,height));
            results = results.concat(this.objects);
            return results;
        }
    }
    return this.objects;
};


// Objects
function gameSystemObjectHandler(parent) {
    this.parent = parent;
    this.activeObjects = [];
    this.inactiveObjects = [];
    this.keyObjects = [];
    this.parent = parent;
    this.datadefault = {};
    this.is_on = true;
    this._mapdata = {};
}
gameSystemObjectHandler.prototype._activate = function(index) {
    if ((this.inactiveObjects[index]['Activate'] !== void 0 ? this.inactiveObjects[index]['Activate']() : false) !== false)
        this.activeObjects.push(this.inactiveObjects.splice(index, 1)[0]);
};
gameSystemObjectHandler.prototype._deactivate = function(index) {
    if ((this.activeObjects[index]['Deactivate'] !== void 0 ? this.activeObjects[index]['Deactivate']() : false) !== false)
        this.inactiveObjects.push(this.activeObjects.splice(index, 1)[0]);
};
gameSystemObjectHandler.prototype._atUpdate = function() {
    this._atLastData = this._atData;
    this._atData = new gameSystemQuadTree(0,this.parent.camera.viewX(),
                                       this.parent.camera.viewY(),
                                       this.parent.camera.viewWidth(),
                                       this.parent.camera.viewHeight());
};
gameSystemObjectHandler.prototype.searchFor = function(key, value, allobjects) {
    var results = [];
    var i = this.activeObjects.length;
    while(i--)
        if (this.activeObjects[i][key] !== void 0 && this.activeObjects[i][key] === value) 
            results.push(this.activeObjects[i]);
    if (allobjects === void 0) return results;
    i = this.inactiveObjects.length;
    while(i--) 
        if (this.inactiveObjects[i][key] !== void 0 && this.inactiveObjects[i][key] === value) 
            results.push(this.inactiveObjects[i]);
    if (results.length > 0)
        return results;
    return false;
    
};

gameSystemObjectHandler.prototype.atPosition = function(x, y, obj) {
    if (this._atLastData === void 0) return false;
    var objects = this._atLastData.get(x,y,1,1);
    if (objects.length > 0) {
        var results = [];
        for (var i=0,l=objects.length;i<l;i++) {
            var object = objects[i];
            if (obj === void 0 || objects[i].constructor.name === obj) {
                if (x <= object.position.x+object.position.width &&
                    x >= object.position.x &&
                    y <= object.position.y+object.position.height &&
                    y >= object.position.y)
                    results.push(objects[i]);
            }   
        }
        if (results.length === 0) return false;
        return results;
    }
    return false;
};
gameSystemObjectHandler.prototype.atPoint = function(x, y, z, obj) {
    if (this._atLastData === void 0) return false;
    var objects = this._atLastData.get(x,y,1,1);
    if (objects.length > 0) {
        var results = [];
        for (var i=0,l=objects.length;i<l;i++) {
            var object = objects[i];
            if (obj === void 0 || objects[i].constructor.name === obj) {
                if (x < object.position.x+object.position.width &&
                    x > object.position.x &&
                    y < object.position.y+object.position.height &&
                    y > object.position.y &&
                    z < object.position.z+object.position.depth &&
                    z > object.position.z)
                    results.push(objects[i]);
            }   
        }
        if (results.length === 0) return false;
        return results;
    }
    return false;
};
gameSystemObjectHandler.prototype.atRect = function(x, y, width, height, obj) {
    if (this._atLastData === void 0) return false;
    var objects = this._atLastData.get(x,y,width,height);
    if (objects.length > 0) {
        var results = [];
        for (var i=0,l=objects.length;i<l;i++) {
            var object = objects[i];
            if (obj === void 0 || objects[i].constructor.name === obj) {
                if (object.position.x <= x+width && 
                    object.position.x+object.position.width >= x &&
                    object.position.y <= y+height &&
                    object.position.y+object.position.height >= y)
                    results.push(object);
            }   
        }
        if (results.length === 0) return false;
        return results;
    }
    return false;
};
gameSystemObjectHandler.prototype.atCube = function(x, y, z, width, height, depth, obj) {
    if (this._atLastData === void 0) return false;
    var objects = this._atLastData.get(x,y,width,height);
    if (objects.length > 0) {
        var results = [];
        for (var i=0,l=objects.length;i<l;i++) {
            var object = objects[i];
            if (obj === void 0 || objects[i].constructor.name === obj) {
                if (object.position.x <= x+width && 
                    object.position.x+object.position.width >= x &&
                    object.position.y <= y+height &&
                    object.position.y+object.position.height >= y &&
                    object.position.z <= z+depth &&
                    object.position.z+object.position.depth >= z)
                    results.push(object);
            }   
        }
        if (results.length === 0) return false;
        return results;
    }
    return false;
    
};
gameSystemObjectHandler.prototype.atCircle = function(x, y, obj) {
};
gameSystemObjectHandler.prototype.atLine = function(x, y, x2, y2, obj) {
};
gameSystemObjectHandler.prototype.atSprite = function(x, y, sprite, obj) {
};
gameSystemObjectHandler.prototype.atCollision = function(obj, obj2) {};

gameSystemObjectHandler.prototype.add = function(objRef) {
    this.activeObjects.push(objRef);
    if (objRef.Init !== void 0)
        objRef.Init();

    objRef.SYSTEM = {id:Math.floor(Math.random()*100000)};
    return objRef;
};
gameSystemObjectHandler.prototype.remove = function(objRef) {
    var i = this.activeObjects.length;
    while (i--) {
        if (this.activeObjects[i] !== void 0 && this.activeObjects[i] === objRef) {
            (this.activeObjects[i].Destroy !== void 0 ? this.activeObjects[i].Destroy() : false);
            (this.activeObjects[i].SYSTEM.keyName !== void 0 ? this.keyObjects[this.activeObjects[i].keyName] = void 0 : false);
            this.activeObjects.splice(i, 1);
            return;
        }
    }
    i = this.inactiveObjects.length;
    while (i--) {
        if (this.inactiveObjects[i] !== void 0 && this.inactiveObjects[i] === objRef) {
            (this.inactiveObjects[i].Destroy !== void 0 ? this.inactiveObjects[i].Destroy() : false);
            (this.inactiveObjects[i].SYSTEM.keyName !== void 0 ? this.keyObjects[this.inactiveObjects[i].keyName] = void 0 : false);
            this.inactiveObjects.splice(i, 1);
            return;
        }
    }
};
gameSystemObjectHandler.prototype.executeEvent = function(eventName, all) {
    var collision = (eventName === 'Step' ? true : false);
    for(var i=0, l=this.activeObjects.length; i<l; ++i) {
        if(this.activeObjects[i] !== void 0 && this.activeObjects[i][eventName] !== void 0)
            this.activeObjects[i][eventName]();
        if (collision && this._atData !== void 0) this._atData.add(this.activeObjects[i]);
    }
    if (all === void 0) return;
    for(var i=0, l=this.inactiveObjects.length; i<l; ++i) {
        if (this.inactiveObjects[i] !== void 0 && this.inactiveObjects[i][eventName] !== void 0)
            this.inactiveObjects[i][eventName]();
    }
};
gameSystemObjectHandler.prototype.deactivateArea = function(x, y, width, height, inside) {
    if (inside === void 0)
        inside = false;
    var i = this.activeObjects.length;
    while (i--)
        if (this.activeObjects[i].position !== void 0) 
            if (!inside) {
                if (this.activeObjects[i].position.x + this.activeObjects[i].position.width < x ||
                        this.activeObjects[i].position.y + this.activeObjects[i].position.height < y ||
                        this.activeObjects[i].position.x > x + width ||
                        this.activeObjects[i].position.y > y + height)
                    this._deactivate(i);
            } else {
                if (this.activeObjects[i].position.x + this.activeObjects[i].position.width > x)
                    if (this.activeObjects[i].position.y + this.activeObjects[i].position.height > y)
                        if (this.activeObjects[i].position.x < x + width)
                            if (this.activeObjects[i].position.y < y + height)
                                this._deactivate(i);
            }
        
};
gameSystemObjectHandler.prototype.activateArea = function(x, y, width, height, inside) {
    if (inside === void 0)
        inside = false;
    var i = this.inactiveObjects.length;
    while (i--)
        if (this.inactiveObjects[i].position !== void 0)
            if (!inside) {
                if (this.inactiveObjects[i].position.x + this.inactiveObjects[i].position.width < x ||
                        this.inactiveObjects[i].position.y + this.inactiveObjects[i].position.height < y ||
                        this.inactiveObjects[i].position.x > x + width ||
                        this.inactiveObjects[i].position.y > y + height)
                    this._activate(i);
            } else {
                if (this.inactiveObjects[i].position.x + this.inactiveObjects[i].position.width > x)
                    if (this.inactiveObjects[i].position.y + this.inactiveObjects[i].position.height > y)
                        if (this.inactiveObjects[i].position.x < x + width)
                            if (this.inactiveObjects[i].position.y < y + height)
                                this._activate(i);
            }
};

// Controls
function gameSystemJoystick(parent) {
    this._parent = parent;
    this._supported = 'createTouch' in document;
    this._enabled = this.supported;
    this._direction = 0;
    this._velocity = 0;
    this._buttons = {};
}
gameSystemJoystick.prototype.BindButtonToElement = function(parentElement, virtualKey) {
    
};
gameSystemJoystick.prototype.UnbindButton = function(virtualKey) {
    
};
gameSystemJoystick.prototype.BindJoystickToElement = function(parentElement) {
    if (!this._supported) return false;
    parentElement.addEventListener( 'touchstart', this._touchStart.bind(this), false );
    parentElement.addEventListener( 'touchmove', this._touchMove.bind(this), false );
    parentElement.addEventListener( 'touchend', this._touchEnd.bind(this), false );
    return true;
};
gameSystemJoystick.prototype._touchStart = function(event) {
    console.log(event.pageX + "/" + event.pageY);
};
gameSystemJoystick.prototype._touchMove = function(event) {
    
};
gameSystemJoystick.prototype._touchEnd = function(event) {
    
};

gameSystemJoystick.prototype.Enabled = function(enable) {
    if (this._supported === false) return false;
    if (enable !== void 0) this._enabled = !enable;
    return this._enabled;
};
gameSystemJoystick.prototype.Direction = function() {
    return this._direction;
};
gameSystemJoystick.prototype.Velocity = function() {
    return this._velocity;
};

// Network
function gameSystemNetworkPlayers(parent) {
    this.parent = parent;
    this._username = {};
    this._id = [];
    this._chatRooms = {};
    this._newPlayerCallback = function() {};
    this._updatePlayerCallback = function() {};
    this._leavePlayerCallback = function() {};
}
gameSystemNetworkPlayers.prototype._incomingCreate = function(data) {
    this._id[data.id] = data;
    this._newPlayerCallback(this._id[data.id]);
};
gameSystemNetworkPlayers.prototype._incoming = function(data) {
    if (data.users !== void 0) {
        for(var i in data.users) {
            this._incoming(data.users[i]);
        }
    } else if (data.id !== void 0) {
        if (this._id[data.id] === void 0) this._incomingCreate(data);
        else {
            this._id[data.id] = $.extend(true, this._id[data.id], data);
            this._updatePlayerCallback(this._id[data.id]);
        }
        if (this._id[data.id].username !== void 0) this._username[this._id[data.id].username] = this._id[data.id];
    }
};
gameSystemNetworkPlayers.prototype._outgoing = function(data) {
    if (data.id !== void 0 && this._id[data.id] !== void 0) {
        if (this._leavePlayerCallback !== void 0) this._leavePlayerCallback(this._id[data.id]);
        this._username[this._id[data.id].username] = void 0;
        this._id[data.id] = void 0;
    }
};

function gameSystemNetwork(parent) {
    this.parent = parent;
    this.players = new gameSystemNetworkPlayers(this);
    this.connection = 0;
    this._key = '';
    this._username = '';
}
gameSystemNetwork.prototype._host = function() {
    var pathArray = window.location.href.split( '/' );
    return pathArray[0] + '//' + pathArray[2] + ':8080';
};
gameSystemNetwork.prototype._connect = function(username,key) {
    if (username !== void 0) this._username = username;
    if (key !== void 0) this._key = key;
    if (this._key === void 0) return this._outputToElement('No Network Key Provided - Try Refreshing');
    if (this._username === void 0) return this._outputToElement('No Username Provided - Try Refreshing');
    if (io === void 0) {
        this._outputToElement('Your Browser Does Not Support Socket.IO');
        this.connection = void 0;
        return;
    }
    this.connection = io.connect(this._host(), {'force new connection': true,
                                                'max reconnection attempts': 5,
                                                'reconnect': false});
    if (this.connection !== void 0) {
        this.connection.on('connect', this._login.bind(this));
        this.connection.on('connect_failed', this._disconnect.bind(this));
        this.connection.on('disconnect', this._disconnect.bind(this));
        this.connection.on('error', this._disconnect.bind(this));
        this.Recieve('P',this.players._incoming.bind(this.players));
        this.Recieve('PL',this.players._outgoing.bind(this.players));
    }
};
gameSystemNetwork.prototype._login = function() {
    var label = 'L', data = {};
    data.username = this._username;
    data.key = this._key;
    this.connection.emit(label, data);
};
gameSystemNetwork.prototype._disconnect = function() {
    this.connection = 0;
    $('#C').html('<center style="font-size:12px; color:rgb(172, 172, 172); padding:20px;">You have been disconnected from the server!<br/><br/>'+
                 'You may have multiple game windows open?<br/><br/> '+
                 'When you have more then one game window open only one is allowed to connect to the server to help safeguard the gameplay.<br/><br/>'+
                 'If this is not the case then try logging out, then logging back in again.<br/>'+
                 'For more help contact <a href="http://PokeWorlds.com/user.php?id=11">Rolyataylor2</a>'+
                 '</center>');
    this._outputToElement('Disconnected From The Server... Retrying in 15 seconds...');
    //setTimeout(this._connect.bind(this),15000);
};
gameSystemNetwork.prototype._consoleToElement = function(element) {
    this._consoleElement = $(element);
};
gameSystemNetwork.prototype._outputToElement = function(text) {
    if (this._consoleElement !== void 0)
        this._consoleElement.append('<b>'+text+'</b>');
    else console.log(text);
};
gameSystemNetwork.prototype.Send = function(type,data) {
    this.connection.emit(type, data);
};
gameSystemNetwork.prototype.Recieve = function(type,callback) {
    this.connection.on(type, callback);
};
gameSystemNetwork.prototype.NewPlayerCallback = function(callback) {
    if (callback !== void 0) this.players._newPlayerCallback = callback;
};
gameSystemNetwork.prototype.UpdatePlayerCallback = function(callback) {
    if (callback !== void 0) this.players._updatePlayerCallback = callback;
};
gameSystemNetwork.prototype.LeavePlayerCallback = function(callback) {
    if (callback !== void 0) this.players._leavePlayerCallback = callback;
};

// Camera
function gameSystemCamera(parent) {
    this.data = {
        x: 0, width: 600,
        y: 0, height: 250,
        top:false, bottom:false, left:false, right:false,
        speed: 0,
        zoom: 2,
        resolution: 0,
        object: false
    };
    this.parent = parent;
    this.parentElement = void 0;
    this.renderer = PIXI.autoDetectRenderer(this.data.width, this.data.height);
    $(this.renderer.view).addClass('game');

    this.stageContainer = new PIXI.Stage(0x000000);
    this.outerContainer = new PIXI.DisplayObjectContainer();
    this.innerContainer = new PIXI.DisplayObjectContainer();
    this.innerContainerTexture = new PIXI.RenderTexture(320, 240);
    this.innerContainerSprite = new PIXI.Sprite(this.innerContainerTexture);

    this.outerContainer.addChild(this.innerContainerSprite);
    this.stageContainer.addChild(this.outerContainer);

    this.takeScreenShot = false;
    this.screenShotData = 0;
}
gameSystemCamera.prototype._bindToElement = function(parentElement) {
    this.parentElement = parentElement;
    this.parentElement.append(this.renderer.view);
    this.parentElement.click(function() {$(this).focus();})
            .focus(function() { $('html').css({'overflow':'hidden','position':'fixed','width':'100%','height':'100%'});})
            .blur(function() { $('html').css({'overflow':'auto','position':'relative','width':'100%','height':'auto'});});
    this._resizeToElement();
    game.unPause('NoCanvasSelected');
    game.object.executeEvent('CameraBindToElement');
    this._resizeToElement();
};
gameSystemCamera.prototype._resizeToElement = function() {
    if (this.parentElement === void 0) return;
    var zoomh = this.parentElement.height() / this.data.height;
    var zoomw = this.parentElement.width() / this.data.width;
    var zoom = Math.min(zoomh, zoomw);
    if (this.parentElement.width() < 600) {
        zoom = 1;
        this.data.width = this.parentElement.width();
        this.data.height = this.parentElement.height();
    }
    this.renderer.view.width = this.data.width * zoom;
    this.renderer.view.height = this.data.height * zoom;

    $(this.renderer.view).css('margin-left', -(~~(this.data.width * zoom) / 2) + 'px').css('margin-top', -(~~(this.data.height * zoom) / 2) + 'px');
    this.outerContainer.scale.x = zoom;
    this.outerContainer.scale.y = zoom;

    this.innerContainer.width = this.data.width;
    this.innerContainer.height = this.data.height;
    this.innerContainerTexture.resize(~~this.data.width, ~~this.data.height);
    this.renderer.resize(this.data.width * zoom, this.data.height * zoom);
};
gameSystemCamera.prototype._updatePosition = function() {
    if (this.data.object !== false) {
        var pos = this.data.object.position;
        this.data.x = (this.data.x+ (-Math.floor(pos.x) + ~~(this.viewWidth() / 2) - (pos.width/2)))/2;
        this.data.y = (this.data.y+ (-Math.floor(pos.y) + ~~(this.viewHeight() / 2) - (pos.height/2)))/2;
    }
    
    if (this.data.top !== false && 
        this.data.top > -this.data.y) this.data.y = -this.data.top;
    if (this.data.left !== false && 
        this.data.left > -this.data.x) this.data.x = -this.data.left;
    if (this.data.bottom !== false && 
        this.data.bottom < -this.data.y+this.viewHeight()) this.data.y = -this.data.bottom+this.viewHeight();
    if (this.data.right !== false &&
        this.data.right < -this.data.x+this.viewWidth()) this.data.x = -this.data.right+this.viewWidth();
    this.data.x = ~~this.data.x;
    this.data.y = ~~this.data.y;
    this.innerContainer.position.x = this.data.x;
    this.innerContainer.position.y = this.data.y;
};
gameSystemCamera.prototype._update = function() {
    this._updatePosition();

    this._sortDepth();
    this.innerContainerTexture.render(this.innerContainer, this.innerContainer.position);
    if (this.innerContainerTexture._uvs !== void 0) {
        this.innerContainerTexture._uvs.x1 = 1;
        this.innerContainerTexture._uvs.x2 = 1;
        this.innerContainerTexture._uvs.y2 = 1;
        this.innerContainerTexture._uvs.y3 = 1;
    }

    this.renderer.render(this.stageContainer);
    if (this.takeScreenShot) {
        var data = this.renderer.view.toDataURL();
        this.screenShotData = $("<img src='" + data + "'/>")[0];
        this.takeScreenShot = false;
    }
};
gameSystemCamera.prototype._sortDepth = function() {
    this.innerContainer.children.sort(function(a, b) {
        if (a.position.z === b.position.z) return a.id-b.id;
        return a.position.z - b.position.z;
    });
};

gameSystemCamera.prototype.viewX = function(newPosition) {
    return -(this.data.x = (newPosition !== void 0 ? this.data.x = -(newPosition) : this.data.x));
};
gameSystemCamera.prototype.viewY = function(newPosition) {
    return -(this.data.y = (newPosition !== void 0 ? this.data.y = -(newPosition) : this.data.y));
};
gameSystemCamera.prototype.viewWidth = function(newWidth) {
    if (newWidth !== void 0) {
        this.data.width = newWidth;
        this.data.zoom = newWidth / this.portWidth();
        this._resizeToElement();
    }
    return this.data.width;
};
gameSystemCamera.prototype.viewHeight = function(newHeight) {
    if (newHeight !== void 0) {
        this.data.height = newHeight;
        this.data.zoom = newHeight / this.portHeight();
        this._resizeToElement();
    }
    return this.data.height;
};
gameSystemCamera.prototype.viewBorderTop = function(top) {
    if (top !== void 0) this.data.top = top;
    return this.data.top;
};
gameSystemCamera.prototype.viewBorderBottom = function(bottom) {
    if (bottom !== void 0) this.data.bottom = bottom;
    return this.data.bottom;
};
gameSystemCamera.prototype.viewBorderLeft = function(left) {
    if (left !== void 0) this.data.left = left;
    return this.data.left;
};
gameSystemCamera.prototype.viewBorderRight = function(right) {
    if (right !== void 0) this.data.right = right;
    return this.data.right;
};

gameSystemCamera.prototype.viewFollowObject = function(objRef) {
    if (objRef !== void 0 && objRef.position !== void 0) {
        this.viewX(objRef.position.x-(this.viewWidth()/2));
        this.viewY(objRef.position.y-(this.viewHeight()/2));
    }
    return (objRef !== void 0 ? this.data.object = objRef : this.data.object);
};
gameSystemCamera.prototype.viewFollowSpeed = function(newSpeed) {
    return (newSpeed !== void 0 ? this.data.speed = newSpeed : this.data.speed);
};
gameSystemCamera.prototype.portWidth = function() {
    return this.parentElement.height();
};
gameSystemCamera.prototype.portHeight = function() {
    return this.parentElement.height();
};
gameSystemCamera.prototype.enableSpriteHud = function(pixiSprite) {
    if (pixiSprite.parent !== void 0 && pixiSprite.parent !== null) return;
    this.outerContainer.addChild(pixiSprite);
    pixiSprite.id = Math.floor(Math.random()*100000);
    return pixiSprite;
};
gameSystemCamera.prototype.disableSpriteHud = function(pixiSprite) {
    if (pixiSprite.parent === void 0 || pixiSprite.parent === null) return;
    this.outerContainer.removeChild(pixiSprite);
    pixiSprite.id = Math.floor(Math.random()*100000);
    return pixiSprite;
};
gameSystemCamera.prototype.enableSprite = function(pixiSprite) {
    if (pixiSprite.parent !== void 0 && pixiSprite.parent !== null) return;
    this.innerContainer.addChild(pixiSprite);
    pixiSprite.id = Math.floor(Math.random()*100000);
    return pixiSprite;
};
gameSystemCamera.prototype.disableSprite = function(pixiSprite) {
    if (pixiSprite.parent === void 0 || pixiSprite.parent === null) return;
    this.innerContainer.removeChild(pixiSprite);
    return pixiSprite;
};

function gameSystemSound() {
    this.sounds = {};
    this.is_muted = false;
    this.is_BGMmuted = false;
}
gameSystemSound.prototype.add = function(url) {
    if (this.sounds[url] !== void 0)
        return url;
    var snd = new Audio(url);
    
    snd.muted = this.is_muted;
    snd.bgm = false;
    snd.loopHandler = function() {
        this.currentTime = 0;
        this.play();
    };
    this.sounds[url] = snd;
    this.sounds[url].channels = [];
    this.sounds[url].channels.push(snd.cloneNode( true ));
    this.sounds[url].channels.push(snd.cloneNode( true ));
    this.sounds[url].channels.push(snd.cloneNode( true ));
    this.sounds[url].channels.push(snd.cloneNode( true ));
    this.sounds[url].channels.push(snd.cloneNode( true ));
    return url;
};
gameSystemSound.prototype.addBGM = function(url) {
    if (this.sounds[url] !== void 0)
        return url;

    var snd = new Audio(url);
    snd.muted = this.is_BGMmuted;
    snd.bgm = true;
    snd.loopHandler = function() {
        this.currentTime = 0;
        this.play();
    };
    this.sounds[url] = snd;
    this.setLooping(url, true);
    return url;
};
gameSystemSound.prototype.remove = function(url) {
    this.sounds[url] = void 0;
};
gameSystemSound.prototype.play = function(url) {
    if (this.sounds[url] === void 0)
        this.add(url);
    if (this.sounds[url] !== void 0) {
        if (this.sounds[url].bgm)
            this.sounds[url].muted = this.is_BGMmuted;
        else {
            if (!this.sounds[url].paused) {
                for(var i in this.sounds[url].channels)
                    if (this.sounds[url].channels[i].paused === true) {
                        this.sounds[url].channels[i].play();
                        return;
                    }
            }
            this.sounds[url].muted = this.is_muted;
        }
        this.sounds[url].play();
        if (this.sounds[url].bgm === true) {
            if (this.sounds['bgm'] !== void 0 && this.sounds['bgm'] !== this.sounds[url]) {
                this.sounds['bgm'].pause();
                this.sounds['bgm'].currentTime = 0;
            }
            this.sounds['bgm'] = this.sounds[url];
        }
    }
};
gameSystemSound.prototype.pause = function(url) {
    if (this.sounds[url] !== void 0) {
        this.sounds[url].pause();
    }
};
gameSystemSound.prototype.stop = function(url) {
    if (this.sounds[url] !== void 0) {
        this.sounds[url].pause();
        this.sounds[url].currentTime = 0;
    }
};
gameSystemSound.prototype.volume = function(url,volume) {
    if (this.sounds[url] !== void 0) {
        if (volume === void 0) return this.sounds[url].volume;
        if (this.sounds[url].channels !== void 0)
            for(var i in this.sounds[url].channels) {
                this.sounds[url].channels[i].volume = volume;
            }
        this.sounds[url].volume = volume;
    }
};
gameSystemSound.prototype.setLooping = function(url, loop) {
    if (this.sounds[url] !== void 0) {
        if (loop) {
            this.sounds[url].addEventListener('ended', this.sounds[url].loopHandler, false);
        } else {
            this.sounds[url].removeEventListener('ended', this.sounds[url].loopHandler, false);
        }
    }
};
gameSystemSound.prototype.mute = function(mute) {
    if (mute === void 0)
        this.is_muted = !this.is_muted;
    else
        this.is_muted = (mute === true);
    for (var i in this.sounds) {
        if (this.sounds[i].bgm === false) {
            this.sounds[i].muted = this.is_muted;
        }
    }
};
gameSystemSound.prototype.muteBGM = function(mute) {
    if (mute === void 0)
        this.is_BGMmuted = !this.is_BGMmuted;
    else
        this.is_BGMmuted = (mute === true);
    for (var i in this.sounds) {
        if (this.sounds[i].bgm === true) {
            this.sounds[i].muted = this.is_BGMmuted;
        }
    }
};

function gameSystem() {
    this.created = Date.now();
    this._state = 0;
    
    this._loop = {
        next: (new Date).getTime(),
        number: 0,
        skip: 1000 / 60
    };
    this._loop.next = (new Date).getTime();
    this._loop.number = 0;
    this._loop.skip = 1000 / 60;
    
    this._pauseState = {
        number: 0
    };
    this.kill = false;

    this.camera = new gameSystemCamera(this);
    this.object = new gameSystemObjectHandler(this);
    this.sound = new gameSystemSound();
    this.joystick = new gameSystemJoystick(this);
    this.network = new gameSystemNetwork(this);
    this.requestAnimationFrame = window.requestAnimationFrame.bind(window);
//    $(window).blur(function() { this.pause('pageHidden'); }.bind(this));
//    $(window).focus( function() { this.unPause('pageHidden'); }.bind(this));
//    $(document).blur(function() { this.pause('pageDocHidden'); }.bind(this));
//    $(document).focus( function() { this.unPause('pageDocHidden'); }.bind(this));
    $(window).resize( this.camera._resizeToElement.bind(this.camera));
    
    if (typeof Stats !== 'undefined') {
        this.stats = new Stats();
        document.body.appendChild( this.stats.domElement );
        $(this.stats.domElement).css('position','absolute').css('bottom','100px').css('right','0px').css('top','auto').css('left','auto');

    }
}
gameSystem.prototype.pause = function(reason) {
    if (this._pauseState[reason] !== void 0)
        return;
    this._pauseState[reason] = true;
    this._pauseState.number += 1;
    if (this._pauseState.number === 1)
        this.object.executeEvent('Paused');
};
gameSystem.prototype.unPause = function(reason) {
    if (this._pauseState[reason] === void 0)
        return;
    this._pauseState[reason] = void 0;
    this._pauseState.number -= 1;
    if (this._pauseState.number === 0)
        this.object.executeEvent('UnPaused');
};
gameSystem.prototype.end = function() {
    this.kill = true;
};
gameSystem.prototype.running = function() { return (this._state === 1);};
gameSystem.prototype._eachFrame = function() {
    if (this.kill) {
        $(window).resize(function() {
            return;
        });
        this.object.executeEvent('GameEnd');
        delete game;
        return;
    }
    if (this._pauseState.number > 0) {
        this._loop.next = (new Date).getTime();
        window.requestAnimationFrame(this._eachFrame.bind(this));
        this._state = 2;
        return;
    }
    this._state = 1;
    if (this.stats !== void 0) this.stats.begin();
    this._loop.number = 0;
    do {
        this._loop.next += this._loop.skip;
        ++this._loop.number;
         this.object.executeEvent('Step');
        this.object._atUpdate();
        keypress = [];
        if (this._loop.number > 10) {
            this._loop.next = Date.now();
            this._loop.number = 1;
        }
    } while (Date.now() > this._loop.next) 
    if (this._loop.number) this.camera._update();
    this.requestAnimationFrame(this._eachFrame.bind(this));
    
    if (this.stats !== void 0) this.stats.end();
};
var game = GAME = void 0;


