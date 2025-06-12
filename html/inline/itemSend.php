<?php
    session_start();
    include_once 'html/_php/mysqli.php';
    include_once 'html/_php/class-player.php';
    include_once 'html/_php/class-items.php';
    include_once 'html/_php/class-messages.php';
    
    if (isset($_POST['token']) && strcmp($_SESSION['token'],$_POST['token']) === 0) {
        $item = ITEMCLASS::byId($_POST['id']);
        $user = PLAYERCLASS::byUsername($_POST['username']);
        if ($user->Id() === 0) die('<div class="error">User does not exist.</div>');
        if (PLAYERCLASS::byMe()->Id() !== $item->UserId()) die('<div class="error">This is not your item.</div>');
        
        $item->Send($user->Id(),$_POST['note']);
        die('Reloading...<script>location.reload(true);</script>');
    }
    
    $item = ITEMCLASS::byId($_GET['arguments'][0]);
    $itemtype = $item->Type();
    
    $token = $_SESSION['token'] = uniqid();
?>
<h1>Send Item?</h1>
<sub>Type in a user to send this item too</sub>
<div class='item'>
    <div class='name'><?php echo $itemtype->Name(); ?></div>
</div>
<form onsubmit='inlinePopupSubmit($(this),"itemSend"); return false;'>
    <input type='hidden' name='id' value='<?php echo $_GET['arguments'][0]; ?>'/>
    <input type='hidden' name='token' value='<?php echo $token; ?>'/>
    <label>Username<input type="text" name="username"/></label>
    <label>Message
        <textarea name='note'>Here is a Item!</textarea></label>
    <input type="submit" value='Send Item'/>
</form>