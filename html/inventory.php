<?php
    include_once '_php/mysqli.php';
    include_once '_php/class-player.php';
    include_once '_php/class-items.php';
    include_once '_php/class-updates.php';
    include_once '_php/TWIG-Var.php';
    $arguments = array();
    
    include_once 'html/_templates/_plugin/notifications.php';
    
    $items = ITEMCLASS::byUserid(PLAYERCLASS::byMe()->Id());
    $arguments['ITEMS'] = array();
    $lastname = '';
    foreach($items as $i) {
        if (strcmp($i->Id(),'') !== 0) {
            $name = $i->Type()->Name();
            if (strcmp($name, $lastname) === 0) {
                $item['total']++;
                $lastname = $name;
            } else {
                if (isset($item)) {
                    $arguments['ITEMS'][] = $item;
                }
                $item = array('id'=>$i->Id(), 'name'=>$name, 'total'=>1);
            }
        }
    }
    if (isset($item)) {
        $arguments['ITEMS'][] = $item;
    }
    $arguments['USERNAME'] = PLAYERCLASS::byMe()->Username();
    $arguments['NETWORKKEY'] = $_SESSION['NetworkKey'] = uniqid();
    $arguments['TOOLBAR'] = include '_templates/_plugin/toolbar.php';
    $arguments['BODY'] = TWIG()->render('/html/_templates/inventory.twig', $arguments);
    
    echo TWIG()->render('/html/_templates/layout.twig', $arguments);