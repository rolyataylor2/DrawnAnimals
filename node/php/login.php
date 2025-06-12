<?php
//ARGUMENT1 = username, ARGUMENT2 = NetworkKey
    session_start();
    require_once 'html/_php/mysqli.php';
    require_once 'html/_php/class-player.php';
    require_once 'html/_php/class-monsters.php';
    $player = PLAYERCLASS::byUsername($argv[1]);
    if ( empty($player->Username()) ) {
        die('Could Not Find Username');
    }
    
    session_destroy();
    session_id($player->SessionId());
    session_start();

    if ( $argv[2] !== $_SESSION['NetworkKey'] ) {
        die('Error Invalid Network Key ' + $argv[2]);
    }

    $data = array();
    
    $following = $player->Monster()->byFollowing();
    if ( !empty($following->Id()) ) {
        $data['following'] = array(
            'id' => $following->Id(),
            'species' => $following->Render()->owUrl()
        );
        
    } else {
        $following = $player->Monster()->byFollowing();
        if ($following->Id() !== null) {
            $data['following'] = array(
                'id' => $following->Id(),
                'species' => $following->Species()->Id()
            );
        } else {
            $data['following'] = array(
                'id' => 0,
                'species' => 0
            );
        }
        
    }
    $data['id'] = $player->Id();
    $data['username'] = strtolower($player->Username());
    $data['avatar_ow'] = $player->AvatarOw();
    $data['avatar'] = $player->Avatar();
    $data['type'] = ($player->Type() !== null ? $player->Type() : '');
    
    $data['canSwim'] = false;
    $data['surfMon'] = 'http://www.drawnimals.com/img/mon/ow/287.g_3.c_.png';
    foreach($player->Monster()->byTeam() as $i) {
        for($ii=0;$ii<4;$ii++) {
            if (strcmp($i->Move($ii)->Name(),'Surf') === 0) {
                $data['canSwim'] = true;
                $data['surfMon'] = $i->Render()->owUrl();
                break;
            }
        }
    }
    if ($data['surfMon'] === 0) {
        $data['surfMon'] = 'http://www.drawnimals.com/img/mon/ow/287.g_3.c_.png';
    }
    
    
    
    
    
    
    $data['region'] = $player->Region();
    $data['location'] = 0;
    $data['location_x'] = $data['x'] = 69;
    $data['location_y'] = $data['y'] = 296;
    $data['color'] = $player->Color();
    $data['battle_id'] = $player->BattleId();
//    $data['coins'] = $player->Stat()->Coins();
//    $data['cash'] = $player->Stat()->Cash();
    $data['sessionid'] = session_id();
//    $data['battle_id'] = $player->Battle()->Id();

//    $data['avatar_ow'] = array();
//    $clothing = $player->AvatarOverworld();
//    foreach($clothing as $i) {
//        $data['avatar_ow'][] = $i->Id();
//    }
    
//    $data['party'] = array();
//    foreach($player->Drawnimal()->byTeam() as $i) {
//        $data['party'][] = array('id' => $i->Id(),
//                                 'name'=> $i->Nickname(),
//                                 'hp'=>$i->Hp(),
//                                 'hpmax'=>$i->Stat('HP'),
//                                 'level'=>$i->Level(),
//                                 'image'=>$i->Render()->imageUrl());
//    }

    echo json_encode($data);
