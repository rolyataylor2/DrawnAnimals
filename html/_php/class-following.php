<?php

class FOLLOWINGCLASS extends TABLETEMPLATE {
    function __construct () {
        parent::__construct('nicapa', 'following');
        $this->className = 'FOLLOWINGCLASS';
    }
    // Constructors
    function byNew($userid,$followid) {
        $instance = new self();
        $instance->connection->select_db($instance->databasePrefix . $instance->database);
        $STMT = $instance->connection->prepare('INSERT INTO ' . $instance->table . '(uid,fid,time) VALUES(?,?,?)') or die(SQL()->error);
        $STMT->bind_param('sss', $userid, $followid,time());
        $STMT->execute();
        $STMT->close();
        $instance->searchKey = 'id';
        $instance->searchValue = $instance->connection->insert_id;
        return $instance;
    }
    function byUserId ($uid) {
        $instance = new self();
        $instance->connection->select_db($instance->databasePrefix . $instance->database);
        $STMT = $instance->connection->prepare('SELECT id,uid,fid FROM ' . $instance->table . ' AS p WHERE uid=? AND NOT EXISTS (SELECT * FROM ' . $instance->table . ' WHERE fid=p.uid AND uid=p.fid )') or die(SQL()->error);
        $STMT->bind_param('i',$uid);
        $STMT->execute();
        $result = $STMT->get_result();
        $list = array();
        $class = 'FOLLOWINGCLASS';
        while($value = $result->fetch_row()) {
            $cached = $instance->_getCache($class,$value[0]);
            if ($cached !== null) {
                $list[] = $cached;
            } else {
                $instance->searchKey = 'id';
                $instance->searchValue = $value[0];
                $list[] = $instance->_setCache($class,$value[0],$instance);
                $instance = new $class();
            }
        }
        if (count($list) === 0) {
            $instance->searchKey = 'id';
            $instance->searchValue = 0;
            $list[] = $instance;
        }
        return $list; 
    }
    function byFollowingId ($uid) {
        $instance = new self();
        $instance->connection->select_db($instance->databasePrefix . $instance->database);
        $STMT = $instance->connection->prepare('SELECT id,uid,fid FROM ' . $instance->table . ' AS p WHERE fid=? AND NOT EXISTS (SELECT * FROM ' . $instance->table . ' WHERE uid=p.fid AND fid=p.uid )') or die(SQL()->error);
        $STMT->bind_param('i',$uid);
        $STMT->execute();
        $result = $STMT->get_result();
        $list = array();
        $class = 'FOLLOWINGCLASS';
        while($value = $result->fetch_row()) {
            $cached = $instance->_getCache($class,$value[0]);
            if ($cached !== null) {
                $list[] = $cached;
            } else {
                $instance->searchKey = 'id';
                $instance->searchValue = $value[0];
                $list[] = $instance->_setCache($class,$value[0],$instance);
                $instance = new $class();
            }
        }
        if (count($list) === 0) {
            $instance->searchKey = 'id';
            $instance->searchValue = 0;
            $list[] = $instance;
        }
        return $list;  
    }
    function byFollowing($uid,$fid) {
        return parent::_get('FOLLOWINGCLASS',['uid','fid'],[$uid,$fid])[0];
    }
    function byFriend($uid) {
        $instance = new self();
        $instance->connection->select_db($instance->databasePrefix . $instance->database);
        $STMT = $instance->connection->prepare('SELECT id,uid,fid FROM ' . $instance->table . ' AS p WHERE uid=? AND EXISTS (SELECT * FROM ' . $instance->table . ' WHERE fid=p.uid AND uid=p.fid )') or die(SQL()->error);
        $STMT->bind_param('i',$uid);
        $STMT->execute();
        $result = $STMT->get_result();
        $list = array();
        $class = 'FOLLOWINGCLASS';
        while($value = $result->fetch_row()) {
            $cached = $instance->_getCache($class,$value[0]);
            if ($cached !== null) {
                $list[] = $cached;
            } else {
                $instance->searchKey = 'id';
                $instance->searchValue = $value[0];
                $list[] = $instance->_setCache($class,$value[0],$instance);
                $instance = new $class();
            }
        }
        if (count($list) === 0) {
            $instance->searchKey = 'id';
            $instance->searchValue = 0;
            $list[] = $instance;
        }
        return $list;    
    }
    // Class Functions
    function Id() {
        return $this->_var('id');
    }
    function UserId ($value = null) {
        return $this->_var('uid', $value);
    }
    function FollowingId ($value = null) {
        return $this->_var('fid', $value);
    }
    function Time() {
        return date('F, jS Y',$this->_var('time'));
    }
}
