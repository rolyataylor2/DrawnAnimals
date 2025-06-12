<?php
    session_start();
    include_once '_php/mysqli.php';
    include_once '_php/class-player.php';
    include_once '_php/class-updates.php';
    include_once '_php/TWIG-Var.php';
    include_once 'html/_php/class-regions.php';
    include_once 'html/_php/class-region-maps.php';
    if (PLAYERCLASS::byMe()->BattleId() != 0) {
        include 'battle.php';
        die();
    }
    $arguments = array();
    include_once 'html/_templates/_plugin/notifications.php';
    
    $arguments['USERNAME'] = PLAYERCLASS::byMe()->Username();
    $arguments['NETWORKKEY'] = $_SESSION['NetworkKey'] = uniqid();
    
    $maps = CREATEREGIONMAPCLASS::byRegion(3,' ');
    $arguments['MAPCOUNT'] = count($maps);
    $arguments['MAPS'] = [];
    foreach($maps as $i) {
        $i->_load();
        $arguments['MAPS'][] = $i->data;
    }
    $arguments['BODY'] = TWIG()->render('/html/_templates/location.twig', $arguments);
    
    //if (isMobile()) echo TWIG()->render('/html/_templates/layout.mobile.twig', $arguments);
    echo TWIG()->render('/html/_templates/layout.twig', $arguments);