<?php


include_once 'html/_php/mysqli.php';
include_once 'html/_php/class-like.php';
// Tasks
$tasklog = [];
$tasklog[] = 'Hatching Eggs '.LIKECLASS::byRemoveByCatagory('eggHatch');
die(json_encode($tasklog));