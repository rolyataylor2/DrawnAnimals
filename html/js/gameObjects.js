
function InitGameObjects() {
    game._eachFrame();
    game.camera._bindToElement($('div#game'));
    screenTransitionIn(swirlEffect,'#000000','#0000FF',1,100);
}
function NavSetOptions(options) {
    for(var i in options) {
        $('nav').append('<a href="#" onclick="'+options[i]+'">'+i+'</a>');
    }
}

//////////////////////////////////////////////////////////////////////////
// Living Dead - Sprite will follow any parentObject at object.position.speed
// 
// @param {any object with position} parentObject
// @returns {characterSkinObject}
//
characterSkinObjectCanvas = document.createElement('canvas');
characterSkinObjectCanvas.width = 256; characterSkinObjectCanvas.height = 256;
characterSkinObjectCanvas.ctx    = characterSkinObjectCanvas.getContext('2d');
function rpgWorldMapTile() {
    for(var i = -15;i<15;i++) 
        for(var ii=-15;ii<15;ii++) {
            var texture = new PIXI.Texture(PIXI.Texture.fromImage('img/e/1/border.png'));
            texture.setFrame(new PIXI.Rectangle(32, 32, 32, 32));

            var sprite = new PIXI.Sprite(texture);
            sprite.position.x = i*32;
            sprite.position.y = ii*32;
            sprite.position.z = -1000000;
            game.camera.enableSprite(sprite);
        }
}
function rpgWorldMapTileGrass() {
    for(var i = -15;i<15;i++) 
        for(var ii=-15;ii<15;ii++) {
            if (Math.random() < 0.2) {
                new rpgWorldObject(i*32,ii*32);
            }
        }
}

function rpgFxLeaf(x,y) {
    this.position = {
        x:x, y:y, z:0, xoffset:0,
        zspeed:0, animationFrame:Math.random()*4
    };
    this.Sprite = new PIXI.Sprite(PIXI.Texture.fromImage('img/e/1/grass.leaf.png'));
    this.Sprite.position.set(x,y,y);
    this.Sprite.anchor.x = this.Sprite.anchor.y = 0.5;
    this.Sprite.rotation = Math.random()*180;
    this.position.zspeed = -0.2-(Math.random()*0.1);
    game.object.add(this);
    game.camera.enableSprite(this.Sprite);
};
rpgFxLeaf.prototype.Step = function() {
    if (this.position.z > 0) {
        this.Sprite.alpha -= 0.05;
        if (this.Sprite.alpha < 0) {
            game.camera.disableSprite(this.Sprite);
            game.object.remove(this);
        }
    }
    
    this.position.zspeed += 0.004;
    this.position.z += this.position.zspeed;
    this.position.animationFrame = (this.position.animationFrame+0.02)%3;
    this.position.xoffset = (Math.sin(this.position.animationFrame*2)*4);
    
    this.Sprite.position.set(this.position.x+this.position.xoffset,
                             this.position.y+this.position.z,
                             this.position.y+6);
};
function rpgWorldObject(x,y,type) {
    this.Texture = new PIXI.Texture(PIXI.Texture.fromImage('img/e/1/grass.png'));
    this.position = {x:x, y:y, z:y+5, width:32, height:32,depth:32,targetscale:1};
    this.Sprite = new PIXI.Sprite(this.Texture);
    this.Sprite.position.x = x;
    this.Sprite.position.y = y+24;
    this.Sprite.position.z = y+5;
    this.Sprite.anchor.y = 1;
    game.camera.enableSprite(this.Sprite);
    game.object.add(this);
};
rpgWorldObject.prototype.Step = function() {
    if (game.object.atPosition(this.position.x+8,this.position.y+8,'characterSkinObject') !== false) {
        this.position.targetscale = 0.8;
    } else {
        this.position.targetscale = 1;
    }
    if (this.Sprite.scale.y < this.position.targetscale-0.05) {
        this.Sprite.scale.y += 0.01;
        if (Math.random() < 0.15) new rpgFxLeaf(this.position.x+(Math.random()*32),this.position.y-5+(Math.random()*16));
    } else if (this.Sprite.scale.y > this.position.targetscale+0.05) {
        this.Sprite.scale.y -= 0.04;
        if (Math.random() < 0.15) new rpgFxLeaf(this.position.x+(Math.random()*32),this.position.y-5+(Math.random()*16));
    }
    else {
        this.Sprite.scale.x = 1;
        this.Sprite.scale.y = this.position.targetscale;
    }
};

