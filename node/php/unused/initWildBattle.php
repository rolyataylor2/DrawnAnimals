<?php
// ARGUMENT1 = session_id, ARGUMENT2 = GrassType/LocationId
$userId = $argv[1];
$environmentId = $argv[2];
$grassId = $argv[3];

require_once 'include/Core/index.php';
require_once 'include/Core/Battle/class-battle.php';
$battle = BATTLECLASS::byNew();
$battle->SetupAddWild(CATALOGDRAWNIMALCLASS::byId(floor(mt_rand(1,600))),20);
$battle->SetupAddPlayer(PLAYERCLASS::byId($userId));
$battle->SetupFinalize();

die($battle->Id());


