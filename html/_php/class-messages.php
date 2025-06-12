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


/**
 * Description of class-items
 *
 * @author User
 */
class MESSAGECLASS extends TABLETEMPLATE {
    function __construct () {
        parent::__construct('nicapa', 'messages');
        $this->className = 'MESSAGECLASS';
    }
    function byNew($uid,$to,$message) { return parent::_new('MESSAGECLASS',['uid','sendto','body','time'],[$uid,$to,$message,time()]); }
    function byAll () {
        return parent::_loadArray('id!=?',0,'MESSAGECLASS');
    }
    function byId ($id) {
        return parent::_loadArray('id=?',$id,'MESSAGECLASS')[0];
    }
    function byUser($uid) {
        return parent::_loadArray('uid=?',$uid,'MESSAGECLASS',' ORDER BY time DESC LIMIT 30');
    }
    function bySendTo($uid) {
        return parent::_get('MESSAGECLASS',['sendto'],[$uid],' ORDER BY time DESC');
    }
    
    function countUserByUnseen($uid) { return parent::_count('MESSAGECLASS',['sendto','status'],[$uid,0]); }
    
    function doMarkAllSeen() {
        SQL()->query('UPDATE nicapa.messages SET status=1 WHERE sendto='.PLAYERCLASS::byMe()->Id().' AND status=0') or die(SQL()->error);
    }
    
    function Id () {
        return $this->_var('id');
    }
    function Status ($value=null) {
        return $this->_var('status',$value);
    }
    function From () {
        return $this->_var('uid');
    }
    function To ($value = null) {
        return $this->_var('sendto', $value);
    }
    function Message ($value = null) {
        return $this->_var('body', $value);
    }
    function Time ($format='F, l jS') {
        return date($format,$this->_var('time'));
    }
    function Item($value=null) {
        return $this->_var('item', $value);
    }
}
