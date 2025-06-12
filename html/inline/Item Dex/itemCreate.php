<?php
session_start();
include_once 'html/_php/mysqli.php';
include_once 'html/_php/class-player.php';
include_once 'html/_php/class-items.php';

if (isset($_POST['token']) && $_POST['token'] === $_SESSION['token']) {
    CREATEITEMCLASS::byNew(PLAYERCLASS::byMe()->Id(),uniqid('NewItem'));
    die('Reloading... <script>location.reload(true);</script>');
}

$token = $_SESSION['token'] = uniqid();
?>
<h1>Create a new item?</h1>
<sub>Create a new item to add to the global Item Dex?</sub><br/>
<button type="button" onclick="inlinePopupSubmit({token:'<?php echo $token; ?>'},'Item%20Dex/itemCreate')">Ok, Create New Item</button>