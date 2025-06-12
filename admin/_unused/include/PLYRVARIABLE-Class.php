<?php

$GLOBALS['PLYRVARIABLE_LOADED'] = array();

function PLYRVARIABLEOBJ($uid) {
    if (!isset($GLOBALS['PLYRVARIABLE_LOADED'][$uid])) {
        $GLOBALS['PLYRVARIABLE_LOADED'][$uid] = new PLYRVARIABLECLASS($uid);
    }
    return $GLOBALS['PLYRVARIABLE_LOADED'][$uid];
}

class PLYRVARIABLECLASS {

    function __construct($uid) {
        $this->parent = PLYROBJ($uid);
        $this->variables = array();
    }

    function SetTemp($name, $value) {
        $_SESSION['variables'][$name] = $value;
    }

    function GetTemp($name) {
        if (!isset($_SESSION['variables'][$name])) {
            return '';
        }
        return $_SESSION['variables'][$name];
    }

    function SetGlobal($name, $value) {
        $STMT = SQL()->prepare('DELETE FROM user_variables WHERE uid=0 AND name=?');
        $STMT->bind_param('s', $name);
        $STMT->execute();
        $STMT->close();

        $STMT = SQL()->prepare('INSERT INTO user_variables(uid,name,value) VALUES(0,?,?)');
        $STMT->bind_param('ss', $name, $value);
        $STMT->execute();
        $STMT->close();
    }

    function GetGlobal($name) {
        $STMT = SQL()->prepare('SELECT value FROM user_variables WHERE uid=0 AND name=?');
        $STMT->bind_param('s', $name);
        $STMT->execute();
        if (($result = $STMT->get_result()) === false || $result->num_rows === 0) {
            $STMT->close();
            return '';
        }
        $STMT->close();
        $row = $result->fetch_row();
        return $row[0];
    }

    function Set($name, $value) {
        $STMT = SQL()->prepare('DELETE FROM user_variables WHERE uid=? AND name=?');
        $STMT->bind_param('is', $this->parent->id, $name);
        $STMT->execute();
        $STMT->close();

        $STMT = SQL()->prepare('INSERT INTO user_variables(uid,name,value) VALUES(?,?,?)');
        $STMT->bind_param('sis', $this->parent->id, $name, $value);
        $STMT->execute();
        $STMT->close();

        $this->variables[$name] = $value;
    }

    function Get($name) {
        if (isset($this->variables[$name])) {
            return $this->variables[$name];
        }
        $STMT = SQL()->prepare('SELECT value FROM user_variables WHERE uid=? AND name=?');
        $STMT->bind_param('is', $this->parent->id, $name);
        $STMT->execute();
        if (($result = $STMT->get_result()) === false || $result->num_rows === 0) {
            $STMT->close();
            return '';
        }
        $STMT->close();
        $row = $result->fetch_row();
        $this->variables[$name] = $row[0];
        return $row[0];
    }

}
