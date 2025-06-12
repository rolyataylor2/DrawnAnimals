<?php

if (!LoggedIn()) {
    die('<script>window.location.href="http://PokeWorlds.com/register.php";</script>');
}
include_once 'html/_php/class-regions.php';
include_once 'html/_php/class-region-maps.php';
function SaveRegionData($region) {
    if ($region->UserId() !== PLAYERCLASS::byMe()->Id()) die('You do not have permissions to edit this region.');
    $region->Name($_POST['name']);
    $region->Description($_POST['description']);
    $region->_save();
    if (file_exists($_FILES['smallbanner']['tmp_name'])) {
        mkdir('/var/www/html/img/location/banners/');
        move_uploaded_file($_FILES['smallbanner']['tmp_name'], '/var/www/html/img/location/banners/smallBanner.'.$region->Id().'.png');
    }
    foreach($_POST['mapId'] as $key=>$i) {
        $pos = json_decode($_POST['mapPos'][$key],true);
        $map = CREATEREGIONMAPCLASS::byId($i);
        if ($map->Region() === $region->Id()) {
            $map->RegionX($pos['left']);
            $map->RegionY($pos['top']);
            $map->_save();
        }
    }
    foreach($_POST['minimapId'] as $key=>$i) {
        $pos = json_decode($_POST['minimapPos'][$key],true);
        $map = CREATEREGIONMAPCLASS::byId($i);
        if ($map->Region() === $region->Id()) {
            $map->MinimapIcon($_POST['minimapIcon'][$key]);
            $map->MinimapX($pos['left']);
            $map->MinimapY($pos['top']);
            $map->_save();
        }
    }
}
if (isset($_GET['id'])) {
    if ($_GET['id'] == -1) {
        if (VerifyPostToken()) {
            $region = CREATEREGIONCLASS::byNew(PLAYERCLASS::byMe()->Id(),$_POST['name']);
            SaveRegionData($region);
            die('<script>history.go(-2);</script>');
        }
    }
    $region = CREATEREGIONCLASS::byId($_GET['id']);
    $region->_load();
    
    if (VerifyPostToken()) {
        SaveRegionData($region);
        die('<script>history.go(-2);</script>');
    }
    $maps = CREATEREGIONMAPCLASS::byRegion($_GET['id'],' ');
    $arguments['MAPCOUNT'] = count($maps);
    $arguments['MAPS'] = [];
    foreach($maps as $i) {
        $i->_load();
        $arguments['MAPS'][] = $i->data;
    }
    $arguments['REGION'] = (isset($region->data)?$region->data:array());
    $arguments['EDIT'] = ($region->UserId() === PLAYERCLASS::byMe()->Id());
    $arguments['TOKEN'] = $_SESSION['token'] = uniqid();
}