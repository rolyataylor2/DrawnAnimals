/*! jQuery v1.8.2 jquery.com | jquery.org/license */
    function RGB2Color(r,g,b) {return '#' + this.byte2Hex(r) + this.byte2Hex(g) + this.byte2Hex(b);}
    function byte2Hex (n){var nybHexString = "0123456789ABCDEF"; return String(nybHexString.substr((n >> 4) & 0x0F,1)) + nybHexString.substr(n & 0x0F,1);}

/* Keypress, Keydown arrays*/
    var keypress = {}, keydown = {}, keyCodes = {8:"backspace", 9:"tab", 13:"enter", 16:"shift", 17:"ctrl", 18:"alt", 19:"pause", 20:"capslock", 27:"escape", 33:"pageup", 34:"pagedown", 35:"end", 36:"home", 37:"left", 38:"up", 39:"right", 40:"down", 45:"insert", 46:"delete", 48:"0", 49:"1", 50:"2", 51:"3", 52:"4", 53:"5", 54:"6", 55:"7", 56:"8", 57:"9", 65:"a", 66:"b", 67:"c", 68:"d", 69:"e", 70:"f", 71:"g", 72:"h", 73:"i", 74:"j", 75:"k", 76:"l", 77:"m", 78:"n", 79:"o", 80:"p", 81:"q", 82:"r", 83:"s", 84:"t", 85:"u", 86:"v", 87:"w", 88:"x", 89:"y", 90:"z", 91:"leftwindowkey", 92:"rightwindowkey", 93:"selectkey", 96:"numpad0", 97:"numpad1", 98:"numpad2", 99:"numpad3", 100:"numpad4", 101:"numpad5", 102:"numpad6", 103:"numpad7", 104:"numpad8", 105:"numpad9", 106:"multiply", 107:"add", 109:"subtract", 110:"decimalpoint", 111:"divide", 112:"f1", 113:"f2", 114:"f3", 115:"f4", 116:"f5", 117:"f6", 118:"f7", 119:"f8", 120:"f9", 121:"f10", 122:"f11", 123:"f12", 144:"numlock", 145:"scrolllock", 186:"semi-colon", 187:"equalsign", 188:"comma", 189:"dash", 190:"period", 191:"forwardslash", 192:"graveaccent", 219:"openbracket", 220:"backslash", 221:"closebraket", 222:"singlequote"};
    for(var i in keyCodes) {
        keypress[keyCodes[i]] = false;
        keydown[keyCodes[i]] = false;
    }
    $(document).keydown(function(a) {
        if (keydown[keyCodes[a.keyCode]] === false)
            keypress[keyCodes[a.keyCode]] = true;
        keydown[keyCodes[a.keyCode]] = true;
    });
    $(document).keyup(function(a) {
        keydown[keyCodes[a.keyCode]] = false;
    });
/* Mouse Position */
var Mouse = {
    x:0, y:0
};
$(document).mousemove(function( event ) {
    Mouse.x = event.pageX; Mouse.y = event.pageY;
});

/*! http://mths.be/visibility v1.0.7 by @mathias | MIT license */
;(function(window, document, $, undefined) {

	var prefix;
	var property;
	// In Opera, `'onfocusin' in document == true`, hence the extra `hasFocus` check to detect IE-like behavior
	var eventName = 'onfocusin' in document && 'hasFocus' in document
		? 'focusin focusout'
		: 'focus blur';
	var prefixes = ['webkit', 'o', 'ms', 'moz', ''];
	var $support = $.support;
	var $event = $.event;

	while ((prefix = prefixes.pop()) != undefined) {
		property = (prefix ? prefix + 'H': 'h') + 'idden';
		if ($support.pageVisibility = typeof document[property] == 'boolean') {
			eventName = prefix + 'visibilitychange';
			break;
		}
	}

	$(/blur$/.test(eventName) ? window : document).on(eventName, function(event) {
		var type = event.type;
		var originalEvent = event.originalEvent;

		// Avoid errors from triggered native events for which `originalEvent` is
		// not available.
		if (!originalEvent) {
			return;
		}

		var toElement = originalEvent.toElement;

		// If it’s a `{focusin,focusout}` event (IE), `fromElement` and `toElement`
		// should both be `null` or `undefined`; else, the page visibility hasn’t
		// changed, but the user just clicked somewhere in the doc. In IE9, we need
		// to check the `relatedTarget` property instead.
		if (
			!/^focus./.test(type) || (
				toElement == undefined &&
				originalEvent.fromElement == undefined &&
				originalEvent.relatedTarget == undefined
			)
		) {
			$event.trigger(
				(
					property && document[property] || /^(?:blur|focusout)$/.test(type)
						? 'hide'
						: 'show'
				) + '.visibility'
			);
		}
	});

}(this, document, jQuery));