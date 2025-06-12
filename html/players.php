<?php
    include_once '_php/mysqli.php';
    include_once '_php/class-player.php';
    include_once '_php/class-updates.php';
    include_once '_php/class-following.php';
    include_once '_php/TWIG-Var.php';
    $arguments = array();
    
    
    include_once 'html/_templates/_plugin/notifications.php';
    
    // Get player list
    $players = PLAYERCLASS::byAll();
    $arugments['PLAYERS'] = array();
    foreach($players as $i) {
        $lastupdate = UPDATECLASS::byUserByLast($i->Id());
        $following = FOLLOWINGCLASS::byFollowing(PLAYERCLASS::byMe()->Id(),$i->Id())->UserId();
        if (count($lastupdate) > 0) $lastupdate = $lastupdate[0];
        $arguments['PLAYERS'][] = array('username'=>$i->Username(),
                                        'lastseen'=>$i->LastSeen(),
                                        'lastupdate'=>$lastupdate->Args(),
                                        'avatar'=>$i->Avatar(),
                                        'following'=>$following);
    }
    $arguments['PLAYERSCOUNT'] = PLAYERCLASS::countAll();
    $arguments['PLAYERSOFFSET'] = (isset($_GET['o'])?$_GET['o']:0);
    
    $arguments['USERNAME'] = PLAYERCLASS::byMe()->Username();
    $arguments['NETWORKKEY'] = $_SESSION['NetworkKey'] = uniqid();
    $arguments['TOOLBAR'] = include '_templates/_plugin/toolbar.php';
    $arguments['BODY'] = TWIG()->render('/html/_templates/players.twig', $arguments);
    
    echo TWIG()->render('/html/_templates/layout.twig', $arguments);