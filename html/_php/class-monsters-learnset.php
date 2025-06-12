<?php
    /*
     * To change this license header, choose License Headers in Project Properties.
     * To change this template file, choose Tools | Templates
     * and open the template in the editor.
     */
    class CREATEABILITYCLASS extends TABLETEMPLATE {
        function __construct() {
            parent::__construct('create', 'abilities');
            $this->className = 'CREATEABILITYCLASS';
        }
        function byNew($uid, $name) { return parent::_new('CREATEABILITYCLASS',['uid','name'],[$uid,$name]); }
        function byAll() { return parent::_get('CREATEABILITYCLASS','id!=?', 0 ); }
        function byId($value) { return parent::_get('CREATEABILITYCLASS',['id'], [$value])[0]; }
        function byName($value, $power=0) { 
            $ability = parent::_get('CREATEABILITYCLASS',['name'], [$value])[0];
            $ability->power = $power;
            return $ability;
        }
        function byUserId($value) { return parent::_get('CREATEABILITYCLASS',['uid'], [$value]); }

        function countAll() { return parent::_count('CREATEABILITYCLASS',['id'],[0],'',['!=']); }
        function countUserId($value) { return parent::_count('CREATEABILITYCLASS',['uid'],[$value]); }

        function Id() { return $this->_var('id'); }
        function UserId() { return $this->_var('uid'); }
        function Name($value=null) { return ucwords($this->_var('name',$value)); }
        function Description($value=null) { return ucwords($this->_var('description',$value)); }

        function Script($value=null) { return $this->_var('script',$value); }
        function ScriptRaw($value=null) { return $this->_var('script_raw',$value); }
        
        function Percentage() {return 0;}
        function Power() {return 0;}
        
    }
    class CREATEMOVECLASS extends TABLETEMPLATE {
        function __construct() {
            parent::__construct('create', 'moves');
            $this->className = 'CREATEMOVECLASS';
        }
        function byNew($uid, $name) { return parent::_new('CREATEMOVECLASS',['uid','name'],[$uid,$name]); }
        function byAll() { return parent::_get('CREATEMOVECLASS',['id'],[0],' ', ['!='] ); }
        function byId($value) { return parent::_get('CREATEMOVECLASS',['id'], [$value])[0]; }
        function byName($value) { return parent::_get('CREATEMOVECLASS',['name'], [$value])[0]; }
        function byUserId($value) { return parent::_get('CREATEMOVECLASS',['uid'], [$value]); }

        function countAll() { return parent::_count('CREATEMOVECLASS',['id'],[0],'',['!=']); }
        function countUserId($value) { return parent::_count('CREATEMOVECLASS',['uid'],[$value]); }

        function Id() { return $this->_var('id'); }
        function UserId() { return $this->_var('uid'); }
        function Name($value=null) { return ucwords($this->_var('name',$value)); }
        function Type($value=null) {return TYPECLASS::byId($this->_var('type',$value));}
        function Power($value=null) {return $this->_var('power',$value);}
        function PP($value=null) {return $this->_var('pp',$value);}
        function Speed($value=null) {return $this->_var('speed',$value);}
        function Acc($value=null) {return $this->_var('accuracy',$value);}
        function Target($value=null) {return $this->_var('target',$value);}
        function DamageType($value=null) {return $this->_var('damage_type',$value);}

        function Script($value=null) { return $this->_var('script',$value); }
        function ScriptRaw($value=null) { return $this->_var('script_raw',$value); }
        function Description($value=null) { return ucwords($this->_var('description',$value)); }
    }

    class MONSTERLEARNSETCLASS extends TABLETEMPLATE {
        function __construct () {
            parent::__construct('create', 'monsters_learnset');
        }
        function byNew ( $monster, $move, $level ) { return parent::_new('MONSTERLEARNSETCLASS',['monster','level','move'],[$monster,$level,$move]); }
        
        function byMonster ( $monster ) { return parent::_get('MONSTERLEARNSETCLASS',['monster'], [$monster],'ORDER BY level');}
        function byMonsterByBelowLevel ( $monster, $level ) { return parent::_get('MONSTERLEARNSETCLASS',['monster','level'], [$monster,$level], '',['=','<=']); }
        function byMonsterByLevel ( $monster, $level ) { return parent::_get('MONSTERLEARNSETCLASS',['monster','level'], [$monster,$level])[0]; }
        function byMonsterByMove ( $monster, $move ) { return parent::_get('MONSTERLEARNSETCLASS',['monster','move'], [$monster,$move]); }
        function byMonsterByMoveByLevel ( $monster, $move,$level) { return parent::_get('MONSTERLEARNSETCLASS', ['monster','move','level'], [$monster,$move,$level])[0]; }
        function byLevel ( $level ) { return parent::_get('MONSTERLEARNSETCLASS',['level'], [$level]); }
        function byMove ( $move ) { return parent::_get('MONSTERLEARNSETCLASS',['move'], [$move]); }
        
        function Id () { return $this->_var('id'); }
        function Monster () { return $this->_var('monster'); }
        function Move () { return $this->_var('move'); }
        function Level () { return $this->_var('level'); }
    }
    class MONSTERABILITYCLASS extends TABLETEMPLATE {
        function __construct () {
            parent::__construct('create', 'monsters_abilities');
        }
        function byNew ( $monster, $ability, $chance ) { return parent::_new('MONSTERABILITYCLASS',['monster','ability','chance'],[$monster,$chance,$ability]); }
        
        function byMonster ( $monster ) { return parent::_get('MONSTERABILITYCLASS',['monster'], [$monster]);}
        function byMonsterByAbility ( $monster, $ability ) { return parent::_get('MONSTERABILITYCLASS',['monster','ability'], [$monster,$ability]); }
        function byAbility ( $move ) { return parent::_get('MONSTERABILITYCLASS',['ability'], [$move]); }
        
        function Id () { return $this->_var('id'); }
        function Monster () { return $this->_var('monster'); }
        function Ability () { return $this->_var('ability'); }
        function Chance () { return $this->_var('chance'); }
    }
    
    class MONSTERITEMCLASS extends TABLETEMPLATE {
        function __construct () {
            parent::__construct('create', 'monsters_items');
        }
        function byNew ( $monster, $item, $chance ) { return parent::_new('MONSTERITEMCLASS',['monster','item','chance'],[$monster,$item,$chance]); }
        
        function byMonster ( $monster ) { return parent::_get('MONSTERITEMCLASS',['monster'], [$monster]);}
        function byMonsterByItem ( $monster, $item ) { return parent::_get('MONSTERITEMCLASS',['monster','item'], [$monster,$item]); }
        function byItem ( $item ) { return parent::_get('MONSTERITEMCLASS',['ability'], [$item]); }
        
        function Id () { return $this->_var('id'); }
        function Monster () { return $this->_var('monster'); }
        function Item () { return $this->_var('item'); }
        function Chance () { return $this->_var('chance'); }
    }
