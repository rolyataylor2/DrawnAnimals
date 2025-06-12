<?php

include_once 'SQL-Var.php';
$GLOBALS['ITEMBASE_LOADED'] = array();

function ITEMBASEOBJ($id, $cash = 0) {
    if (!isset($GLOBALS['ITEMBASE_LOADED'][$id])) {
        $GLOBALS['ITEMBASE_LOADED'][$id] = new ITEMBASECLASS($id, $cash);
    }
    return $GLOBALS['ITEMBASE_LOADED'][$id];
}

class ITEMBASECLASS {

    function __construct($id) {
        $this->data = array();
        if (is_numeric($id)) {
            $this->data['id'] = intval($id);
        } else {
            $STMT = SQL()->prepare('SELECT * FROM system_items WHERE name=?');
            $STMT->bind_param('s', strtolower($id));
            $STMT->execute();
            if (($result = $STMT->get_result()) === false || $result->num_rows === 0) {
                return false;
            }
            $STMT->close();
            $this->data = $result->fetch_assoc();
        }
    }

    function _save() {
        if (!isset($this->newdata)) {
            return false;
        }
        // @todo check permissions to make sure uid = plyr()->id or plyr()->isadmin()
        $types = '';
        $params = array();
        $query = 'UPDATE user_items SET id=id';
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
        $STMT = $this->destructdb->prepare($query) or die($this->destructdb->error);
        call_user_func_array('mysqli_stmt_bind_param', array_merge(array($STMT, $types), $params));

        $STMT->execute();
        $STMT->close();
    }

    function _load() {
        if (!isset($this->data['id'])) {
            return false;
        }
        if (isset($this->data['name'])) {
            return true;
        }
        $STMT = SQL()->prepare('SELECT * FROM system_items WHERE id=?');
        $STMT->bind_param('i', $this->data['id']);
        $STMT->execute();
        if (($result = $STMT->get_result()) === false || $result->num_rows === 0) {
            return false;
        }
        $STMT->close();
        $this->data = $result->fetch_assoc();
        return true;
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

    function Id() {
        return $this->_var('id');
    }

    function Name($value = null) {
        return $this->_var('name', $value);
    }

    function Catagory($value = null) {
        return $this->_var('catagory', $value);
    }

    function Cash($value = null) {
        return $this->_var('cash', $value);
    }

    function Description($value = null) {
        return $this->_var('description', $value);
    }

    function ScriptRequirement($value = null) {
        return $this->_var('script_requirement', $value);
    }

    function ScriptBattleSelect($value = null) {
        return $this->_var('script_battle_select', $value);
    }

    function ScriptBattleExecute($value = null) {
        return $this->_var('script_battle_execute', $value);
    }

    function ScriptSelect($value = null) {
        return $this->_var('script_select', $value);
    }

    function ScriptExecute($value = null) {
        return $this->_var('script_execute', $value);
    }

}

class ITEMCLASS {

    function __construct($id) {
        $this->data = array();
        if (is_numeric($id)) {
            if ($id > 0) {
                $this->data['id'] = intval($id);
            }
        } else {
            //create new item
            $STMT = SQL()->prepare("INSERT INTO user_items (type) VALUES (0)");
            $STMT->execute();
            $this->data['id'] = SQL()->insert_id;
            $STMT->close();
        }
        $this->destructdb = SQL();
    }

    function __destruct() {
        if (!isset($this->newdata)) {
            return;
        }
        $types = '';
        $params = array();
        $query = 'UPDATE user_items SET `id`=`id`';
        foreach ($this->newdata as $variable => $value) {
            $query .= ', `' . $variable . '`=?';
            $params[] = &$this->newdata[$variable];
            if (is_numeric($value)) {
                $types .= 'i';
            } else {
                $types .= 's';
            }
        }
        $query .= ' WHERE `id`=' . $this->data['id'];
        if (strlen($types) === 0) {
            return;
        }
        $STMT = $this->destructdb->prepare($query) or die($this->destructdb->error);
        call_user_func_array('mysqli_stmt_bind_param', array_merge(array($STMT, $types), $params)) or die(SQL()->error);

        $STMT->execute();
        $STMT->close();
    }