function characterSkinObject(parentObject) {
    this.position = {
        x: 0, y: 0, z: 1,
        width: 32, height: 32, depth: 0,
        collision: true,
        direction: 1,
        animationFrame: 0,
        animationBob:0,
        target: [],
        previous: {
            x: 0,
            y: 0,
            z: 0
        },
        speed: 2
    };
    
    this.Sprite = new PIXI.DisplayObjectContainer();
    this.SurfShadow = new PIXI.Sprite(new PIXI.Texture(PIXI.Texture.fromImage(parentObject.ParentObject.surfMon)));
    this.SurfShadow.visible = false;
    this.inWater = true;
    this.Shadow = new PIXI.DisplayObjectContainer();
    
    this.Shadow.tint = 0x000000;
    this.Shadow.scale.y = -0.5;
    this.Shadow.alpha = 0.5;
    
    this.ParentObject = parentObject;
    this.Sprite.position.x = this.Shadow.position.x = this.position.x = this.ParentObject.position.x;
    this.Sprite.position.y = this.Shadow.position.y = this.position.y = this.ParentObject.position.y;
    
    game.object.add(this);
    game.camera.enableSprite(this.Sprite);
    game.camera.enableSprite(this.Shadow);
    game.camera.enableSprite(this.SurfShadow);
}
characterSkinObject.prototype.Step = function() {
    var oldpos = this.position.previous;
    var newpos = this.ParentObject.position;
    if (Math.round(oldpos.x/32) !== Math.round(newpos.x/32) || 
        Math.round(oldpos.y/32) !== Math.round(newpos.y/32)) {
        this.position.previous = {
            x: Math.round(newpos.x/32)*32,
            y: Math.round(newpos.y/32)*32
        };
        this.position.target.push(this.position.previous);
    }
    if (this.position.target.length > 0) {
        if (Math.abs(this.position.x-this.position.target[0].x) > 100 || Math.abs(this.position.y-this.position.target[0].y) > 100) {
            this.position.x = this.position.target[0].x;
            this.position.y = this.position.target[0].y;
        }
        this.position.animationFrame += 0.07*this.position.speed;
        if (this.position.target[0].x < this.position.x) {
            this.position.direction = 2;
            this.position.x -= this.position.speed;
        }
        if (this.position.target[0].x > this.position.x) {
            this.position.direction = 3;
            this.position.x += this.position.speed;
        }
        if (this.position.target[0].y < this.position.y) {
            this.position.direction = 0;
            this.position.y -= this.position.speed;
        }
        if (this.position.target[0].y > this.position.y) {
            this.position.direction = 1;
            this.position.y += this.position.speed;
        }
        if (Math.abs(this.position.x - this.position.target[0].x) < 1)
            this.position.x = this.position.target[0].x;
        if (Math.abs(this.position.y - this.position.target[0].y) < 1)
            this.position.y = this.position.target[0].y;
        if (this.position.x === this.position.target[0].x && this.position.y === this.position.target[0].y) {
            this.position.target.shift();
            var tile = mapGetTile(this.position.x+16,this.position.y+16);
            this.inWater = (tile === 2 || tile === 3);
        }
    } else {
        this.position.animationFrame = 0;
        this.position.direction = this.ParentObject.position.direction;
    }

    //In water
    if (this.inWater) {
        this.Shadow.visible = false;
        this.SurfShadow.visible = true;
        this.position.animationBob += 0.07;
        this.position.animationBob %= 2;
        
        for(var i=0;i<this.Sprite.children.length;i++) {
            var width = (this.Sprite.children[i].texture.baseTexture.width/4);
            this.Sprite.children[i].texture.setFrame(new PIXI.Rectangle(0, (~~this.position.direction)*width, width, width-4));
        }
        var width = (this.SurfShadow.texture.baseTexture.width/2);
        this.SurfShadow.texture.setFrame(new PIXI.Rectangle((~~this.position.animationBob)*width, (~~this.position.direction)*width, width, width-10));
        this.SurfShadow.scale.set(2,2);
        
        this.Sprite.position.x = this.SurfShadow.position.x = this.position.x-16;
        this.Sprite.position.y = this.SurfShadow.position.y = this.position.y+this.ParentObject.position.z-8-24-(Math.sin(this.position.animationBob*2));
        this.Sprite.position.z = this.SurfShadow.position.z = this.position.y-0.0001;
        this.Sprite.position.x += (this.position.direction===2?8:(this.position.direction===3?-8:1));
        this.SurfShadow.position.y += 20;
        this.SurfShadow.position.z -= 1;
        this.SurfShadow.position.z += this.position.direction*2;
    } else {
        this.position.animationFrame %= 4;
        for(var i=0;i<this.Sprite.children.length;i++) {
            var width = (this.Sprite.children[i].texture.baseTexture.width/4);
            this.Sprite.children[i].texture.setFrame(new PIXI.Rectangle((~~this.position.animationFrame)*width, (~~this.position.direction)*width, width, width));
            this.Shadow.children[i].texture.setFrame(new PIXI.Rectangle((~~this.position.animationFrame)*width, (~~this.position.direction)*width, width, width));
        }
        
        this.Shadow.visible = true;
        this.SurfShadow.visible = false;
        this.Sprite.position.x = this.Shadow.position.x = this.position.x-16;
        this.Sprite.position.y = this.Shadow.position.y = this.position.y+this.ParentObject.position.z-8-32;
        this.Sprite.position.z = this.Shadow.position.z = this.position.y-0.0001;

        this.Shadow.position.x += -this.ParentObject.position.z/4.1;
        this.Shadow.position.y += 85-(this.ParentObject.position.z/2);
        this.Shadow.position.z -= 1;

        this.Shadow.scale.x = (this.ParentObject.position.z/128)+1
        this.Shadow.scale.y = -(this.ParentObject.position.z/128)-0.5;
    }
};
characterSkinObject.prototype.Destroy = function() {
    game.camera.disableSprite(this.Sprite);
    game.camera.disableSprite(this.Shadow);
};
characterSkinObject.prototype.resetPosition = function() {
    this.position.target = [];
    this.position.x = this.ParentObject.position.x;
    this.position.y = this.ParentObject.position.y;
    this.position.z = this.ParentObject.position.z;
    this.position.direction = this.ParentObject.position.direction;
};
characterSkinObject.prototype.resetSpriteLayers = function() {
   this.Sprite.removeChildren();
   this.Shadow.removeChildren();
};
characterSkinObject.prototype.addSpriteLayer = function(imageUrl) {
    this.Sprite.addChild(new PIXI.Sprite(new PIXI.Texture(PIXI.Texture.fromImage('img/a/' +imageUrl))));
    var sprite = new PIXI.Sprite(new PIXI.Texture(PIXI.Texture.fromImage('img/a/' +imageUrl)));
    sprite.tint = 0x000000;
    this.Shadow.addChild(sprite);
};
characterSkinObject.prototype.isMoving = function() {
    return (this.position.target.length !== 0 ||
        this.position.x !== this.position.x ||
        this.position.y !== this.position.y);
};

function characterDrawnimalFollower(parentObject,imageurl) {
    if (imageurl === 0) return void 0;
    if (parentObject.follower !== void 0) {
        game.object.remove(parentObject.follower);
    }
    parentObject.follower = this;
    
    this.position = {
        x: parentObject.position.x,
        y: parentObject.position.y,
        z: 1,
        offsetx:-16,
        offsety:-32,
        width: 32,
        height: 32,
        depth: 0,
        solid: false,
        direction: 1,
        animationFrame: 0,
        target: [],
        previous: {
            x: 0,
            y: 0,
            z: 0
        },
        speed: 2
    };
    this.Texture = new PIXI.Texture.fromImage(imageurl);
    this.Sprite = new PIXI.Sprite(this.Texture);
    this.Sprite.scale.x = 2;
    this.Sprite.scale.y = 2;
    this.Shadow = new PIXI.Sprite(this.Texture);
    this.Shadow.tint = 0x000000;
    this.Shadow.scale.y = -1;
    this.Shadow.scale.x = 2;
    this.Shadow.anchor.y = 1.9;
    this.Shadow.alpha = 0.6;
    
    this.ParentObject = parentObject;
    this.Sprite.position.x = this.ParentObject.position.x;
    this.Sprite.position.y = this.ParentObject.position.y;
    this.Sprite.position.z = 0;
    game.camera.enableSprite(this.Sprite);
    game.camera.enableSprite(this.Shadow);
    game.object.add(this);
}
characterDrawnimalFollower.prototype.Step = function() {
    var oldpos = this.position.previous;
    var newpos = this.ParentObject.Sprite.position;
    if (Math.round(oldpos.x/32) !== Math.round(newpos.x/32) || 
        Math.round(oldpos.y/32) !== Math.round(newpos.y/32)) {
        this.position.previous = {
            x: Math.round(newpos.x/32)*32,
            y: Math.round(newpos.y/32)*32
        };
        this.position.target.push(this.position.previous);
    }
    var targetlength = 1;
    var tile = mapGetTile(this.position.x,this.position.y);
    if (tile >= 10 && tile <= 12) {
        this.position.previous = {
            x: Math.round(newpos.x/32)*32,
            y: Math.round(newpos.y/32)*32
        };
        this.position.target.push(this.position.previous);
    };
    if (this.ParentObject.Sprite.inWater) {
        if (this.Sprite.alpha > 0) {
            this.Sprite.alpha -= 0.1;
            if (this.Shadow.alpha > 0) this.Shadow.alpha -= 0.1;
        } else {
            this.Sprite.visible = false;
            this.Shadow.visible = false;
            this.position.target = [];
            this.position.x = newpos.x;
            this.position.y = newpos.y;
            this.position.direction = newpos.direction;
        }
    } else {
        if (this.Sprite.alpha < 1) {
            this.Sprite.alpha += 0.1;
        }
        if (this.Shadow.alpha < 0.6) this.Shadow.alpha += 0.1;
        this.Sprite.visible = true;
        this.Shadow.visible = true;
    }
                
    this.position.z += this.position.zspeed;
    if (this.position.z < 0) {
        this.position.zspeed += 0.2;
    } else {
        this.position.z = 0;
        this.position.zspeed = 0;
    }
    
    if (this.position.target.length > targetlength) {
        if (this.position.target[0].x < this.position.x - this.position.speed) {
            this.position.direction = 2;
            this.position.x -= this.position.speed;
        } else if (this.position.target[0].x > this.position.x + this.position.speed) {
            this.position.direction = 3;
            this.position.x += this.position.speed;
        } else if (this.position.target[0].y < this.position.y - this.position.speed) {
            this.position.direction = 0;
            this.position.y -= this.position.speed;
        } else if (this.position.target[0].y > this.position.y + this.position.speed) {
            this.position.direction = 1;
            this.position.y += this.position.speed;
        } else {
            this.position.x = this.position.target[0].x;
            this.position.y = this.position.target[0].y;
            this.position.target.shift();
            if (this.position.target.length === 1) {
                var tile = mapGetTile(this.position.target[0].x,this.position.target[0].y);
                if (tile >= 10 && tile <= 12) {
                    this.position.zspeed = -3;
                }
            }
        }
    }

    this.position.animationFrame += 0.07;
    this.position.animationFrame %= 2;
    
    var width = (this.Sprite.texture.baseTexture.width/2);
    this.Sprite.texture.setFrame(new PIXI.Rectangle((~~this.position.animationFrame)*width, (~~this.position.direction)*width, width, width));
    this.Sprite.position.x = this.Shadow.position.x = this.position.x-((this.Sprite.width-32)/2);
    this.Sprite.position.y = this.Shadow.position.y = this.position.y-(width-32)-8-(Math.sin(this.position.animationFrame*2))+this.position.offsety+this.position.z;
    this.Sprite.position.z = this.Shadow.position.z = this.position.y-0.0001;
    this.Shadow.position.y += 24+(Math.sin(this.position.animationFrame*2))+(Math.sin(this.position.animationFrame*2))-this.position.z;
    this.Shadow.position.z -= 1;
};
characterDrawnimalFollower.prototype.Destroy = function() {
    game.camera.disableSprite(this.Sprite);
    game.camera.disableSprite(this.Shadow);
};
characterDrawnimalFollower.prototype.resetPosition = function() {
    this.position.target = [];
    this.position.x = this.ParentObject.position.x;
    this.position.y = this.ParentObject.position.y;
    this.position.direction = this.ParentObject.position.direction;
};

