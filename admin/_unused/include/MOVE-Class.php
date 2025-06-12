<?php

$GLOBALS['MOVE_LOADED'] = array();

function MOVEOBJ($movename) {
    $movename = strtolower($movename);
    if (!isset($GLOBALS['MOVE_LOADED'][$movename])) {
        $GLOBALS['MOVE_LOADED'][$movename] = new MOVECLASS($movename);
    }
    return $GLOBALS['MOVE_LOADED'][$movename];
}

class MOVECLASS {

    function __construct($movename) {
        $this->data = array();
        $this->newdata = array();
        if (is_numeric($movename)) {
            $this->data['id'] = intval($movename);
        } else {
            $this->data['name'] = $movename;
        }
    }

    function _load() {
        if (isset($this->data['type'])) {
            return;
        }
        if (isset($this->data['id'])) {
            $STMT = SQL()->prepare('SELECT * FROM system_attacks WHERE id=?');
            $STMT->bind_param('i', $this->data['id']);
        } else {
            $STMT = SQL()->prepare('SELECT * FROM system_attacks WHERE name=?');
            $STMT->bind_param('s', $this->data['name']);
        }
        $STMT->execute();
        if (($result = $STMT->get_result()) === false || $result->num_rows === 0) {
            $STMT->close();
            return false;
        }
        $this->data = $result->fetch_assoc();
    }

    function _save() {
        if (!isset($this->newdata)) {
            return;
        }
        $types = '';
        $params = array();
        $query = 'UPDATE system_attacks SET id=id';
        foreach ($this->newdata as $variable => $value) {
            $query .= ', ' . $variable . '=?';
            $params[] = &$this->newdata[$variable];
            if (is_numeric($value)) {
                $types .= 'i';
            } else {
                $types .= 's';
            }
        }
        $query .= ' WHERE id=' . $this->data['id'];
        if (strlen($types) === 0) {
            return;
        }
        $STMT = SQL()->prepare($query) or die(SQL()->error);
        call_user_func_array('mysqli_stmt_bind_param', array_merge(array($STMT, $types), $params));

        $STMT->execute();
        $STMT->close();
    }

    function _var($name, $set = null) {
        if ($this->_load() === false) {
            return false;
        }
        if ($set != null) {
            $this->newdata[$name] = $set;
        }
        if (isset($this->newdata[$name])) {
            return $this->newdata[$name];
        }
        if (isset($this->data[$name])) {
            return $this->data[$name];
        }
    }

    function Name($value = null) {
        return $this->_var('name', $value);
    }

    function Power($value = null) {
        return $this->_var('power', $value);
    }

    function Type($value = null) {
        return $this->_var('type', $value);
    }

    function Acc($value = null) {
        return $this->_var('accuracy', $value);
    }

    function PPmax($value = null) {
        return $this->_var('pp', $value);
    }

    function Speed($value = null) {
        return $this->_var('speed', $value);
    }

    function DmgType($value = null) {
        return $this->_var('damage_type', $value);
    }

    function ScriptSelect($value = null) {
        return $this->_var('script_select', $value);
    }

    function ScriptExecute($value = null) {
        return $this->_var('script_execute', $value);
    }

    function Description($value = null) {
        return $this->_var('description', $value);
    }

}
