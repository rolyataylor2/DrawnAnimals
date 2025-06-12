////Goal is to refine all variables to one object, also to autimate layers, and to impliment WebGL

//Prototypes
Number.prototype.clamp = function(min, max) {
  return Math.min(Math.max(this, min), max);
};
function degToRad(deg) {return deg * (Math.PI/180);};
function pointDirection(x1,y1,x2,y2) {
  return (Math.atan2(-(y1 - y2),x1 - x2)*180/Math.PI+360)%360;
}
GameTileSize = 32; GameInChatBox = false;
sys = {
    imgdir:'img/',
    spritedir:'img/spr/'
};
//Game Engine
function GameSystem() {
    var source = {
        renderMethod:0, MouseX:0, MouseY:0,
        canvasFinal:0,
        enable3d:true,
        
        view:{'X':0,'Y':0,'width':1000,'height':1000},
        debug:{fps:0,fps_ms:15,lastfps:(new Date).getTime()},
        loaders:0,
        GL:{enabled:0,renderer:0,camera:0,scene:0,ambientLight:0},
        loop:{'skiptick':16,'nexttick':0, 'looptick':0},
        pausedBody:false, pausedMenu:false, pausedMessage:false,
        init:function() {
            source.systemAddLoader('Initiallizing...');
            source.initCanvas();
            $(window).resize(source.changeResize);
            $(window).focus(function() {
                Game.pausedBody = false;
            }).blur(function() {
                Game.pausedBody = true;
            });
            //// Chat log
            source.changeResize();
            source.initLoop();
            source.initLayers();
            
            source.debug.stats = new Stats();
            source.debug.stats.setMode(0); // 0: fps, 1: ms
            source.debug.stats.domElement.style.position = 'absolute';
            source.debug.stats.domElement.style.left = '0px';
            source.debug.stats.domElement.style.top = '0px';
            $('#GMW').append( source.debug.stats.domElement );
            
            source.systemRemLoader('Initiallizing... Done');
        },
        initCanvas:function(noGL) {
            if (noGL === void 0)
                try {
                    source.initGL();
                }
                catch(e) {
                    source.canvasFinal = $('<canvas id="explore-canvas">Nadda</canvas>').appendTo('#GMW');
                    source.canvasFinal.attr('width',source.view.width).attr('height',source.view.height);
                    source.canvasFinal.tempctx = source.canvasFinal[0].getContext("2d");
                    if (console !== void 0) console.log('webGL not supported');
                }
            else {
                source.canvasFinal = $('<canvas id="explore-canvas">Nadda</canvas>').appendTo('#GMW');
                source.canvasFinal.attr('width',source.view.width).attr('height',source.view.height);
                source.canvasFinal.tempctx = source.canvasFinal[0].getContext("2d");
                if (console !== void 0) console.log('Fallback to 2dContext');
            }
            
            GameCanvasCTX = source.canvasFinal.tempctx;
           
        },
        initGL:function() {
            source.GL.renderer = new THREE.WebGLRenderer();
            source.GL.renderer.setClearColor( 0x000000, 1 );
            source.GL.renderer.setSize( $('#GMW').width(), $('#GMW').height() );
            $('#GMW').append(source.GL.renderer.domElement);
            
            source.GL.scene = new THREE.Scene();
            source.GL.camera = new THREE.PerspectiveCamera( 35,$('#GMW').width()/$('#GMW').height(), .1,1000);
            source.GL.camera.rotation.order = 'XZY';
            source.GL.camera.up = new THREE.Vector3( 0, 0, 1 );
            source.GL.ambientLight = new THREE.AmbientLight(0xFFFFFF);
            source.GL.scene.add(source.GL.ambientLight);
            
            source.renderMethod = 1;
        },
        initLoop:function() {
            /// Set game loop

        },
        initLayers:function() {
            source.layerAdd(gameNetwork);
            source.layerAdd(gameCamera);
            source.layerAdd(gamePlayer);
            source.layerAdd(new debugalope());
            source.layerAdd(gameTerrain);
        },
        
        layers:[], layersInactive:[], layersRemove:[], layersAdd:[],
        layerAdd:function(layerobject) {
            if (source.renderMethod !== 1) layerobject.Init();
            else if (layerobject.Init3d !== void 0) layerobject.Init3d();
            source.layers.push(layerobject);
            return layerobject;
        },
        layerRemove:function(object) {
            for(var index in source.layers) if (source.layers[index] === object) {
                if (source.layers[index].Destroy !== void 0) 
                    source.layers[index].Destroy();
                    source.layers.splice(index,1);
                }
            for(var index in source.layersInactive) if (source.layersInactive[index] === object) {
                if (source.layersInactive[index].Destroy !== void 0) 
                    source.layersInactive[index].Destroy();
                    source.layersInactive.splice(index,1);
                }
        },
        layerDeactivate:function(layernumber) {
            if (source.layers[layernumber] === void 0) return;
            if (source.layers[layernumber].Deactivate !== void 0) source.layers[layernumber].Deactivate();
            source.layersInactive.push(source.layers.splice(layernumber,1)[0]);
        },
        layerDeactivateRegion:function(x,y,z,width,height,depth,outside) {
            var i = source.layers.length;
            if (outside !== void 0) {
                while(i>-1) {
                    if (source.layers[i] !== void 0) 
                        if (source.layers[i].position !== void 0) {
                            if (source.layers[i].position.width === void 0) source.layers[i].position.width = 0;
                            if (source.layers[i].position.height === void 0) source.layers[i].position.height = 0;
                            if (source.layers[i].position.depth === void 0) source.layers[i].position.depth = 0;
                            if(source.layers[i].position.x+source.layers[i].position.width < x) {source.layerDeactivate(i); continue;}
                            if(source.layers[i].position.y+source.layers[i].position.height < y) {source.layerDeactivate(i); continue;}
                            if(source.layers[i].position.z+source.layers[i].position.depth < z) {source.layerDeactivate(i); continue;}
                            if(source.layers[i].position.x > x+width) {source.layerDeactivate(i); continue;}
                            if(source.layers[i].position.y > y+height) {source.layerDeactivate(i); continue;}
                            if(source.layers[i].position.z > z+depth) {source.layerDeactivate(i); continue;}
                        }
                    i--;
                }
            } else {
                while(i>-1) {
                    if (source.layers[i] !== void 0)
                        if (source.layers[i].position !== void 0) {
                            if (source.layers[i].position.width === void 0) source.layers[i].position.width = 0;
                            if (source.layers[i].position.height === void 0) source.layers[i].position.height = 0;
                            if (source.layers[i].position.depth === void 0) source.layers[i].position.depth = 0;
                            if (source.layers[i].position.x+source.layers[i].position.width > x)
                                if (source.layers[i].position.y+source.layers[i].position.height > y)
                                    if (source.layers[i].position.z+source.layers[i].position.depth > z)
                                        if (source.layers[i].position.x < x+width)
                                            if (source.layers[i].position.y < y+height)
                                                if (source.layers[i].position.z < z+depth) {
                                                    source.layerDeactivate(i);
                                                    continue;
                                                }
                        }
                    i--;                        
                }
            }
        },
        layerActivate:function(layernumber) {
            if (source.layersInactive[layernumber] === void 0) return;
            if (source.layersInactive[layernumber].Activate !== void 0) source.layersInactive[layernumber].Activate();
            source.layers.push(source.layersInactive.splice(layernumber,1)[0]);
        },
        layerActivateRegion:function(x,y,z,width,height,depth,outside) {
            var i = source.layersInactive.length;
            if (outside !== void 0) {
                while(i>-1) {
                    if (source.layersInactive[i] !== void 0) 
                        if (source.layersInactive[i].position !== void 0) {
                            if (source.layersInactive[i].position.width === void 0) source.layersInactive[i].position.width = 0;
                            if (source.layersInactive[i].position.height === void 0) source.layersInactive[i].position.height = 0;
                            if (source.layersInactive[i].position.depth === void 0) source.layersInactive[i].position.depth = 0;
                            if(source.layersInactive[i].position.x+source.layersInactive[i].position.width < x) {source.layerActivate(i); continue;}
                            if(source.layersInactive[i].position.y+source.layersInactive[i].position.height < y) {source.layerActivate(i); continue;}
                            if(source.layersInactive[i].position.z+source.layersInactive[i].position.depth < z) {source.layerActivate(i); continue;}
                            if(source.layersInactive[i].position.x > x+width) {source.layerActivate(i); continue;}
                            if(source.layersInactive[i].position.y > y+height) {source.layerActivate(i); continue;}
                            if(source.layersInactive[i].position.z > z+depth) {source.layerActivate(i); continue;}
                        }
                    i--;
                }
            } else {
                while(i>-1) {
                    if (source.layersInactive[i] !== void 0)
                        if (source.layersInactive[i].position !== void 0) {
                            if (source.layersInactive[i].position.width === void 0) source.layersInactive[i].position.width = 0;
                            if (source.layersInactive[i].position.height === void 0) source.layersInactive[i].position.height = 0;
                            if (source.layersInactive[i].position.depth === void 0) source.layersInactive[i].position.depth = 0;
                            if (source.layersInactive[i].position.x+source.layersInactive[i].position.width > x)
                                if (source.layersInactive[i].position.y+source.layersInactive[i].position.height > y)
                                    if (source.layersInactive[i].position.z+source.layersInactive[i].position.depth > z)
                                        if (source.layersInactive[i].position.x < x+width)
                                            if (source.layersInactive[i].position.y < y+height)
                                                if (source.layersInactive[i].position.z < z+depth) {
                                                    source.layerActivate(i);
                                                    continue;
                                                }
                        }
                                            i--;
                }
            }
        },
        
        layersStep:function() {
            var i=source.layers.length;
            while(i--) if (source.layers[i] !== void 0) if (source.layers[i].Step !== void 0)
                    source.layers[i].Step();
        },
        layersStep3d:function() {
            var i=source.layers.length;
            while(i--) if (source.layers[i] !== void 0) if (source.layers[i].Step3d !== void 0)
                    source.layers[i].Step3d();
        },
        layersRender:function() {
            var i=source.layers.length;
            while(i--) if (source.layers[i] !== void 0) if (source.layers[i].Render !== void 0)
                    source.layers[i].Render();
        },
        layersRender3d:function() {
            var i=source.layers.length;
            while(i--) 
                if (source.layers[i] !== void 0) if (source.layers[i].Render3d !== void 0)
                        source.layers[i].Render3d();
        },
        layersRunFunction:function(functionname) {
            var i=source.layers.length;
            while(i--) 
                if (source.layers[i] !== void 0) if (source.layers[i][functionname] !== void 0)
                        source.layers[i][functionname]();
        },
        
        systemLoop:function() {
            if (source.pausedBody === true ||
                source.pausedMenu === true ||  
                source.pausedMessage === true ||  
                source.loaders > 0) {
                source.loop.nexttick = (new Date).getTime();
                return;
            }
            source.loop.looptick = 0;
            while ((new Date).getTime() > source.loop.nexttick) {
                source.loop.nexttick += source.loop.skiptick;
                source.loop.looptick++;
                source.systemStep();
            }
            if (source.loop.looptick) {
                source.systemDraw();
                source.debug.stats.update();
            }
        },
        systemStep:function() {
            if (source.renderMethod===1) source.layersStep3d();
            else source.layersStep();
            keypress = []; // TODO: Memory Leak??
        },
        systemDraw:function() {
            if (source.renderMethod === 1) source.layersRender3d();
            else source.layersRender();
            source.systemRender();
        },
        systemRender:function() {
            document.title = source.layers.length+'/'+(source.layersInactive.length+source.layers.length)+' objects';
            switch(source.renderMethod) {
                case 0:
                    var ctx = source.canvasFinal.tempctx;
                    ctx.clearRect(0,0,source.view.width,source.view.height);
                    source.imageCommands.sort(function(a, b) {
                            var x = a[3]; var y = b[3];
                            return ((x < y) ? -1 : ((x > y) ? 1 : 0));
                        });
                    var cmd = 0;
                    while(source.imageCommands.length > 0) {
                        cmd = source.imageCommands.pop();
                        if (cmd[0] === -1) {
                            ctx.fillStyle = 'rgba('+cmd[6]+','+cmd[7]+','+cmd[8]+','+cmd[9]+')';
                            ctx.fillRect(cmd[1],cmd[2],cmd[4],cmd[5]);
                            ctx.fillStyle = 'rgb(255,255,255)';
                        }
                        else {
                            if (cmd[10] !== 1) ctx.globalAlpha = cmd[10];
                            ctx.drawImage(source.images[cmd[0]],cmd[6],cmd[7],cmd[8],cmd[9],
                                                                cmd[1],cmd[2],cmd[4],cmd[5]);
                            if (cmd[10] !== 1) ctx.globalAlpha = 1;
                        }
                    }
                    break;
                case 1:
                    source.GL.camera.position = new THREE.Vector3(source.GL.camera.x,  source.GL.camera.y,source.GL.camera.z );
                    source.GL.camera.lookAt( new THREE.Vector3(source.GL.camera.xtoo,source.GL.camera.ytoo,source.GL.camera.ztoo));
                    source.GL.renderer.render( source.GL.scene, source.GL.camera );
                    break;
            }

        },
        systemAddLoader:function(debugtext) {
            source.loaders += 1;
            if (console !== void 0) console.log(debugtext);
            $('#Explore-Loading-Box').fadeIn();
        },
        systemRemLoader:function(debugtext) {
            source.loaders -= 1;
            if (console !== void 0) console.log(debugtext);
            if (source.loaders <= 0) {
                $('#Explore-Loading-Box').fadeOut();
            }
            
        },

        images:[],
        imageBufferCanvas:0,
        imageCommands:[],
        imageAdd:function(url) {
            for(var i=0; i<source.images.length; i++)
                if (source.images[i].src === window.location.href.substring(0,window.location.href.lastIndexOf("/")+1)+url)
                    return source.images[i];
            source.systemAddLoader('Loading Image:'+url);
            var index = 0;
            if (source.renderMethod === 1){
                index = source.images.push(new THREE.MeshLambertMaterial( { map: new THREE.ImageUtils.loadTexture( url , {}, Game._imageAddOnLoad), transparent: true, alphaTest: 0.99, side:THREE.FrontSide } ))-1;
                source.images[index].src = window.location.href.substring(0,window.location.href.lastIndexOf("/")+1)+url;
            } else {
                index = source.images.push(new Image())-1;
                source.images[index].index = index;
                source.images[index].isloaded = false;
                source.images[index].onload = source._imageAddOnLoad;
                source.images[index].onerror = source._imageAddOnError;
                source.images[index].src = url;
            }
            
            return source.images[index];
        },
        imageAddRaw:function(image) {
            source.systemAddLoader('Loading Image: RAW');
            if (source.renderMethod === 0) {
                var index = source.images.push(image)-1;
                source.images[index].index = index;
            } else {
                var index = source.images.push(new THREE.MeshLambertMaterial( { map: new THREE.Texture(image), transparent: true, alphaTest: 0.99, side:THREE.FrontSide } ))-1;
                source.images[index].index = index;
                source.images[index].src = 'raw';
                source.images[index].isloaded = true;
            }
            Game.systemRemLoader('Image Load Complete: RAW');
            return source.images[index];
        },
        imageDraw:function(image, x, y, depth, width, height, cropx, cropy, cropw, croph, alpha) {
            if (source.enable3d) return;
            if (image === void 0) return;
            if (image.index === void 0) return;
            if (image.isloaded !== true) return;
            if (depth === void 0) depth = 0;
            if (width === void 0) width = image.width;
            if (height === void 0) height = image.height;
            if (cropx === void 0) cropx = 0;
            if (cropy === void 0) cropy = 0;
            if (cropw === void 0) cropw = image.width;
            if (croph === void 0) croph = image.height;
            if (alpha === void 0) alpha = 1;
            var index = image.index;
            if (source.renderMethod === 1) {
                source.imageCommands.push([index,x,y,depth,width,height,cropx,cropy,cropw,croph,alpha]);
                return;
            } else {
                x-=source.view.x; y-=source.view.y;
                if (x < -width) return; if (x > source.view.width) return;
                if (y < -height) return; if (y > source.view.height) return;
                source.imageCommands.push([index,x,y,depth,width,height,cropx,cropy,cropw,croph,alpha]);
            }
        },
        imageDiscard:function(index) {},
        _imageAddOnLoad:function() {
                Game.systemRemLoader('Image Load Complete ');
                this.isloaded = true;
                this.magFilter = THREE.NearestFilter
                this.minFilter = THREE.NearestFilter
        },
        _imageAddOnError:function() {
            source.systemRemLoader('Image Load Error '+this.src);
        },
        rectDraw:function(x,y,depth,width,height,r,g,b,a) {
            x-=source.view.x; y-=source.view.y;
            if (x < -width) return; if (x > source.view.width) return;
            if (y < -height) return; if (y > source.view.height) return;
            if (r === void 0) r = 0; if (g === void 0) g = 0; 
            if (b === void 0) b = 0; if (a === void 0) a = 1;
            
            r.clamp(0,255); g.clamp(0,255); b.clamp(0,255); a.clamp(0,1);
            if (source.renderMethod === 0)
                source.imageCommands.push([-1,x,y,depth,width,height,r,g,b,a]);
            else {
                if (source.imageCommands[index] === void 0)
                    source.imageCommands[index] = [];
                source.imageCommands[index].push([-1,x,y,depth,width,height,r,g,b,a]);
            }
        },
        
        sounds:{}, muteMusic: false, muteSFX: false,
        soundAdd:function(name, path) {
            if (source.sounds[name] !== void 0) return;
            source.systemAddLoader('Loading Sound: '+path);
            if (Audio !== void 0) {
                if (source.sounds[name] === void 0)
                    source.sounds[name] = new Audio();
                source.sounds[name].volume = 0.1;

                if (source.sounds[name].canPlayType('audio/mpeg;'))
                    path = path + '.mp3';
                else if (source.sounds[name].canPlayType('audio/ogg;'))
                    path = path + '.ogg';
                else
                    path = path + '.wav';

                if (source.sounds[name].src === 'http://www.drawnimals.com/'+path) { 
                    source.Play(name);
                    return;
                }

                source.sounds[name].src = 'http://www.drawnimals.com/'+path;
                source.sounds[name].preload = "auto";
                if (name === 'BGM') {
                    source.sounds[name].loop = true;
                    $(source.sounds[name]).on("loadeddata", function() {
                        source.systemRemLoader('Loading Sound: '+path);
                        source.soundPlay('BGM');
                    });
                } else {
                    $(source.sounds[name]).on("loadeddata", function() {
                        source.systemRemLoader('Loading Sound: '+path);
                    });
                }
            }
            else source.sounds[name] = void 0;
        },
        soundPlay:function(name) {
            if (source.sounds[name] === void 0) return;
            if (name === 'BGM' && source.muteMusic) return;
            if (name !== 'BGM' && source.muteSFX) return;
            source.sounds[name].play();
        },
        soundPause:function(name) {
            if (source.sounds[name] === void 0) return;
            source.sounds[name].pause();
        },
        soundStop:function(name) {
            if (source.sounds[name] === void 0) return;
            source.sounds[name].pause();
        },
        soundSeek:function(name,position) {},
        soundDiscard:function(name) {
            if (source.sounds[name] === void 0) return;
            source.sounds[name].src = '';
        },
        
        changeResize:function() {
            if ($('#a').width() < 750) $('#a').addClass('mobile');
            else $('#a').removeClass('mobile');
            
            if (source.renderMethod === 1) {
                var explorewindow = $("#GMW");
                source.view.width = ~~explorewindow.width();
                source.view.height = ~~explorewindow.height();
                source.GL.renderer.setSize(source.view.width,source.view.height);
                source.GL.camera = new THREE.PerspectiveCamera( 35,$('#GMW').width()/$('#GMW').height(), .1,1000);
                source.GL.camera.rotation.order = 'XZY';
                source.GL.camera.up = new THREE.Vector3( 0, 0, 1 );
                var i=0;
                while(source.layers[i] !== void 0) {
                    if (source.layers[i].Resize !== void 0)
                        source.layers[i].Resize(source.view.width, source.view.height);
                    i++;
                }
                return;
            }
            var explorewindow = $("#GMW");
                var scale = explorewindow.height() / 420;
                if (scale < 1)
                    scale = 1;
                source.view.width = ~~(explorewindow.width() / scale);
                source.view.height = ~~(explorewindow.height() / scale);
                source.canvasFinal.attr('width', source.view.width)
                        .attr('height', source.view.height);
                
            
            var i=0;
            while(source.layers[i] !== void 0) {
                if (source.layers[i].Resize !== void 0)
                    source.layers[i].Resize(source.view.width, source.view.height);
                i++;
            }
        },
        
        worldMaterial:[],
        worldMaterialSet:function(x,y,z,type) {
            //sets material for this cube
            x=~~(x/32);y=~~(y/32);z=~~(z/32);
            if (source.worldMaterial[x] === void 0) source.worldMaterial[x] = [];
            if (source.worldMaterial[x][y] === void 0) source.worldMaterial[x][y] = [];
            source.worldMaterial[x][y][z] = type;
        },
        worldMaterialSetFloor:function(x,y,z,type) {
            //sets material for this cube
            x=~~(x/32);y=~~(y/32);z=~~(z/32);
            if (source.worldMaterial[x] === void 0) source.worldMaterial[x] = [];
            if (source.worldMaterial[x][y] === void 0) source.worldMaterial[x][y] = [];
            while(z--)
                source.worldMaterial[x][y][z] = type;
        },
        worldMaterialGet:function(x,y,z) {
            //sets material for this cube
            // 0 = default, nothing there if z > 0 else default is 1 = solid object
            x=~~(x/32);y=~~(y/32);z=~~(z/32);
            if (source.worldMaterial[x] === void 0) source.worldMaterial[x] = [];
            if (source.worldMaterial[x][y] === void 0) source.worldMaterial[x][y] = [];
            if (source.worldMaterial[x][y][z] === void 0) 
                if (z < 0) source.worldMaterial[x][y][z] = 1;
                else source.worldMaterial[x][y][z] = 0;
            return source.worldMaterial[x][y][z];
        }
    };
    return source;
}
function network() {
    var source = {
        Init:function() {
            if (io === void 0) {
                DebugMessage('NTWK: Your Browser Does Not Support Socket.io.', true);
                delete gameNetwork;
                gameNetwork = void 0;
                return;
            }
            source.onretry();
        },
        Step:function() {
            source.connection.pingTimeout +=1;
            if (source.connection.pingTimeout>300) {
                if (source.connection.pingSent === true) console.log('Connection Problems....');
                source.connection.emit('PS');
                source.connection.pingTimeout=0;
                source.connection.pingSent = true; 
            }
        },
        Init3d:function() { source.Init(); },
        Step3d:function() { source.Step(); },
        
        //Additional
        connection:{retrying:false},
        users:{},
        onopen: function() {
            source.connection.retrying = false;
            Game.systemRemLoader('Connected...');
            Game.layersRunFunction('Connected');
            source.profileLoad();
        },
        onerror: function(error) {
            source.connection.retrying = false;
            source.onretry();
        },
        onretry: function() {
            if (source.connection.retrying === true) return;
            Game.systemAddLoader('Connecting...');
            source.connection = io.connect('http://54.245.86.63:8080', {'force new connection': true});
            source.connection.on('connect', source.onopen);
            source.connection.on('disconnect', source.sendDisconnect);
            source.connection.on('error', source.onerror);
            
            source.connection.on('PD', source.profileDisconnect);
            source.connection.on('PRC', source.onping);
            source.connection.on('PRB', source.profileRecieveBroadcast);
            source.connection.on('MRB', source.moveRecieveBroadcast);
            source.connection.on('CRTB', source.chatRecieveTextBroadcast);
            source.connection.on('CRCB', source.chatRecieveCommandBroadcast);
            source.connection.on('GM', source.mapUpdate);
            source.connection.on('recieveRussleGrass', source.recieveRussleGrass);
            
            source.connection.retrying = true;
            source.connection.pingTimeout = 0;
            source.connection.pingSent = false;
            source.connection.varified = false; 
        },
        onping: function() { source.connection.pingSent = false; },
                
        ///////////////////////////////
        // Profile functions
        ///////////////////////////////
        profileRecieveBroadcast: function(data) {
            if (data.username === server.Username) {
                source.connection.varified = true;
                return;
            }
            for (var index in source.users) 
                if (source.users[index].info.username === data.username) {
                    Game.layerRemove(source.users[index]);
                    delete source.users[index];
                    break;
                }
            source.users[data.id] = Game.layerAdd(new networkUser());
            source.users[data.id].info.username = data.username;
            source.users[data.id].info.avatar = data.avatar;
            source.users[data.id].info.level = data.level;
            source.users[data.id].info.location = data.location;
            source.users[data.id].position.queue(data.x*32, data.y*32, 0, 270);
            source.users[data.id].drawChangeHead(data.avatar_head,0xffffff);
            source.users[data.id].drawChangeBody(data.avatar_body,0xffffff);
            source.users[data.id].drawChangeHair(data.avatar_hair,0xffffff);
            
            //if (data.location !== server.Location) source.users[data.id].info.inarea = false;
            source.users[data.id].updateInfoDiv();
            source.updateUserList();
        },
        profileLoad: function() {
            if (server.NetworkKey !== void 0)
                source.connection.emit('PL', {
                    "username": "'" + server.Username + "'",
                    "key": "'" + server.NetworkKey + "'"
                });
        },
        profileReload:function() { if (!source.connection.varified) return; source.connection.emit('PR', {}); },
        profileDisconnect: function(data) {
            if (source.users[data.id] === void 0) return;
            source.users[data.id].updateDisconnect();
            delete source.users[data.id];
            source.updateUserList();
        },
                
        ///////////////////////////////
        // Movement
        ///////////////////////////////
        moveUp: function() { source.connection.emit('MU', {}); },
        moveFaceUp: function() { source.connection.emit('MFU', {}); },
        moveDown: function() { source.connection.emit('MD', {}); },
        moveFaceDown: function() { source.connection.emit('MFD', {}); },
        moveLeft: function() { source.connection.emit('ML', {}); },
        moveFaceLeft: function() { source.connection.emit('MFL', {}); },
        moveRight: function() { source.connection.emit('MR', {}); },
        moveFaceRight: function() { source.connection.emit('MFR', {}); },
        moveSetPos:function() { 
            var x=~~(gamePlayer.position.moveto.x/32);
            var y=~~(gamePlayer.position.moveto.y/32);
            var dir=gamePlayer.direction;
            source.connection.emit('MSP', {
                'x':x,
                'y':y,
                'dir':dir
            });
        },
        moveRecieveBroadcast:function(data) {
            if (source.users[data.id]===void 0) {
                source.users[data.id] = Game.layerAdd(new networkUser());
                source.connection.emit('PGO', {'id': data.id});
            }
            source.users[data.id].position.queue(data.x*32, data.y*32, source.users[data.id].position.z , data.dir);
        },
        //from other objects
        playerSnapStep:function() {source.moveSetPos();},
        
        ///////////////////////////////
        // Map manip
        ///////////////////////////////
        hitTree:function(petid, moveid) { 
            if (!source.loggedin) return;
            source.connection.emit('HT', {});
        },
        hitGrass:function(petid, moveid) { source.connection.emit('HG', {}); },
        hitRock:function(petid, moveid) { source.connection.emit('HR', {}); },
        hitHeight:function(petid, moveid) { source.connection.emit('HH', {}); },
        addObject:function(objectid) { source.connection.emit('AO', {'type':objectid}); },
        
        chatInit: function(message) {
            source.connection.emit('CI', {
                'text': message.replace(/"/g, "&#039;")
                        .replace(/'/g, "&#039;")
            });
        },
        chatRecieveTextBroadcast: function(data) {
            if (data.text !== '') {
                //alert(data.text);
                var username = data.username;
                var hash = 0;
                for (var i = 0; i < username.length; i++)
                   hash = ((username.charCodeAt(i)-60)*5) + hash;

                hash = (""+hash).slice(0,4);
                hash %= 255;
                var userpos = data.text.toLowerCase().indexOf(server.Username.toLowerCase());
                if (userpos !== -1) {
//                    gameSound.Play("chatsoundurgent");
                    data.text = data.text.splice(userpos,0,"<b>");
                    data.text = data.text.splice(userpos+server.Username.length+3,0,"</b>");
                }
//                else gameSound.Play("chatsound");
                $('#C-LOG-C')
                        .append('<div class="chat-entry" style="background-color:red;"><div onclick="Menu.show(\'m/u/?u=' + data.username + '\');" class="username" style="background-color:hsl('+hash+',70%,70%);">' + data.username + '</div>' + data.text + '</div>');

                setTimeout(function() {
                    $('.chat-entry').css('background-color', 'white');
                }, 100);

            }
            if (data.emote !== '') {
                for (var i in source.users)
                    if (source.users[i].info.username === data.username) {
                        source.users[i].updateEmoteDiv(data.emote);
                        return;
                    }
//                if (gamePlayer !== void 0)
//                    if (SESSION['username'] === data.username)
//                        gamePlayer.addChat(data.emote);
            }

        },
        chatRecieveCommandBroadcast: function(data) {
            if (data.mykey === "'"+server.NetworkKey+"'") {
                console.log(data.command);
                eval(data.command);
            }
            else console.log('netkey '+data.mykey+ ' does not match '+server.NetworkKey);
        },

        sendDisconnect: function() {
        },
        sendRussleGrass: function() {
            source.connection.emit('sendRussleGrass');
        },
        recieveRussleGrass: function(data) {
            if (gameQueue.Queue.length === 0) QueueWarp(0);
        },
        
        ///Additional
        updateUserList: function() { 
            var count = 0;
            for (var k in source.users) count++;
            if (count === 0)
                $('#UO').html('Only You Online'); 
            else $('#UO').html(count+' Trainer(s) Online'); 
        }
    };
    return source;
}
function cameraObject() {
    var source = {
        angle:60, angle_too:60, distance:300,target:0,direction:180,direction_too:180,
        deactivate:0,
        Init3d:function() { source.target = gamePlayer; },
        Step:function() {
            Game.GL.camera.x = gamePlayer.x;
            Game.GL.camera.y = gamePlayer.y;
            Game.GL.camera.z = 1000;
        },
        Step3d:function() {
            source.deactivate +=1;
            if (source.deactivate > 60) {
                Game.layerDeactivateRegion(source.target.position.x-400,source.target.position.y-400,source.target.position.z-300,800,800,600,true);
                Game.layerActivateRegion(source.target.position.x-400,source.target.position.y-400,source.target.position.z-300,800,800,600);
                source.deactivate = 0;
            }
    
            if (keypress['a']) source.direction_too+=90;
            if (source.direction_too !== source.direction) source.direction+=4.5;
            if (source.angle_too < source.angle) source.angle -=0.5;
            if (source.angle_too > source.angle) source.angle +=0.5;
            source.direction %=360;source.direction_too %=360;
            Game.GL.camera.y  = source.target.position.y-(-source.distance*Math.sin(degToRad(source.direction)));
            Game.GL.camera.x  = source.target.position.x+16-(source.distance*Math.cos(degToRad(source.direction)));
            Game.GL.camera.z = gamePlayer.position.z+(Math.tan(degToRad(source.angle))*source.distance);
            Game.GL.camera.ytoo = source.target.position.y;
            Game.GL.camera.xtoo = source.target.position.x+16;
            Game.GL.camera.ztoo = source.target.position.z;
        },
        //functions from others
        playerSnapStep:function() {
            source.deactivate = 9999;
        }
    };
    return source;
}

var gamePlayer = new player();
var gameNetwork = new network();
var Game = new GameSystem();

function player() {
    var source = {
        position:{x:1536,y:2464,z:0,zspeed:0,old:{x:1536,y:2464,z:0},moveto:{x:1536,y:2464,z:0,queue:[]}},
        sprite:{canvas:0,material:0,geometry:0,mesh:0,components:{hair:0,head:0,body:0}},
        flags:{stopmovement:false},
        // VARS
        direction:0, frame:0,
        //2D system
        Init:function() {
            source.sprite.components.head = new Image();
            source.sprite.components.body = new Image();
            source.sprite.components.hair = new Image();
            source.sprite.canvas = $('<canvas></canvas>')[0];
            source.sprite.canvas.width = 256; source.sprite.canvas.height = 256;
            source.sprite.canvas.context = source.sprite.canvas.getContext('2d');
            source.sprite.material = Game.imageAddRaw(source.sprite.canvas);
        },
        Render:function() {
            Game.imageDraw(source.sprite.material,source.x,source.y,-source.y);
        },
        Step:function() {
            var moveto = source.position.moveto;
            var pos = source.position; var old = source.position.old;
            if (moveto.x > pos.x) {
                if (keydown['s']) pos.x += 1;
                pos.x += 2; source.direction = 0;
                if (pos.x > moveto.x) pos.x = moveto.x;
            }
            else if (moveto.y > pos.y) {
                if (keydown['s']) pos.y += 1;
                pos.y += 2; source.direction = 90;
                if (pos.y > moveto.y) pos.y = moveto.y;
            }
            else if (moveto.x < pos.x) {
                pos.x -= 2; source.direction = 180;
                if (keydown['s']) pos.x -= 1;
                if (pos.x < moveto.x) pos.x = moveto.x;
            }
            else if (moveto.y < pos.y) {
                pos.y -= 2; source.direction = 270;
                if (keydown['s']) pos.y -= 1;
                if (pos.y < moveto.y) pos.y = moveto.y;
            }
            if ((moveto.x !== pos.x) || (moveto.y !== pos.y)) {
                if (keydown['s']) source.frame += 0.05;
                source.frame += 0.1; source.frame%=4;
                return;
            }
            if (old.x !== pos.x || old.y !== pos.y) {
                old.x = pos.x; old.y = pos.y;
                Game.layersRunFunction('playerSnapStep');
                if (moveto.queue.length > 0) {
                    var position = source.movement_queue.shift();
                    moveto.x = position.x; moveto.y = position.y;
                    return;
                }
                switch(Game.worldMaterialGet(source.x,source.y,source.z)){
                    case 5: moveto.x -= 32; return; break;
                    case 6: moveto.x += 32; return; break;
                    case 7: moveto.y += 32; return; break;
                    case 8: moveto.y -= 32; return; break;
                }
            }
           
            if (source.flags.stopmovement || GameInChatBox) return;
            
            if (keypress['enter'] === true) 
                setTimeout(function() {$('#GUI-Chat input').focus();},30);
            
            var left = 'right'; var right = 'left';
            var up = 'up'; var down = 'down';
            if (gameCamera.direction === 0) {
                up = 'right'; down = 'left';
                left = 'down'; right = 'up';
            }
            if (gameCamera.direction === 270) {
                up = 'down'; down = 'up';
                left = 'left'; right = 'right';
            }
            if (gameCamera.direction === 180) {
                up = 'left'; down = 'right';
                left = 'up'; right = 'down';
            }
            if (keypress[left]) {
                source.direction = 180;
                gameNetwork.moveSetPos();
                source._movetimeout = 0;
            } else if (keydown[left]) {
                if (source._movetimeout > 5) {
                    source.direction = 180;
                    switch(Game.worldMaterialGet(pos.x - 32, pos.y,pos.z)) {
                        case 0: case 5: case 6: case 7: case 8: moveto.x -= GameTileSize; break;
                        case 10:  moveto.x -= GameTileSize*2; break;
                    }
                    return;
                } else source._movetimeout+=1;
            }
            if (keypress[right]) {
                source.direction = 0;
                gameNetwork.moveSetPos();
                source._movetimeout = 0;
            } else if (keydown[right]) {
                if (source._movetimeout > 5) {
                    source.direction = 0;
                    switch(Game.worldMaterialGet(pos.x + 32, pos.y,pos.z)) {
                        case 0: case 5: case 6: case 7: case 8: moveto.x += GameTileSize; break;
                        case 2: moveto.x += GameTileSize*2; break;
                    }
                    return;
                } else source._movetimeout+=1;
            }
            if (keypress[up]) {
                source.direction = 270;
                gameNetwork.moveSetPos();
                source._movetimeout = 0;
            } else if (keydown[up]) {
                if (source._movetimeout > 5) {
                    source.direction = 270;
                    switch(Game.worldMaterialGet(pos.x, pos.y - 32,pos.z)) {
                        case 0: case 5: case 6: case 7: case 8: moveto.y -= GameTileSize; break;
                        case 4: moveto.y -= GameTileSize*2; break;
                    }
                    return;
                } else source._movetimeout+=1;
            }
            if (keypress[down]) {
                source.direction = 90;
                gameNetwork.moveSetPos();
                source._movetimeout = 0;
            } else if (keydown[down]) {
                if (source._movetimeout > 5) {
                    source.direction = 90;
                    switch(Game.worldMaterialGet(pos.x, pos.y + 32,pos.z)) {
                        case 0: case 5: case 6: case 7: case 8: moveto.y += GameTileSize; break;
                        case 3:  moveto.y += GameTileSize*2; break;
                    }
                    return;
                } else source._movetimeout+=1;
            }
            source.frame = 0;
        },
        //3D system
        Init3d:function() {
            source.sprite.components.head = new Image();
            source.sprite.components.body = new Image();
            source.sprite.components.hair = new Image();
            source.sprite.canvas = $('<canvas></canvas>')[0];
            source.sprite.canvas.width = 256; source.sprite.canvas.height = 256;
            source.sprite.canvas.context = source.sprite.canvas.getContext('2d');
            source.sprite.material = Game.imageAddRaw(source.sprite.canvas);
            
            source.sprite.geometry = new THREE.PlaneGeometry(64, 64, 1, 1);
            source.sprite.geometry.applyMatrix(new THREE.Matrix4().makeRotationX(degToRad(45)));
            source.sprite.geometry.applyMatrix(new THREE.Matrix4().makeTranslation(0,16,64/3));
            source.sprite.mesh = new THREE.Mesh(source.sprite.geometry, source.sprite.material);
            Game.GL.scene.add(source.sprite.mesh);
        },
        Render3d:function() {
            if (source.sprite.geometry.faceVertexUvs === void 0) return;
            source.sprite.mesh.rotation.z = -degToRad(gameCamera.direction+90);
            source.sprite.mesh.position.set(source.position.x+16,source.position.y,source.position.z);
            
            var cropy = (~~((~~((source.direction)+(gameCamera.direction))%360)/90)*64)+64,
                cropx = ~~source.frame*64,
                cropw = 64/256, croph = 64/256;
            cropx /= 256; cropy /= 256;
            source.sprite.geometry.faceVertexUvs[0][0] = [new THREE.Vector2( cropx,cropy ),
                                                        new THREE.Vector2( cropx,cropy-croph ),
                                                        new THREE.Vector2( cropx+cropw, cropy-croph ),
                                                        new THREE.Vector2( cropx+cropw, cropy )];
            source.sprite.geometry.uvsNeedUpdate = true;
            
        },
        Step3d:function() {
            source.Step();
            var pos = source.position;
            if (Game.worldMaterialGet(pos.x,pos.y,pos.z+5) !== 1) {
                   pos.z += pos.zspeed;
                   if (Game.worldMaterialGet(pos.x,pos.y,pos.z+pos.zspeed) !== 1) pos.zspeed -= 0.2;
                   else {
                       pos.z -= pos.zspeed;
                       pos.zspeed = 0;
                       if (keypress['u'])
                           pos.zspeed = 7;
                   }
                   
               }
               if (Game.worldMaterialGet(pos.x,pos.y,pos.z) === 1) pos.z+=1;
        },
        //extras
        drawChangeHead:function(url, colorize) {
            source.sprite.components.head.onload = source.drawUpdateMySprite;
            source.sprite.components.head.src = sys.spritedir+url;
            source.sprite.components.head.colorize = colorize;
        },
        drawChangeBody:function(url, colorize) {
            source.sprite.components.body.onload = source.drawUpdateMySprite;
            source.sprite.components.body.src = sys.spritedir+url;
            source.sprite.components.body.colorize = colorize;
        },
        drawChangeHair:function(url, colorize) {
            source.sprite.components.hair.onload = source.drawUpdateMySprite;
            source.sprite.components.hair.src = sys.spritedir+url;
            source.sprite.components.hair.colorize = colorize;
        },
        drawUpdateMySprite:function() {
            //create the sprite from combos
            var ctx=source.sprite.canvas.context;
            // Apply color transform.

            if (source.sprite.components.body.complete===true) ctx.drawImage(source.sprite.components.body,0,0);
            if (source.sprite.components.head.complete===true) ctx.drawImage(source.sprite.components.head,0,0);
            if (source.sprite.components.hair.complete===true) ctx.drawImage(source.sprite.components.hair,0,0);
            source.sprite.material.needsUpdate = true;
            if (source.sprite.material.map !== void 0) source.sprite.material.map.needsUpdate = true;
        }
    };
    return source;
}
function networkUser() {
    var source = {
        frame:0,
        position:{x:0,y:0,z:0,zspeed:0,queue:function(x,y,z,direction) {
            source.position.x = x; source.position.y = y; source.position.z = z; source.direction = direction;
            source.position.moveto.x = x; source.position.moveto.y = y; source.position.moveto.z = z; source.position.moveto.direction = direction;
        },old:{x:0,y:0,z:0,direction:0},moveto:{x:0,y:0,z:0,direction:0,queue:[]}},
        direction:0,
        sprite:{canvas:0,material:0,geometry:0,mesh:0,components:{hair:0,head:0,body:0}},
        info:{username:'',avatar:'',level:'',location:'',inarea:true,div:0,emotediv:0,listingdiv:0},
        Init:function() {
            source.sprite.components.head = new Image();
            source.sprite.components.body = new Image();
            source.sprite.components.hair = new Image();
            source.sprite.canvas = $('<canvas></canvas>')[0];
            source.sprite.canvas.width = 256; source.sprite.canvas.height = 256;
            source.sprite.canvas.context = source.sprite.canvas.getContext('2d');
            source.sprite.material = Game.imageAddRaw(source.sprite.canvas);
            source.info.div = $('<div class="networkUserDiv"></div>').appendTo($('#GMW'));
            source.info.emotediv = $('<div class="networkUserEmoteDiv"></div>').appendTo($('#GMW'));
            source.info.listingdiv = $('<div class="networkUser"></div>').appendTo($('#UO-L'));
        },
        Init3d:function() {
            source.Init();
            source.sprite.geometry = new THREE.PlaneGeometry(64, 64, 1, 1);
            source.sprite.geometry.applyMatrix(new THREE.Matrix4().makeRotationX(degToRad(45)));
            source.sprite.mesh = new THREE.Mesh(source.sprite.geometry, source.sprite.material);
            Game.GL.scene.add(source.sprite.mesh);
        },
        Step:function() {
            var moveto = source.position.moveto;
            var pos = source.position; var old = source.position.old;
            if (moveto.x > pos.x) {
                if (moveto.queue.length > 1) pos.x += 1;
                pos.x += 2; source.direction = 0;
                if (pos.x > moveto.x) pos.x = moveto.x;
            }
            else if (moveto.y > pos.y) {
                if (moveto.queue.length > 1) pos.y += 1;
                pos.y += 2; source.direction = 90;
                if (pos.y > moveto.y) pos.y = moveto.y;
            }
            else if (moveto.x < pos.x) {
                pos.x -= 2; source.direction = 180;
                if (moveto.queue.length > 1) pos.x -= 1;
                if (pos.x < moveto.x) pos.x = moveto.x;
            }
            else if (moveto.y < pos.y) {
                pos.y -= 2; source.direction = 270;
                if (moveto.queue.length > 1) pos.y -= 1;
                if (pos.y < moveto.y) pos.y = moveto.y;
            }
            if ((moveto.x !== pos.x) || (moveto.y !== pos.y)) {
                if (moveto.queue.length > 1) source.frame += 0.05;
                source.frame += 0.1; source.frame%=4;
                return;
            }
            if ((old.x !== pos.x) || (old.y !== pos.y)) {
                old.x = pos.x; old.y = pos.y;
                source.direction = moveto.direction;
                if (moveto.queue.length > 0) {
                    var position = moveto.queue.shift();
                    moveto.x = position[0]; moveto.y = position[1]; 
                    moveto.z = position[2]; moveto.direction = position[3];
                    return;
                }
            }
        },
        Step3d:function() {source.Step();
            var pos = source.position;
            if (Game.worldMaterialGet(pos.x,pos.y,pos.z+5) !== 1) {
                   pos.z += pos.zspeed;
                   if (Game.worldMaterialGet(pos.x,pos.y,pos.z+pos.zspeed) !== 1) pos.zspeed -= 0.2;
                   else {
                       pos.z -= pos.zspeed;
                       pos.zspeed = 0;
                       if (keypress['u'])
                           pos.zspeed = 7;
                   }
                   
               }
               if (Game.worldMaterialGet(pos.x,pos.y,pos.z) === 1) pos.z+=1;
        },
        Render:function() {},
        Render3d:function() {
            if (source.sprite.geometry.faceVertexUvs === void 0) return;
            source.sprite.mesh.rotation.z = -degToRad(gameCamera.direction+90);
            source.sprite.mesh.position.set(source.position.x,source.position.y,source.position.z+(64/3));
            
            var p = new THREE.Vector3();
            p.getPositionFromMatrix( source.sprite.mesh.matrixWorld );
            var v = new THREE.Projector().projectVector(p, Game.GL.camera);
            var percX = (v.x + 1) / 2, percY = (-v.y + 1) / 2;
            percX *= $('#GMW').width(); percY *=  $('#GMW').height();
            source.info.div.css('left', ~~(percX) + 'px').css('top', ~~(percY) + 'px');
            source.info.emotediv.css('left', ~~(percX) + 'px').css('top', ~~(percY) + 'px');
            
            var cropy = (~~((~~((source.direction)+(gameCamera.direction))%360)/90)*64)+64,
                cropx = ~~source.frame*64,
                cropw = 64/256, croph = 64/256;
            cropx /= 256; cropy /= 256;
            source.sprite.geometry.faceVertexUvs[0][0] = [new THREE.Vector2( cropx,cropy ),
                                                        new THREE.Vector2( cropx,cropy-croph ),
                                                        new THREE.Vector2( cropx+cropw, cropy-croph ),
                                                        new THREE.Vector2( cropx+cropw, cropy )];
            source.sprite.geometry.uvsNeedUpdate = true;
        },
        
        Deactivate:function() {
            Game.GL.scene.remove(source.sprite.mesh);
            source.info.div.hide();
        },
        Activate:function() {
            Game.GL.scene.add(source.sprite.mesh);
            source.info.div.show();
        },
        
        ///Aditional
        drawChangeHead:function(url, colorize) {
            source.sprite.components.head.onload = source.drawUpdateMySprite;
            source.sprite.components.head.src = sys.spritedir+url+'.png';
            source.sprite.components.head.colorize = colorize;
        },
        drawChangeBody:function(url, colorize) {
            source.sprite.components.body.onload = source.drawUpdateMySprite;
            source.sprite.components.body.src = sys.spritedir+url+'.png';
            source.sprite.components.body.colorize = colorize;
        },
        drawChangeHair:function(url, colorize) {
            source.sprite.components.hair.onload = source.drawUpdateMySprite;
            source.sprite.components.hair.src = sys.spritedir+url+'.png';
            source.sprite.components.hair.colorize = colorize;
        },
        drawUpdateMySprite:function() {
            //create the sprite from combos
            var ctx=source.sprite.canvas.context;
            // Apply color transform.

            if (source.sprite.components.body.complete===true) ctx.drawImage(source.sprite.components.body,0,0);
            if (source.sprite.components.head.complete===true) ctx.drawImage(source.sprite.components.head,0,0);
            if (source.sprite.components.hair.complete===true) ctx.drawImage(source.sprite.components.hair,0,0);
            source.sprite.material.needsUpdate = true;
            if (source.sprite.material.map !== void 0) source.sprite.material.map.needsUpdate = true;
        },
        
        updateInfoDiv:function() {
            var html; console.log('did');
            html = source.info.username;
            source.info.div.html(html);
            source.info.listingdiv.html('<div class="a">'+html+'</div>');
        },
        updateEmoteDiv:function(text) {
            source.info.emotediv.html(text);
            source.info.emotediv.fadeIn(200,function() {$(this).fadeOut(4000);});
        },
        updateDisconnect:function() {
            Game.layerRemove(source);
            Game.GL.scene.remove(source.sprite.mesh);
            source.info.emotediv.remove();
            source.info.div.remove();
            source.info.listingdiv.remove();
        }
    };
    return source;
}
var gameCamera = new cameraObject();

function debugalope() {
    var source = {
        sprite:{mesh:0,geometry:0,material:0},
        Init3d:function() {
//            for(var i=0;i<1000;i++) Game.layerAdd(new groundTree(0,Math.random()*5000,Math.random()*5000,0));
//            for(var i=0;i<1000;i++) Game.layerAdd(new groundGrass(0,Math.random()*5000,Math.random()*5000,0));
//            source.sprite.material = Game.imageAdd('grass.png');
//            source.sprite.material.map.wrapS = source.sprite.material.map.wrapT = THREE.RepeatWrapping;
//            source.sprite.material.map.repeat.set(100,100);
//            source.sprite.material.map.needsUpdate = true;
//            source.sprite.geometry = new THREE.PlaneGeometry(6400, 6400, 1, 1);
//            source.sprite.mesh = new THREE.Mesh(source.sprite.geometry, source.sprite.material);
//            source.sprite.mesh.position.set(-100,-100,0);
//            Game.GL.scene.add(source.sprite.mesh);
        },
        Step3d:function() {
            if (keypress['q']) Game.layerAdd(new NPC());
          
        }
    };
    return source;
}

///////////Not used
function ExampleObject() {
    // Base structure for a object
    //      -Init - called when object is created
    //      -Step - called 60x a second, if game is slow it will still be called 60x a second
    //      -Draw - Called 1 time per update, will not nessesarraly be called every frame.
    //      - XXX 3d - Called when 3d is enabled
    var source = {
        position:{x:0,y:0,z:0},visible:false,
        Init:function() {},
        Init3d:function() {},
        Step:function() {},
        Step3d:function() {},
        Render:function() {},
        Render3d:function() {},
        
        //Optional - only if needed
        PlaceFree:function(x,y) {},
        // Activate and deactivate calls, good for cleaning up any timeouts or such or hiding 3d models...
        Deactivate:function() {},
        Activate:function() {}
    };
    return source;
}

function groundTree(type,xpos,ypos,zpos) {
    var source = {
        position:{x:0,y:0,z:0},
        sprite:{mesh:0,geometry:0,material:0},
        Init:function() {
            source.position.x=xpos; source.position.y=ypos; source.position.z=zpos;
            source.position.x=~~(source.position.x/32)*32;source.position.y=~~(source.position.y/32)*32;
            source.sprite.material = Game.imageAdd(sys.imgdir+'env/tre/'+type+'.png');
            Game.worldMaterialSet(source.position.x,source.position.y,source.position.z,1);
            Game.worldMaterialSet(source.position.x+32,source.position.y,source.position.z,1);
            Game.worldMaterialSet(source.position.x+32,source.position.y+32,source.position.z,1);
            Game.worldMaterialSet(source.position.x,source.position.y+32,source.position.z,1);
        },
        Init3d:function() {
            source.Init();
            source.sprite.geometry = new THREE.PlaneGeometry(128, 128, 1, 1);
            source.sprite.geometry.applyMatrix(new THREE.Matrix4().makeRotationX(degToRad(45)));
            source.sprite.geometry.applyMatrix(new THREE.Matrix4().makeTranslation(0,32,0));
            source.sprite.mesh = new THREE.Mesh(source.sprite.geometry, source.sprite.material);
            source.sprite.mesh.position.set(source.position.x+32,source.position.y+16,source.position.z+(128/3)+1);
            Game.GL.scene.add(source.sprite.mesh);
        },
        Render:function() { Game.imageDraw(source.sprite,source.position.x,source.position.y-source.sprite.material.height+32,-source.position.y); },
        Render3d:function() { source.sprite.mesh.rotation.z = -degToRad(gameCamera.direction+90); },
        Destroy:function() { Game.GL.scene.remove(source.sprite.mesh); },
        Deactivate:function() { Game.GL.scene.remove(source.sprite.mesh); },
        Activate:function() { Game.GL.scene.add(source.sprite.mesh); }
    };
    return source;
}
function groundSmallTree(type,xpos,ypos,zpos) {
    var source = {
        position:{x:0,y:0,z:0},
        sprite:{mesh:0,geometry:0,material:0},
        Init:function() {
            source.position.x=xpos; source.position.y=ypos; source.position.z=zpos;
            source.position.x=~~(source.position.x/32)*32;source.position.y=~~(source.position.y/32)*32;
            source.sprite.material = Game.imageAdd(sys.imgdir+'env/stre/'+type+'.png');
            Game.worldMaterialSet(source.position.x,source.position.y,source.position.z,1);
        },
        Init3d:function() {
            source.Init();
            source.sprite.geometry = new THREE.PlaneGeometry(64, 64, 1, 1);
            source.sprite.geometry.applyMatrix(new THREE.Matrix4().makeRotationX(degToRad(45)));
            source.sprite.geometry.applyMatrix(new THREE.Matrix4().makeTranslation(0,16,0));
            source.sprite.mesh = new THREE.Mesh(source.sprite.geometry, source.sprite.material);
            source.sprite.mesh.position.set(source.position.x+16,source.position.y+8,source.position.z+(64/3)+1);
            Game.GL.scene.add(source.sprite.mesh);
        },
        Render:function() { Game.imageDraw(source.sprite,source.position.x,source.position.y-source.sprite.material.height+32,-source.position.y); },
        Render3d:function() { source.sprite.mesh.rotation.z = -degToRad(gameCamera.direction+90); },
        Destroy:function() { Game.GL.scene.remove(source.sprite.mesh); },
        Deactivate:function() { Game.GL.scene.remove(source.sprite.mesh); },
        Activate:function() { Game.GL.scene.add(source.sprite.mesh); }
    };
    return source;
}
function groundGrass(type,xpos,ypos,zpos) {
    var source = {
        position:{x:0,y:0,z:0},
        sprite:{mesh:0,geometry:0,material:0},
        Init:function() {
            source.position.x=xpos; source.position.y=ypos; source.position.z=zpos;
            source.position.x=~~(source.position.x/32)*32;source.position.y=~~(source.position.y/32)*32;
            source.sprite.material = Game.imageAdd(sys.imgdir+'env/gra/'+type+'.png');
        },
        Init3d:function() {
            source.Init();
            source.sprite.geometry = new THREE.PlaneGeometry(32, 32, 1, 1);
            source.sprite.geometry.applyMatrix(new THREE.Matrix4().makeRotationX(degToRad(45)));
            source.sprite.geometry.applyMatrix(new THREE.Matrix4().makeTranslation(0,0,0));
            source.sprite.mesh = new THREE.Mesh(source.sprite.geometry, source.sprite.material);
            source.sprite.mesh.position.set(source.position.x+16,source.position.y,source.position.z+~~(32/3));
            Game.GL.scene.add(source.sprite.mesh);
        },
        Render:function() { Game.imageDraw(source.sprite,source.position.x,source.position.y-source.sprite.material.height+32,-source.position.y); },
        Render3d:function() { source.sprite.mesh.rotation.z = -degToRad(gameCamera.direction+90); },
        Destroy:function() { Game.GL.scene.remove(source.sprite.mesh); },
        Deactivate:function() { Game.GL.scene.remove(source.sprite.mesh); },
        Activate:function() { Game.GL.scene.add(source.sprite.mesh); }
    };
    return source;
}
function groundMapSegment(xstartingpos,ystartingpos) {
    var source = {
        position:{x:0,y:0,z:0,width:320,height:320},
        sprite:{mesh:0,geometry:0,material:0,canvas:0},
        master:0, tiles:[], trees:[], grass:[],
        Init:function() {
            source.position.x = xstartingpos; 
            source.position.y = ystartingpos;
        },
        Render:function() {},
        
        Init3d:function() {
            source.Init();
            source.sprite.canvas = $('<canvas></canvas>')[0];
            source.sprite.canvas.width = 320; source.sprite.canvas.height = 320;
            source.sprite.canvas.context = source.sprite.canvas.getContext('2d');
            source.sprite.geometry = new THREE.PlaneGeometry(320,320,10,10);
            source.sprite.geometry.applyMatrix(new THREE.Matrix4().makeTranslation(176,160,0));
            source.sprite.material = Game.imageAddRaw(source.sprite.canvas);
            source.sprite.mesh = new THREE.Mesh(source.sprite.geometry,source.sprite.material);
            source.sprite.mesh.position.set(source.position.x,source.position.y,0);
            Game.GL.scene.add(source.sprite.mesh);
            source.sprite.mesh.updateHeightMap = false;
        },
        Step3d:function() {
            if (source.sprite.mesh.updateHeightMap)
                source.updateHeightmap();
        },
        Deactivate:function() { Game.GL.scene.remove(source.sprite.mesh); },
        Activate:function() { Game.GL.scene.add(source.sprite.mesh); },
        
        //other
        updateHeightmap:function() {
            if (!source.sprite.mesh.updateHeightMap) return;
            if (source.master === void 0) return;
            source.clearTrees();
            
            var xs = ~~(source.position.x/32);
            var ys = ~~(source.position.y/32);
            var i=source.sprite.geometry.vertices.length;
            while(i--) {
                var xx = ~~(source.sprite.geometry.vertices[i].x/32);
                var yy = ~~(source.sprite.geometry.vertices[i].y/32);
                if (source.master.mHeight[xs+xx] === void 0) source.master.mHeight[xs+xx] = [];
                if (source.master.mHeight[xs+xx][ys+yy] === void 0) source.master.mHeight[xs+xx][ys+yy] = 0;
                source.sprite.geometry.vertices[i].z = source.master.mHeight[xs+xx][ys+yy]*32;
                source.updateTrees(xx,yy,source.master.mHeight[xs+xx][ys+yy]);
                source.updateTiles(xx,yy,source.master.mHeight[xs+xx][ys+yy]);
            }
            source.sprite.mesh.updateHeightMap = false;
            source.sprite.geometry.verticesNeedUpdate = true;
            
        },
        updateTiles:function(x,y) {
            var ctx = source.sprite.canvas.context;
            var tilenum = 13;
            var yc = ~~(tilenum/3);
            var xc = tilenum-(yc*3);
            ctx.drawImage(source.master.groundImages.edges[0],xc*32,yc*32,32,32,(x*32),(y*32),32,32);
            var hmap = source.master.mHeight;
            x+=~~(source.position.x/32); y+=~~(source.position.y/32);
            if (hmap[x] === void 0) return;
            if (hmap[x-1] === void 0) return;
            if (hmap[x+1] === void 0) return;
            if (hmap[x][y-1] === void 0) return;
            if (hmap[x][y+1] === void 0) return;
            if (hmap[x-1][y-1] === void 0) return;
            if (hmap[x-1][y] === void 0) return;
            if (hmap[x-1][y+1] === void 0) return;
            if (hmap[x+1][y-1] === void 0) return;
            if (hmap[x+1][y] === void 0) return;
            if (hmap[x+1][y+1] === void 0) return;
            var zpos=hmap[x][y];
            Game.worldMaterialSetFloor(x*32,y*32,((hmap[x][y]+6)*32),0);
            Game.worldMaterialSetFloor(x*32,y*32,((hmap[x][y])*32),1);
            if (zpos !== hmap[x-1][y] ||
                zpos !== hmap[x-1][y+1] ||
                zpos !== hmap[x-1][y-1] ||
                zpos !== hmap[x+1][y] ||
                zpos !== hmap[x+1][y+1] ||
                zpos !== hmap[x+1][y-1] ||
                zpos !== hmap[x][y-1] ||
                zpos !== hmap[x][y+1]) {
                    var tilenum = -1;
                    Game.worldMaterialSetFloor(x*32,y*32,(hmap[x][y]+2)*32,1);
                    if (zpos > hmap[x+1][y] && 
                        zpos === hmap[x][y+1] && 
                        zpos > hmap[x+1][y+1]) tilenum = 3;
                    if (zpos < hmap[x+1][y] && 
                        zpos === hmap[x][y+1] && 
                        zpos < hmap[x+1][y+1]) tilenum = 5;
                    if (zpos === hmap[x+1][y] && 
                        zpos < hmap[x][y+1] && 
                        zpos < hmap[x+1][y+1]) tilenum = 7;
                    if (zpos === hmap[x+1][y] && 
                        zpos > hmap[x][y+1] && 
                        zpos > hmap[x+1][y+1]) tilenum = 1;
                    if (zpos === hmap[x+1][y] && 
                        zpos > hmap[x][y+1] && 
                        zpos === hmap[x+1][y+1]) tilenum = 2;
                    if (zpos === hmap[x+1][y] && 
                        zpos < hmap[x][y+1] && 
                        zpos === hmap[x+1][y+1]) tilenum = 9;
                    if (zpos > hmap[x+1][y] && 
                        zpos === hmap[x][y+1] && 
                        zpos === hmap[x+1][y+1]) tilenum = 6;
                    if (zpos < hmap[x+1][y] && 
                        zpos === hmap[x][y+1] && 
                        zpos === hmap[x+1][y+1]) tilenum = 11;
                    if (zpos > hmap[x+1][y] && 
                        zpos > hmap[x][y+1] && 
                        zpos > hmap[x+1][y+1]) tilenum = 0;
                    if (zpos < hmap[x+1][y] && 
                        zpos < hmap[x][y+1] && 
                        zpos < hmap[x+1][y+1]) tilenum = 8;
                    if (zpos === hmap[x+1][y] && 
                        zpos === hmap[x][y+1] && 
                        zpos > hmap[x+1][y+1]) tilenum = 4;
                    if (zpos === hmap[x+1][y] && 
                        zpos === hmap[x][y+1] && 
                        zpos < hmap[x+1][y+1]) tilenum = 12;

                    if (tilenum === -1) return;
                    yc = ~~(tilenum/3);
                    xc = tilenum-(yc*3);
                    x-=~~(source.position.x/32); y-=~~(source.position.y/32);
                    ctx.drawImage(source.master.groundImages.edges[0],xc*32,yc*32,32,32,x*32,y*32,32,32);
                }
            source.sprite.material.map.wrapS = 1; source.sprite.material.map.wrapT = 1;
            source.sprite.material.map.flipY = false; source.sprite.material.map.needsUpdate = true;
            
        },
        
        updateTrees:function(x,y,z) {
            if (x !== ~~(x/2)*2) return;
            if (y !== ~~(y/2)*2) return;
            var xp = ~~(source.position.x/32), yp = ~~(source.position.y/32);
            var xi = x+xp, yi = y+yp;
            if (source.master.mTrees[xi] === void 0) return;
            if (source.master.mTrees[xi][yi] === void 0) return;
            if (source.master.mTrees[xi][yi+1] === void 0) return;
            if (source.master.mTrees[xi+1] === void 0) return;
            if (source.master.mTrees[xi+1][yi] === void 0) return;
            if (source.master.mTrees[xi+1][yi+1] === void 0) return;
            
            if ((source.master.mHeight[xi+1][yi] !== z) ||
                (source.master.mHeight[xi][yi+1] !== z) ||
                (source.master.mHeight[xi+1][yi+1] !== z)) return;
            
            var trees = [];
            trees[0] = [];
            trees[1] = [];
            trees[0][0] = (source.master.mTrees[xi][yi] >= 1);
            trees[1][0] = (source.master.mTrees[xi+1][yi] >= 1);
            trees[0][1] = (source.master.mTrees[xi][yi+1] >= 1);
            trees[1][1] = (source.master.mTrees[xi+1][yi+1] >= 1);
            var grass = [];
            grass[0] = [];
            grass[1] = [];
            grass[0][0] = (source.master.mGrass[xi][yi] >= 1);
            grass[1][0] = (source.master.mGrass[xi+1][yi] >= 1);
            grass[0][1] = (source.master.mGrass[xi][yi+1] >= 1);
            grass[1][1] = (source.master.mGrass[xi+1][yi+1] >= 1);
            
            if (trees[0][0] && trees[1][0] && trees[0][1] && trees[1][1]) {
                source.setTree(xi,yi,z,groundTree);
            } else {
                if (trees[0][0]) source.setTree(xi,yi,z,groundSmallTree);
                else if (grass[0][0]) source.setTree(xi,yi,z,groundGrass);
                if (trees[1][0]) source.setTree(xi+1,yi,z,groundSmallTree);
                else if (grass[1][0]) source.setTree(xi+1,yi,z,groundGrass);
                if (trees[0][1]) source.setTree(xi,yi+1,z,groundSmallTree);
                else if (grass[0][1]) source.setTree(xi,yi+1,z,groundGrass);
                if (trees[1][1]) source.setTree(xi+1,yi+1,z,groundSmallTree);
                else if (grass[1][1]) source.setTree(xi+1,yi+1,z,groundGrass);
            }
        },
        clearTrees:function() {
            while(source.trees.length)
                Game.layerRemove(source.trees.pop());
        },
        setTree:function(x,y,z,tree) {
            source.trees.push(Game.layerAdd(new tree(0,(x*32),(y*32),z*32)));
        }
    };
    return source;
}
function groundMasterMap() {
    var source = {
        mapPieces:[], groundImages:{edges:[],environments:[]},
        Init:function() {},
        Init3d:function() {
            source.groundImages.edges[0] = new Image();
            source.groundImages.edges[0].src = sys.imgdir+'grn/edge0.png';
        },
        Connected:function() { gameNetwork.connection.on('GM', source.mapUpdate); },
        
        //Additional
        mTrees:[],mGrass:[],mRocks:[],mHeight:[],mObjects:[],
        mapGetPieces:function(x,y,w,h) {
            w = ~~(~~((x+w)/32)/50);
            h = ~~(~~((y+h)/32)/50);
            x = ~~(~~(x/32)/50);
            y = ~~(~~(y/32)/50);
            for(var i=x; i<=w; i++) {
                for(var ii=y; ii<=h;ii++) {
                    if (source.mapPieces[i*5] === void 0) source.mapPieces[i*5] = [];
                    if (source.mapPieces[i*5][ii*5] === void 0) {
                        source.mapGet(((i)*50)-1,((ii)*50)-1,52,52);
                        for(var xi=0;xi<5;xi++) 
                            for(var yi=0;yi<5;yi++) {
                                var xx=(i*5)+xi, yy=(ii*5)+yi;
                                if (source.mapPieces[xx] === void 0) source.mapPieces[xx] = [];
                                source.mapPieces[xx][yy] = Game.layerAdd(new groundMapSegment((xx)*10*32,(yy)*10*32));
                                source.mapPieces[xx][yy].master = source;
                            }
                    }
                }
            }
        },
                
        mapGet:function(x,y,w,h) { gameNetwork.connection.emit('GM', { 'x':x, 'y':y, 'w':w, 'h':h }); },
        mapUpdate:function(mapArray) {
            var i = mapArray.Width, ii=0;
            while(i--) {
                ii = mapArray.Height;
                
                if (source.mTrees[i+mapArray.Offset.x] === void 0) 
                    source.mTrees[i+mapArray.Offset.x] = [];
                if (source.mGrass[i+mapArray.Offset.x] === void 0)
                    source.mGrass[i+mapArray.Offset.x] = [];
                if (source.mRocks[i+mapArray.Offset.x] === void 0) 
                    source.mRocks[i+mapArray.Offset.x] = [];
                if (source.mHeight[i+mapArray.Offset.x] === void 0) 
                    source.mHeight[i+mapArray.Offset.x] = [];
                if (source.mObjects[i+mapArray.Offset.x] === void 0) 
                    source.mObjects[i+mapArray.Offset.x] = [];
                while(ii--) {
                    source.mTrees[i+mapArray.Offset.x][ii+mapArray.Offset.y] = mapArray.mTrees[i][ii];
                    source.mGrass[i+mapArray.Offset.x][ii+mapArray.Offset.y] = mapArray.mGrass[i][ii];
                    source.mRocks[i+mapArray.Offset.x][ii+mapArray.Offset.y] = mapArray.mRocks[i][ii];
                    source.mHeight[i+mapArray.Offset.x][ii+mapArray.Offset.y] = mapArray.mHeight[i][ii];
                    source.mObjects[i+mapArray.Offset.x][ii+mapArray.Offset.y] = mapArray.mObjects[i][ii];
                    var x = ~~((i+mapArray.Offset.x)/10); var y = ~~((ii+mapArray.Offset.y)/10);
                    if (source.mapPieces[x] === void 0) source.mapPieces[x] = [];
                    if (source.mapPieces[x][y] === void 0) continue;
                    
                    source.mapPieces[x][y].sprite.mesh.updateHeightMap = true;
                }
            }
        },
                
        ///Functions from others
        playerSnapStep:function() {source.mapGetPieces(gamePlayer.position.x-400,gamePlayer.position.y-400,800,800);}
    };
    return source;
   
}
var gameTerrain = new groundMasterMap();



function groundNPC(xpos,ypos,zpos) {
    var source = {
        position:{x:0,y:0,z:0},
        sprite:{mesh:0,geometry:0,material:0,canvas:0,components:{head:0,hair:0,body:0},emotediv:0},
        flags:{},
        
        Init:function() {
            source.position.x=xpos; source.position.y=ypos; source.position.z=zpos;
            source.position.x=~~(source.position.x/32)*32;source.position.y=~~(source.position.y/32)*32;
            source.sprite.components.head = new Image();
            source.sprite.components.body = new Image();
            source.sprite.components.hair = new Image();
            source.sprite.canvas = $('<canvas></canvas>')[0];
            source.sprite.canvas.width = 256; source.sprite.canvas.height = 256;
            source.sprite.canvas.context = source.sprite.canvas.getContext('2d');
            source.sprite.material = Game.imageAddRaw(source.sprite.canvas);
            source.sprite.emotediv = $('<div class="networkUserEmoteDiv"></div>').appendTo($('#GMW'));
        },
        Init3d:function() {
            source.Init();
            source.sprite.geometry = new THREE.PlaneGeometry(64, 64, 1, 1);
            source.sprite.geometry.applyMatrix(new THREE.Matrix4().makeRotationX(degToRad(45)));
            source.sprite.mesh = new THREE.Mesh(source.sprite.geometry, source.sprite.material);
            Game.GL.scene.add(source.sprite.mesh);
        },
        Render:function() { 
            Game.imageDraw(source.sprite,source.position.x,source.position.y-source.sprite.material.height+32,-source.position.y);
            //Game.worldMaterialSet(source.position.x,source.position.y,source.position.z,1);
        },
        Render3d:function() { 
            source.sprite.mesh.position.set(source.position.x,source.position.y,source.position.z+(64));
            source.sprite.mesh.rotation.z = -degToRad(gameCamera.direction+90); 
            //Game.worldMaterialSet(source.position.x,source.position.y,source.position.z,1);
        },
        Deactivate:function() { Game.GL.scene.remove(source.sprite.mesh); },
        Activate:function() { Game.GL.scene.add(source.sprite.mesh); },
        
        //Additional
        checkEvent:function() {},
        drawChangeHead:function(url, colorize) {
            source.sprite.components.head.onload = source.drawUpdateMySprite;
            source.sprite.components.head.src = sys.spritedir+url+'.png';
            source.sprite.components.head.colorize = colorize;
            return source;
        },
        drawChangeBody:function(url, colorize) {
            source.sprite.components.body.onload = source.drawUpdateMySprite;
            source.sprite.components.body.src = sys.spritedir+url+'.png';
            source.sprite.components.body.colorize = colorize;
            return source;
        },
        drawChangeHair:function(url, colorize) {
            source.sprite.components.hair.onload = source.drawUpdateMySprite;
            source.sprite.components.hair.src = sys.spritedir+url+'.png';
            source.sprite.components.hair.colorize = colorize;
            return source;
        },
        drawUpdateMySprite:function() {
            //create the sprite from combos
            var ctx=source.sprite.canvas.context;
            // Apply color transform.

            if (source.sprite.components.body.complete===true) ctx.drawImage(source.sprite.components.body,0,0);
            if (source.sprite.components.head.complete===true) ctx.drawImage(source.sprite.components.head,0,0);
            if (source.sprite.components.hair.complete===true) ctx.drawImage(source.sprite.components.hair,0,0);
            source.sprite.material.needsUpdate = true;
            if (source.sprite.material.map !== void 0) source.sprite.material.map.needsUpdate = true;
        },
        drawChangeAlpha:function() {
            return source;
        },
        updateEmoteDiv:function(text) {
            source.sprite.emotediv.html(text);
            source.sprite.emotediv.fadeIn(200,function() {$(this).fadeOut(4000);});
            return source;
        },
        
        moveUp:function(spaces) {},
        moveDown:function(spaces) {},
        moveLeft:function(spaces) {},
        moveRight:function(space) {},
        moveToPoint:function(x,y,z) {},
        moveSetSpeed:function(speed) {},
        
        moveSchedule:[], moveScheduleLoop:false,
        moveRecordPoint:function(x,y,z,hour) {
            source.moveSchedule.push([x,y,z,hour]);
            return source;
        },
        moveRecordUp:function(spaces,hour) {
            var i = source.moveSchedule.length;
            var x=xpos,y=ypos-(spaces*32),z=zpos;
            if (i > 0) {
                x=source.moveSchedule[i][0];
                y=source.moveSchedule[i][1]-(spaces*32);
                z=source.moveSchedule[i][2];
                if (hour === void 0) hour = source.moveSchedule[i][3]+(spaces);
            }
            if (hour === void 0) hour = (spaces);
            
            source.moveSchedule.push([x,y,z,hour]);
            return source;
        },
        moveRecordDown:function(spaces,hour) {
            var i = source.moveSchedule.length;
            var x=xpos,y=ypos+(spaces*32),z=zpos;
            if (i > 0) {
                x=source.moveSchedule[i][0];
                y=source.moveSchedule[i][1]+(spaces*32);
                z=source.moveSchedule[i][2];
                if (hour === void 0) hour = source.moveSchedule[i][3]+(spaces);
            }
            if (hour === void 0) hour = (spaces);
            source.moveSchedule.push([x,y,z,hour]);
            return source;
        },
        moveRecordLeft:function(spaces,hour) {
            var i = source.moveSchedule.length;
            var x=xpos-(spaces*32),y=ypos,z=zpos;
            if (i > 0) {
                x=source.moveSchedule[i][0]-(spaces*32);
                y=source.moveSchedule[i][1];
                z=source.moveSchedule[i][2];
                if (hour === void 0) hour = source.moveSchedule[i][3]+(spaces);
            }
            if (hour === void 0) hour = (spaces);
            source.moveSchedule.push([x,y,z,hour]);
            return source;
        },
        moveRecordRight:function(spaces,hour) {
            var i = source.moveSchedule.length;
            var x=xpos,y=ypos+(spaces*32),z=zpos;
            if (i > 0) {
                x=source.moveSchedule[i][0]+(spaces*32);
                y=source.moveSchedule[i][1];
                z=source.moveSchedule[i][2];
                if (hour === void 0) hour = source.moveSchedule[i][3]+(spaces);
            }
            if (hour === void 0) hour = (spaces);
            source.moveSchedule.push([x,y,z,hour]);
            return source;
        },
        moveRecordDisappear:function(hour) {
            moveSchedule.push([-1,-1,-1,hour]);
            return source;
        },
        moveRecordArray:function(array) {
            source.moveSchedule = array;
            return source;
        },
        moveRecordGetPos:function(hour) {
            var movePrevious,moveNext;
            var i = source.moveSchedule.length;
            if (source.moveScheduleLoop) hour %= source.moveSchedule[i][3];
            while(i--)
                if (source.moveSchedule[i][3] > hour)
                    moveNext = source.moveSchedule[i].slice(0);
                else {
                    movePrevious = source.moveSchedule[i].slice(0);
                    if (moveNext === void 0) moveNext = movePrevious;
                    break;
                }
            if (moveNext[0] === -1) return moveNext;
            if (movePrevious[0] === -1) return movePrevious;
            for(i=0;i<3;i++) movePrevious[i] += (movePrevious[i]-moveNext[i])*((hour-movePrevious[3])/(moveNext[3]-movePrevious[3]));
            return movePrevious;
        }
    };
    return source;
}



function envBuilding() {
    var source = {
        position:{x:0,y:0,z:0},
        sprite:{canvas:0,material:[],geometry:[],mesh:[],group:0},
        //2d
        Init:function() {
            source.position.x = gamePlayer.position.x;
            source.position.y = gamePlayer.position.y;
            source.position.z = 0;
        },
        Render:function() {},
        //3d
        Init3d:function() {
            source.Init();
            source.sprite.group = new THREE.Object3D();
            
            source.GeoAddWall(Game.imageAdd('grass.png'),64,128,0,0,0);
            source.GeoAddWall(Game.imageAdd('grass.png'),100,128,90,64,0);
            source.GeoAddWall(Game.imageAdd('grass.png'),64,128,180,64,100);
            source.GeoAddWall(Game.imageAdd('grass.png'),100,128,270,0,100);
            
            Game.worldMaterialSet(source.position.x,source.position.y,source.position.z,1);
            Game.worldMaterialSet(source.position.x+32,source.position.y,source.position.z,1);
            Game.worldMaterialSet(source.position.x+32,source.position.y+32,source.position.z,1);
            Game.worldMaterialSet(source.position.x,source.position.y+32,source.position.z,1);
            source.sprite.group.position.set(source.position.x,source.position.y,source.position.z);
            Game.GL.scene.add(source.sprite.group);
        },
        Render3d:function() {},
        Deactivate:function() {
            Game.GL.scene.remove(source.sprite.group);
        },
        Activate:function() {
            Game.GL.scene.add(source.sprite.group);
        },
        //Optional
        GeoAddWall:function(material,width,height,direction,x,y) {
            ///create a wall
            source.sprite.material.push(material);
            x -= 16; y -= 16;
            var index = source.sprite.geometry.push(new THREE.PlaneGeometry(width, height))-1;
            source.sprite.geometry[index].applyMatrix(new THREE.Matrix4().makeTranslation(width/2,-height/2,0));
            source.sprite.geometry[index].applyMatrix(new THREE.Matrix4().makeRotationX(degToRad(90)));
            source.sprite.geometry[index].applyMatrix(new THREE.Matrix4().makeRotationZ(degToRad(direction)));
            source.sprite.geometry[index].applyMatrix(new THREE.Matrix4().makeTranslation(x,y,height));
            index = source.sprite.mesh.push(new THREE.Mesh(source.sprite.geometry[index],material))-1;
            source.sprite.group.add(source.sprite.mesh[index]);
        },
        GeoAddRoof:function(cx,cy,cz) {
            var index = source.sprite.geometry.push(new THREE.PlaneGeometry(width, height))-1;
            source.sprite.geometry[index].applyMatrix(new THREE.Matrix4().makeTranslation(width/2,-height/2,0));
            source.sprite.geometry[index].applyMatrix(new THREE.Matrix4().makeRotationX(degToRad(90)));
            source.sprite.geometry[index].applyMatrix(new THREE.Matrix4().makeRotationZ(degToRad(direction)));
            source.sprite.geometry[index].applyMatrix(new THREE.Matrix4().makeTranslation(x,y,height));
            index = source.sprite.mesh.push(new THREE.Mesh(source.sprite.geometry[index],material))-1;
            source.sprite.group.add(source.sprite.mesh[index]);
        }
    };
    return source;
}




function NPC() {
    var source = {
        x:0,y:0,z:0,sprite:0,frame:0,direction:0,
        Init:function() {
        },
        Init3d:function() {
            source.x = gamePlayer.x;
            source.y = gamePlayer.y;
            source.x = ~~(source.x/32)*32;
            source.y = ~~(source.y/32)*32;
            source.sprite = Game.imageAdd('fennekin2.png');
            source.Geometry = new THREE.PlaneGeometry(64, 64, 1, 1);
            source.Geometry.applyMatrix(new THREE.Matrix4().makeRotationX(degToRad(45)));
            source.Mesh = new THREE.Mesh(source.Geometry, source.sprite);
            source.Mesh.position.set(source.x+16,source.y+16,source.z+(88/2));
            Game.GL.scene.add(source.Mesh);
        },
        Step:function() {},
        Step3d:function() {
            source.frame+=0.1;
            source.frame%=4;
        },
        Render:function() {
            Game.imageDraw(source.sprite,source.x,source.y-source.sprite.height+32,-source.y);
        },
        Render3d:function() {
            if (source.Mesh.geometry.faceVertexUvs === void 0) return;
            dir = degToRad(gameCamera.direction+90);
            source.Mesh.rotation.z = -dir;
            source.Mesh.position.set(source.x,source.y,source.z+(88/2));
            
            var r = ~~((source.direction)+(gameCamera.direction))%360;
            var cropy = (~~(r/90)*64)+64;
            var cropx = ~~source.frame*64;
            if (~~source.frame === 3) cropx = 64;
            cropx /= source.sprite.map.image.width;
            cropy /= source.sprite.map.image.height;
            var cropw = 64/source.sprite.map.image.width;
            var croph = 64/source.sprite.map.image.height;
            source.Mesh.geometry.faceVertexUvs[0][0] = [new THREE.Vector2( cropx,cropy ),
                                                        new THREE.Vector2( cropx,cropy-croph ),
                                                        new THREE.Vector2( cropx+cropw, cropy-croph ),
                                                        new THREE.Vector2( cropx+cropw, cropy )];
            source.Mesh.geometry.uvsNeedUpdate = true;
        },
        Deactivate:function() { Game.GL.scene.remove(source.Mesh); },
        Activate:function() { Game.GL.scene.add(source.Mesh); }
    };
    return source;
}

function timeObj() {
    var source = {
        Step:function() {
            //set overlay color based on time of day in game
        },
        Step3d:function() {
            //set ambiant light color based on time of day.
        },
        getTime:function() {
            //return in game time;
        }
    };
    return source;
}
gameTime = new timeObj();

function groundObj() {
    var source = {
        ///create a bunch of mesh for each part of the map, activate and deactivate based on player location.
    };
    return source;
}
gameTime = new timeObj();