<?php

    class BLOGPOSTCLASS extends TABLETEMPLATE {
        function __construct () { parent::__construct('nicapa', 'blog'); }
        
        // Constructors
        function byNew ( $uid,$subject,$content ) { return parent::_new('BLOGPOSTCLASS',['content','subject','uid'],[$content,$subject,$uid]); }
        
        function byAll ()  { return parent::_all('BLOGPOSTCLASS');}
        function byName ( $name ) { return parent::_get('BLOGPOSTCLASS',['name'], [$name]);}
        function byId ($id) { return parent::_get('BLOGPOSTCLASS',['id'], [$id])[0];}
        function byOlderThan($time) { return parent::_get('BLOGPOSTCLASS',['post_date'], [$time],' ORDER BY post_date DESC LIMIT 5 ',['<']);}
        
        // Class Functions
        function Id () {return $this->_var('id');}
        function UserId() { return $this->_var('uid'); }
        function Date( $value = null ) { return Date('F, jS Y',$this->_var('post_date', $value)); }
        function Subject ( $value = null ) {return $this->_var('subject', $value);}
        function Content ( $value = null ) {return $this->_var('content', $value);}
    }