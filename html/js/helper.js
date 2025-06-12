/* 
 * Copyright (c) 2014 User.
 * All rights reserved. This program and the accompanying materials
 * are made available under the terms of the Eclipse Public License v1.0
 * which accompanies this distribution, and is available at
 * http://www.eclipse.org/legal/epl-v10.html
 *
 * Contributors:
 *    User - initial API and implementation and/or initial documentation
 */

String.prototype.toTitleCase = function() {
    return this.replace(/\w\S*/g, function(txt) {
        return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();
    });
};
String.prototype.toColor = function() {
   for (var i = 0, hash = 0; i < this.length; hash = this.charCodeAt(i++) + ((hash << 5) - hash));
    color = Math.floor(Math.abs((Math.sin(hash) * 10000) % 1 * 16777216)).toString(16);
    return '#' + Array(6 - color.length + 1).join('0') + color;
};
String.prototype.pad = function(width, z) {
  z = z || '0';
  return this.length >= width ? this : new Array(width - this.length + 1).join(z) + this;
};

NotifyUserArray = [];
function NotifyUser(title,content) {
    if (title !== void 0 && content !== void 0) {
        NotifyUserArray.push([title,content]);
    }
    if ($('#N').hasClass('show'))
        return;
    var notify = NotifyUserArray.shift();
    
    $('#N>header').html(notify[0]);
    $('#N>article').html(notify[1]);
    $('#N').addClass('show');
    
    setTimeout(function() {
        $('#N').removeClass('show');
        if (NotifyUserArray.length > 0)
            setTimeout(NotifyUser,1000);
    },5000);
};

inlineUrl = '';
function inlineLoad(callback,name,args) {
    var data = [];
    for(var i=2;i<arguments.length;i++) {
        data.push(arguments[i]);
    }
    $.ajax({
        type: "GET",
        url:'/inline/'+name+'.php?',
        data:{'arguments':data},
        success: callback,
        error:function() {
            $('#inlinePopupContainer').fadeIn(500);
            $('#inlinePopup').html("<h1>I AM ERROR</h1>I'm sorry. There was a problem with contacting the server...").fadeIn(500);
            callback();
        }
    });
}
function inlineSubmit(callback,formobject,name) {
    if (name !== void 0) inlineUrl = name;
    if (formobject.serialize !== void 0) formobject = formobject.serialize();
    $.ajax({
        type: "POST",
        url:'/inline/'+inlineUrl+'.php?',
        data:formobject,
        success: callback,
        error:function() {
            $('#inlinePopupContainer').fadeIn(500);
            $('#inlinePopup').html("<h1>I AM ERROR</h1>I'm sorry. There was a problem with contacting the server...").fadeIn(500);
        }
    });
};
function inlinePopup(name,args) {
    inlineLoad(function(data) {
        $('#inlinePopupContainer').fadeIn(500);
        $('#inlinePopup').html(data).fadeIn(500);
    },name,args);
}
function inlinePopupClose() {
    $('#inlinePopupContainer').fadeOut(200);
    $('#inlinePopup').fadeOut(200);
    game.sound.play('bgm');
    game.sound.stop('sfx/m/evolving.mp3');
}
function inlinePopupSubmit(form,url) {
    if (url !== void 0) inlineUrl = url;
    inlineSubmit(function(data) {
        $('#inlinePopupContainer').fadeIn(500);
        $('#inlinePopup').html(data).fadeIn(500);
    },form);
}

function Like(catagory,item,event) {
    $(this).attr('onclick','');
    inlineLoad(function(data){ 
        $(this).attr('onclick','return UnLike.call(this,"'+catagory+'",'+item+',event);').addClass('selected');
        var child = $(this).children('span');
        if (child.length > 0) child.html(parseInt(child.html())+1);
        console.log(data);
    }.bind(this),'like',catagory,item); 
    event.stopPropagation(); return false;
}
function UnLike(catagory,item,event) {
    $(this).attr('onclick','');
    inlineLoad(function(data){ 
        $(this).attr('onclick','return Like.call(this,"'+catagory+'",'+item+',event);').removeClass('selected');
        var child = $(this).children('span');
        if (child.length > 0) child.html(parseInt(child.html())-1);
    }.bind(this),'unlike',catagory,item); 
    event.stopPropagation(); return false;
}
$(document).ready(function() {
    $('.fav_icon:not(.selected)').each(function(index,element) {
        var catagory = $(element).data('catagory');
        var item = $(element).data('item');
        $(element).attr('onclick','return Like.call(this,"'+catagory+'",'+item+',event);');
    });
    $('.fav_icon.selected').each(function(index,element) {
        var catagory = $(element).data('catagory');
        var item = $(element).data('item');
        $(element).attr('onclick','return UnLike.call(this,"'+catagory+'",'+item+',event);');
    });
    
});

gameUserJoin = void 0;
gameUserUpdate = void 0;
gameUserLeave = void 0;

function OnlineUserJoined(data) {
    var color = data.color;
    console.log(color);
    if (color === void 0 || color === '' || color === null || color.indexOf('#') !== 0 || !(color.length === 7 || color.length === 4)) color = data.username.toColor();
    $('#onlineNowList').append('<a href="http://PokeWorlds.com/user.php?id='+data.username+'" style="background-color:'+color+';" data-user="'+data.username+'" >'+data.username.toTitleCase()+'</a>');
    if (gameUserJoin !== void 0) gameUserJoin(data);
};
function OnlineUserLeft(data) {
    $('#onlineNowList>a[data-user="'+data.username+'"]').remove();
    if (gameUserLeave !== void 0) gameUserLeave(data);
};
function OnlineUserUpdate(data) {
    if (gameUserUpdate !== void 0) gameUserUpdate(data);
};

