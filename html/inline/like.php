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
include_once 'html/_php/mysqli.php';
include_once 'html/_php/class-player.php';
include_once 'html/_php/class-monsters.php';
include_once 'html/_php/class-region-maps.php';
include_once 'html/_php/class-like.php';
if (!LoggedIn()) die('not logged in');

$id = intval($_GET['arguments'][1]);
switch($_GET['arguments'][0]) {
    case 'createMonster':
        $item = CREATEMONSTERCLASS::byId($id);
        if ($item->Like() === false) die('failed');
        break;
    case 'createMap':
        $item = CREATEREGIONMAPCLASS::byId($id);
        if ($item->Like() === false) die('failed');
        break;
    case 'eggHatch':
        if (!LIKECLASS::byNew(PLAYERCLASS::byMe()->Id(),'eggHatch',$id)) {
            die('failed');
        } else {
            $item = MONSTERCLASS::byId($id);
            if ($item->Egg()) {
                $item->EggWarm(true);
                $item->_save();
            }
        }
        break;
}
        