    function _load() {
        if (!isset($this->data['id'])) {
            return false;
        }
        if (isset($this->data['type'])) {
            return true;
        }

        $STMT = SQL()->prepare("SELECT * FROM user_items WHERE id=?");
        $STMT->bind_param('i', $this->data['id']);
        $STMT->execute();
        if (($result = $STMT->get_result()) === false || $result->num_rows === 0) {
            return false;
        }
        $STMT->close();
        $this->data = $result->fetch_assoc();
        return true;
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

    function Id($value = null) {
        return $this->_var('id', $value);
    }

    function Owner($value = null) {
        return PLYROBJ($this->_var('uid', $value));
    }

    function Type($value = null) {
        if ($value !== null && !is_numeric($value)) {
            $STMT = SQL()->prepare('SELECT id FROM system_items WHERE name=?');
            $STMT->bind_param('s', $value);
            $STMT->execute();
            if (($result = $STMT->get_result()) === false || $result->num_rows === 0) {
                $STMT->close();
                return false;
            }
            $STMT->close();
            $row = $result->fetch_row();
            $value = $row[0];
        }
        return $this->_var('type', $value);
    }

    function Selling($value = null) {
        return $this->_var('selling', $value);
    }

    function Storage($value = null) {
        return $this->_var('storage', $value);
    }

    function Base() {
        return ITEMBASEOBJ($this->_var('type'));
    }

    function Delete() {
        if (!$this->_load()) {
            return false;
        }
        $STMT = SQL()->prepare("DELETE FROM user_items WHERE id=?");
        $STMT->bind_param('i', $this->_var('id'));
        $STMT->execute();
        $STMT->close();
        unset($this->data);
        unset($this->newdata);
        return true;
    }

}

class SHOPCLASS {

    function __construct($id) {
        $this->data = array();
        $this->data['id'] = intval($id);
    }

    function _load() {
        if (!isset($this->data['id'])) {
            return false;
        }
        if (isset($this->data['name'])) {
            return true;
        }

        $STMT = SQL()->prepare("SELECT * FROM system_shops WHERE id=?");
        $STMT->bind_param('i', $this->data['id']);
        $STMT->execute();
        if (($result = $STMT->get_result()) === false || $result->num_rows === 0) {
            return false;
        }
        $STMT->close();
        $this->data = $result->fetch_assoc();
        return true;
    }

    function _save() {
        if (!isset($this->newdata)) {
            return;
        }
        $types = '';
        $params = array();
        $query = 'UPDATE system_shops SET id=id';
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

    function Greeting($value = null) {
        return $this->_var('greeting', $value);
    }

    function SoldOut($value = null) {
        return $this->_var('soldout', $value);
    }

    function NotEnough($value = null) {
        return $this->_var('notenough', $value);
    }

    function GoodBye($value = null) {
        return $this->_var('goodbye', $value);
    }

    function Currency($value = null) {
        return $this->_var('currency', $value);
    }

    function Inventory() {
        $id = $this->_var('id');
        $STMT = SQL()->prepare('SELECT item.id, item.price, base.name, base.catagory '
                . 'FROM system_shops_inventory AS item '
                . 'LEFT JOIN system_items AS base '
                . 'ON item.type=base.id '
                . 'WHERE sid=?');
        $STMT->bind_param('i', $id);

        $STMT->execute();
        if (($result = $STMT->get_result()) === false) {
            return array();
        }
        $STMT->close();
        $list = array();
        while ($item = $result->fetch_assoc()) {
            $list[] = array('name' => $item['name'],
                'price' => $item['price'],
                'catagory' => $item['catagory'],
                'id' => $item['id']);
        }
        return $list;
    }

    function Catagories() {
        $id = $this->_var('id');
        $STMT = SQL()->prepare('SELECT cat '
                . 'FROM system_shops_inventory '
                . 'WHERE sid=? '
                . 'GROUP BY cat '
                . 'ORDER BY cat');
        $STMT->bind_param('i', $id);

        $STMT->execute();
        if (($result = $STMT->get_result()) === false) {
            return array();
        }
        $STMT->close();
        $list = array();
        while ($item = $result->fetch_assoc()) {
            $list[] = $item['cat'];
        }
        return $list;
    }

    function Item($id) {
        $sid = $this->_var('id');
        $STMT = SQL()->prepare('SELECT shop.id, shop.type, shop.quantity, shop.price, base.name, base.catagory '
                . 'FROM system_shops_inventory AS shop '
                . 'LEFT JOIN system_items AS base '
                . 'ON shop.type=base.id '
                . 'WHERE shop.sid=? AND shop.id=?') or die(SQL()->error);
        $STMT->bind_param('ii', $sid, intval($id));

        $STMT->execute();
        if (($result = $STMT->get_result()) === false) {
            return array();
        }
        $STMT->close();
        $item = $result->fetch_assoc();
        return array('name' => $item['name'],
            'price' => $item['price'],
            'catagory' => $item['catagory'],
            'quantity' => $item['quantity'],
            'type' => $item['type'],
            'id' => $item['id']);
    }

    function Add($type, $price, $quantity) {
        $STMT = SQL()->prepare('UPDATE system_shop_inventory SET quantity=quantity+? AND price=? WHERE sid=? AND type=?');
        $STMT->bind_param('iii', $quantity, $price, $this->_var('id'), $type);
        $STMT->execute();
        $STMT->close();
        if (SQL()->affected_rows === 0) {
            $STMT = SQL()->prepare('INSERT INTO system_shop_inventory (type,price,quantity,cat) '
                    . 'VALUES(?,?,?,(SELECT catagory FROM system_items WHERE id=?))');
            $STMT->bind_param('iiii', $type, $price, $quantity, $type);
            $STMT->execute();
            $STMT->close();
        }
    }

    function Remove($type, $quantity) {
        $STMT = SQL()->prepare('SELECT quantity FROM system_shops_inventory WHERE sid=? AND type=?');
        $STMT->bind_param('ii', $this->_var('id'), $type);
        $STMT->execute();
        if (($result = $STMT->get_result()) === false || $result->num_rows === 0) {
            $STMT->close();
            return false;
        }
        $STMT->close();
        $row = $result->fetch_row();
        if ($row[0] <= $quantity) {
            $STMT = SQL()->prepare('DELETE FROM system_shops_inventory '
                    . 'WHERE sid=? AND type=?');
            $STMT->bind_param('ii', $this->_var('id'), $type);
            $STMT->execute();
            $STMT->close();
        } else {
            $STMT = SQL()->prepare('UPDATE system_shops_inventory SET quantity=quantity-? WHERE sid=? AND type=?');
            $STMT->bind_param('iii', $quantity, $this->_var('id'), $type);
            $STMT->execute();
            $STMT->close();
        }
    }

}

class TRANSACTIONCLASS {

