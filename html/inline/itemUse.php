<?php
    session_start();
    include_once 'html/_php/mysqli.php';
    include_once 'html/_php/class-player.php';
    include_once 'html/_php/class-items.php';
    
    $item = ITEMCLASS::byId($_GET['arguments'][0]);
    $itemtype = $item->Type();
?>
<h1><?php echo $itemtype->Name(); ?></h1>
<sub><?php echo (strcmp($itemtype->Description(),'')===0?'No Description Availiable':$itemtype->Description()); ?></sub>
<img src='<?php echo $itemtype->Image(); ?>'/>
<ul>
    <?php echo (strcmp($itemtype->Markup(),'')===0?'<li>Item cant be used here!</li>':$itemtype->Markup()); ?>
</ul>
