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
include_once 'html/_php/TWIG-Var.php';
include_once 'html/_php/class-player.php';
include_once 'html/_php/class-updates.php';
$results = [];
foreach(UPDATECLASS::byRandom() as $i) {
    $results[] = $i->Render();
}
die(json_encode($results));
