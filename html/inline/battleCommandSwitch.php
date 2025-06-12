<?php

$token = $_SESSION['token'] = uniqid();

if ($player->Monster()->byTeamByLeader()->Hp() === 0) {
    $arguments['DEAD'] = true;
} else {
    $arguments['DEAD'] = false;
}


foreach (PLAYERCLASS::byMe()->Monster()->byTeam() as $i) {
    if ($i->BattleLeader() === 1) $onclick = '';
    if ($i->Hp() === 0) $onclick = '';
    $onclick = 'BTTL.sendCommand(\'a=2&v={\\\'id\\\':' . $i->Id() . '}&token=' . $token . '\')';
    echo $i->Render()->badgeHorizontal('hp|onlyalive|onlynonleader|classes|image', 'onclick="'.$onclick.'"');
}
?>
<a href='javascript:' style="background-color:pink;" onclick="BTTL.sendCommand();">Back</a>

