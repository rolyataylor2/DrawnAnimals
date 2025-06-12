<?php

    class TYPECLASS {
        static $typearray = array('none', 'normal', 'fighting', 'flying',
            'poison', 'ground', 'rock', 'bug',
            'ghost', 'steel', 'fire', 'water',
            'grass', 'electric', 'phychic',
            'ice', 'dragon', 'dark', 'fairy','cosmic','sound');
        static $colorarray = array('#000', '#999966', '#cc3333', '#9999ff',
            '#993399', '#cccc66', '#cc9933', '#99cc33',
            '#666699', '#c0c0c0', '#ff9933', '#6699ff',
            '#66cc66', '#ffcc33', '#ff6699',
            '#99cccc', '#6633ff', '#666633','#ff77aa','#214A63','#57B1BE');
        static $effectivearray =     [ [1, 1,	1,	1,	1,	1,	1,	1,	1,	1,	1,	1,	1,	1,	1,	1,	1,	1,	1],
                                        [1, 1,	1,	1,	1,	1,	0.5,	1,	0,	0.5,	1,	1,	1,	1,	1,	1,	1,	1,	1],
                                        [1, 2,	1,	0.5,	0.5,	1,	2,	0.5,	0,	2,	1,	1,	1,	1,	0.5,	2,	1,	2,	0.5],
                                        [1, 1,	2,	1,	1,	1,	0.5,	2,	1,	0.5,	1,	1,	2,	0.5,	1,	1,	1,	1,	1],
                                        [1, 1,	1,	1,	0.5,	0.5,	0.5,	1,	0.5,	0,	1,	1,	2,	1,	1,	1,	1,	1,	2],
                                        [1, 1,	1,	0,	2,	1,	2,	0.5,	1,	2,	2,	1,	0.5,	2,	1,	1,	1,	1,	1],
                                        [1, 1,	0.5,	2,	1,	0.5,	1,	2,	1,	0.5,	2,	1,	1,	1,	1,	2,	1,	1,	1],
                                        [1, 1,	0.5,	0.5,	0.5,	1,	1,	1,	0.5,	0.5,	0.5,	1,	2,	1,	2,	1,	1,	2,	0.5],
                                        [1, 0,	1,	1,	1,	1,	1,	1,	2,	1,	1,	1,	1,	1,	2,	1,	1,	0.5,	1],
                                        [1, 1,	1,	1,	1,	1,	2,	1,	1,	0.5,	0.5,	0.5,	1,	0.5,	1,	2,	1,	1,	2],
                                        [1, 1,	1,	1,	1,	1,	0.5,	2,	1,	2,	0.5,	0.5,	2,	1,	1,	2,	0.5,	1,	1],
                                        [1, 1,	1,	1,	1,	2,	2,	1,	1,	1,	2,	0.5,	0.5,	1,	1,	1,	0.5,	1,	1],
                                        [1, 1,	1,	0.5,	0.5,	2,	2,	0.5,	1,	0.5,	0.5,	2,	0.5,	1,	1,	1,	0.5,	1,	1],
                                        [1, 1,	1,	2,	1,	0,	1,	1,	1,	1,	1,	2,	0.5,	0.5,	1,	1,	0.5,	1,	1],
                                        [1, 1,	2,	1,	2,	1,	1,	1,	1,	0.5,	1,	1,	1,	1,	0.5,	1,	1,	0,	1],
                                        [1, 1,	1,	2,	1,	2,	1,	1,	1,	0.5,	0.5,	0.5,	2,	1,	1,	0.5,	2,	1,	1],
                                        [1, 1,	1,	1,	1,	1,	1,	1,	1,	0.5,	1,	1,	1,	1,	1,	1,	2,	1,	0],
                                        [1, 1,	0.5,	1,	1,	1,	1,	1,	2,	1,	1,	1,	1,	1,	2,	1,	1,	0.5,	0.5],
                                        [1, 1,	2,	1,	0.5,	1,	1,	1,	1,	0.5,	0.5,	1,	1,	1,	1,	1,	2,	2,	1]];
        public $typeId;
        function __construct () {
            //Nothing
        }
        // Constructors
        function byId ( $id ) {
            $instance = new self();
            $instance->typeId = $id;
            if (!isset(TYPECLASS::$typearray[$instance->typeId])) {
                $instance->typeId = 0;
            }
            return $instance;
        }
        function byName ( $name ) {
            $instance = new self();
            $instance->typeId = array_search(strtolower($name), TYPECLASS::$typearray);
            return $instance;
        }
        function byAll() {
            return TYPECLASS::$typearray;
        }
        // Class Functions
        function Id () {
            return $this->typeId;
        }
        function Name () {
            return TYPECLASS::$typearray[$this->typeId];
        }
        function Color () {
            return TYPECLASS::$colorarray[$this->typeId];
        }
        function Attacking (TYPECLASS $defender) {
            return TYPECLASS::$effectivearray[$this->Id()][$defender->Id()];
        }
        function Defending (TYPECLASS $attacker) {
            return TYPECLASS::$effectivearray[$attacker->Id()][$this->Id()];
        }


    }
