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
class UPDATECLASS extends TABLETEMPLATE {
    function __construct () {
        parent::__construct('nicapa', 'updates');
        $this->className = 'UPDATECLASS';
    }
    function byNew($uid,$args,$type=0) {
        $instance = new self();
        $instance->connection->select_db($instance->databasePrefix . $instance->database);
        $STMT = $instance->connection->prepare('INSERT INTO ' . $instance->table . '(uid,args,type,time) VALUES(?,?,?,?)') or die(SQL()->error);
        $STMT->bind_param('isii', $uid, $args, $type, time());
        $STMT->execute();
        $STMT->close();
        $instance->searchKey = 'id';
        $instance->searchValue = $instance->connection->insert_id;
        return $instance;
    }
    function byAll () {
        return parent::_loadArray('id!=?',0,'UPDATECLASS');
    }
    function byUser($uid) {
        return parent::_loadArray('uid=?',$uid,'UPDATECLASS',' ORDER BY time DESC LIMIT 30');
    }
    function byUserByLast($uid) {
        return parent::_get('UPDATECLASS',['uid','type'],[$uid,0],' ORDER BY time DESC LIMIT 1');
    }
    function byRandom() {
        return parent::_get('UPDATECLASS',['id','type','type','type'],[0,7,8,1],' ORDER BY RAND() LIMIT 10',['!=','!=','!=','!=']);
    }
    function Id () {
        return $this->_var('id');
    }
    function Type () {
        return $this->_var('type');
    }
    function UserId ($value = null) {
        return $this->_var('uid', $value);
    }
    function Args ($value = null) {
        return $this->_var('args', $value);
    }
    function Render () {
        if (file_exists('/var/www/html/_templates/_plugin/updates/'.$this->Type().'.twig')) {
            $args = array();
            $args['ARGS'] = explode('|',$this->Args());
            $args['USERNAME'] = PLAYERCLASS::byId($this->UserId())->Username();
            $args['TIME'] = $this->Time();
            return TWIG()->render('html/_templates/_plugin/updates/'.$this->Type().'.twig',$args);
        } else {return $this->Args();}
    }
    function Time ($format='F, l jS') {
        return date($format,$this->_var('time'));
    }
}
