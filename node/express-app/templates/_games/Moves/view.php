<?php

include_once 'html/_php/class-monsters-learnset.php';

if (isset($_GET['id'])) {
    $item = CREATEMOVECLASS::byId($_GET['id']);
    $item->_load();
    $arguments['MOVE'] = $item->data;
    $arguments['EDIT'] = ($item->UserId() === PLAYERCLASS::byMe()->Id());
}
