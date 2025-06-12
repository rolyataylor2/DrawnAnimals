<?php

include_once 'html/_php/class-region-maps.php';

if (isset($_GET['id'])) {
    $item = CREATEREGIONMAPCLASS::byId($_GET['id']);
    $item->_load();
    $arguments['MAP'] = $item->data;
    $arguments['EDIT'] = ($item->UserId() === PLAYERCLASS::byMe()->Id());
}