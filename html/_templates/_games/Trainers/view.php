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
include_once 'html/_php/class-trainers.php';
include_once 'html/_php/class-regions.php';
$trainer = CREATETRAINERCLASS::byId($_GET['id']);
$trainer->_load();
$trainer->data['label'] =  $trainer->Type()->Name();
$trainer->data['region'] =  $trainer->Region()->Name();
$arguments['TRAINER'] = $trainer->data;
$arguments['EDIT'] = ($trainer->UserId()==PLAYERCLASS::byMe()->Id());

$arguments['TEAMS'] = [];
$arguments['TEAMSWIN'] = [];
foreach(CREATETRAINERCLASSMONSTER::byTrainerId($_GET['id']) as $i) {
    if (empty($i->Id())) continue;
    if (!isset($arguments['TEAMS'][$i->Team()])) {
        $arguments['TEAMSWIN'][$i->Team()] = PLAYERCLASSTRAINERS::byUserIdByTrainerByTeam(PLAYERCLASS::byMe()->Id(),$_GET['id'],$i->Team())->Win();
        $arguments['TEAMS'][$i->Team()] = [];
    }
    
    $arguments['TEAMS'][$i->Team()][] = array('id'=>$i->Species()->Id());
}



