<?php

include_once 'html/_php/class-messages.php';
include_once 'html/_php/class-monsters.php';
include_once 'html/_php/class-battle-request.php';
$arguments['notifyMessageCount'] = MESSAGECLASS::countUserByUnseen(PLAYERCLASS::byMe()->Id());
$battleRequests = BATTLEREQUESTCLASS::byUserIdByActive(PLAYERCLASS::byMe()->Id());
if (!empty($battleRequests[0]->Id())) {
    $arguments['notifyBattleRequestCount'] = count($battleRequests);
}
$arguments['notifyCoins'] = PLAYERCLASS::byMe()->Experience()->Coins();
$arguments['notifyCash'] = PLAYERCLASS::byMe()->Experience()->Cash();

$arguments['SITETHEME'] = PLAYERCLASS::byMe()->SiteTheme();
$arguments['SITECOLOR'] = PLAYERCLASS::byMe()->Color();
if (empty($arguments['SITECOLOR'])) $arguments['SITECOLOR'] = '#000';
$color = hex2RGB($arguments['SITECOLOR']);

if ($color['red'] < 128 && $color['blue'] < 128 && $color['green'] < 128) {
    $arguments['SITETEXTCOLOR'] = '#FFF';
} else {
    $arguments['SITETEXTCOLOR'] = '#000';
}

$arguments['MYTEAM'] = [];
foreach(PLAYERCLASS::byMe()->Monster()->byTeam() as $i) {
    if (!empty($i->Id()))
    {$arguments['MYTEAM'][] = array('id'=>$i->Id(),'sid'=>$i->Species()->Id(), 'hp'=>$i->Hp(), 'imageUrl'=>$i->Render()->imageUrl());}
}
// Random Events
