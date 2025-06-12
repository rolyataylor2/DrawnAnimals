<?php
class SYSGLOSSARY {
    var $TypeNames = array('???', 'Normal', 'Strong', 'Sky',
        'Toxic', 'Ground', 'Mineral', 'Insect',
        'Spirit', 'Alloy', 'Fire', 'Water',
        'Plant', 'Electric', 'Mind',
        'Ice', 'Mythic', 'Dark','Fairy');
    var $TypeColors = array('#000', '#999966', '#cc3333', '#9999ff',
        '#993399', '#cccc66', '#cc9933', '#99cc33',
        '#666699', '#c0c0c0', '#ff9933', '#6699ff',
        '#66cc66', '#ffcc33', '#ff6699',
        '#99cccc', '#6633ff', '#666633','#ff77aa');
    var $TypeEffectiveChart =     [ [0, 0,	0,	0,	0,	0,	0,	0,	0,	0,	0,	0,	0,	0,	0,	0,	0,	0,	0],
                                    [0, 1,	1,	1,	1,	1,	0.5,	1,	0,	0.5,	1,	1,	1,	1,	1,	1,	1,	1,	1],
                                    [0, 2,	1,	0.5,	0.5,	1,	2,	0.5,	0,	2,	1,	1,	1,	1,	0.5,	2,	1,	2,	0.5],
                                    [0, 1,	2,	1,	1,	1,	0.5,	2,	1,	0.5,	1,	1,	2,	0.5,	1,	1,	1,	1,	1],
                                    [0, 1,	1,	1,	0.5,	0.5,	0.5,	1,	0.5,	0,	1,	1,	2,	1,	1,	1,	1,	1,	2],
                                    [0, 1,	1,	0,	2,	1,	2,	0.5,	1,	2,	2,	1,	0.5,	2,	1,	1,	1,	1,	1],
                                    [0, 1,	0.5,	2,	1,	0.5,	1,	2,	1,	0.5,	2,	1,	1,	1,	1,	2,	1,	1,	1],
                                    [0, 1,	0.5,	0.5,	0.5,	1,	1,	1,	0.5,	0.5,	0.5,	1,	2,	1,	2,	1,	1,	2,	0.5],
                                    [0, 0,	1,	1,	1,	1,	1,	1,	2,	1,	1,	1,	1,	1,	2,	1,	1,	0.5,	1],
                                    [0, 1,	1,	1,	1,	1,	2,	1,	1,	0.5,	0.5,	0.5,	1,	0.5,	1,	2,	1,	1,	2],
                                    [0, 1,	1,	1,	1,	1,	0.5,	2,	1,	2,	0.5,	0.5,	2,	1,	1,	2,	0.5,	1,	1],
                                    [0, 1,	1,	1,	1,	2,	2,	1,	1,	1,	2,	0.5,	0.5,	1,	1,	1,	0.5,	1,	1],
                                    [0, 1,	1,	0.5,	0.5,	2,	2,	0.5,	1,	0.5,	0.5,	2,	0.5,	1,	1,	1,	0.5,	1,	1],
                                    [0, 1,	1,	2,	1,	0,	1,	1,	1,	1,	1,	2,	0.5,	0.5,	1,	1,	0.5,	1,	1],
                                    [0, 1,	2,	1,	2,	1,	1,	1,	1,	0.5,	1,	1,	1,	1,	0.5,	1,	1,	0,	1],
                                    [0, 1,	1,	2,	1,	2,	1,	1,	1,	0.5,	0.5,	0.5,	2,	1,	1,	0.5,	2,	1,	1],
                                    [0, 1,	1,	1,	1,	1,	1,	1,	1,	0.5,	1,	1,	1,	1,	1,	1,	2,	1,	0],
                                    [0, 1,	0.5,	1,	1,	1,	1,	1,	2,	1,	1,	1,	1,	1,	2,	1,	1,	0.5,	0.5],
                                    [0, 1,	2,	1,	0.5,	1,	1,	1,	1,	0.5,	0.5,	1,	1,	1,	1,	1,	2,	2,	1]];
    function TypeEffective($atk,$def) {return $this->TypeEffectiveChart[$atk][$def];}
    function GetHour() {}
    function GetMinute() {}
    
    }
$GLOBALS['GLSS'] = new SYSGLOSSARY();
function GLSS() {return $GLOBALS['GLSS'];}

function ProcessBBcode($str) {
    $str = htmlspecialchars($str);
    
    // The array of regex patterns to look for
    $format_search =  [
        '/\n/i',
        '/\[b\](.*?)\[\/b\]/i',
        '/\[i\](.*?)\[\/i\]/i',
        '/\[u\](.*?)\[\/u\]/i',
        '/\[icon\](.*?)\[\/icon\]/i',
        '/#(([^\s]+)?)/i'
    ]; 

    // The matching array of strings to replace matches with
    $format_replace = [
        '<br/>',
        '<strong>$1</strong>',
        '<em>$1</em>',
        '<span style="text-decoration: underline;">$1</span>',
        '<img src="img/drawnimals/$1.png"/>',
        '<a href="#" onclick="NODE.Send.Chat.JoinRoom(\'$1\')">#$1</a>'
        
    ];

    // Perform the actual conversion
    $str = preg_replace($format_search, $format_replace, $str); 
    return $str;
}