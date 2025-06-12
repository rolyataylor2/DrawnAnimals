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
include_once 'html/_php/class-monsters.php';
$arguments['LOCATION'] = [];
$arguments['LOCATION']['name'] = 'Laboratory';
$arguments['LOCATION']['background'] = 'lab.jpg';
$arguments['LOCATION']['character'] = 'prof';
$arguments['LOCATION']['isform'] = false;

$arguments['LOCATION']['dialog'] = [];
$status = PLAYERCLASS::byMe()->Variable('laboratory/prof-oak');
if (VerifyPostToken()) {
    if (empty($status->Id())) {
        $player = PLAYERCLASS::byMe();
        $player->Gender($_POST['gender']);
        $player->_save();
        $species = CREATEMONSTERCLASS::byId(rand(0,400));
        $monster = MONSTERCLASS::byNew($player,$species,5);
        $status = $player->Variable('laboratory/prof-oak',1);
    }
    
}
if (empty($status->Id())) {
    $arguments['LOCATION']['isform'] = true;
    $arguments['LOCATION']['dialog'][] = ['text'=>'<input type="hidden" name="g" value="laboratory"/>Hello nice to meet you',
                                          'name'=>'???'];
    $arguments['LOCATION']['dialog'][] = ['text'=>'My name is Prof. Oak, I am a pokemon professor!',
                                          'name'=>'Prof Oak',
                                          'pageprev'=>'changeCharacter("prof",25,"outToLeft","inFromRight");'];
    $arguments['LOCATION']['dialog'][] = ['text'=>'Hi How are you? It is very nice to meet new trainers!! My name is...',
                                          'name'=>'???',
                                          'pagenext'=>'changeCharacter("prof-assist",25,"outToRight","inFromLeft");',
                                          'pageprev'=>'changeCharacter("prof-assist",25,"outToRight","inFromLeft");'];
    $arguments['LOCATION']['dialog'][] = ['text'=>'Yes yes yes.. Her name is Doctor Fennel, she is visiting from Striaton City. She studies pokemon dreams. But that isnt important right now...',
                                          'name'=>'Prof Oak',
                                          'pagenext'=>'changeCharacter("prof",25,"outToLeft","inFromRight");'];
    $arguments['LOCATION']['dialog'][] = ['text'=>'Eh I forget... Are you a boy or a girl?'
                                                . '<label>Boy<input type="radio" name="gender" value="0"/></label>'
                                                . '<label>Girl<input type="radio" name="gender" value="1"/></label>'
                                                . '<label>Neither<input type="radio" name="gender" value="2" checked="checked"/></label>',
                                          'name'=>'Prof Oak'];
    $arguments['LOCATION']['dialog'][] = ['text'=>'Sorry about that... I\'m getting old, I think I may be getting senial...',
                                          'name'=>'Prof Oak'];
    $arguments['LOCATION']['dialog'][] = ['text'=>'Anyway it is about time to start your journey!',
                                          'name'=>'Prof Oak'];
} else {
    switch($status->Value()) {
        case 1: 
            $arguments['LOCATION']['dialog'][] = ['text'=>'Ok thank you very much, I have entered your information into this device.',
                                                  'name'=>'Prof Oak'];
            $arguments['LOCATION']['dialog'][] = ['text'=>'It is called a Pokedex. It will help you keep track of the pokemon you have captured. It will also give you helpful information about the pokemon around you. Use it as often as possible.',
                                                  'name'=>'Prof Oak'];
            $arguments['LOCATION']['dialog'][] = ['text'=>'I almost forgot something.',
                                                  'name'=>'Prof Oak',
                                                  'pageprev'=>'changeCharacter("prof",25,"outToLeft","inFromRight");'];
            $arguments['LOCATION']['dialog'][] = ['text'=>'Here is your "'.$species->Name().'"! Congratulations you are on your way to becoming a pokemon master.',
                                                  'name'=>'Prof Oak',
                                                  'pagenext'=>'changeCharacter("http://www.drawnimals.com/img/_games/Monster Dex/uploads/'.$species->Id().'.g_3.c_.png",25,"outToRight","inFromLeft");',
                                                  'pageprev'=>'changeCharacter("http://www.drawnimals.com/img/_games/Monster Dex/uploads/'.$species->Id().'.g_3.c_.png",25,"outToRight","inFromLeft");'];
            $arguments['LOCATION']['dialog'][] = ['text'=>'Come back anytime and I will check on your progress and, if I can, I will help you out.',
                                                  'name'=>'Prof Oak',
                                                  'pagenext'=>'changeCharacter("prof",25,"outToLeft","inFromRight");'];
            $status->Value(2);
            $status->_save();
            break;
        case 2:
            $arguments['LOCATION']['dialog'][] = ['text'=>'Come back anytime and I will check on your progress and, if I can, I will help you out.',
                                                  'name'=>'Prof Oak'];
            break;
    }
}
$arguments['TOKEN'] = $_SESSION['token'] = uniqid();



        
