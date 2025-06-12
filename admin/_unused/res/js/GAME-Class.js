/**
 * 
 * @returns {undefined}
 */
(function() {
    var requestAnimationFrame = window.requestAnimationFrame || window.mozRequestAnimationFrame ||
            window.webkitRequestAnimationFrame || window.msRequestAnimationFrame;
    window.requestAnimationFrame = requestAnimationFrame;
})();
function GAMECLASS() {
    var source = {
        Objects: {
            Active: [],
            Inactive: [],
            Key: {},
            Add: function(objRef, keyName) {
                source.Objects.Active.push(objRef);
                objRef.SYSTEM = {};
                if (keyName !== void 0) {
                    objRef.SYSTEM[keyName] = keyName;
                    source.Objects.Key[keyName] = objRef;
                }
                if (source.Draw.GL.Enabled) {
                    if (objRef.InitGL) {
                        objRef.InitGL();
                    } else {
                        if (objRef.Init !== void 0)
                            objRef.Init();
                    }
                } else {
                    if (objRef.Init !== void 0)
                        objRef.Init();
                }
                return objRef;
            },
            Remove: function(objRef) {
                var i = 0;
                while (i < source.Objects.Active.length || i < source.Objects.Inactive.length) {
                    if (source.Objects.Active[i] !== void 0)
                        if (source.Objects.Active[i] === objRef) {
                            if (source.Objects.Active[i].Destroy !== void 0)
                                source.Objects.Active[i].Destroy();
                            if (source.Objects.Active[i].SYSTEM.keyName !== void 0)
                                source.Objects.Key[source.Objects.Active[i].keyName] = void 0;
                            if (source.Objects.Active[i].SYSTEM.viewObject)
                                source.Draw.View.FollowObject(void 0);
                            source.Objects.Active.splice(i, 1);
                            return;
                        }
                    if (source.Objects.Inactive[i] !== void 0)
                        if (source.Objects.Inactive[i] === objRef) {
                            if (source.Objects.Inactive[i].Destroy !== void 0)
                                source.Objects.Inactive[i].Destroy();
                            if (source.Objects.Inactive[i].SYSTEM.keyName !== void 0)
                                source.Objects.Key[source.Objects.Inactive[i].keyName] = void 0;
                            if (source.Objects.Inactive[i].SYSTEM.viewObject)
                                source.Draw.View.FollowObject(void 0);
                            source.Objects.Inactive.splice(i, 1);
                            return;
                        }
                    i++;
                }
            },
            Activate: {
                Object: function(objectIndex) {
                    source.Objects.Active.push(source.Objects.Inactive.splice(objectIndex, 1));
                },
                Within: function(x, y, width, height) {
                    var i = 0;
                    while (i < source.Objects.Inactive.length) {
                        if (source.Objects.Inactive[i] !== void 0)
                            if (source.Objects.Inactive[i].Position !== void 0) {
                                var pos = source.Objects.Inactive[i].Position;
                                if (pos.x + pos.width > x)
                                    if (pos.y + pos.height > y)
                                        if (pos.x < x + width)
                                            if (pos.y < y + height) {
                                                source.Objects.Activate.Object(i);
                                                continue;
                                            }
                            }
                        i++;
                    }
                },
                Outside: function(x, y, width, height) {
                    var i = 0;
                    while (i < source.Objects.Inactive.length) {
                        if (source.Objects.Inactive[i] !== void 0)
                            if (source.Objects.Inactive[i].Position !== void 0) {
                                var pos = source.Objects.Inactive[i].Position;
                                if (pos.x + pos.width < x)
                                    if (pos.y + pos.height < y)
                                        if (pos.x > x + width)
                                            if (pos.y > y + height) {
                                                source.Objects.Activate.Object(i);
                                                continue;
                                            }
                            }
                        i++;
                    }
                },
                OnlyWithin: function(x, y, width, height) {
                    var i = 0;
                    while (i < source.Objects.Inactive.length || i < source.Objects.Active.length) {
                        var fnd = false;
                        if (source.Objects.Inactive[i] !== void 0)
                            if (source.Objects.Inactive[i].Position !== void 0) {
                                var pos = source.Objects.Inactive[i].Position;
                                if (pos.x + pos.width > x)
                                    if (pos.y + pos.height > y)
                                        if (pos.x < x + width)
                                            if (pos.y < y + height) {
                                                source.Objects.Activate.Object(i);
                                                fnd = true;
                                            }
                            }
                        if (source.Objects.Active[i] !== void 0)
                            if (source.Objects.Active[i].Position !== void 0) {
                                var pos = source.Objects.Active[i].Position;
                                if (pos.x + pos.width < x)
                                    if (pos.y + pos.height < y)
                                        if (pos.x > x + width)
                                            if (pos.y > y + height) {
                                                source.Objects.Dectivate.Object(i);
                                                fnd = true;
                                            }
                            }
                        if (!fnd)
                            i++;
                    }
                },
                OnlyOutside: function(x, y, width, height) {
                    var i = 0;
                    while (i < source.Objects.Inactive.length || i < source.Objects.Active.length) {
                        var fnd = false;
                        if (source.Objects.Inactive[i] !== void 0)
                            if (source.Objects.Inactive[i].Position !== void 0) {
                                var pos = source.Objects.Inactive[i].Position;
                                if (pos.x + pos.width < x)
                                    if (pos.y + pos.height < y)
                                        if (pos.x > x + width)
                                            if (pos.y > y + height) {
                                                source.Objects.Activate.Object(i);
                                                fnd = true;
                                            }
                            }
                        if (source.Objects.Active[i] !== void 0)
                            if (source.Objects.Active[i].Position !== void 0) {
                                var pos = source.Objects.Active[i].Position;
                                if (pos.x + pos.width > x)
                                    if (pos.y + pos.height > y)
                                        if (pos.x < x + width)
                                            if (pos.y < y + height) {
                                                source.Objects.Dectivate.Object(i);
                                                fnd = true;
                                            }
                            }
                        if (!fnd)
                            i++;
                    }
                }
            },
            Deactivate: {
                Object: function(objectIndex) {
                    source.Objects.Inactive.push(source.Objects.Active.splice(objectIndex, 1));
                },
                Within: function(x, y, width, height) {
                    var i = 0;
                    while (i < source.Objects.Active.length) {
                        if (source.Objects.Active[i] !== void 0)
                            if (source.Objects.Active[i].Position !== void 0) {
                                var pos = source.Objects.Active[i].Position;
                                if (pos.x + pos.width > x)
                                    if (pos.y + pos.height > y)
                                        if (pos.x < x + width)
                                            if (pos.y < y + height) {
                                                source.Objects.Deactivate.Object(i);
                                                continue;
                                            }
                            }
                        i++;
                    }
                },
                Outside: function(x, y, width, height) {
                    var i = 0;
                    while (i < source.Objects.Active.length) {
                        if (source.Objects.Active[i] !== void 0)
                            if (source.Objects.Active[i].Position !== void 0) {
                                var pos = source.Objects.Active[i].Position;
                                if (pos.x + pos.width < x)
                                    if (pos.y + pos.height < y)
                                        if (pos.x > x + width)
                                            if (pos.y > y + height) {
                                                source.Objects.Deactivate.Object(i);
                                                continue;
                                            }
                            }
                        i++;
                    }
                },
                OnlyWithin: function(x, y, width, height) {
                    var i = 0;
                    while (i < source.Objects.Inactive.length || i < source.Objects.Active.length) {
                        var fnd = false;
                        if (source.Objects.Inactive[i] !== void 0)
                            if (source.Objects.Inactive[i].Position !== void 0) {
                                var pos = source.Objects.Inactive[i].Position;
                                if (pos.x + pos.width < x)
                                    if (pos.y + pos.height < y)
                                        if (pos.x > x + width)
                                            if (pos.y > y + height) {
                                                source.Objects.Activate.Object(i);
                                                fnd = true;
                                            }
                            }
                        if (source.Objects.Active[i] !== void 0)
                            if (source.Objects.Active[i].Position !== void 0) {
                                var pos = source.Objects.Active[i].Position;
                                if (pos.x + pos.width > x)
                                    if (pos.y + pos.height > y)
                                        if (pos.x < x + width)
                                            if (pos.y < y + height) {
                                                source.Objects.Dectivate.Object(i);
                                                fnd = true;
                                            }
                            }
                        if (!fnd)
                            i++;
                    }
                },
                OnlyOutside: function(x, y, width, height) {
                    var i = 0;
                    while (i < source.Objects.Inactive.length || i < source.Objects.Active.length) {
                        var fnd = false;
                        if (source.Objects.Inactive[i] !== void 0)
                            if (source.Objects.Inactive[i].Position !== void 0) {
                                var pos = source.Objects.Inactive[i].Position;
                                if (pos.x + pos.width > x)
                                    if (pos.y + pos.height > y)
                                        if (pos.x < x + width)
                                            if (pos.y < y + height) {
                                                source.Objects.Activate.Object(i);
                                                fnd = true;
                                            }
                            }
                        if (source.Objects.Active[i] !== void 0)
                            if (source.Objects.Active[i].Position !== void 0) {
                                var pos = source.Objects.Active[i].Position;
                                if (pos.x + pos.width < x)
                                    if (pos.y + pos.height < y)
                                        if (pos.x > x + width)
                                            if (pos.y > y + height) {
                                                source.Objects.Dectivate.Object(i);
                                                fnd = true;
                                            }
                            }
                        if (!fnd)
                            i++;
                    }
                }
            },
            Get: {
                ByKey: function(keyName) {
                    if (source.Objects.Key[keyName] === void 0)
                        return null;
                    return source.Objects.Key[keyName];
                },
                ByPosition: function(x, y) {
                    var i = 0;
                    while (i < source.Objects.Active.length) {
                        if (source.Objects.Active[i] !== void 0)
                            if (source.Objects.Active[i].Position !== void 0) {
                                var pos = source.Objects.Active[i].Position;
                                if (pos.x + pos.width > x && pos.y + pos.height > y)
                                    if (pos.x < x && pos.y < y)
                                        return source.Objects.Active[i];

                            }
                        i++;
                    }
                }
            },
            Execute: {
                Step: function() {
                    var i = source.Objects.Active.length;
                    while (i--) {
                        if (source.Objects.Active[i].Step !== void 0)
                            source.Objects.Active[i].Step();
                        if (source.Objects.Active[i].Position !== void 0)
                            if (source.Objects.Active[i].Position.Solid === true)
                                source.Collision.Add(source.Objects.Active[i]);
                    }
                },
                Draw: function() {
                    var i = source.Objects.Active.length;
                    while (i--) {
                        if (source.Objects.Active[i].Draw !== void 0)
                            source.Objects.Active[i].Draw();
                    }
                },
                DrawGL: function() {
                    var i = source.Objects.Active.length;
                    while (i--) {
                        if (source.Objects.Active[i].DrawGL !== void 0)
                            source.Objects.Active[i].DrawGL();
                    }
                }
            }
        },
        Images: {
            Data:[],
            Add: function(imagepath) {
                var img = new Image();
                img.src = imagepath;
                img.onload(function() {});
                Data.push(img);
            }
        },
        Draw: {
            Data: [],
            Canvas: void 0, Context: void 0,
            GL: {
                Enabled: false,
                Add: function(sprite) {
                    source.Draw.GL.Scene.add(sprite);
                },
                Remove: function() {
                    source.Draw.GL.Scene.remove(sprite);
                },
                NewSprite: function(imagepath, width, height, offsetx, offsety) {
                    var geometry = new THREE.PlaneGeometry(width, height, 1, 1);
                    geometry.applyMatrix(new THREE.Matrix4().makeRotationX(degToRad(45)));
                    geometry.applyMatrix(new THREE.Matrix4().makeTranslation(offsetx, offsety, 0));
                    return new THREE.Mesh(source.sprite.geometry, source.Images.Add(imagepath));
                    ;
                },
                Render: function() {
                    source.Draw.GL.Camera.position = new THREE.Vector3(source.Draw.View.Data.x,
                            source.Draw.View.Data.y,
                            source.Draw.View.Data.z);
                    if (source.Draw.View.Data.object !== void 0)
                        source.Draw.GL.Camera.lookAt(new THREE.Vector3(source.Draw.View.Data.object.Position.x,
                                source.Draw.View.Data.object.Position.y,
                                source.Draw.View.Data.object.Position.z));
                    source.Draw.GL.Renderer.render(source.Draw.GL.Scene, source.Draw.GL.Camera);
                },
                Resize:function() {
                    source.Draw.GL.Renderer.setSize(~~source.Draw.Canvas.width(),
                                                    ~~source.Draw.Canvas.height());
                    source.Draw.GL.Camera = new THREE.PerspectiveCamera( 35,source.Draw.Canvas.width()/source.Draw.Canvas.height(), 0.1,1000);
                    source.Draw.GL.Camera.rotation.order = 'XZY';
                    source.Draw.GL.Camera.up = new THREE.Vector3( 0, 0, 1 );
                }
            },
            Render: function() {
                source.Draw.View.Update();
                if (source.Draw.GL.Enabled)
                    return source.Draw.GL.Render();
                var i = source.Draw.Data.length;
                while (i--) {
                    ///
                }
                source.Draw.Data = [];
            },
            Image: function() {
            },
            Rectangle: function() {
            },
            View: {
                Data: {
                    x: 0, y: 0, z: 0,
                    width:0, height:0,
                    object: void 0
                },
                Update: function() {
                    if (source.Draw.View.Data.object === void 0)
                        return;

                    var too = source.Draw.View.Data.object.Position;
                    var from = source.Draw.View.Data;

                    source.Draw.View.Data.x = ~~(from.x + ((too.x - from.x) / 2));
                    source.Draw.View.Data.y = ~~(from.y + ((too.y - from.y) / 2));
                    source.Draw.View.Data.z = ~~(from.z + ((too.z - from.z) / 2));
                },
                FollowObject: function(objRef) {
                    if (source.Draw.View.Data.object !== void 0)
                        source.Draw.View.Data.object.SYSTEM.viewObject = false;
                    source.Draw.View.Data.object = void 0;
                    if (objRef === void 0)
                        return;
                    if (objRef.Position === void 0)
                        return;
                    source.Draw.View.Data.object = objRef;
                    objRef.SYSTEM.viewObject = true;
                }

            },
            Set: {
                Canvas: function(canvasid) {
                    source.Draw.Canvas = $('#' + canvasid);
                    try {
                        source.Draw.GL.Renderer = new THREE.WebGLRenderer();
                        source.Draw.GL.Renderer.setClearColor(0x000000, 1);
                        source.Draw.GL.Renderer.setSize(source.Draw.Canvas.width(),
                                source.Draw.Canvas.height());

                        source.Draw.GL.Scene = new THREE.Scene();
                        source.Draw.GL.Camera = new THREE.PerspectiveCamera(35,
                                source.Draw.Canvas.width() / source.Draw.Canvas.height(),
                                0.1, 1000);
                        source.Draw.GL.Camera.rotation.order = 'XZY';
                        source.Draw.GL.Camera.up = new THREE.Vector3(0, 0, 1);
                        source.Draw.GL.Light = new THREE.AmbientLight(0xFFFFFF);
                        source.Draw.GL.Scene.add(source.Draw.GL.Light);
                        var newcanvas = $(source.Draw.GL.Renderer.domElement);
                        source.Draw.Canvas.replaceWith(newcanvas);
                        source.Draw.Canvas = newcanvas;
                        source.Draw.Canvas.addClass('ContentRpgCanvas');
                        source.Draw.GL.Enabled = true;
                    } catch (e) {
                        source.Draw.Context = source.Draw.Canvas[0].getContext("2d");
                        source.System.Debug.Warning('WebGl is not supported. ' + e);
                    }
                    source.Draw.Set.Resize();
                    $(window).resize(source.Draw.Set.Resize);
                    source.System.UnPause('NoCanvasSelected');
                },
                Resize:function() {
                    if (source.Draw.GL.Enabled)
                        return source.Draw.GL.Resize();
                    source.Draw.Canvas.attr('width',source.Draw.Canvas.width());
                    source.Draw.Canvas.attr('height',source.Draw.Canvas.height());
                    source.Draw.View.Data.width = source.Draw.Canvas.width();
                    source.Draw.View.Data.height = source.Draw.Canvas.height();
                }
                
            }
        },
        Collision: {
            Data: [], NewData: [],
            Add: function(objRef) {
                var pos = objRef.Position;
                var x = ~~(pos.x / 16), y = ~~(pos.y / 16), z = ~~(pos.z / 16),
                        w = ~~(pos.width / 16), h = ~~(pos.height / 16);
                while (w--)
                    while (h--) {
                        if (source.Collision.NewData[x + w] === void 0)
                            source.Collision.NewData[x + w] = [];
                        if (source.Collision.NewData[x + w][y + h] === void 0)
                            source.Collision.NewData[x + w][y + h] = [];
                        source.Collision.NewData[x + w][y + h][z] = objRef;
                    }
            },
            Get: function(x, y, z) {
                x = ~~(x / 16);
                y = ~~(y / 16);
                z = ~~(z / 16);
                if (source.Collision.Data[x] === void 0)
                    return void 0;
                if (source.Collision.Data[x][y] === void 0)
                    return void 0;
                if (source.Collision.Data[x][y][z] === void 0)
                    return void 0;
                return source.Collision.Data[x][y][z];
            },
            Reset: function() {
                source.Collision.Data = source.Collision.NewData;
                source.Collision.NewData = [];
            }
        },
        Map: {
            Data: {
                Trees: [],
                Objects: [],
                Grass: [],
                Wall: []
            },
            Update: function(maparray, offsetx, offsety) {
            },
            Get: function(x, y, w, h) {
            }
        },
        System: {
            endGame: false,
            pausedGame: {length: 0},
            Start: function() {
                source.System.Loop.Next = (new Date).getTime();
                source.System.Execute.EachFrame();
                source.System.Start = void 0;
            },
            End: function() {
                source.System.endGame = true;
            },
            Pause: function(reason) {
                if (source.System.pausedGame[reason] !== void 0)
                    return;
                source.System.pausedGame[reason] = true;
                source.System.pausedGame.length += 1;
            },
            UnPause: function(reason) {
                if (source.System.pausedGame[reason] === void 0)
                    return;
                source.System.pausedGame[reason] = void 0;
                source.System.pausedGame.length -= 1;
            },
            Loop: {
                Next: 0,
                Number: 0,
                Skip: 1000 / 60
            },
            Debug: {
                Init: function(elementId) {
                    if (Stats === void 0)
                        return;
                    source.System.Debug.Stats = new Stats();
                    source.System.Debug.Stats.setMode(0);
                    source.System.Debug.Stats.domElement.style.position = 'absolute';
                    source.System.Debug.Stats.domElement.style.left = '0px';
                    source.System.Debug.Stats.domElement.style.top = '0px';
                    $('#' + elementId).append(source.System.Debug.Stats.domElement);
                },
                Warning: function(text) {
                    if (console !== void 0)
                        console.log('WARNING: ' + text);
                }
            },
            Execute: {
                EachFrame: function() {
                    if (source.System.endGame) {
                        $(window).resize(function() {});
                        delete GAME;
                        return;
                    }
                    if (source.System.pausedGame.length > 0) {
                        source.System.Loop.Next = (new Date).getTime();
                        window.requestAnimationFrame(source.System.Execute.EachFrame);
                        return;
                    }
                    source.System.Loop.Number = 0;
                    while ((new Date).getTime() > source.System.Loop.Next) {
                        source.System.Loop.Next += source.System.Loop.Skip;
                        source.System.Loop.Number++;
                        source.System.Execute.Step();
                    }
                    if (source.System.Loop.Number) {
                        source.System.Execute.Draw();
                        if (source.System.Debug.Stats !== void 0)
                            source.System.Debug.Stats.Update();
                    }
                    window.requestAnimationFrame(source.System.Execute.EachFrame);
                },
                Step: function() {
                    source.Objects.Execute.Step();
                    source.Collision.Reset();
                },
                Draw: function() {
                    if (source.Draw.Canvas === void 0) {
                        source.System.Debug.Warning('No Canvas Selected ADVICE: Use GAMECLASS.Draw.Set.Canvas(elementid) to select a canvas.');
                        source.System.Pause('NoCanvasSelected');
                        return;
                    }
                    if (source.Draw.GL.Enabled)
                        source.Objects.Execute.DrawGL();
                    else
                        source.Objects.Execute.Draw();
                    source.Draw.Render();
                }
            }
        }
    };
    return source;
}

function GAMEOBJECT() {
    var source = {
        Position: {
            x: 0, y: 0, z: 0, width: 0, height: 0, depth: 0, solid: false
        }
    };
    return source;
}
GAME = void 0;