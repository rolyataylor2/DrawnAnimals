<?php
session_start();
include_once 'html/_php/mysqli.php';
include_once 'html/_php/class-player.php';
include_once 'html/_php/class-monsters.php';

if (isset($_POST['token']) && $_POST['token'] === $_SESSION['token']) {
    $monster = CREATEMONSTERCLASS::byId($_POST['itemId']);
    if ($monster->UserId() === PLAYERCLASS::byMe()->Id()) {
        $monster->_delete();
        die('Reloading... <script>location.reload(true);</script>');
    }
    die('You are not the creator of this monster...');
}

$token = $_SESSION['token'] = uniqid();
?>
<h1>Delete This Monster?</h1>
<sub>Are you sure you want to remove this monster from the global dex and delete all data associated with it?</sub><br/>
<button type="button" onclick="inlinePopupSubmit({token:'<?php echo $token; ?>', itemId:<?php echo $_GET['arguments'][0]; ?>},'Monster%20Dex/monsterDelete')">Ok, Delete this monster</button>