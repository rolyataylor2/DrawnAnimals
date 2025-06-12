<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<style>
    .mainTable {float:left; width:80%; height:80%; margin:50px 10%; }
    .mainTable a {display: block;
                width: 80%; height: 80%;
                margin: 0 5%; padding: 2.5% 0;
                text-align: center; font-size: 110%;
                text-decoration: none;
                color: white; text-shadow: 1px 1px 2px #333;
                border-radius: 5px 40px;
                background-color: rgb(58, 66, 155);
                box-shadow: 2px 2px 2px rgba(0, 0, 0, 1), inset 0px 0px 10px rgba(0, 0, 0, 1);
                border: 5px solid #FFF;
                transition: 1s all; -webkit-transition: 0.2s all;
    }
    .mainTable a:hover { border-color:orange;
            box-shadow:2px 2px 2px rgba(0,0,0,0.5), inset 0px 0px 20px rgba(100,0,100,0.75);}
    .mainTable a img {width:30%; max-width:48px; }
</style>
<table class='mainTable'>
    <tr>
        <td><a href="javascript:Menu.Party()"><img src="img/sit/menu/party.png"/><br/>Party</a></td>
        <td><a href="javascript:Menu.Pokedex();"><img src="img/sit/menu/pokedex.png"/><br/>Pokedex</a></td>
    </tr>
    <tr>
        <td><a href="javascript:Menu.Bag();"><img src="img/sit/menu/bag.png"/><br/>Bag</a></td>
        <td><a href="javascript:Menu.Profile();"><img src="img/sit/menu/profile.png"/><br/>Profile</a></td>
    </tr>
    <tr>
        <td><a href="javascript:Menu.Messages();"><img src="img/sit/menu/messages.png"/><br/>Messages</a></td>
        <td><a href="javascript:Menu.Settings();"><img src="img/sit/menu/settings.png"/><br/>Settings</a></td>
    </tr>
    <tr>
        <td><a href="javascript:Menu.Friends();"><img src="img/sit/menu/friends.png"/><br/>Friends</a></td>
        <td><a href="javascript:Menu.Logout();"><img src="img/sit/menu/logout.png"/><br/>Logout</a></td>
    </tr>
</table>
<script>
    var toolbar = '<a href="javascript:Menu.hide();"><img src="img/sit/toolbar/back.png"/>Close</a>';
    Menu.addToolbar(toolbar);
</script>