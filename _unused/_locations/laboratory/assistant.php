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
include_once 'html/_php/class-items.php';
$arguments['LOCATION'] = [];
$arguments['LOCATION']['name'] = 'Laboratory';
$arguments['LOCATION']['background'] = 'lab.jpg';
$arguments['LOCATION']['character'] = 'prof-assist';
$arguments['LOCATION']['dialog'] = [];
$status = PLAYERCLASS::byMe()->Variable('laboratory/assist');
if (empty($status->Id())) {
    $arguments['LOCATION']['dialog'][] = ['text'=>'Hi, I\'m new here. Nice to meet you! My name is Doctor Fennel.',
                                        'name'=>'Doctor Fennel'];
    $arguments['LOCATION']['dialog'][] = ['text'=>'I am here helping Oak while my dream study clinic is being renovated.',
                                    'name'=>'Doctor Fennel'];
    PLAYERCLASS::byMe()->Variable('laboratory/assist',1);
} else {
    switch($status->Value()) {
        case 1:
            $arguments['LOCATION']['dialog'][] = ['text'=>'Good luck on your journey.',
                                        'name'=>'Doctor Fennel'];
            if ($status->_var('date_time') < strtotime('last sunday')) {
                $arguments['LOCATION']['dialog'][] = ['text'=>'I hope you are well supplied. Here take this Potion, it will help you out on your journey.',
                                        'name'=>'Doctor Fennel'];
                ITEMCLASS::byNew(PLAYERCLASS::byMe()->Id(),  CREATEITEMCLASS::byName('potion')->Id());
                PLAYERCLASS::byMe()->Variable('laboratory/assist',1);
            }
            break;
    }

}