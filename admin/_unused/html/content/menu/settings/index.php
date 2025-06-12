<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<style>
    .settingsMenu {
        float:left; width:80%; margin:50px 10%;
        background-color:white; border-radius:20px; box-shadow:0 0 4px black, inset 0 0 15px rgba(0,0,100,0.6);
        overflow:hidden; height:auto;
    }
    .settingsMenu label {
        display: block;
        width: 80%;
        border-bottom: 1px solid rgb(141, 141, 141);
        padding: 10px 10%;
        font-size: 120%;
        font-weight: bolder;
        text-shadow: 2px 2px 3px #CCC;
    }
    .settingsMenu label input {float:right;}
    .settingsMenu label.title {background-color:#333; color:white; text-shadow: 2px 2px 3px #000; text-align:center; }
</style>
<form class='settingsMenu'>
    <label class='title'>General</label>
    <label>Sound Effects<input name="soundEffects" type="checkbox" checked /></label>
    <label>Music <input name="soundBGM" type="checkbox" checked/></label>
    <label class='title'>Show Widgets</label>
    <label>Team<input name="showTeam" type="checkbox" checked onchange='GUIEditChanged();'/></label>
    <label>My Status<input name="showPlayer" type="checkbox" checked onchange='GUIEditChanged();'/></label>
    <label>Chat<input name="showChat" type="checkbox" checked onchange='GUIEditChanged();'/></label>
    <label>Clock<input name="showClock" type="checkbox" checked onchange='GUIEditChanged();'/></label>
    <label>Online Now<input name="showOnline" type="checkbox" checked onchange='GUIEditChanged();'/></label>
    <label>Map<input name="showMap" type="checkbox" checked onchange='GUIEditChanged();'/></label>
    <label onclick='$(".draggable .handle").toggle(100);'>Edit Positions</label>
    <label onclick='GUIEditChanged(true);'>Hide Widgets</label>
    <label class='title'>Team Widget</label>
    <label>Only Show Lead<input name="teamShowAll" type="checkbox" checked onchange='GUIEditChanged();'/></label>
    <label>Large Lead<input name="teamLargeFirst" type="checkbox" checked onchange='GUIEditChanged();'/></label>
    <label>Vertical<input name="teamVertical" type="checkbox" checked onchange='GUIEditChanged();'/></label>
    <label class='title'>Chat Widget</label>
    <label>Fade Out Old<input type='checkbox'/></label>
    <label>Semi-Transparent<input type='checkbox'/></label>
    <label>Show When Using Only<input type='checkbox'/></label>
    <label class='title'>Other</label>
    <label><a href='javascript:Menu.SettingsChangeTheme();'>Change Theme</a></label>
</form>
<script>
    var toolbar = '<a href="javascript:Menu.hide();"><img src="img/sit/toolbar/cancel.png"/>Cancel</a>';
    toolbar += '<a style="float:right;" href="javascript:Menu.hide();"><img src="img/sit/toolbar/confirm.png"/>Save</a>';
    Menu.addToolbar(toolbar);
</script>