function playerObject(parentObject,x,y) {
    var x = x || 0, y = y || 0;
    this.position = {
        x: x, y: y, z: 1, zspeed:0,
        width: 32, height: 32,
        collision:true, direction: 0
    };
    this.ParentObject = parentObject;
    this.Sprite = new characterSkinObject(this);
    this.Follower = 0;
    this.keyDelay = 4;
    this.croppedObjects = false;
    game.object.add(this);
    game.camera.viewFollowObject(this.Sprite);
}
playerObject.prototype.Step = function() {
    this.position.z += this.position.zspeed;
    if (this.position.z < 0) this.position.zspeed += 0.2;
    else this.position.z = this.position.zspeed = 0;
    
    if (this.ParentObject.battle_id !== 0) return;
    if (!this.Sprite.isMoving()) {
        if (keydown['a'] && this.position.z === 0) { this.position.zspeed = -3.5; } 
        else if (keydown['up']) { 
            this.position.direction = 0;
            if (this.keyDelay === 4)  game.network.Send('MF',{'d':this.position.direction});
            if (--this.keyDelay<0) {
                switch(mapGetTile(this.position.x+16,this.position.y-16)) {
                    case 0: case 4: case 5: case 6: case 7: case 8: case 9:
                        game.network.Send('MU',{});
                        this.position.y -= 32;
                        break;
                    case 2: case 3:
                        if (this.ParentObject.canSwim === true) {
                            game.network.Send('MU',{});
                            this.position.y -= 32;
                        }
                    break;
                    case 17:
                        for(var i=0; i<20; i++) {
                            var tilestop = mapGetTile(this.position.x+16, this.position.y-(i*32));
                            if (tilestop !== 17) {
                                if ((tilestop >= 1 && tilestop <= 3) || (tilestop >= 10 && tilestop <= 12) ) i-=1;
                                this.position.y -= i*32;
                                game.network.Send('MU',{});
                                i = 1000;
                             }
                        }
                        break;
                }
            }
        }
        else if (keydown['down']) {
            this.position.direction = 1;
            if (this.keyDelay === 4)  game.network.Send('MF',{'d':this.position.direction});
            if (--this.keyDelay<0) {
                switch(mapGetTile(this.position.x+16,this.position.y+48)) {
                    case 0: case 4: case 5: case 6: case 7: case 8: case 9:
                        game.network.Send('MD',{});
                        this.position.y += 32;
                        break;
                    case 2: case 3:
                        if (this.ParentObject.canSwim === true) {
                            game.network.Send('MD',{});
                            this.position.y += 32;
                        }
                    break;
                    case 10:
                        var tile = mapGetTile(this.position.x+16,this.position.y+80);
                        if (tile === 0 || (tile > 3 && tile < 10)) {
                            game.network.Send('MD',{});
                            this.position.y += 64;
                            this.position.zspeed = -3;
                        }
                    break;
                    case 17:
                        for(var i=0; i<20; i++) {
                            var tilestop = mapGetTile(this.position.x+16, this.position.y+(i*32)+16);
                            if (tilestop !== 17) {
                                if ((tilestop >= 1 && tilestop <= 3) || (tilestop >= 10 && tilestop <= 12) ) i-=1;
                                this.position.y += i*32;
                                game.network.Send('MD',{});
                                i = 1000;
                             }
                        }
                        break;
                }
            }
        }
        else if (keydown['left']) {
            this.position.direction = 2; 
            if (this.keyDelay === 4)  game.network.Send('MF',{'d':this.position.direction});
            if (--this.keyDelay<0) {
                switch(mapGetTile(this.position.x-16,this.position.y+16)) {
                    case 0: case 4: case 5: case 6: case 7: case 8: case 9:
                        game.network.Send('ML',{});
                        this.position.x -= 32;
                        break;
                    case 2: case 3:
                        if (this.ParentObject.canSwim === true) {
                            game.network.Send('ML',{});
                            this.position.x -= 32;
                        }
                    break;
                    case 12:
                        var tile = mapGetTile(this.position.x-64-16,this.position.y+16);
                        if (tile === 0 || (tile > 3 && tile < 10)) {
                            game.network.Send('ML',{});
                            this.position.x -= 64;
                            this.position.zspeed = -3;
                        }
                    break;
                    case 17:
                        for(var i=0; i<20; i++) {
                            var tilestop = mapGetTile(this.position.x+(i*32)-16, this.position.y+16);
                            if (tilestop !== 17) {
                                if ((tilestop >= 1 && tilestop <= 3) || (tilestop >= 10 && tilestop <= 12) ) i-=1;
                                this.position.x -= i*32;
                                game.network.Send('ML',{});
                                i = 1000;
                             }
                        }
                        break;
                }
            }
        }
        else if (keydown['right']) {
            this.position.direction = 3; 
            if (this.keyDelay === 4)  game.network.Send('MF',{'d':this.position.direction});
            if (--this.keyDelay<0) {
                switch(mapGetTile(this.position.x+48,this.position.y+16)) {
                    case 0: case 4: case 5: case 6: case 7: case 8: case 9:
                        game.network.Send('MR',{});
                        this.position.x += 32;
                        break;
                    case 2: case 3:
                        if (this.ParentObject.canSwim === true) {
                            game.network.Send('MR',{});
                            this.position.x += 32;
                        }
                    break;
                    case 11:
                        var tile = mapGetTile(this.position.x+80,this.position.y+16); 
                        if (tile === 0 || (tile > 3 && tile < 10)) {
                            game.network.Send('MR',{});
                            this.position.x += 64;
                            this.position.zspeed = -3;
                        }
                    break;
                    case 17:
                        for(var i=0; i<20; i++) {
                            var tilestop = mapGetTile(this.position.x+(i*32)+16, this.position.y+16);
                            if (tilestop !== 17) {
                                if ((tilestop >= 1 && tilestop <= 3) || (tilestop >= 10 && tilestop <= 12) ) i-=1;
                                this.position.x += i*32;
                                game.network.Send('MR',{});
                                i = 1000;
                             }
                        }
                        break;
                }
            }
        }
        else {
            if (this.keyDelay < 4) {
                game.network.Send('MS',{'d':this.position.direction});
            }
            this.keyDelay = 4;
        }
        if (this.croppedObjects === false) {
            game.object.deactivateArea(game.camera.viewX() - 32, game.camera.viewY() - 32,
                    game.camera.viewWidth() + 64, game.camera.viewHeight() + 64, false);
            game.object.activateArea(game.camera.viewX() - 32, game.camera.viewY() - 32,
                game.camera.viewWidth() + 64, game.camera.viewHeight() + 64, true);
            this.Collides();
            this.croppedObjects = true;
        }
    } else this.croppedObjects = false;
};
playerObject.prototype.Collides = function() {
    var stuff = game.object.atRect(game.camera.viewX() - 32, game.camera.viewY() - 32,
                game.camera.viewWidth() + 64, game.camera.viewHeight() + 64);
    //var stuff = game.object.atRect(this.position.x,this.position.y,this.position.width,this.position.height);
    for(var i in stuff) {
        if (stuff[i].constructor.name === 'mapObject') {
            stuff[i].load();
        }
    }

    var top = game.camera.viewY();
    var left = game.camera.viewX();
    var right = left+game.camera.viewWidth();
    var bottom = top+game.camera.viewHeight();
    
    if (game.object.atPosition(left+16,top-16,'mapObject') === false) game.camera.viewBorderTop(top);
    else if (game.object.atPosition(right-16,top-16,'mapObject') === false) game.camera.viewBorderTop(top);
    else game.camera.viewBorderTop(false);
    
    if (game.object.atPosition(left-16,top+16,'mapObject') === false) game.camera.viewBorderLeft(left);
    else if (game.object.atPosition(left-16,bottom-16,'mapObject') === false) game.camera.viewBorderLeft(left);
    else game.camera.viewBorderLeft(false);
    
    if (game.object.atPosition(right+16,top+16,'mapObject') === false) game.camera.viewBorderRight(right);
    else if (game.object.atPosition(right+16,bottom-16,'mapObject') === false) game.camera.viewBorderRight(right);
    else game.camera.viewBorderRight(false);
    
    if (game.object.atPosition(left+16,bottom+16,'mapObject') === false) game.camera.viewBorderBottom(bottom);
    else if (game.object.atPosition(right-16,bottom+16,'mapObject') === false) game.camera.viewBorderBottom(bottom);
    else game.camera.viewBorderBottom(false);
    
    var ok = 0;
    while(ok!==2) {
        ok = 0;
        if (game.object.atPosition(left+16,top+16,'mapObject') === false) game.camera.viewBorderTop((top+=16));
        else if (game.object.atPosition(right-16,top+16,'mapObject') === false) game.camera.viewBorderTop((top+=16));
        else if (game.object.atPosition(left+16,bottom-16,'mapObject') === false) game.camera.viewBorderBottom((bottom-=16));
        else if (game.object.atPosition(right-16,bottom-16,'mapObject') === false) game.camera.viewBorderBottom((bottom-=16));
        else ok += 1;
        if (game.object.atPosition(left+16,top+16,'mapObject') === false) game.camera.viewBorderLeft((left+=16));
        else if (game.object.atPosition(left+16,bottom-16,'mapObject') === false) game.camera.viewBorderLeft((left+=16));
        else if (game.object.atPosition(right-16,top+16,'mapObject') === false) game.camera.viewBorderRight((right-=16));
        else if (game.object.atPosition(right-16,bottom-16,'mapObject') === false) game.camera.viewBorderRight((right-=16));
        else ok += 1;
    }
    
};
playerObject.prototype.Update = function() {
    if (this.ParentObject.battle_id !== 0) {
        if (this.ParentObject.battle_id !== 1) {
            screenTransitionOut(swirlEffect,'#000000','#0000FF',1,0);
            this.ParentObject.battle_id = 1;
        }
        return;
    }
    this.position.x = this.ParentObject.x * 32;
    this.position.y = this.ParentObject.y * 32;
    this.position.direction = this.ParentObject.d;
    if (this.Sprite.Sprite.children.length === 0) {
        this.Sprite.addSpriteLayer(this.ParentObject.avatar_ow+'.png');
    }
    if (this.Follower === 0)
        this.Follower = new characterDrawnimalFollower(this,this.ParentObject.following.species);
    this.Collides();
};
PLYR = void 0;

