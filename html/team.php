<?php
    include_once '_php/mysqli.php';
    include_once '_php/class-player.php';
    include_once '_php/class-monsters.php';
    include_once '_php/class-updates.php';
    include_once '_php/TWIG-Var.php';
    $arguments = array();
    
    
    include_once 'html/_templates/_plugin/notifications.php';
    
    $_GET['o'] = (!isset($_GET['o'])?0:$_GET['o']);
    $items = PLAYERCLASS::byMe()->Monster()->byTeam();
    $arguments['TEAM'] = array();
    foreach($items as $i) {
        if (!empty($i->Id()))
        $arguments['TEAM'][] = $i->Render()->badgeHorizontal('image|classes|hpguage|hp|level','href="http://PokeWorlds.com/mon.php?id='.$i->Id().'"');
    }
    
    $items = PLAYERCLASS::byMe()->Monster()->byBox();
    $arguments['MONSTERS'] = array();
    foreach($items as $i) {
        if (!empty($i->Id()))
        $arguments['MONSTERS'][] = $i->Render()->badgeHorizontal('image|level','href="http://PokeWorlds.com/mon.php?id='.$i->Id().'"');
    }
    $arguments['OFFSET'] = intval($_GET['o']);
    $arguments['USERNAME'] = PLAYERCLASS::byMe()->Username();
    $arguments['NETWORKKEY'] = $_SESSION['NetworkKey'] = uniqid();
    $arguments['TOOLBAR'] = include '_templates/_plugin/toolbar.php';
    $arguments['BODY'] = TWIG()->render('/html/_templates/team.twig', $arguments);
    
    echo TWIG()->render('/html/_templates/layout.twig', $arguments);