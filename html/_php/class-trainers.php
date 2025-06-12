<?php
class CREATETRAINERCLASSCLASS extends TABLETEMPLATE {
    function __construct() { parent::__construct('create', 'trainers_class'); }
    function byAll() { return parent::_get('CREATETRAINERCLASSCLASS',['id'], [0],' ORDER BY title',['!=']); }
    function byId($id) { return parent::_get('CREATETRAINERCLASSCLASS',['id'], [$id])[0]; }
    
    function Id() { return $this->_var('id'); }
    function Name() { return $this->_var('title'); }
}
class CREATETRAINERCLASSMONSTER extends TABLETEMPLATE {
    function __construct() { parent::__construct('create', 'trainers_monsters'); }
    function byId($id) { return parent::_get('CREATETRAINERCLASSMONSTER',['id'], [$id])[0]; }
    function byTrainerId($tid,$variant=0) { return parent::_get('CREATETRAINERCLASSMONSTER',['tid','variant'], [$tid,$variant]); }
    function Id() { return $this->_var('id'); }
    
    function Trainer($value=null) { return CREATETRAINERCLASS::byId($this->_var('tid',$value)); }
    function Team($value=null) { return $this->_var('variant',$value); }
    function Species($value=null) { return CREATEMONSTERCLASS::byId($this->_var('sid',$value));}
    function Level($value=null) { return $this->_var('level',$value); }
    function Difficulty($value=null) { return $this->_var('difficulty',$value); }
}
class CREATETRAINERCLASS extends TABLETEMPLATE {
    function __construct() { parent::__construct('create', 'trainers'); }
    
    function byNew($uid, $name) { return parent::_new('CREATETRAINERCLASS',['uid','name'],[$uid,$name]); }
    function byAll() { return parent::_all('CREATETRAINERCLASS'); }
    function byId($id) { return parent::_get('CREATETRAINERCLASS',['id'], [$id])[0]; }
    function byUserId($id) { return parent::_get('CREATETRAINERCLASS', ['uid'], [$id]); }
    
    function Id() { return $this->_var('id'); }
    function UserId($value = null) { return $this->_var('uid', $value); }
    function Type($value = null) { return CREATETRAINERCLASSCLASS::byId($this->_var('class', $value)); }
    function Name($value=null) { return $this->_var('name',$value); }
    function Difficulty($value=null) { return $this->_var('difficulty',$value); }
    function Region($value=null) { return CREATEREGIONCLASS::byId($this->_var('region',$value)); }
    function RewardScript($value=null) { return $this->_var('reward',$value); }
    function RewardScriptRaw($value=null) { return $this->_var('reward_raw',$value); }
    
    function Team($number=0) { return CREATETRAINERCLASSMONSTER::byTrainerId($this->Id(),$number); }
}