function onlinePlayerObject(parentObject) {
    this.Sprite = 0;
    this.Follower = 0;
    this.Status = 0;
    this.position = {
        x: parentObject.x*32,
        y: parentObject.y*32,
        z: 1,
        width: 64,
        height: 64,
        solid: false,
        direction: 0
    };
    this.ParentObject = parentObject;
    this.resetPosition();
    this.Sprite = new characterSkinObject(this);
    this.Sprite.position.speed = 1;
    this.Sprite.addSpriteLayer(this.ParentObject.avatar_ow+'.png');
    this.SpriteNameTagBG = new PIXI.Graphics();
    this.SpriteNameTag = new PIXI.Text("...", {font:"12px 'Droid Sans Mono'", fill:"black", stroke: "#000",strokeThickness: 1});
    
    game.object.add(this);
    game.object.add(this.Sprite);
    game.camera.enableSprite(this.SpriteNameTag);
    game.camera.enableSprite(this.SpriteNameTagBG);
}
onlinePlayerObject.prototype.Step = function() {
    this.resetPosition();
    if (this.Status === 'Leaving Game') {
        game.object.remove(this);
    }
};
onlinePlayerObject.prototype.Destroy = function() {
    game.object.remove(this.Sprite);
    if (this.Follower !== 0)
        game.object.remove(this.Follower);
    game.camera.disableSprite(this.SpriteNameTag);
    game.camera.disableSprite(this.SpriteNameTagBG);
    if (this.follower !== void 0) {
        game.object.remove(this.follower);
    }
    
};
onlinePlayerObject.prototype.UnPaused = function() {
//    this.resetPosition();
//    if (this.follower !== void 0) {
//        this.follower.resetPosition();
//    }
//    this.Sprite.resetPosition();
};
onlinePlayerObject.prototype.resetPosition = function() {
    this.position.x = this.ParentObject.x * 32;
    this.position.y = this.ParentObject.y * 32;
    this.position.direction = this.ParentObject.d;
    if (this.Sprite !== 0) {
        this.SpriteNameTag.position.x = this.SpriteNameTagBG.position.x = this.Sprite.Sprite.position.x-(this.SpriteNameTag.width/4)+16;
        this.SpriteNameTag.position.y = this.SpriteNameTagBG.position.y = this.Sprite.Sprite.position.y-10;
        this.SpriteNameTag.position.z = this.SpriteNameTagBG.position.z = this.Sprite.Sprite.position.z+10;
        this.SpriteNameTagBG.position.z-=1;
    }
};
onlinePlayerObject.prototype.Update = function() {
    if (this.SpriteNameTag.text === '...') {
        var color = this.ParentObject.color;
        if (color === void 0 || color === '' || color === null || color.indexOf('#') !== 0 || !(color.length === 7 || color.length === 4)) color = this.ParentObject.username.toColor();
        this.SpriteNameTag.setStyle({font:"12px 'Droid Sans Mono'", fill:color, stroke: "#000",strokeThickness: 1})
        this.SpriteNameTag.setText(this.ParentObject.username.toTitleCase());
        this.SpriteNameTagBG.clear();
        this.SpriteNameTagBG.beginFill(0x000000);
        this.SpriteNameTagBG.drawRect(0, 0, this.ParentObject.username.length*8, this.SpriteNameTag.height);
    }
    if (this.Follower === 0)
        this.Follower = new characterDrawnimalFollower(this,this.ParentObject.following.species);
};
onlinePlayerObject.prototype.statusOffline = function() {
    this.fadeOut = true;
    this.ParentObject = void 0;
    game.object.remove(this);
};

