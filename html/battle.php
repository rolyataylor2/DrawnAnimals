<?php
    include_once '_php/mysqli.php';
    include_once '_php/class-player.php';
    include_once '_php/class-battle.php';
    include_once '_php/class-updates.php';
    include_once '_php/class-monsters.php';
    include_once '_php/TWIG-Var.php';
    $arguments = array();
    
    
    include_once 'html/_templates/_plugin/notifications.php';
    
    
    $_SESSION['battleid'] = 0;
    if (isset($_GET['id'])) {
        $_SESSION['battleid'] = intval($_GET['id']);
    }
    if (PLAYERCLASS::byMe()->BattleId() != 0) {
        $_SESSION['battleid'] = PLAYERCLASS::byMe()->BattleId();
        $arguments['MYDIVLABEL'] = '' . PLAYERCLASS::byMe()->Username() . '-' . PLAYERCLASS::byMe()->Id();
    }
    
    $arguments['USERNAME'] = PLAYERCLASS::byMe()->Username();
    $arguments['NETWORKKEY'] = $_SESSION['NetworkKey'] = uniqid();
    $arguments['TOOLBAR'] = include '_templates/_plugin/toolbar.php';
    $arguments['BODY'] = TWIG()->render('/html/_templates/battle.twig', $arguments);
    
    echo TWIG()->render('/html/_templates/layout.twig', $arguments);