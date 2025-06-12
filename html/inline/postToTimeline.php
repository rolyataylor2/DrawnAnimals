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
include_once 'html/_php/class-updates.php';
include_once 'html/_php/TWIG-Var.php';
if (strlen($_GET['arguments'][0]) < 0) die('Post not long enough.');
if (strlen($_GET['arguments'][0]) > 256) die('Post to long.');
$update = UPDATECLASS::byNew(PLAYERCLASS::byMe()->Id(),$_GET['arguments'][0],7);
die($update->Render());