gameUserJoin = function(data) {
    if (!game.running()) return;
    if (data.username === game.network._username) {
        if (PLYR === void 0) PLYR = new playerObject(data,data.x*32,data.y*32);
        PLYR.ParentObject = data;
        data.gameObject = PLYR;
    } else {
        data.gameObject = new onlinePlayerObject(data);
        data.gameObject.Update();
    }
};
gameUserUpdate = function(data) {
    if (!game.running()) return;
    if (data.gameObject === void 0) {
        gameUserJoin(data);
    }
    if (data.gameObject.Update !== void 0)
        data.gameObject.Update();
};
gameUserLeave = function(data) {
    if (!game.running()) return;
    if (data.gameObject !== void 0) {
        data.gameObject.statusOffline();
        data.gameObject = void 0;
    }
};

// TImeline should be an array containing objects [ { 't','p':{'x','y'}}]
function npcObject(timeline) {
    this.timeline = timeline;
    this.nextPosition = 0;
    this.visible = false;
};
npcObject.prototype.Step = function() {
    if (this.timeline[this.nextPosition].t < inGameTime) {
        this.position.target.push(this.timeline[this.nextPosition].p);
        this.nextPosition +=1;
    }
};
npcObject.prototype.LoadPathToNext = function() {};
npcObject.prototype.load = function() {};


function weatherSystem() {
    game.object.add(this);
    this.weatherObjects = [];
    this.weatherWindSpeed = 0;
    this.weatherWindDirection = 0;
};
weatherSystem.prototype.setWind = function(direction,speed) {
    this.weatherWindDirection = direction;
    this.weatherWindSpeed = speed;
};
weatherSystem.prototype.startBlizzard = function() {
    this.stopWeather();
    for(var i=0;i<250;i++)
        this.weatherObjects.push(new weatherParticleSnow(game.camera.viewX()+(Math.random()*800)-400,game.camera.viewY()+(Math.random()*800)-400,this));
    for(var i=0;i<100;i++)
        this.weatherObjects.push(new weatherParticleIceStorm(game.camera.viewX()+(Math.random()*800)-400,game.camera.viewY()+(Math.random()*800)-400,this));
};
weatherSystem.prototype.startSnowing = function() {
    this.stopWeather();
    for(var i=0;i<200;i++)
        this.weatherObjects.push(new weatherParticleSnow(game.camera.viewX()+(Math.random()*800)-400,game.camera.viewY()+(Math.random()*800)-400,this));
};
weatherSystem.prototype.startRaining = function() {
    this.stopWeather();
    for(var i=0;i<100;i++)
        this.weatherObjects.push(new weatherParticleRain(game.camera.viewX()+(Math.random()*800)-400,game.camera.viewY()+(Math.random()*800)-400,this));
};
weatherSystem.prototype.startFog = function() {
    this.stopWeather();
    for(var i=0;i<200;i++)
        this.weatherObjects.push(new weatherParticleCloud(game.camera.viewX()+(Math.random()*900)-450,game.camera.viewY()+(Math.random()*800)-400,this));
};
weatherSystem.prototype.startSmog = function() {
    this.stopWeather();
    for(var i=0;i<200;i++)
        this.weatherObjects.push(new weatherParticleSmog(game.camera.viewX()+(Math.random()*900)-450,game.camera.viewY()+(Math.random()*800)-400,this));
};
weatherSystem.prototype.startCloudy = function() {
    this.stopWeather();
    for(var i=0;i<10;i++) {
        var x = game.camera.viewX()+(Math.random()*1000)-550;
        var y = game.camera.viewY()+(Math.random()*1000)-550;
        this.weatherObjects.push(new weatherParticleCloudShadows(x,y,this));
    }
};
weatherSystem.prototype.startPoison = function() {
    this.stopWeather();
    for(var i=0;i<200;i++)
        this.weatherObjects.push(new weatherParticlePoison(game.camera.viewX()+(Math.random()*900)-450,game.camera.viewY()+(Math.random()*800)-400,this));
};
weatherSystem.prototype.startSandStorm = function() {
    this.stopWeather();
    for(var i=0;i<200;i++)
        this.weatherObjects.push(new weatherParticleSandStorm(game.camera.viewX()+(Math.random()*1000)-500,game.camera.viewY()+(Math.random()*800)-400,this));
};
weatherSystem.prototype.startLights = function() {
    this.stopWeather();
    for(var i=0;i<100;i++)
        this.weatherObjects.push(new weatherParticleLight(game.camera.viewX()+(Math.random()*800)-400,game.camera.viewY()+(Math.random()*800)-400,this));
};
weatherSystem.prototype.stopWeather = function() {
    for(var i in this.weatherObjects) {
        this.weatherObjects[i].stop();
    }
    this.weatherObjects = [];
};

//Weather sun beams
// Weather hail
function weatherParticleSnow(x,y,parent) {
    this.parent = parent;
    this.Texture = new PIXI.Texture.fromImage('img/i/snow.png');
    this.Sprite = new PIXI.Sprite(this.Texture);
    this.Sprite.position.x = x;
    this.Sprite.position.y = y;
    this.Sprite.position.z = 100000;
    this.Sprite.scale.x = (Math.random()*0.9)+0.1;
    this.Sprite.scale.y = this.Sprite.scale.x;
    this.Sprite.scale.default = this.Sprite.scale.x;
    this.Sprite.pivot.x = 8;
    this.Sprite.pivot.y = 8;
    this.Sprite.rotation = Math.random()*100;
    this.Sprite.alpha = 0;
    this.Sprite.alphaT = true;
    this.Sprite.twist = Math.random()*100;
    
    game.camera.enableSprite(this.Sprite);
    game.object.add(this);
}
weatherParticleSnow.prototype.Step = function() {
    this.Sprite.twist += 0.01;
    this.Sprite.rotation += 0.01;
    this.Sprite.scale.x = this.Sprite.scale.default*Math.sin(this.Sprite.twist);
    this.Sprite.position.x+=(this.Sprite.scale.default*Math.random())-(this.Sprite.scale.default/2)+this.parent.weatherWindSpeed;
    this.Sprite.position.y+=this.Sprite.scale.default;
    this.Sprite.position.z = 100000;
    
    if (this.Sprite.alphaT && this.Sprite.alpha < 1)
        this.Sprite.alpha += 0.001;
    if (!this.Sprite.alphaT) {
        this.Sprite.alpha -= 0.001;
        if (this.Sprite.alpha < 0)
            game.object.remove(this);
    }
    
    this.loop();
};
weatherParticleSnow.prototype.Destroy = function() {
    game.camera.disableSprite(this.Sprite);
};
weatherParticleSnow.prototype.stop = function() {
    this.Sprite.alphaT = false;
};

function weatherParticleRain(x,y,parent) {
    this.Texture = new PIXI.Texture.fromImage('img/i/rain.png');
    this.Sprite = new PIXI.Sprite(this.Texture);
    this.Sprite.position.x = x;
    this.Sprite.position.y = y;
    this.Sprite.position.z = 100000;
    this.Sprite.scale.x = (Math.random()*0.9)+0.1;
    this.Sprite.scale.y = this.Sprite.scale.x;
    this.Sprite.scale.default = this.Sprite.scale.x;
    this.Sprite.pivot.x = 8;
    this.Sprite.pivot.y = 8;
    this.Sprite.alpha = 0;
    this.Sprite.alphaT = true;
    
    game.camera.enableSprite(this.Sprite);
    game.object.add(this);
}
weatherParticleRain.prototype.Step = function() {
    //this.Sprite.position.x+=(this.Sprite.scale.default*Math.random())-(this.Sprite.scale.default/2);
    this.Sprite.position.y+=this.Sprite.scale.default*6;
    this.Sprite.position.z = 100000;
    
    if (this.Sprite.alphaT && this.Sprite.alpha < 1)
        this.Sprite.alpha += 0.001;
    if (!this.Sprite.alphaT) {
        this.Sprite.alpha -= 0.001;
        if (this.Sprite.alpha < 0)
            game.object.remove(this);
    }
    
    this.loop();
    
};
weatherParticleRain.prototype.Destroy = function() {
    game.camera.disableSprite(this.Sprite);
};
weatherParticleRain.prototype.stop = function() {
    this.Sprite.alphaT = false;
};

