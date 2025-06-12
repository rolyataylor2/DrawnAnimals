<?php

include_once 'html/_php/class-items.php';

if (isset($_GET['id'])) {
    $item = CREATEITEMCLASS::byId($_GET['id']);
    $item->_load();
    $arguments['ITEM'] = $item->data;
    $arguments['EDIT'] = ($item->UserId() === PLAYERCLASS::byMe()->Id());
}
