<?php

switch(rand(0,2)) {
    case 0: 
        
        break;
    case 1:
        break;
    case 2:
        break;
    default:
        break;
}
$exp = PLAYERCLASS::byMe()->Experience();
$exp->Coins(1000);
$exp->_save();
$arguments['ONLOAD'] = 'inlinePopupSubmit({"id":0,"amount":1000},"rewardShow");';