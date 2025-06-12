<?php
include_once 'html/_php/class-regions.php';
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
$region = CREATEREGIONCLASS::byId($_GET['id']);
$region->_load();
$arguments['REGION'] = $region->data;
$arguments['EDIT'] = ($region->UserId() == PLAYERCLASS::byMe()->Id());
$arguments['STARTERS'] = [];
foreach(CREATEMONSTERCLASS::byRegionByStarter($region->Id()) as $i) {
    $i->_load();
    $arguments['STARTERS'][] = $i->data;
}
    
$arguments["MAPGRID"] = [];
for($i=0;$i<20;$i++) {
    $arguments['MAPGRID'][] = [];
    for($ii=0;$ii<20;$ii++) {
        $arguments['MAPGRID'][$i][] = 0;
    }
}
$arguments['MAPS'] = [];