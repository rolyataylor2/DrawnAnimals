<?php

include_once 'html/_php/class-region-maps.php';

if (isset($_GET['id'])) {
    $item = CREATEREGIONMAPCLASS::byId($_GET['id']);
    $item->_load();
    $mapdata = json_decode($item->TileData(),true);
    if (VerifyPostToken()) {
        $mapdata['collisions'] = json_decode($_POST['collisiondata'],true);
        $item->TileData(json_encode($mapdata));
        $item->_save();
        die('<script>history.go(-1);</script>');
    }
    $arguments['MAP'] = $item->data;
    $arguments['MAP']['collisions'] = (isset($mapdata['collisions'])?json_encode($mapdata['collisions']):'[]');

    $arguments['EDIT'] = ($item->UserId() === PLAYERCLASS::byMe()->Id());
    $arguments['TOKEN'] = $_SESSION['token'] = uniqid();
}