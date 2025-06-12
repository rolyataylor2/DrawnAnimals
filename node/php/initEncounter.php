<?php

/* 
 * Copyright (c) 2014 User.
 * All rights reserved. This program and the accompanying materials
 * are made available under the terms of the Eclipse Public License v1.0
 * which accompanies this distribution, and is available at
 * http://www.eclipse.org/legal/epl-v10.html
 *
 * Contributors:
 *    User - initial API and implementation and/or initial documentation
 */
session_start();
include_once 'html/_php/mysqli.php';
include_once 'html/_php/class-player.php';
include_once 'html/_php/class-battle.php';
include_once 'html/_php/class-updates.php';
include_once 'html/_php/class-region-maps-wild.php';
include_once 'html/_php/class-monsters.php';
$uid = $argv[1];
$tile = $argv[2];
$map = $argv[3];
$player = PLAYERCLASS::byId($argv[1]);
session_destroy();
session_id($player->SessionId());
session_start();

$possible = [];
$total = 0;
foreach(CREATEREGIONMAPCLASSWILD::byMapByMethod($map,$tile-2) as $i) {
    if (empty($i->Id())) { continue; }
    $monster = [];
    $monster['monster'] = CREATEMONSTERCLASS::byId($i->Species());
    $monster['minlevel'] = $i->MinLevel();
    $monster['maxlevel'] = $i->MaxLevel();
    
    $monster['chance'] = eval($monster['monster']->AppearanceScript());
    if (empty($monster['chance'])) { $monster['chance'] = 500; }
    $total += $monster['chance'];
    $monster['chance'] = $total;
    
    $possible[] = $monster;
}
$total = rand(0,$total);
foreach($possible as $i) {
    if ($total < $i['chance']) {
        $battle = BATTLECLASS::byNew();
        $battle->SetupAddWild($i['monster'], rand($i['minlevel'],$i['maxlevel']));
        $battle->SetupAddPlayer($player);
        $battle->SetupFinalize();
        die($battle->Id());
    }
}

$battle = BATTLECLASS::byNew();
$battle->SetupAddWild(CREATEMONSTERCLASS::byId(321), rand(3,5));
$battle->SetupAddPlayer($player);
$battle->SetupFinalize();
die($battle->Id());



