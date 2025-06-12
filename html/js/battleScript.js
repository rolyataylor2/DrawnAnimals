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
function gameBattle() {
    this._queue = [];
    this._progress = 0;
    this._timeout = 0;
    this.myDivLabel = '';
    this.sendCommand('r=1');
}
gameBattle.prototype.deQueue = function() {
    this._progress = 0;
    var event = this._queue.shift();
    if (event === void 0) {
        this.eventCommand();
        return;
    }
    if (this[event.name] !== void 0) {
        this[event.name].apply(this,event.args);
    } else {
        this.nextQueue();
    }
};
gameBattle.prototype.enQueue = function(name,args) {
    this._queue.push({ name:name, args:args });
};
gameBattle.prototype.nextQueue = function(timeout) {
    if (timeout === void 0)
        timeout = 10;
    clearTimeout(this._timeout);
        this._timeout = setTimeout(this.deQueue.bind(this),timeout);
};
gameBattle.prototype.clearQueue = function() {
    this._queue = [];
};
gameBattle.prototype.eventCreateTrainer = function(divlabel) {
    if ($('.playerContainer.'+divlabel).length === 0) {
        var div = $('<div></div>').addClass('playerContainer').addClass(divlabel).addClass('hidden');
        if (divlabel.indexOf('AI') !== -1 && divlabel.indexOf('Wild') === -1) div.addClass('trainer');
        else if (divlabel.indexOf('Wild') === -1 && this.myDivLabel === divlabel) div.addClass('me');
        div.append('<img src="http://PokeWorlds.com/img/mon/116.g_3.c_.png" class="image"/>');
        div.append('<div class="status"><div class="name"></div><div class="level"></div><img class="gender" src="http://PokeWorlds.com/img/gender0.png"/><div class="caught"></div><div class="hpguage"><div class="max"><div class="min"></div></div><div class="hp"></div></div></div>')
        div.appendTo($('#battledisplay'));
    }
};
gameBattle.prototype.eventWait = function(amount) {
    this.nextQueue(amount);
};
gameBattle.prototype.eventPlaySound = function(url,speed,volume) {
    game.sound.add(url);
    game.sound.sounds[url].playbackRate = speed;
    game.sound.sounds[url].volume = volume;
    game.sound.play(url);
    this.nextQueue(100);
};
gameBattle.prototype.eventStopSoundBgm = function() {
    game.sound.stop('bgm');
    this.nextQueue(100);
};
gameBattle.prototype.eventPlaySoundBgm = function(url) {
    game.sound.addBGM(url);
    game.sound.sounds[url].volume = 0.5;
    game.sound.play(url);
    this.nextQueue(100);
};
gameBattle.prototype.eventAnimateAttack = function(slotfrom,slottoo,attack) {
    this.nextQueue();
};
gameBattle.prototype.eventFaintDrawnimal = function(divlabel) {
    this.eventCreateTrainer(divlabel);
    $('.playerContainer.'+divlabel).addClass('fainted').delay(2000).queue(function(next){
        $(this).hide().removeClass('fainted').addClass('hidden').delay(2000).show(0);
        next();
    });
    this.nextQueue(1000);
};
gameBattle.prototype.eventHideDrawnimal = function(divlabel) {
    this.eventCreateTrainer(divlabel);
    $('.playerContainer.'+divlabel).addClass('hidden');
    game.sound.add('http://PokeWorlds.com/sfx/returnDrawnimalTrainer.wav');
    game.sound.play('http://PokeWorlds.com/sfx/returnDrawnimalTrainer.wav');
    this.nextQueue(1000);
};
gameBattle.prototype.eventShowDrawnimal = function(divlabel) {
    this.eventCreateTrainer(divlabel);
    $('.playerContainer.'+divlabel).removeClass('hidden');
    if ($('.playerContainer.'+divlabel).hasClass('trainer')) {
        game.sound.add('http://PokeWorlds.com/sfx/sendoutDrawnimalTrainer.wav');
        game.sound.play('http://PokeWorlds.com/sfx/sendoutDrawnimalTrainer.wav');
    }
    this.nextQueue(1000);
};
gameBattle.prototype.eventChangeDrawnimalImage = function(divlabel,url) {
    this.eventCreateTrainer(divlabel);
    $('.playerContainer.'+divlabel+' img')[0].src = url;
    this.nextQueue();
};
gameBattle.prototype.eventChangeDrawnimalHp = function(divlabel,hp,hptotal) {
    this.eventCreateTrainer(divlabel);
    $('.playerContainer.'+divlabel+' .hpguage .min').css('width',((hp/hptotal)*100)+'%');
    $('.playerContainer.'+divlabel+'.me .hpguage .hp').html('HP: '+hp+'/'+hptotal);
    this.nextQueue(1000);
};
gameBattle.prototype.eventChangeDrawnimalOwner = function(divlabel,text) {
    this.eventCreateTrainer(divlabel);
    $('.playerContainer.'+divlabel+' .owner').html(text);
    this.nextQueue();
};
gameBattle.prototype.eventChangeDrawnimalLeft = function(divlabel,total,left) {
    this.eventCreateTrainer(divlabel);
    $('.playerContainer.'+divlabel+' .left').html(left+'/'+total);
    this.nextQueue();
};
gameBattle.prototype.eventChangeDrawnimalName = function(divlabel,text) {
    this.eventCreateTrainer(divlabel);
    $('.playerContainer.'+divlabel+' .name').html(text);
    this.nextQueue();
};
gameBattle.prototype.eventChangeDrawnimalLevel = function(divlabel,level) {
    this.eventCreateTrainer(divlabel);
    $('.playerContainer.'+divlabel+' .level').html('LV.'+level);
    this.nextQueue();
};
gameBattle.prototype.eventChangeDrawnimalType = function(divlabel,html) {
    this.eventCreateTrainer(divlabel);
    this.nextQueue();
};

gameBattle.prototype.eventDialog = function(text, userid) {
    this._progress += 1;
    $('#battledialog').html(text.slice(0,this._progress));
    if (this._progress >= text.length) 
        this.nextQueue(1000);
    else
        setTimeout(function() { this.eventDialog(text, userid); }.bind(this),32);
};
gameBattle.prototype.eventGotoGame = function() {
    window.location.reload(true);
};
gameBattle.prototype.eventCommand = function() {
    this.sendCommand();
};

gameBattle.prototype.sendCommand = function(arg) {
    $('#battlecontrols a, #battlecontrols div').attr('onclick','').unbind('click').css('opacity','0.9');
    $.ajax('http://PokeWorlds.com/inline/battleCommand.php?' + (arg !== void 0 ? arg : ''),{
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            $('#battledialog').html('Error With URL: battleCommand.php... Reason: ' + errorThrown);
        },
        success: function(data, textStatus, XMLHttpRequest) {
            $('#battlecontrols').fadeOut(300,function() {
                if (data !== '') {
                    if (data === '<script></script>')
                        return BTTL.sendCommand();
                    $(this).html(data);
                    if (BTTL._queue.length === 0) 
                        $(this).fadeIn(300);
                    else
                        BTTL.nextQueue(1000);
                }
            });
        },
        type: "GET",
        dataType: "html"
    });
}