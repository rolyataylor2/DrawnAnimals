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

    class BATTLEREQUESTCLASS extends TABLETEMPLATE {
        function __construct () { parent::__construct('dr_battles', 'requests'); }
        
        // Constructors
        function byNew ( $uid ) {
            $oldrequest = BATTLEREQUESTCLASS::byUserIdByRequestIdByActive($uid,PLAYERCLASS::byMe()->Id());
            if (!empty($oldrequest->Id())) { return $oldrequest; }
            return parent::_new('BATTLEREQUESTCLASS',['uid','uid_from','date_time'],[$uid,PLAYERCLASS::byMe()->Id(),time()]);
        }
        
        function byUserIdByRequestIdByActive($uid,$uid_from) { return parent::_get('BATTLEREQUESTCLASS',['uid','uid_from','date_time'], [$uid,$uid_from,time()-300],'',['=','=','>='])[0]; }
        function byUserIdByActive($uid) { return parent::_get('BATTLEREQUESTCLASS',['uid','date_time'], [$uid,time()-300],'',['=','>=']); }
        function byRequestIdByActive($uid_from) { return parent::_get('BATTLEREQUESTCLASS',['uid_from','date_time'], [$uid_from,time()-300],'',['=','>=']); }
        function byUserId($uid) { return parent::_get('BATTLEREQUESTCLASS',['uid'], [$uid],'',['=']); }
        function byUserFromId($uid) { return parent::_get('BATTLEREQUESTCLASS',['uid_from'], [$uid],'',['=']); }
        function doClearExpired($uid,$uid_from) {
            $instance = new BATTLEREQUESTCLASS();
            SQL()->select_db($instance->databasePrefix . $instance->database);
            $time = time()-300;
            $STMT = SQL()->prepare('DELETE FROM ' . $instance->table . " WHERE (uid=? AND uid_from=? AND date_time < $time) OR (uid=? AND uid_from=? AND date_time < $time)") or die(SQL()->error);
            $STMT->bind_param('iiii',$uid,$uid_from,$uid_from,$uid);
            $STMT->execute();
            $STMT->close();
            return SQL()->error;
        }
        // Class Functions
        function Id () {return $this->_var('id');}
        function UserId() { return $this->_var('uid'); }
        function UserFrom( ) { return $this->_var('uid_from'); }
        function Time () {return $this->_var('date_time');}
    }