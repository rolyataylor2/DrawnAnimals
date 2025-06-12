<?php
session_start();
include_once 'html/_php/mysqli.php';
include_once 'html/_php/class-player.php';
include_once 'html/_php/class-monsters.php';

if (isset($_POST['token']) && $_POST['token'] === $_SESSION['token']) {
    CREATEMONSTERCLASS::byNew(PLAYERCLASS::byMe()->Id(),uniqid('NewItem'));
    die('Reloading... <script>location.reload(true);</script>');
}

$token = $_SESSION['token'] = uniqid();
?>
<h1>Create a new monster?</h1>
<sub>Create a new item to add to the global Monster Dex?</sub><br/>
<button type="button" onclick="inlinePopupSubmit({token:'<?php echo $token; ?>'},'Monster%20Dex/monsterCreate')">Ok, Create New Monster</button>