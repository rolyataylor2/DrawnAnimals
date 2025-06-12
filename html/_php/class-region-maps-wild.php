<?php

    class CREATEREGIONMAPCLASSWILD extends TABLETEMPLATE {
        function __construct () { parent::__construct('create', 'regions_maps_wild'); }
        
        // Constructors
        function byNew ( $mapid, $method, $sid, $minlv, $maxlv ) { return parent::_new('CREATEREGIONMAPCLASSWILD',['map_id','method','sid','min_lv','max_lv'],[$mapid,$method,$sid,$minlv,$maxlv]); }
        
        function byAll ()  { return parent::_all('CREATEREGIONMAPCLASSWILD');}
        function byMap ( $mapid ) { return parent::_get('CREATEREGIONMAPCLASSWILD',['map_id'], [$mapid]);}
        function byMapByMethod ( $mapid , $method=0) { return parent::_get('CREATEREGIONMAPCLASSWILD',['map_id','method'], [$mapid,$method]);}
        function byId ($id) { return parent::_get('CREATEREGIONMAPCLASSWILD',['id'], [$id])[0];}
        function bySpecies ($sid) { return parent::_get('CREATEREGIONMAPCLASSWILD',['sid'], [$sid]);}
        
        
        // Class Functions
        function Id () {return $this->_var('id');}
        function Map() { return $this->_var('map_id'); }
        function Method ( $value = null ) {return $this->_var('method', $value);}
        function Species ( $value = null ) {return $this->_var('sid', $value);}
        function MinLevel( $value = null ) {return $this->_var('min_lv', $value);}
        function MaxLevel( $value = null ) {return $this->_var('max_lv', $value);}

    }