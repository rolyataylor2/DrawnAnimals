<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<style>
    .friendsMenu {
        float:left; width:90%; height:80%; margin:4% 5%;
    }
    .friendsMenu .friend {
        float:left; width:100%; height:64px; background-color:white; border-radius:20px;
        overflow:hidden;
    }
</style>
<div class="friendsMenu">
    <div class="friend">Friend 1</div>
</div>
<script>
    var toolbar = '<a href="javascript:Menu.hide();"><img src="img/sit/toolbar/back.png"/>Close</a>';
    toolbar += '<a style="float:right;" href="javascript:Menu.hide();"><img src="img/sit/toolbar/back.png"/>Rivels</a>';
    toolbar += '<a style="float:right;" href="javascript:Menu.hide();"><img src="img/sit/toolbar/back.png"/>Strangers</a>';
    toolbar += '<a style="float:right;" href="javascript:Menu.hide();"><img src="img/sit/toolbar/back.png"/>Battlers</a>';
    Menu.addToolbar(toolbar);
</script>