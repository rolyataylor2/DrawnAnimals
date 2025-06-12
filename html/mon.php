<?php
    include_once '_php/mysqli.php';
    include_once '_php/class-like.php';
    include_once '_php/class-player.php';
    include_once '_php/class-monsters.php';
    include_once '_php/class-updates.php';
    include_once '_php/TWIG-Var.php';
    $arguments = array();
    
    include_once 'html/_templates/_plugin/notifications.php';
    $arguments['MONSTER'] = [];
    $monster = MONSTERCLASS::byId($_GET['id']);
    $monster->_load();
    $arguments['MONSTER']['imageurl'] = $monster->Render()->imageUrl();
    $arguments['MONSTER'] = array_merge($arguments['MONSTER'],$monster->data);
    $arguments['MONSTER']['username'] = $monster->Owner()->Username();
    $arguments['MONSTER']['MOVE'] = array();
    for($i=0;$i<4;$i++) {
        $move = $monster->Move($i);
        $move->_load();
        $arguments['MONSTER']['MOVE'][] = $move->data;
    }
    $arguments['MONSTER']['HPGUAGE'] = $monster->Render()->guageHp();
    $arguments['MONSTER']['EXPGUAGE'] = $monster->Render()->guageExp();
    $arguments['MONSTER']['STATTABLE'] = $monster->Render()->statTable();
    $arguments['EDIT'] = ($monster->Owner()->Id()===PLAYERCLASS::byMe()->Id()?'edit':'');
    $arguments['INPARTY'] = ($arguments['EDIT'] && $monster->PartyPos() > 0);
    $arguments['FOLLOWING'] = ($monster->Id() === PLAYERCLASS::byMe()->Follower());
    

    $color2 = $monster->Species()->TypePrimary()->Color();
    $color1 = hex2RGB($color2);
    $color1['red'] = max(0,$color1['red']-75);
    $color1['blue'] = max(0,$color1['blue']-75);
    $color1['green'] = max(0,$color1['green']-75);
    $color1 = '#'.dechexpad($color1['red']).dechexpad($color1['green']).dechexpad($color1['blue']);
    
    
    $color4 = $monster->Species()->TypeSecondary()->Color();
    if (strcmp($color4,'#000')===0) $color4 = $color2;
    $color3 = hex2RGB($color4);
    $color3['red'] = max(0,$color3['red']-75);
    $color3['blue'] = max(0,$color3['blue']-75);
    $color3['green'] = max(0,$color3['green']-75);
    $color3 = '#'.dechexpad($color3['red']).dechexpad($color3['green']).dechexpad($color3['blue']);
    
    
    $arguments['BACKGROUND'] =  "background: $color1; /* Old browsers */
    background: -moz-linear-gradient(-45deg,  $color1 0%, $color2 50%, $color3 51%, $color4 100%); /* FF3.6+ */
    background: -webkit-gradient(linear, left top, right bottom, color-stop(0%,$color1), color-stop(50%,$color2), color-stop(51%,$color3), color-stop(100%,$color4)); /* Chrome,Safari4+ */
    background: -webkit-linear-gradient(-45deg,  $color1 0%,$color2 50%,$color3 51%,$color4 100%); /* Chrome10+,Safari5.1+ */
    background: -o-linear-gradient(-45deg,  $color1 0%,$color2 50%,$color3 51%,$color4 100%); /* Opera 11.10+ */
    background: -ms-linear-gradient(-45deg,  $color1 0%,$color2 50%,$color3 51%,$color4 100%); /* IE10+ */
    background: linear-gradient(135deg,  $color1 0%,$color2 50%,$color3 51%,$color4 100%); /* W3C */
    filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='$color1', endColorstr='$color2',GradientType=1 ); /* IE6-9 fallback on horizontal gradient */";
    
    
    $species = $monster->Species();
    $species->_load();
    $arguments['MONSTER'] = array_merge($species->data,$arguments['MONSTER']);
    
    $arguments['ADMIN'] = PLAYERCLASS::byMe()->isAdmin('userAdmin');
    $arguments['USERNAME'] = PLAYERCLASS::byMe()->Username();
    $arguments['NETWORKKEY'] = $_SESSION['NetworkKey'] = uniqid();
    if ($monster->Egg()) {
        $arguments['MONSTER']['ready'] = $monster->EggWarm();
        $arguments['MONSTER']['warmed'] = (LIKECLASS::byUserByCatagoryByItem(PLAYERCLASS::byMe()->Id(),'eggHatch',$monster->Id()) === 1);
       
        $arguments['BODY'] = TWIG()->render('/html/_templates/mon.egg.twig', $arguments);
    } else {
        $arguments['BODY'] = TWIG()->render('/html/_templates/mon.twig', $arguments);
    }
    
    echo TWIG()->render('/html/_templates/layout.twig', $arguments);