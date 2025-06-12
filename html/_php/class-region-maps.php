<?php

    class CREATEREGIONMAPCLASS extends TABLETEMPLATE {
        function __construct () { parent::__construct('create', 'regions_maps'); }
        
        // Constructors
        function byNew ( $uid,$name ) { return parent::_new('CREATEREGIONMAPCLASS',['name','uid'],[$name,$uid]); }
        
        function byAll ()  { return parent::_all('CREATEREGIONMAPCLASS');}
        function byName ( $name ) { return parent::_get('CREATEREGIONMAPCLASS',['name'], [$name]);}
        function byRegion ( $region , $addon='') { return parent::_get('CREATEREGIONMAPCLASS',['region'], [$region],$addon);}
        function byId ($id) { return parent::_get('CREATEREGIONMAPCLASS',['id'], [$id])[0];}
        function byUserId ($uid) { return parent::_get('CREATEREGIONMAPCLASS',['uid'], [$uid]);}
        
        function countAll ()  {  return parent::_count('CREATEREGIONMAPCLASS',['id'],[0],'',['!=']); }
        function countUserId($value) { return parent::_count('CREATEREGIONMAPCLASS',['uid'],[$value]); }
        
        // Class Functions
        function Id () {return $this->_var('id');}
        function UserId() { return $this->_var('uid'); }
        function Name ( $value = null ) {return $this->_var('name', $value);}
        function Description ( $value = null ) {return $this->_var('description', $value);}
        function MinimapIcon( $value = null ) {return $this->_var('minimap_icon', $value);}
        function MinimapX( $value = null ) {return $this->_var('minimap_x', $value);}
        function MinimapY( $value = null ) {return $this->_var('minimap_y', $value);}
        function Reference( $value = null ){return $this->_var('ref_id', $value);}
        function Start( $value = null ) {return $this->_var('start', $value);}
        function Region( $value = null ) {return $this->_var('region', $value);}
        function RegionX( $value = null ) {return $this->_var('region_x', $value);}
        function RegionY( $value = null ) {return $this->_var('region_y', $value);}
        function Width( $value = null ) {return $this->_var('width', $value);}
        function Height( $value = null ) {return $this->_var('height', $value);}
        function TileData( $value = null ) {
            if ($value !== null) {
                $value = gzcompress($value,9);
            }
            if (strlen($this->_var('tile_data')) > 0) { 
                return gzuncompress($this->_var('tile_data',$value));
            } else {
                return $this->_var('tile_data',$value);
            }
        }
        
        function UnLike() { 
            if (empty(PLAYERCLASS::byMe()->Id())) return false;
            return LIKECLASS::byRemove(PLAYERCLASS::byMe()->Id(),'CREATEREGIONMAPCLASS',$this->Id());
            
        }
        function Like() { 
            if (empty(PLAYERCLASS::byMe()->Id())) return false;
            return LIKECLASS::byNew(PLAYERCLASS::byMe()->Id(),'CREATEREGIONMAPCLASS',$this->Id());
            
        }
        function Liked() { 
            if (empty(PLAYERCLASS::byMe()->Id())) return false;
            return LIKECLASS::byUserByCatagoryByItem(PLAYERCLASS::byMe()->Id(),'CREATEREGIONMAPCLASS',$this->Id()) === 1;
        }
        function Likes() { return LIKECLASS::byCatagoryByItem('CREATEREGIONMAPCLASS',$this->Id()); }
        

    }