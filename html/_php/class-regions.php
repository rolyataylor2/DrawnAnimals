<?php

    class CREATEREGIONCLASS extends TABLETEMPLATE {
        function __construct () { parent::__construct('create', 'regions'); }
        
        // Constructors
        function byNew ( $uid,$name ) { return parent::_new('CREATEREGIONCLASS',['name','uid'],[$name,$uid]); }
        
        function byAll ()  { return parent::_all('CREATEREGIONCLASS');}
        function byName ( $name ) { return parent::_get('CREATEREGIONCLASS',['name'], [$name]);}
        function byId ($id) { return parent::_get('CREATEREGIONCLASS',['id'], [$id])[0];}
        function byUserId ($uid) { return parent::_get('CREATEREGIONCLASS',['uid'], [$uid]);}
        
        function countAll ()  {  return parent::_count('CREATEREGIONCLASS',['id'],[0],'',['!=']); }
        function countUserId($value) { return parent::_count('CREATEREGIONCLASS',['uid'],[$value]); }
        
        // Class Functions
        function Id () {return $this->_var('id');}
        function UserId() { return $this->_var('uid'); }
        function Permissions($uid,$setPermission=null) {
            $perm = explode('|',$this->_var('permissions'));
            $users = array();
            foreach($perm as $i) {
                $user = explode('~',$i);
                if ($uid===intval($user[0])) {
                    if (isset($setPermission)) $user[1] = $setPermission;
                    $returnvalue = $user[1];
                }
                $users[] = $user;
            }
            if (!isset($returnvalue)) {
                if (isset($setPermission)) {
                    $users[] = array($uid,$setPermission);
                    $returnvalue = $setPermission;
                } else {
                    $returnvalue = '---';
                }
            }
            
            $saving = array();
            foreach($users as $i) {
                $saving[] = implode('~',$i);
            }
            $this->_var('permissions',implode('|',$saving));
            return $returnvalue;
        }
        
        
        function Name ( $value = null ) {return $this->_var('name', $value);}
        function Description ( $value = null ) {return $this->_var('description', $value);}
    }