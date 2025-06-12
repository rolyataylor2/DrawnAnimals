<?php
    session_start();
    include_once 'html/_php/mysqli.php';
    include_once 'html/_php/class-player.php';
    include_once 'html/_php/class-items.php';
    if (isset($_POST['token']) && strcmp($_SESSION['token'],$_POST['token']) === 0) {
        $item = ITEMCLASS::byId($_POST['id']);
        if (PLAYERCLASS::byMe()->Id() === $item->UserId()) $item->_delete();
        else die('Item does not belong to you.');
        die('Reloading...<script>location.reload(true);</script>');
    }
    $item = ITEMCLASS::byId($_GET['arguments'][0]);
    $itemtype = $item->Type();
    $token = $_SESSION['token'] = uniqid();
?>
<h1>Discard Item?</h1>
<sub>Are you sure you want to discard this item? Once you click 'Ok' the item will be lost forever...</sub>
<div class='item'>
    <div class='name'><?php echo $itemtype->Name(); ?></div>
</div><br/>

<button type="button" onclick="inlinePopupSubmit({'id':<?php echo $item->Id(); ?>,'token':'<?php echo $token; ?>'},'itemDiscard');">Ok, Discard this Item</button>