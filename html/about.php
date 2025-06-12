<?php
    include_once '_php/mysqli.php';
    include_once '_php/class-player.php';
    include_once '_php/TWIG-Var.php';
    $arguments = array();
    
    
    include_once 'html/_templates/_plugin/notifications.php';
    
    $arguments['USERNAME'] = PLAYERCLASS::byMe()->Username();
    $arguments['NETWORKKEY'] = $_SESSION['NetworkKey'] = uniqid();
    $arguments['TOOLBAR'] = include '_templates/_plugin/toolbar.php';
    $arguments['BODY'] = TWIG()->render('/html/_templates/about.twig', $arguments);
    
    echo TWIG()->render('/html/_templates/layout.twig', $arguments);