function weatherParticleLight(x,y,parent) {
    this.Texture = new PIXI.Texture.fromImage('img/i/light.png');
    this.Sprite = new PIXI.Sprite(this.Texture);
    this.Sprite.position.x = x;
    this.Sprite.position.y = y;
    this.Sprite.position.z = 100000;
    this.Sprite.scale.x = (Math.random()*0.9)+0.1;
    this.Sprite.scale.y = this.Sprite.scale.x;
    this.Sprite.scale.default = this.Sprite.scale.x;
    this.Sprite.pivot.x = 8;
    this.Sprite.pivot.y = 8;
    this.Sprite.alpha = 0;
    this.Sprite.alphaT = true;
    
    game.camera.enableSprite(this.Sprite);
    game.object.add(this);
}
weatherParticleLight.prototype.Step = function() {
    this.Sprite.position.y-=this.Sprite.scale.default;
    this.Sprite.position.z = 100000;
    
    if (this.Sprite.alphaT && this.Sprite.alpha < 0.7)
        this.Sprite.alpha += 0.001;
    if (!this.Sprite.alphaT) {
        this.Sprite.alpha -= 0.001;
        if (this.Sprite.alpha < 0)
            game.object.remove(this);
    }
    
    if (this.Sprite.position.y < game.camera.viewY()-64){
        this.Sprite.position.y += Math.random()*800;
        this.Sprite.alpha = 0;
    }
    this.loop();
};
weatherParticleLight.prototype.Destroy = function() {
    game.camera.disableSprite(this.Sprite);
};
weatherParticleLight.prototype.stop = function() {
    this.Sprite.alphaT = false;
};

function weatherParticleCloud(x,y,parent) {
    this.Texture = new PIXI.Texture.fromImage('img/i/cloud.png');
    this.Sprite = new PIXI.Sprite(this.Texture);
    this.Sprite.position.x = x;
    this.Sprite.position.y = y;
    this.Sprite.position.z = 100000;
    this.Sprite.scale.x = (Math.random()*2)+0.4;
    this.Sprite.scale.y = this.Sprite.scale.x;
    this.Sprite.scale.default = this.Sprite.scale.x;
    this.Sprite.alpha = 0;
    this.Sprite.alphaT = true;
    this.dir = Math.round(Math.random()*2)-1;
    
    game.camera.enableSprite(this.Sprite);
    game.object.add(this);
}
weatherParticleCloud.prototype.Step = function() {
    this.Sprite.position.x-= (this.Sprite.scale.default*this.dir)/2;
    this.Sprite.position.z = 100000;
    
    if (this.Sprite.alphaT && this.Sprite.alpha < 0.7)
        this.Sprite.alpha += 0.001;
    if (!this.Sprite.alphaT) {
        this.Sprite.alpha -= 0.001;
        if (this.Sprite.alpha < 0)
            game.object.remove(this);
    }
    
    this.loop();
    
};
weatherParticleCloud.prototype.Destroy = function() {
    game.camera.disableSprite(this.Sprite);
};
weatherParticleCloud.prototype.stop = function() {
    this.Sprite.alphaT = false;
};

function weatherParticleSmog(x,y,parent) {
    this.Texture = new PIXI.Texture.fromImage('img/i/blackfog.png');
    this.Sprite = new PIXI.Sprite(this.Texture);
    this.Sprite.position.x = x;
    this.Sprite.position.y = y;
    this.Sprite.position.z = 100000;
    this.Sprite.scale.x = (Math.random()*2)+0.4;
    this.Sprite.scale.y = this.Sprite.scale.x;
    this.Sprite.scale.default = this.Sprite.scale.x;
    this.Sprite.alpha = 0;
    this.Sprite.alphaT = true;
    this.dir = Math.round(Math.random()*2)-1;
    
    game.camera.enableSprite(this.Sprite);
    game.object.add(this);
}
weatherParticleSmog.prototype.Step = function() {
    this.Sprite.position.x-= (this.Sprite.scale.default*this.dir)/2;
    this.Sprite.position.z = 100000;
    
    if (this.Sprite.alphaT && this.Sprite.alpha < 0.8)
        this.Sprite.alpha += 0.001;
    if (!this.Sprite.alphaT) {
        this.Sprite.alpha -= 0.001;
        if (this.Sprite.alpha < 0)
            game.object.remove(this);
    }
    
    this.loop();
    
};
weatherParticleSmog.prototype.Destroy = function() {
    game.camera.disableSprite(this.Sprite);
};
weatherParticleSmog.prototype.stop = function() {
    this.Sprite.alphaT = false;
};

function weatherParticlePoison(x,y,parent) {
    this.Texture = new PIXI.Texture.fromImage('img/i/poison.png');
    this.Sprite = new PIXI.Sprite(this.Texture);
    this.Sprite.position.x = x;
    this.Sprite.position.y = y;
    this.Sprite.position.z = 100000;
    this.Sprite.scale.x = (Math.random()*2)+0.4;
    this.Sprite.scale.y = this.Sprite.scale.x;
    this.Sprite.scale.default = this.Sprite.scale.x;
    this.Sprite.alpha = 0;
    this.Sprite.alphaT = true;
    this.dir = Math.round(Math.random()*2)-1;
    
    game.camera.enableSprite(this.Sprite);
    game.object.add(this);
}
weatherParticlePoison.prototype.Step = function() {
    this.Sprite.position.x-= ((2-this.Sprite.scale.default)*this.dir);
    this.Sprite.position.z = 100000;
    
    if (this.Sprite.alphaT && this.Sprite.alpha < 0.7)
        this.Sprite.alpha += 0.001;
    if (!this.Sprite.alphaT) {
        this.Sprite.alpha -= 0.001;
        if (this.Sprite.alpha < 0)
            game.object.remove(this);
    }
    
    this.loop();
    
};
weatherParticlePoison.prototype.Destroy = function() {
    game.camera.disableSprite(this.Sprite);
};
weatherParticlePoison.prototype.stop = function() {
    this.Sprite.alphaT = false;
};

function weatherParticleSandStorm(x,y,parent) {
    this.Texture = new PIXI.Texture.fromImage('img/i/sand.png');
    this.Sprite = new PIXI.Sprite(this.Texture);
    this.Sprite.position.x = x;
    this.Sprite.position.y = y;
    this.Sprite.position.z = 100000;
    this.Sprite.scale.x = (Math.random()*2)+0.4;
    this.Sprite.scale.y = this.Sprite.scale.x;
    this.Sprite.scale.default = this.Sprite.scale.x;
    this.Sprite.alpha = 0;
    this.Sprite.alphaT = true;
    this.dir = Math.round(Math.random()*2)-1;
    
    game.camera.enableSprite(this.Sprite);
    game.object.add(this);
    this.Sprite.rotation = Math.random()*360;
}
weatherParticleSandStorm.prototype.Step = function() {
    this.Sprite.position.x-= this.Sprite.scale.default;
    this.Sprite.position.z = 100000;
    this.Sprite.rotation += 0.01;
    
    if (this.Sprite.alphaT && this.Sprite.alpha < 0.7)
        this.Sprite.alpha += 0.001;
    if (!this.Sprite.alphaT) {
        this.Sprite.alpha -= 0.001;
        if (this.Sprite.alpha < 0)
            game.object.remove(this);
    }
    
    this.loop();
    
};
weatherParticleSandStorm.prototype.Destroy = function() {
    game.camera.disableSprite(this.Sprite);
};
weatherParticleSandStorm.prototype.stop = function() {
    this.Sprite.alphaT = false;
};

