<?php
    require_once 'include/Core/index.php';
    $player = PLAYERCLASS::byId($argv[1]);
    $text = ProcessBBcode(str_replace('&apos;',"'",$argv[2])); $room = $argv[3];
    $chat = LOGCHATCLASS::byNew($player,$text,$room);
    die(json_encode(array('text'=>$text,'room'=>$room)));
    
