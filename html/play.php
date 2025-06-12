<?php
    session_start();
    include_once '_php/mysqli.php';
    include_once '_php/class-player.php';
    include_once '_php/class-updates.php';
    include_once '_php/TWIG-Var.php';
    
    if (isset($_GET['g'])) {
        $_SESSION['currentGame'] = str_replace('.','',$_GET['g']);
    }
    
    $arguments = array();
    
    
    include_once 'html/_templates/_plugin/notifications.php';
    
    $arguments['USERNAME'] = PLAYERCLASS::byMe()->Username();
    $arguments['NETWORKKEY'] = $_SESSION['NetworkKey'] = uniqid();
    if (strlen($arguments['USERNAME']) <= 0 || !isset($_SESSION['currentGame'])) {
        $arguments['BODY'] = 'You must be logged in to view this page';
    }
    $arguments['TOOLBAR'] = include '_templates/_plugin/toolbar.php';
    $arguments['BODY'] = include '_templates/_plugin/index.php';
    
    //if (isMobile()) echo TWIG()->render('/html/_templates/layout.mobile.twig', $arguments);
    echo TWIG()->render('/html/_templates/layout.twig', $arguments);