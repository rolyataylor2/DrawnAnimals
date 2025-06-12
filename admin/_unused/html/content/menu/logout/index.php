<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<style>
    .logoutMenu {width: 80%; height: auto;
                margin: 150px auto;
                padding:20px;
                background-color: #0A246A;
                border-radius: 10px 40px;
                overflow: hidden;
                text-align:center;
                font-size:150%;
                color:white;
    }
</style>
<div class="logoutMenu">
Are You Sure You Want To Logout?
</div>
<script>
    var toolbar = '<a href="javascript:Menu.hide();"><img src="img/sit/toolbar/back.png"/>Close</a>';
    toolbar += '<a style="float:right;" href="javascript:Menu.LogoutConfirmed();"><img src="img/sit/toolbar/back.png"/>Logout</a>';
                
    Menu.addToolbar(toolbar);
</script>