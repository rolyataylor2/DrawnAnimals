<?php
    session_start();
    include_once '_php/mysqli.php';
    include_once '_php/class-player.php';
    include_once '_php/class-updates.php';
    include_once '_php/class-blog.php';
    include_once '_php/TWIG-Var.php';
    $arguments = array();
    
    // LOGIN
    if (isset($_POST['username']) && isset($_POST['pass'])) {
        $player = PLAYERCLASS::byUsernameByPassword($_POST['username'],$_POST['pass']);
        if (!empty($player->Id())) {
            $_SESSION['MyID'] = $player->Id();
            $player->SessionId(true);
            if (date('Ymd', $player->_var('datelastseen')) != date('Ymd', strtotime('today'))) {
                include 'html/_templates/_plugin/dailyLoginReward.php';
            }
            $player->LastSeen(true);
            $player->_save();
        }
    }
    
    
    include_once 'html/_templates/_plugin/notifications.php';
    
    $arguments['POSTS'] = array();
    foreach(BLOGPOSTCLASS::byOlderThan(time()) as $i) {
        if (!empty($i->Id())) {
            $arguments['POSTS'][] = array('subject'=>$i->Subject(),
                                          'content'=>$i->Content(),
                                          'username'=>PLAYERCLASS::byId($i->UserId())->Username(),
                                          'avatar'=>PLAYERCLASS::byId($i->UserId())->Avatar());
        }
    }
    
    $arguments['USERNAME'] = PLAYERCLASS::byMe()->Username();
    $arguments['NETWORKKEY'] = $_SESSION['NetworkKey'] = uniqid();
    $arguments['TOOLBAR'] = include '_templates/_plugin/toolbar.php';
    $arguments['BODY'] = TWIG()->render('/html/_templates/index.twig', $arguments);
    //if (isMobile()) echo TWIG()->render('/html/_templates/layout.mobile.twig', $arguments);
    echo TWIG()->render('/html/_templates/layout.twig', $arguments);
    
