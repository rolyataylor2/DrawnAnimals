<?php
session_start();
include_once 'html/_php/mysqli.php';
include_once 'html/_php/class-player.php';
include_once 'html/_php/class-items.php';

if (isset($_POST['token']) && $_POST['token'] === $_SESSION['token']) {
    $item = CREATEITEMCLASS::byId($_POST['itemId']);
    if ($item->UserId() === PLAYERCLASS::byMe()->Id()) {
        $item->_delete();
        die('Reloading... <script>location.reload(true);</script>');
    }
    die('You are not the creator of this item...');
}

$token = $_SESSION['token'] = uniqid();
?>
<h1>Delete This Item?</h1>
<sub>Are you sure you want to remove this item from the global dex and delete all data associated with it?</sub><br/>
<button type="button" onclick="inlinePopupSubmit({token:'<?php echo $token; ?>', itemId:<?php echo $_GET['arguments'][0]; ?>},'Item%20Dex/itemDelete')">Ok, Delete this item</button>