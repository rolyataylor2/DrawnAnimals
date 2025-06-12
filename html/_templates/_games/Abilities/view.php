<?php

include_once 'html/_php/class-monsters-learnset.php';

if (isset($_GET['id'])) {
    $ability = CREATEABILITYCLASS::byId($_GET['id']);
    $ability->_load();
    $arguments['ABILITY'] = $ability->data;
    $arguments['EDIT'] = ($ability->UserId() === PLAYERCLASS::byMe()->Id());
}