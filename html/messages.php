<?php
    include_once '_php/mysqli.php';
    include_once '_php/class-player.php';
    include_once '_php/class-messages.php';
    include_once '_php/class-updates.php';
    include_once '_php/class-battle-request.php';
    include_once '_php/TWIG-Var.php';
    
    $arguments = array();
    MESSAGECLASS::doMarkAllSeen();
    
    include_once 'html/_templates/_plugin/notifications.php';
    
    
    $messages = MESSAGECLASS::bySendTo(PLAYERCLASS::byMe()->Id());
    $arguments['MESSAGES'] = array();
    foreach($messages as $i) {
        if (intval($i->Id()) === 0) break;
        $arguments['MESSAGES'][] = array('id'=>$i->Id(),
                                        'from'=>PLAYERCLASS::byId($i->From())->Username(),
                                        'item'=>$i->Item(),
                                        'message'=>substr($i->Message(),0,20).'...',
                                        'time'=>$i->Time());
    }
    
    
    $battleRequests = BATTLEREQUESTCLASS::byUserId(PLAYERCLASS::byMe()->Id());
    $arguments['BATTLEREQUESTS'] = [];
    $arguments['BATTLEREQUESTSEXPIRED'] = [];
    foreach($battleRequests as $i) {
        if (empty($i->Id())) continue;
        $from = PLAYERCLASS::byId($i->UserFrom());
        if ($i->Time() < time()-300) {
            $arguments['BATTLEREQUESTSEXPIRED'][] = ['from'=>$from->Username()];
        } else {
            $arguments['BATTLEREQUESTS'][] = ['from'=>$from->Username(),'timeleft'=>(300-(time()-$i->time()))];
        }
    }
    $battleSentRequests = BATTLEREQUESTCLASS::byUserFromId(PLAYERCLASS::byMe()->Id());
    $arguments['BATTLESENTREQUESTS'] = [];
    $arguments['BATTLESENTREQUESTSEXPIRED'] = [];
    foreach($battleSentRequests as $i) {
        if (empty($i->Id())) continue;
        $from = PLAYERCLASS::byId($i->UserId());
        if ($i->Time() < time()-300) {
            $arguments['BATTLESENTREQUESTSEXPIRED'][] = ['from'=>$from->Username()];
        } else {
            $arguments['BATTLESENTREQUESTS'][] = ['from'=>$from->Username(),'timeleft'=>(300-(time()-$i->time()))];
        }
    }
    
    $arguments['TOKEN'] = $_SESSION['token'] = uniqid();
    
    $arguments['USERNAME'] = PLAYERCLASS::byMe()->Username();
    $arguments['NETWORKKEY'] = $_SESSION['NetworkKey'] = uniqid();
    $arguments['TOOLBAR'] = include '_templates/_plugin/toolbar.php';
    $arguments['BODY'] = TWIG()->render('/html/_templates/messages.twig', $arguments);
    
    echo TWIG()->render('/html/_templates/layout.twig', $arguments);