    function __construct($id, $uid = null, $purchased = null, $cash = null, $ddollars = null) {
        if ($id < 0) {
            $STMT = SQL()->prepare('INSERT INTO user_cash_transactions (uid, purchased, cash, ddollars, ipaddress, datecreated) VALUES(?,?,?,?,?,?)');
            $STMT->bind_param('isiisi', $uid, $purchased, $cash, $ddollars, $_SERVER['REMOTE_ADDR'], time());
            $STMT->execute();
            $this->data['id'] = SQL()->insert_id;
            $STMT->close();
        } else {
            $this->data['id'] = intval($id);
        }
    }

    function _load() {
        if (!isset($this->data['id'])) {
            return false;
        }
        if (isset($this->data['uid'])) {
            return true;
        }

        $STMT = SQL()->prepare("SELECT * FROM user_cash_transactions WHERE id=?");
        $STMT->bind_param('i', $this->data['id']);
        $STMT->execute();
        if (($result = $STMT->get_result()) === false || $result->num_rows === 0) {
            return false;
        }
        $STMT->close();
        $this->data = $result->fetch_assoc();
        return true;
    }

    function _var($name) {
        if ($this->_load() === false) {
            return false;
        }
        if (isset($this->data[$name])) {
            return $this->data[$name];
        }
    }

    function User() {
        return PLYROBJ($this->_var('uid'));
    }

    function Purchased() {
        return $this->_var('purchased');
    }

    function Cash() {
        return $this->_var('cash');
    }

    function DDollars() {
        return $this->_var('ddollars');
    }

    function IpAddress() {
        return $this->_var('ipaddress');
    }

    function Date($format) {
        return date($format, $this->_var('datecreated'));
    }

}

class REGIONCLASS {

    function __construct($id) {
        if (is_numeric($id)) {
            $this->data['id'] = intval($id);
        } else {
            $this->data['name'] = $id;
        }
    }

    function _load() {
        if (isset($this->data['uid'])) {
            return;
        }
        if (isset($this->data['id'])) {
            $STMT = SQL()->prepare('SELECT * FROM system_regions WHERE id=?');
            $STMT->bind_param('i', $this->data['id']);
        } else {
            $STMT = SQL()->prepare('SELECT * FROM system_regions WHERE name=?');
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
        $query = 'UPDATE system_regions SET id=id';
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

    function Owner($value = null) {
        return $this->_var('uid', $value);
    }

    function Approved($value = null) {
        return $this->_var('approved', $value);
    }

    function Admins($value = null) {
        return $this->_var('auid', $value);
    }

    function Name($value = null) {
        return $this->_var('name', $value);
    }

    function Description($value = null) {
        return $this->_var('description', $value);
    }

    function AppearanceScript($value = null) {
        return $this->_var('script_appearance', $value);
    }

    function LocationLink($value = null, $x = null, $y = null) {
        return array('location' => $this->_var('location_start', $value),
            'x' => $this->_var('location_start_x', $x),
            'Y' => $this->_var('location_start_Y', $y));
    }

}

class ADMINREPORTCLASS {

    function __construct() {
        SQL()->query('INSERT INTO admin_reported (created) VALUES (' . time() . ')');
        $this->data['id'] = SQL()->insert_id;
        $this->destructdb = SQL();
    }
    function __destruct() {
        
    }
    function _var($name,$set=null) {
        
    }
    function Reporter($value = null) {
        return $this->_var('ruid',$value);
    }

    function UserInvolved($value = null) {
        return $this->_var('uid',$value);
    }

    function Catagory($value = null) {
        return $this->_var('catagory',$value);
    }

    function Description($value = null) {
        return $this->_var('description',$value);
    }

    function Created() {
        return $this->_var('created');
    }

    function Resolved($value = null) {
        return $this->_var('resolved',$value);
    }

    function Admin($value = null) {
        return $this->_var('admin',$value);
    }

}
