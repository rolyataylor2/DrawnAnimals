<?php
class CREATEITEMCLASS extends TABLETEMPLATE {
    function __construct() {
        parent::__construct('create', 'items');
        $this->className = 'CREATEITEMCLASS';
    }
    function byNew($uid, $name) { return parent::_new('CREATEITEMCLASS',['uid','name'],[$uid,$name]); }
    function byAll() { return parent::_loadArray('id!=?', 0, 'CREATEITEMCLASS'); }
    function byName($value) { return parent::_loadArray('name=?', $value, 'CREATEITEMCLASS')[0]; }
    function byId($value) { return parent::_loadArray('id=?', $value, 'CREATEITEMCLASS')[0]; }
    function byUserId($value) { return parent::_loadArray('uid=?', $value, 'CREATEITEMCLASS'); }
    
    function countAll() { return parent::_count('CREATEITEMCLASS',['id'],[0],'',['!=']); }
    function countUserId($value) { return parent::_count('CREATEITEMCLASS',['uid'],[$value]); }
    
    function Id() { return $this->_var('id'); }
    function UserId() { return $this->_var('uid'); }
    function Name($value=null) { return ucwords($this->_var('name',$value)); }
    function Description($value=null) { return ucwords($this->_var('description',$value)); }
    function Script($value=null) { return $this->_var('script',$value); }
    function ScriptRaw($value=null) { return $this->_var('script_raw',$value); }
    function Markup($value=null) { return $this->_var('markup',$value); }
    function Image($upload=false) { 
        if($upload) { move_uploaded_file($_FILES['image']['tmp_name'], '/var/www/html/'.$this->Image()); }
        return 'img/items/'.$this->Id().'.png';
    }
}
class ITEMSTORAGECLASS extends TABLETEMPLATE {
    function __construct() {
        parent::__construct('nicapa', 'items_storage');
        $this->className = 'ITEMSTORAGECLASS';
    }
    function byNew($uid, $type) { return parent::_new('ITEMSTORAGECLASS',['uid','type'],[$uid,$type]); }
    function byAll() { return parent::_loadArray('id!=?', 0, 'ITEMSTORAGECLASS'); }
    function byUserId($id) { return parent::_loadArray('uid=?', $id, 'ITEMSTORAGECLASS'); }
    
    function Id() { return $this->_var('id'); }
    function UserId($uid = null) { return $this->_var('uid', $uid); }
    function Type($type = null) { return CREATEITEMCLASS::byId($this->_var('type', $type)); }
    function Move() {
        ITEMCLASS::byNew($this->_var('uid'),$this->_var('type'));
        $this->_delete();
    }
}
class ITEMCLASS extends TABLETEMPLATE {
    function __construct() {
        parent::__construct('nicapa', 'items');
        $this->className = 'ITEMCLASS';
    }
    function byNew($uid, $type) { return parent::_new('ITEMCLASS',['uid','type'],[$uid,$type]); }
    function byAll() { return parent::_all('ITEMCLASS'); }
    function byId($id) { return parent::_get('ITEMCLASS',['id'], [$id])[0]; }
    function byUserId($id) { return parent::_get('ITEMCLASS', ['uid'], [$id]); }
    
    function Id() { return $this->_var('id'); }
    function UserId($uid = null) { return $this->_var('uid', $uid); }
    function Type($type = null) { return CREATEITEMCLASS::byId($this->_var('type', $type)); }
    function Move() {
        ITEMSTORAGECLASS::byNew($this->_var('uid'),$this->_var('type'));
        $this->_delete();
    }
    function Send($userid,$note) {
        $message = MESSAGECLASS::byNew($this->UserId(),$userid,$note);
        $message->Item($this->Id());
        $message->_save();
        $this->UserId(0);
        $this->_save();
    }
   
    function Name() {
        return $this->Type()->Name();
    }
}