function weatherParticleIceStorm(x,y,parent) {
    this.Texture = new PIXI.Texture.fromImage('img/i/icestorm.png');
    this.Sprite = new PIXI.Sprite(this.Texture);
    this.Sprite.position.x = x;
    this.Sprite.position.y = y;
    this.Sprite.position.z = 100000;
    this.Sprite.scale.x = (Math.random()*2)+0.4;
    this.Sprite.scale.y = this.Sprite.scale.x;
    this.Sprite.scale.default = this.Sprite.scale.x;
    this.Sprite.alpha = 0;
    this.Sprite.alphaT = true;
    this.dir = Math.round(Math.random()*2)-1;
    
    game.camera.enableSprite(this.Sprite);
    game.object.add(this);
    this.Sprite.rotation = Math.random()*360;
}
weatherParticleIceStorm.prototype.Step = function() {
    this.Sprite.position.x-= this.Sprite.scale.default;
    this.Sprite.position.z = 100000;
    this.Sprite.rotation += 0.01;
    
    if (this.Sprite.alphaT && this.Sprite.alpha < 0.7)
        this.Sprite.alpha += 0.001;
    if (!this.Sprite.alphaT) {
        this.Sprite.alpha -= 0.001;
        if (this.Sprite.alpha < 0)
            game.object.remove(this);
    }
    
    this.loop()
    
};
weatherParticleIceStorm.prototype.Destroy = function() {
    game.camera.disableSprite(this.Sprite);
};
weatherParticleIceStorm.prototype.stop = function() {
    this.Sprite.alphaT = false;
};

function weatherParticleCloudShadows(x,y,parent) {
    this.parent = parent;
    this.Texture = new PIXI.Texture.fromImage('img/i/cloudshadow.png');
    this.Sprite = new PIXI.Sprite(this.Texture);
    this.Sprite.position.x = x;
    this.Sprite.position.y = y;
    this.Sprite.position.z = 100000;
    this.Sprite.scale.x = (Math.random()*2)+0.4;
    this.Sprite.scale.y = this.Sprite.scale.x;
    this.Sprite.scale.default = this.Sprite.scale.x;
    this.Sprite.alpha = 0;
    this.Sprite.alphaT = true;
    
    game.camera.enableSprite(this.Sprite);
    game.object.add(this);
}
weatherParticleCloudShadows.prototype.Step = function() {
    this.Sprite.position.z = 100000;
    this.Sprite.position.x += this.parent.weatherWindSpeed;
    if (this.Sprite.alphaT && this.Sprite.alpha < 0.075)
        this.Sprite.alpha += 0.001;
    if (!this.Sprite.alphaT) {
        this.Sprite.alpha -= 0.001;
        if (this.Sprite.alpha < 0)
            game.object.remove(this);
    }
    
    this.loop();
    
};
weatherParticleCloudShadows.prototype.Destroy = function() {
    game.camera.disableSprite(this.Sprite);
};
weatherParticleCloudShadows.prototype.stop = function() {
    this.Sprite.alphaT = false;
};

weatherParticleSnow.prototype.loop = 
weatherParticleRain.prototype.loop = 
weatherParticleLight.prototype.loop = 
weatherParticleCloud.prototype.loop = 
weatherParticleSmog.prototype.loop = 
weatherParticlePoison.prototype.loop = 
weatherParticleSandStorm.prototype.loop = 
weatherParticleIceStorm.prototype.loop = 
weatherParticleCloudShadows.prototype.loop = function() {
    if (this.Sprite.position.x < game.camera.viewX()-50)
        this.Sprite.position.x += game.camera.viewWidth()+100;
    if (this.Sprite.position.x > game.camera.viewX()+game.camera.viewWidth()+50)
        this.Sprite.position.x -= game.camera.viewWidth()+100;
    if (this.Sprite.position.y < game.camera.viewY()-50)
        this.Sprite.position.y += game.camera.viewHeight()+100;
    if (this.Sprite.position.y > game.camera.viewY()+game.camera.viewHeight()+50)
        this.Sprite.position.y -= game.camera.viewHeight()+100;
};

/////////////////////////////////////////////////////////////////////////
// Region
////////////////////////////////////////////////////////////////////////
function mapTile(x,y,z,width,height,depth,index,tileset) {
    index -= 1;
    this.position = {
        x: x,
        width: width,
        y: y,
        height: height,
        z: 0,
        depth: 16
    };
    var ypos = Math.floor(index/8);
    var xpos = index-(ypos*8);
    this.Texture = new PIXI.Texture(PIXI.Texture.fromImage(tileset));
    this.Texture.setFrame(new PIXI.Rectangle(xpos*width, ypos*height, width, height));
    this.Sprite = new PIXI.Sprite(this.Texture);
    this.Sprite.position.x = x;
    this.Sprite.position.y = y;
    this.Sprite.position.z = z;
    
    game.camera.enableSprite(this.Sprite);
    game.object.add(this);
}
mapTile.prototype.Destroy = function() {
    game.camera.disableSprite(this.Sprite);
};
mapTile.prototype.Activate = function() {
    game.camera.enableSprite(this.Sprite);
};
mapTile.prototype.Deactivate = function() {
    game.camera.disableSprite(this.Sprite);
};

function mapObject(id,x,y,width,height) {
    // As soon as this object is in view Ajax the map data...
    this.position = {
        x: x,
        width: width,
        y: y,
        height: height,
        z: 0,
        depth: 16,
        collision: true
    };
    this._rendered = false;
    this._data = void 0;
    this._tiles = [];
    this.myId = id;
    game.object.add(this);
};
mapObject.prototype.load = function() {
    if (this._data !== void 0) return;
    this._data = true;
    $.ajax({
        url:'http://PokeWorlds.com/play.php?g=Locations&p=data&id='+this.myId,
        success:function(data) {
            this._data = JSON.parse(data); 
            var loader = new PIXI.AssetLoader(['img/location/tileset/'+this.myId+'.png']);
            loader.onComplete = this.render.bind(this);
            loader.load();
        }.bind(this)
    });
};
mapObject.prototype.getTileType = function(x,y) {
    if (this._data === void 0) return 0;
    if (this._data.collisions === void 0) return 0;
    x -= this.position.x;
    y -= this.position.y;
    x = Math.floor(x/32);
    y = Math.floor(y/32);
    var index = y*this._data.width;
    index += x;
    if (this._data.collisions[index] === void 0 || this._data.collisions[index] === null) return 0;
    return this._data.collisions[index];
};
mapObject.prototype.render = function() {
    if (this._data === void 0) return;
    if (this._rendered) return;
    for(var i in this._data.layers) {
        var currentlayer = this._data.layers[i];
        if (!currentlayer.visible) continue;
        var zpos = parseInt(i);
        if (i === this._data.playerlayer) zpos = parseInt(i)-64;
        if (i < this._data.playerlayer) zpos = parseInt(i)-128;
        
        var xpos = 0, ypos = 0;
        for(var ii in currentlayer.data) {
            var index = currentlayer.data[ii];
            if (index!==0) {
                this._tiles.push(new mapTile(xpos+this.position.x,ypos+this.position.y,(ypos+this.position.y)+zpos,
                                            this._data.tilewidth,this._data.tileheight,0,
                                            index,'img/location/tileset/'+this.myId+'.png'));
            }
            xpos += this._data.tilewidth;
            if (xpos >= this._data.width*this._data.tilewidth) {
                xpos = 0;
                ypos += this._data.tileheight;
            }
        }
    }
    
    this._rendered = true;
};
mapObject.prototype.Destroy = function() {
    for(var i in this._tiles) {
        game.object.remove(this._tiles[i]);
    };
};
function mapGetTile(x,y) {
    var stuff = game.object.atPosition(x,y,'mapObject');
    if (stuff.length === false) return 0;
    if (stuff[0] === void 0) return 0;
    return stuff[0].getTileType(x,y);
}




