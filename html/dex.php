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
include_once 'html/_php/TWIG-Var.php';
include_once 'html/_php/mysqli.php';
include_once 'html/_php/class-regions.php';
include_once 'html/_php/class-monsters.php';
$arguments = [];

$region = CREATEREGIONCLASS::byId($_GET['id']);
$arguments['REGIONAME'] = $region->Name();

$monsters = CREATEMONSTERCLASS::byRegion($_GET['id'],"ORDER BY number");
$arguments['MONSTERS'] = [];
foreach($monsters as $i) {
    if (strcmp($i->Form(),'Basic') === 0)
    $arguments['MONSTERS'][] = ['image'=>$i->Render()->imageUrl(),
                                'name'=>$i->Name(),
                                'form'=>'',
                                'index'=>$i->Index()];
}
echo TWIG()->render('/html/_templates/dex.twig', $arguments);
