<?php

/* 
 * Arguments - Function(username,token)
 */

session_start();
include_once 'html/_php/mysqli.php';
include_once 'html/_php/class-player.php';
include_once 'html/_php/class-following.php';
if (isset($_GET['arguments'][0])) {
    $user = (is_string($_GET['arguments'][0]) ? PLAYERCLASS::byUsername($_GET['arguments'][0]) : PLAYERCLASS::byId($_GET['arguments'][0]));
    if (strcmp($_GET['arguments'][1],'true') === 0) {
        FOLLOWINGCLASS::byNew(PLAYERCLASS::byMe()->Id(),$user->Id());
    } else {
        FOLLOWINGCLASS::byFollowing(PLAYERCLASS::byMe()->Id(),$user->Id())->_delete();
    }
}
