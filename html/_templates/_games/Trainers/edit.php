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
include_once 'html/_php/class-html-code-table.php';
include_once 'html/_php/class-trainers.php';

if (isset($_GET['id'])) {
    $trainer = CREATETRAINERCLASS::byId($_GET['id']);
    $trainer->_load();
    $arguments['TRAINER'] = $trainer->data;
    
    $teamtable = HTMLLISTABLETABLECLASS::byNew('TeamTable');
    $teamtable->addLabel('Team Number');
    $teamtable->addLabel('Pokemon');
    $teamtable->addLabel('Level');
    $teamtable->addLabel('Difficulty');
    $arguments['TEAMTABLE'] = $teamtable->renderHTML();
    
    $rewardtable = HTMLCODETABLECLASS::byNew('RewardTable');
    $rewardtable->createFunction('Give Item')->addText('Give Winner A Item');
    $arguments['REWARDTABLE'] = $rewardtable->renderHTML();
    
    $arguments['CLASSES'] = [];
    foreach(CREATETRAINERCLASSCLASS::byAll() as $i) {
        $arguments['CLASSES'][] = $i->Name();
    }


    include_once 'html/_php/class-regions.php';
    $arguments['REGIONS'] = [];
    foreach(CREATEREGIONCLASS::byAll() as $i) {
        $arguments['REGIONS'][] = $i->Name();
    }
}



