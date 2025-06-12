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
    include_once 'html/_php/class-messages.php';
    
    if (isset($_GET['arguments'][1]) && strcmp($_GET['arguments'][1],$_SESSION['token'])===0) {
        $message = MESSAGECLASS::byId($_GET['arguments'][0]);
        if (PLAYERCLASS::byMe()->Id() === $message->To()) {
            $message->_delete();
            die('success');
        } else die('not owner');
    } else die('invalid token');