// Chat
function OnlineSendChat(event) { 
    if (event.keyCode === 13) {
        var text = $('#C>input').val();
        var room = '';
        if (text === '') {
            setTimeout(function() {$('#C>input').blur()},10);;
        } else {
            game.network.Send('C',{text:text,room:room}); 
            $('#C>input').val('');
        }
    }
};
function OnlineJoinRoom(room) { game.network.Send('CJ',{room:room}); };
function OnlineLeaveRoom(room) { game.network.Send('CU',{room:room}); };
function OnlineRecieveChat(data,silent) {
    if (data.objects !== void 0) {
        for(var i in data.objects) {
            OnlineRecieveChat(data.objects[i],true);
        }
    } else {
        if (!silent) {
            game.sound.play('sfx/notify-chat.ogg');
        }
        var color = '';
        if (data.color === void 0){
            if (game.network.players._username[data.username] !== void 0) color = game.network.players._username[data.username].color;
        } else color = data.color;
        if (color === void 0 || color === '' || 
            color === null || color.indexOf('#') !== 0 || 
            !(color.length === 7 || color.length === 4)) color = data.username.toColor();
    
        if (data.room.indexOf('@') !== -1) {
            data.room = data.room.replace('@','-');
        }
        var lastelementname = $('#chat-'+data.room+' li:last-child a').html();
        
        if (lastelementname !== void 0 && lastelementname.toLowerCase() === data.username.toLowerCase()) {
            $('#chat-'+data.room+' li:last-child').append('<br/>'+data.text);
        } else {
            var element =  $('<li style="border-color:'+color+';"><a href="http://PokeWorlds.com/user.php?id='+data.username+'" style="background-color:'+color+';">'+data.username.toTitleCase()+'</a>'+data.text+'</li>');
            if ($('#chat-'+data.room).length === 0) {
                $('#C').append('<ul style="display:none;" id="chat-'+data.room+'"></ul>');
                $('#rooms').addClass('alert').append('<option value="chat-'+data.room+'">'+data.room+'</option>');
            }
            element.appendTo($('#chat-'+data.room));
            setTimeout(function() {$(this).addClass('showing');}.bind(element),100);
        }
       
    }
};

function LoadNewsTicker() {
    inlineLoad(function(data) {
        try {
            var articles = JSON.parse(data);
            NewsTicker(articles);
        } catch(err) {
            console.log(data);
        }
    },'newsTicker');
}
function NewsTicker(articles) {
    for(var i in articles) {
        var element = $('<li>'+articles[i]+'</li>');
        element.appendTo($('#NewsTicker'));
        element.on('webkitTransitionEnd otransitionend oTransitionEnd msTransitionEnd transitionend',   
            function(e) {
                var element = $(this).next();
                console.log(element.length);
                if (element.length === 0) {
                    LoadNewsTicker();
                    console.log('eh?');
                } else {
                    $(this).remove();
                    element.css('left','-'+(element.width()+200)+'px');
                }
            }.bind(element));
    }
    $('#NewsTicker li:first-child').css('left','-'+($('#NewsTicker li:first-child').width()+200)+'px');
}

function inGameTime() {
    // 14400 = 4 hours real time or 1 day ingame time
    return Date.now()%(14400*7);
}
function inGameWeekday() {
    return Math.floor(inGameTime()/14400);
}
function inGameHour() {
    return Math.floor(((inGameTime()%14400)/14400)*24);
}
function inGameMinute() {}

function blendTwoColors(color1, color2, progress) {
    var r = parseInt(color1.substring(1, 3),16);
    var g = parseInt(color1.substring(3, 5),16);
    var b = parseInt(color1.substring(5, 7),16);
    
    var r2 = parseInt(color2.substring(1, 2),16);
    var g2 = parseInt(color2.substring(3, 5),16);
    var b2 = parseInt(color2.substring(6, 8),16);
    
    r = Math.min(r+Math.floor(((r-r2)*progress)),255).toString(16).pad(2);
    g = Math.min(g+Math.floor(((g-g2)*progress)),255).toString(16).pad(2);
    b = Math.min(b+Math.floor(((b-b2)*progress)),255).toString(16).pad(2);
    return r+g+b;
}
function getIntFromColor(Red, Green, Blue) {
    Red = (Red << 16) & 0x00FF0000; //Shift red 16-bits and mask out other stuff
    Green = (Green << 8) & 0x0000FF00; //Shift Green 8-bits and mask out other stuff
    Blue = Blue & 0x000000FF; //Mask out anything not blue.

    return 0xFF000000 | Red | Green | Blue; //0xFF000000 for 100% Alpha. Bitwise OR everything together.
}
function RadToDeg(Value) {
    return Value * (180 / Math.PI);
}
function PointDirection(X1, Y1, X2, Y2) {
    return RadToDeg(Math.atan2(Y2 - Y1, X2 - X1));
}
function LengthDir_X(dist, dir) {
    return Math.cos(dir * 3.14 / 180) * dist;
}
function LengthDir_Y(dist, dir) {
    return Math.sin(dir * 3.14 / 180) * dist;
}

///// RPG GAME

//// DUNGEON CRAWLER
