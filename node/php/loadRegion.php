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
include_once 'html/_php/class-region-maps.php';
$maps = CREATEREGIONMAPCLASS::byRegion(3,' ');
$collisionmap = [];
foreach($maps as $i) {
    $tiledata = json_decode($i->TileData(),true);
    if (isset($tiledata['collisions'])) {
        $map = [];
        $map['id'] = $i->Id();
        $map['x'] = $i->RegionX()/32;
        $map['y'] = $i->RegionY()/32;
        $map['width'] = $i->Width()/32;
        $map['height'] = $i->Height()/32;
        $map['data'] = $tiledata['collisions'];
        $collisionmap[] = $map;
    }
}
die(json_encode($collisionmap));