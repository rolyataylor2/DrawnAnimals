<?php
    require_once 'PLYR-Class.php';
    $letters = PLYR()->Letter();
    
    /** Template Class */
    echo TWIG()->renderFind(__FILE__,array('LETTERS'=>$letters));
    
?>
<style>
    .messageMenu {
        float:left; width:90%; height:80%; margin:4% 5%;
    }
    .messageMenu .message {
        float:left; width:100%; height:64px; background-color:white; border-radius:20px;
        overflow:hidden;
    }
</style>
<div class="messageMenu">
    <?php
    $letters = $GLOBALS['Myself']->GetLetters();
    while($letter = array_pop($letters)):
        printf('<div class="message"><div class="sender">%s</div>%s</div>',
                $letter['sender'],
                $letter['subject']);
    endwhile;
    ?>
</div>
<script>
    var toolbar = '<a href="javascript:Menu.hide();"><img src="img/sit/toolbar/back.png"/>Close</a>';
    Menu.addToolbar(toolbar);
</script>