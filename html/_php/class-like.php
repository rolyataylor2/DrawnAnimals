<?php


    class LIKECLASS extends TABLETEMPLATE {
        function __construct () { parent::__construct('nicapa', 'likes'); }
        
        function __catagory($name) {
            $possible = [];
            $possible[] = 'CREATEMONSTERCLASS';
            $possible[] = 'CREATEREGIONMAPCLASS';
            $possible[] = 'eggHatch';
            return array_search($name,$possible);
        }
        // Constructors
        function byNew ( $uid,$catagory,$item ) {
            if (LIKECLASS::byUserByCatagoryByItem($uid,$catagory,$item) !== 0) return false;
            
            $catagorykey = LIKECLASS::__catagory($catagory);
            return parent::_new('LIKECLASS',['item_id','catagory','uid'],[$item,$catagorykey,$uid]);
        }
        function byRemove($uid,$catagory,$item) {
            if (LIKECLASS::byUserByCatagoryByItem($uid,$catagory,$item) === 0) return false;
            
            $catagorykey = LIKECLASS::__catagory($catagory);
            parent::_get('LIKECLASS',['item_id','catagory','uid'],[$item,$catagorykey,$uid])[0]->_delete();
            return true;
        }
        function byRemoveByCatagory($catagory) {
            $catagory = LIKECLASS::__catagory($catagory);
            $instance = new LIKECLASS();
            SQL()->select_db($instance->databasePrefix . $instance->database);
            $STMT = SQL()->prepare('DELETE FROM ' . $instance->table . ' WHERE catagory=?');
            $STMT->bind_param('i',$catagory);
            $STMT->execute();
            $STMT->close();
            return SQL()->error;
        }
        function byAll ()  { return parent::_all('LIKECLASS');}
        function byUserId ( $uid ) { return parent::_count('LIKECLASS',['uid'], [$uid]);}
        function byUserByCatagoryByItem($uid,$catagory,$item) { 
            $catagory = LIKECLASS::__catagory($catagory);
            return parent::_count('LIKECLASS',['uid','catagory','item_id'], [$uid,$catagory,$item]);
        }
        function byCatagoryByItem($catagory,$item) {
            $catagory = LIKECLASS::__catagory($catagory);
            return parent::_count('LIKECLASS',['catagory','item_id'], [$catagory,$item]);
        }
        
        // Class Functions
        function Id () {return $this->_var('id');}
        function UserId() { return $this->_var('uid'); }
        function Catagory( $value = null ) { return $this->_var('catagory', $value); }
        function Item ( $value = null ) {return $this->_var('item_id', $value);}
    }