/////////////////////////////////////////////////////////////////////////
// NPC Object - Single Page - Load from server (timeline). 
// On appear event, ajax to see if should appear.
// @returns {playerObject}
//
function npcObject(id) {
    this.Sprite = new characterSkinObject(this);
    this.Sprite.addSpriteLayer('1.png');
    this.position = {
        x: 0,
        y: 0,
        z: 1,
        width: 64,
        height: 64,
        solid: false,
        direction: 0
    };
    game.object.add(this.Sprite);
    
    this.visible = false;
    this.id = id;
    this.timeline = [];
    this.eventQueue = [];
}
npcObject.prototype.add = function(at, event, args) {
    // EventName = function(), args = '[1,2,3]'
    this.timeline[at] = {"name":event, "args":JSON.parse(args)};
    return this;
};
npcObject.prototype.Step = function() {
    // Queue up events according to timestamp....
    var time = inGameTime();
    while(this.lastEvent++ < time) {
        this.lastEvent %= 86400;
        if (this.timeline[this.lastEvent] !== void 0) {
            this.eventQueue.push(this.timeline[this.lastEvent]);
        }
    }
    
    // Execute The Event, In event return true if finished
    if (this[this.eventQueue[0].name] !== void 0 &&
        this[this.eventQueue[0].name].call(this,this.eventQueue[0].args)) {
        this.eventQueue.shift();
    };
};
npcObject.prototype.eventAppear = function(x,y,effect) {
    this.position.x = x;
    this.position.y = y;
    this.Sprite.Sprite.alpha = 1;
//    $.ajax('ajax/getObjectExists.php?id='+this.id+'&ts='+data.timestamp,{
//        success:function(html){
//            JSON.parse(html);
//            object.visible = html.visible;
//        }
//    });
};
npcObject.prototype.eventMoveTo = function(x,y) {
    this.position.x = x;
    this.position.y = y;
};
npcObject.prototype.eventDisappear = function(x,y,effect) { 
    this.position.x = x;
    this.position.y = y;
    this.Sprite.Sprite.alpha = 0;
};
npcObject.prototype.eventOpacity = function(opacity) {
    this.Sprite.Sprite.alpha = opacity;
};

swirlEffect = [16,
	1,	2,	3,	4,	5,	6,	7,	8,	9,	10,	11,	12,	13,	14,	15,	16,
	42,	43,	44,	45,	46,	47,	48,	49,	50,	51,	52,	53,	54,	55,	56,	17,
	41,	76,	77,	78,	79,	80,	81,	82,	83,	84,	85,	86,	87,	88,	57,	18,
	40,	75,	102,	103,	104,	105,	106,	107,	108,	109,	110,	111,	112,	89,	58,	19,
	39,	74,	101,	100,	99,	98,	97,	96,	95,	94,	93,	92,	91,	90,	59,	20,
	38,	73,	72,	71,	70,	69,	68,	67,	66,	65,	64,	63,	62,	61,	60,	21,
	37,	36,	35,	34,	33,	32,	31,	30,	29,	28,	27,	26,	25,	24,	23,	22];
function screenTransitionIn(effectArray,fadeFrom,fadeTo,speed,delay) {
    var arrayWidth = effectArray[0];
    var arrayHeight = Math.floor(effectArray.length/arrayWidth)+1;
    for(var i=1;i<effectArray.length;i++) {
        var y = Math.floor((i-1)/arrayWidth);
        var x = (i-1)-(y*arrayWidth);
        new screenTransitionBlock(x,y,arrayWidth,arrayHeight,effectArray[i],fadeFrom,fadeTo,speed,delay,false);
    }
}
function screenTransitionOut(effectArray,fadeFrom,fadeTo,speed,delay) {
    var arrayWidth = effectArray[0];
    var arrayHeight = Math.floor(effectArray.length/arrayWidth)+1;
    var totalwait = 0;
    for(var i=1;i<effectArray.length;i++) {
        var y = Math.floor((i-1)/arrayWidth);
        var x = (i-1)-(y*arrayWidth);
        new screenTransitionBlock(x,y,arrayWidth,arrayHeight,effectArray[i],fadeFrom,fadeTo,speed,delay,true);
        totalwait = Math.max(totalwait,effectArray[i]);
    }
    setTimeout(function() { window.location.reload(true); },(totalwait+delay+50)*17);
}
function screenTransitionBlock(x,y,arrayWidth,arrayHeight,timer,colorIn,colorOut,speed,delay,reverse) {
    this.Sprite = new PIXI.Graphics();
    this.x = x;
    this.y = y;
    this.arrayWidth = arrayWidth;
    this.arrayHeight = arrayHeight;
    this.starttimer = timer;
    this.timer = timer+delay;
    this.colorIn = colorIn;
    this.colorOut = colorOut;
    this.speed = speed;
    this.reverse = reverse;
    
    game.camera.enableSpriteHud(this.Sprite);
    game.object.add(this);
};
screenTransitionBlock.prototype.Step = function() {
    this.timer-=this.speed;
    var blockWidth = (game.camera.viewWidth()/this.arrayWidth);
    var blockHeight = (game.camera.viewHeight()/(this.arrayHeight-1));
    if (!this.reverse) {
        if (this.timer < 15) this.Sprite.alpha = (this.timer/15);
        
    } else {
        if (this.timer < 15) this.Sprite.alpha = 1-(this.timer/15);
        else this.Sprite.alpha = 0;
    }
    
    //var color = blendTwoColors(this.colorIn,this.colorOut,0.5);
    this.Sprite.clear();
    this.Sprite.beginFill(0x000000);
    this.Sprite.drawRect ( this.x*blockWidth,  this.y*blockHeight,  blockWidth,  blockHeight );
    if (this.timer < 0) game.object.remove(this);
};
screenTransitionBlock.prototype.Destroy = function() {
    if (!this.reverse)
        game.camera.disableSpriteHud(this.Sprite);
};

//////////////////////////////////////////////////////////////////////////
// Water Background Tile
//
function waterTiledBackground(x, y) {
    this.Sprite = 0;
    this.currentFrame = 0;
    this.nextFrame = 1;
    this.position = {
        x: x,
        y: y,
        z: -11000001,
        width: 32,
        height: 32,
        solid: false
    };
}
waterTiledBackground.prototype.Init = function() {
    var baseTexture = new PIXI.Texture.fromImage('img/u/o/tester.png');
    this.Sprite = new PIXI.Sprite(new PIXI.Texture(baseTexture,new PIXI.Rectangle(0, 0, 32, 32)));
    this.Sprite.texture.baseTexture.scaleMode = 1;
    this.Sprite.texture.baseTexture._powerOf2 = true;
    game.camera.enableSprite(this.Sprite);
    this.Sprite.position.x = this.position.x;
    this.Sprite.position.y = this.position.y;
    this.Sprite.position.z = this.position.z;
};
waterTiledBackground.prototype.Step = function() {
    this.currentFrame += 0.05;
    if (this.currentFrame > this.nextFrame) {
        this.currentFrame %= 31;
        this.nextFrame = ~~this.currentFrame + 1;
        var yy = ~~(this.currentFrame/6);
        var xx = (this.currentFrame-(yy*6)) * 32;
        var yy = yy*32;
        this.Sprite.texture.setFrame(new PIXI.Rectangle(xx, yy, 32, 32));
    }
};
waterTiledBackground.prototype.Destroy = function() {
    game.camera.disableSprite(this.Sprite);
    game.camera.destroy(this.Sprite);
};
waterTiledBackground.prototype.Activate = function() {
    game.camera.enableSprite(this.Sprite);
};
waterTiledBackground.prototype.Deactivate = function() {
    game.camera.disableSprite(this.Sprite);
};
