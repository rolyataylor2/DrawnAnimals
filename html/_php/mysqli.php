<?php
    $GLOBALS['SQL'] = new mysqli("localhost", "root", "0309289DB00B44E407D92E42CC9D10112D", "space");
    if ( $GLOBALS['SQL']->connect_errno > 0 ) {
        die('Unable to connect to database [' . $SQL->connect_error . ']');
    }
    function getIsCrawler($userAgent) {
        $crawlers = 'Google|msnbot|Rambler|Yahoo|AbachoBOT|accoona|' .
        'AcioRobot|ASPSeek|CocoCrawler|Dumbot|FAST-WebCrawler|' .
        'GeonaBot|Gigabot|Lycos|MSRBOT|Scooter|AltaVista|IDBot|eStyle|Scrubby|crawl';
        $isCrawler = (preg_match("/$crawlers/i", $userAgent) > 0);
        return $isCrawler;
    }
    if (getIsCrawler(isset($_SERVER['HTTP_USER_AGENT'])?$_SERVER['HTTP_USER_AGENT']:'')) {
        if (!isset($_SESSION)) {session_start();}
        $_SESSION['MyID'] = 1;
    }
    function SQL () {
        if ( ! isset($GLOBALS['SQL']) ) {
            die('Database is unaccessable for some reason.');
        }
        return $GLOBALS['SQL'];
    }
    
    $GLOBALS['tableTemplateCache'] = array();
    $GLOBALS['CACHE'] = array();
    
    class TABLETEMPLATE {
        public $connection;
        public $databasePrefix;
        public $database;
        public $table;
        public $data;
        public $newdata;
        private $permissionString;
        public $searchValue;
        public $searchKey;
        function __construct ( $database, $tablename ) {
            $this->connection = SQL();
            $this->databasePrefix = '';
            $this->database = $database;
            $this->table = $tablename;
            $this->data = array();
            $this->newdata = array();
            $this->permissionString = '';
        }
        function _reloadid() {
            $this->searchKey = 'id';
            $this->searchValue = $this->_var('id');
            unset($this->data);
            unset($this->newdata);
            $this->_load();
        }
        function _load () {
            if ( ! isset($this->data) ) {
                return null;
            }
            if ( count($this->data) > 0 ) {
                return true;
            }
            
            $this->connection->select_db($this->databasePrefix . $this->database);
            $STMT = $this->connection->prepare('SELECT * FROM ' . $this->table . ' WHERE ' . $this->searchKey . '=?') or die(SQL()->error);

            if ( is_string($this->searchValue) ) {
                $valueType = 's';
            } else {
                $valueType = 'i';
            }
            $STMT->bind_param($valueType, $this->searchValue) or die(SQL()->error);
            $STMT->execute();
            if ( ($result = $STMT->get_result()) === false ) {
                unset($this->data);
                return false;
            }
            $this->data = $result->fetch_assoc();
            $STMT->close();
        }
        function _loadCheckCache($searchKey, $searchValue, $class) {
            if (!isset($GLOBALS['tableTemplateCache'][$class])) {
                $GLOBALS['tableTemplateCache'][$class] = array();
            }
            if (!isset($GLOBALS['tableTemplateCache'][$class][$searchKey])) {
                $GLOBALS['tableTemplateCache'][$class][$searchKey] = array();
            }
            if (isset($GLOBALS['tableTemplateCache'][$class][$searchKey][$searchValue])) {
                return $GLOBALS['tableTemplateCache'][$class][$searchKey][$searchValue];
            }
            return false;
        }
        function _setCheckCache ($searchKey, $searchValue, $class, $instance) {
            if (!isset($GLOBALS['tableTemplateCache'][$class])) {
                $GLOBALS['tableTemplateCache'][$class] = array();
            }
            if (!isset($GLOBALS['tableTemplateCache'][$class][$searchKey])) {
                $GLOBALS['tableTemplateCache'][$class][$searchKey] = array();
            }
            $GLOBALS['tableTemplateCache'][$class][$searchKey][$searchValue] = $instance;
            return $instance;
        }
        function _loadArray ( $searchKey, $searchValue, $class, $addon=null ) {
            $instance = new $class();
            
            if ($addon===null) {
                if ($instance->_loadCheckCache($searchKey,$searchValue,$class) !== false) {
                    return $instance->_loadCheckCache($searchKey,$searchValue,$class);
                }
                $addon = '';
                if (!isset($_GET['l'])) { $_GET['l'] = 30; }
                $addon .= ' LIMIT '.intval($_GET['l']);
                if (!isset($_GET['o'])) { $_GET['o'] = 0; }
                $addon .= ' OFFSET '.intval($_GET['o']);
            }
            
            SQL()->select_db($instance->databasePrefix . $instance->database);
            $STMT = SQL()->prepare('SELECT * FROM ' . $instance->table . ' WHERE ' . $searchKey . ' '.$addon) or die(SQL()->error);

            if ( is_string($searchValue) ) {
                $valueType = 's';
            } else {
                $valueType = 'i';
            }

            $STMT->bind_param($valueType, $searchValue);
            $STMT->execute();
            if ( ($result = $STMT->get_result()) === false ) {
                unset($instance->data);
                $list = array();
                $list[] = $instance;
                $STMT->close();
                return $instance;
            }
            $list = array();
            while ( $data = $result->fetch_assoc() ) {
                $instance = new $class();
                $instance->data = $data;
                $list[] = $instance;
            }
            $STMT->close();
            if (count($list) === 0) {
                unset($instance->data);
                $list = array();
                $list[] = $instance;
                return $list;
            }
            
            return $instance->_setCheckCache($searchKey,$searchValue,$class,$list);
                
        }
        function _loadCount ( $searchKey, $searchValue, $class ) {
            $instance = new $class();
            SQL()->select_db($instance->databasePrefix . $instance->database);
            $STMT = SQL()->prepare('SELECT COUNT(*) FROM ' . $instance->table . ' WHERE ' . $searchKey);

            if ( is_string($searchValue) ) {
                $valueType = 's';
            } else {
                $valueType = 'i';
            }

            $STMT->bind_param($valueType, $searchValue);
            $STMT->execute();
            if ( ($result = $STMT->get_result()) === false ) {
                return 0;
            }
            $data = $result->fetch_row();

            return $data[0];
        }
        function _var ( $variableName, $newValue = null, $relative =null ) {
            if ( $this->_load() === false ) {
                return false;
            }
            if ($relative !== null) {
                $newValue = $this->_var($variableName)+$newValue;
            }
            if ( $newValue !== null ) {
                $this->newdata[$variableName] = $newValue;
            }
            if ( isset($this->newdata[$variableName]) ) {
                return $this->newdata[$variableName];
            }
            if ( isset($this->data[$variableName]) ) {
                return $this->data[$variableName];
            }
        }
        function _save () {
            $this->connection->select_db($this->databasePrefix . $this->database);
            if ( count($this->newdata) === 0 ) {
                return false;
            }
            if ( empty($this->Id()) ) {
                return false;
            }
            // @todo check permissions to make sure uid = plyr()->id or plyr()->isadmin()
            $types = '';
            $params = array();
            $query = 'UPDATE ' . $this->table . ' SET id=id';
            foreach ( $this->newdata as $variable => $value ) {
                if (strcmp($value,'')===0) {
                    $query .= ', ' . $variable . '=NULL';
                } else {
                    $query .= ', ' . $variable . '=?';
                    $params[] = &$this->newdata[$variable];
                    $types .= ( is_numeric($value) ? 'i': 's');
                }
            }
            $query .= ' WHERE id=' . $this->data['id'];
            if ( strlen($types) === 0 ) {
                return;
            }
            
            $STMT = $this->connection->prepare($query) or die($this->connection->error);
            call_user_func_array('mysqli_stmt_bind_param', array_merge(array($STMT, $types), $params));
            $STMT->execute();
            $STMT->close();
            $this->data = array_merge($this->data,$this->newdata);
            $this->newdata = array();
        }
        function _delete () {
            if ($this->Id() === -1) return;
            $this->connection->select_db($this->databasePrefix . $this->database);
            $this->connection->query('DELETE FROM ' . $this->table . ' WHERE id=' . $this->Id());
            unset($this->data);
        }
        
        function _setCache($class,$id,$object) {
            if (!isset($GLOBALS['CACHE'][$class])) 
                $GLOBALS['CACHE'][$class] = array();
            $GLOBALS['CACHE'][$class][$id] = $object;
            return $object;
        }
        function _getCache($class,$id) {
            if (isset($GLOBALS['CACHE'][$class][$id]))
                return $GLOBALS['CACHE'][$class][$id];
            else return null;
        }
        function _new($class, $keys, $values) {
            $instance = new $class();
            SQL()->select_db($instance->databasePrefix . $instance->database);
            
            
            $valueType = '';
            $valueValues = array();
            $query = 'INSERT INTO ' . $instance->table .'(';
            $valuestring = '(';
            $i = count($keys);
            while($i--) {
                $query .= $keys[$i].',';
                if (!is_numeric($values[$i])) {
                    $valuestring .= '?,';
                    $valueType .= 's';
                    $valueValues[] = &$values[$i];
                } else {
                    $valuestring .= $values[$i].',';
                }
            }
            $query = substr($query, 0, -1).') VALUES '.substr($valuestring, 0, -1).')';
            if (strlen($valueType) > 0) {
                $STMT = SQL()->prepare($query) or die(SQL()->error);
                call_user_func_array('mysqli_stmt_bind_param', array_merge(array($STMT, $valueType), $valueValues)) or die(SQL()->error); 
                $STMT->execute();
            } else SQL()->query($query) or die(SQL()->error);
            $instance->searchKey = 'id';
            $instance->searchValue = $instance->connection->insert_id;
            return $instance;
        }
        function _all($class, $addon="") {
            $instance = new $class();
            SQL()->select_db($instance->databasePrefix . $instance->database);
            if (strcmp($addon,'') === 0) {
                $_GET['o'] = ( isset($_GET['o']) ? intval($_GET['o']) : 0);
                $_GET['l'] = ( isset($_GET['l']) ? intval($_GET['l']) : 30);
                $addon = ' LIMIT '.$_GET['o'].','.$_GET['l'];
            }
            $query = 'SELECT id FROM ' . $instance->table . ' WHERE 1=1 '.$addon;
            
            $STMT = SQL()->prepare($query) or die(SQL()->error);
            $STMT->execute();
            
            $list = array();
            $result = $STMT->get_result();
            
            while($value = $result->fetch_row()) {
                $cached = $instance->_getCache($class,$value[0]);
                if ($cached !== null) {
                    $list[] = $cached;
                } else {
                    $instance->searchKey = 'id';
                    $instance->searchValue = $value[0];
                    $list[] = $instance->_setCache($class,$value[0],$instance);
                    $instance = new $class();
                }
            }
            if (count($list) === 0) {
                $instance->searchKey = 'id';
                $instance->searchValue = 0;
                $list[] = $instance;
            }
            return $list;
        }
        function _get($class, $keys, $values,$addon='', $operators=array()) {
            $instance = new $class();
            SQL()->select_db($instance->databasePrefix . $instance->database);
            
            $i = count($keys);
            $valueType = '';
            $valueValues = array();
            $query = 'SELECT id FROM ' . $instance->table . ' WHERE ';
            while($i--) {
                if (isset($operators[$i])) {
                    $operator = $operators[$i];
                } else {
                    $operator = '=';
                }
                if (is_numeric($values[$i])) {
                    $query .= $keys[$i].' '.$operator.$values[$i].' AND ';
                } else {
                    $query .= $keys[$i].$operator.'? AND ';
                    $valueType .= 's';
                    $valueValues[] = &$values[$i];
                }
            }
            if (strcmp($addon,'') === 0) {
                $_GET['o'] = ( isset($_GET['o']) ? intval($_GET['o']) : 0);
                $_GET['l'] = ( isset($_GET['l']) ? intval($_GET['l']) : 30);
                $addon = ' LIMIT '.$_GET['o'].','.$_GET['l'];
            }
            $query .= '1=? '.$addon;
            $valueType .= 'i';
            $number = 1;
            $valueValues[] = &$number;
            $STMT = SQL()->prepare($query) or die(SQL()->error);
            call_user_func_array('mysqli_stmt_bind_param', array_merge(array($STMT, $valueType), $valueValues)); 
            $STMT->execute();
            
            $list = array();
            $result = $STMT->get_result();
            
            while($value = $result->fetch_row()) {
                $cached = $instance->_getCache($class,$value[0]);
                if ($cached !== null) {
                    $list[] = $cached;
                } else {
                    $instance->searchKey = 'id';
                    $instance->searchValue = $value[0];
                    $list[] = $instance->_setCache($class,$value[0],$instance);
                    $instance = new $class();
                }
            }
            if (count($list) === 0) {
                $instance->searchKey = 'id';
                $instance->searchValue = 0;
                $list[] = $instance;
            }
            return $list;
        }
        function _count($class, $keys, $values,$addon='', $operators=array()) {
            $instance = new $class();
            SQL()->select_db($instance->databasePrefix . $instance->database);
            
            $i = count($keys);
            $valueType = '';
            $valueValues = array();
            $query = 'SELECT id FROM ' . $instance->table . ' WHERE ';
            while($i--) {
                if (isset($operators[$i])) {
                    $operator = $operators[$i];
                } else {
                    $operator = '=';
                }
                if (is_numeric($values[$i])) {
                    $query .= $keys[$i].' '.$operator.$values[$i].' AND ';
                } else {
                    $query .= $keys[$i].$operator.'? AND ';
                    $valueType .= 's';
                    $valueValues[] = &$values[$i];
                }
            }
            $query .= '1=? '.$addon;
            $valueType .= 'i';
            $number = 1;
            $valueValues[] = &$number;
            $STMT = SQL()->prepare($query) or die(SQL()->error);
            call_user_func_array('mysqli_stmt_bind_param', array_merge(array($STMT, $valueType), $valueValues)); 
            $STMT->execute();
            $result = $STMT->get_result();
            return $result->num_rows;
        }
        
        function _search($keys,$values,$class,$addon='') {
            $instance = new $class();
            SQL()->select_db($instance->databasePrefix . $instance->database);
            
            $i = count($keys);
            $valueType = '';
            $valueValues = array();
            $query = 'SELECT id FROM ' . $instance->table . ' WHERE ';
            while($i--) {
                if (is_numeric($values[$i])) {
                    $query .= $keys[$i].' ='.$values[$i].' AND ';
                } else {
                    $query .= $keys[$i].' LIKE ? AND ';
                    $valueType .= 's';
                    $valueValues[] = &$values[$i];
                }
            }
            if (strcmp($addon,'') === 0) {
                $_GET['o'] = ( isset($_GET['o']) ? intval($_GET['o']) : 0);
                $_GET['l'] = ( isset($_GET['l']) ? intval($_GET['l']) : 30);
                $addon = ' LIMIT '.$_GET['o'].','.$_GET['l'];
            }
            $query .= '1=? '.$addon;
            $valueType .= 'i';
            $number = 1;
            $valueValues[] = &$number;
            $STMT = SQL()->prepare($query) or die(SQL()->error);
            call_user_func_array('mysqli_stmt_bind_param', array_merge(array($STMT, $valueType), $valueValues)); 
            $STMT->execute();
            
            $list = array();
            $result = $STMT->get_result();
            
            while($value = $result->fetch_row()) {
                $cached = $instance->_getCache($class,$value[0]);
                if ($cached !== null) {
                    $list[] = $cached;
                } else {
                    $instance->searchKey = 'id';
                    $instance->searchValue = $value[0];
                    $list[] = $instance->_setCache($class,$value[0],$instance);
                    $instance = new $class();
                }
            }
            if (count($list) === 0) {
                $instance->searchKey = 'id';
                $instance->searchValue = 0;
                $list[] = $instance;
            }
            return $list;
            
        }
        function _renderSearchResult($show) {
            if (!isset($this->data)) { return ''; }
            $twigfile = '/var/www/html/ajax/_plugins/searchresult/'.$this->className.'.twig';
            if (!file_exists($twigfile)) { return ''; }
            return TWIG()->render($twigfile, array('data' => $this->data, 'show' => $show));
        }
        function _renderFullSummery($show) {
            if (!isset($this->data)) { return ''; }
            $twigfile = '/var/www/html/ajax/_plugins/fullsummery/'.$this->className.'.twig';
            if (!file_exists($twigfile)) { return ''; }
            return TWIG()->render($twigfile, array('data' => $this->data, 'show' => $show));
        }
    }

    class MAPOBJECT extends TABLETEMPLATE {
        function __construct () {
            parent::__construct('space', 'usr_maps');
        }
        // Constructors
        function byNew ( $uid, $name, $sprite, $parent, $xpos,$ypos) {
            $instance = new Self();
            $instance->connection->select_db($instance->databasePrefix . $instance->database);
            $STMT = $instance->connection->prepare('INSERT INTO ' . $instance->table . '(uid,name,sprite,parent,xposition,yposition) VALUES (?,?,?,?,?,?)') or die($instance->connection->error);
            $STMT->bind_param('isiiii', $uid,$name, $sprite, $parent, $xpos, $ypos);
            $STMT->execute();
            $STMT->close();
            $instance->searchKey = 'id';
            $instance->searchValue = $instance->connection->insert_id;
            return $instance;
        }
        function byOwner(PLAYERCLASS $player) {
            return MAPOBJECT::_get('MAPOBJECT',['uid'],[$player->Id()]);
        }
        function byId ($id) {
            $instance = new self();
            $instance->searchKey = 'id';
            $instance->searchValue = $id;
            return $instance;
        }
        function byName ( $name) {
            $instance = new self();
            $instance->searchKey = 'name';
            $instance->searchValue = $name;
            return $instance;
        }
        function byParent ($parent) {
            if (is_numeric($parent)) {
                return MAPOBJECT::_get('MAPOBJECT',['parent'],[$parent]);
            }
            return MAPOBJECT::_get('MAPOBJECT',['parent'],[$parent->Id()]);
        }
        // Class Functions
        function Id () {
            return $this->_var('id');
        }
        function Uid() {
            return $this->_var('uid');
        }
        function ParentMap() {}
        function Name ( $value = null ) {
            return $this->_var('name', $value);
        }
        function Sprite() {
             return $this->_var('sprite');
        }
        function X ( ) {
            return $this->_var('xposition');
        }
        function Y ( ) {
            return $this->_var('yposition');
        }
        function Children() {
            return MAPOBJECT::byParent($this);
        }
        function Data ($value=null ) {
            return $this->_var('data', $value);
        }


    }
    
    class MysqlEdit {
        private $title = 'Variable';
        private $segments = array();
        private $hint = 'No Hints';
        
        function Title($title) {
            $this->title = $title;
            return $this;
        }
        function Hints($hints) {
            $this->hint = $hints;
        }
        function Group($name) {
            $this->segments[] = $name;
        }
        function Show($name, $hint, $cellname) {
            $this->segments[] = array('name'=>$name, 'hint'=>$hint, 'row'=>$cellname, 'edit'=>false, 'value'=>'none');
        }
        function Edit($name, $hint, $cellname, $type) {
            $this->segments[] = array('name'=>$name,'inputname'=> strtolower(preg_replace("/[^A-Za-z]/", '', $name)),'hint'=>$hint, 'row'=>$cellname, 'edit'=>$type, 'value'=>'none');
        }
        function Image($name, $hint, $uploadpath, $approvepath, $url) {
            $object = array('name'=>$name,'inputname'=> strtolower(preg_replace("/[^A-Za-z]/", '', $name)),
                            'filename'=>$uploadpath,'approvedfilename'=>$approvepath,'url'=>$url,'edit'=>'image',
                            'uploaded'=>false,'approved'=>false, 'hint'=>$hint);
            if (file_exists($uploadpath)) $object['uploaded'] = true;
            if (file_exists($approvepath)) $object['approved'] = true;
            $this->segments[] = $object;
        }
        
        function SaveRow() {
            
        }
        function RenderHtml() {
            $open = false;
            $title = strtolower(preg_replace("/[^A-Za-z]/", '', $this->title));
            $html = '<section class="tableEditor" title="'.$this->hint.'">';
            $html .= '<input type="hidden" name="'.$title.'id" value=""/>';
            $html .= '<header>'.$this->title.'</header>';
            foreach($this->segments as $i) {
                if (is_string($i)) {
                    if ($open) {
                        $html .= '<div class="group">';
                    } else {
                        $html .= '</div><div class="group">';
                    }
                    $html .= '<header>'.$i.'</header>';
                    $open = true;
                } else {
                    $html .= '<article class="cell" title="'.$i['hint'].'">';
                    $html .= '<header>'.$i['name'].'</header>';
                    if ($i['edit'] !== false) {
                        switch($i['edit']) {
                            case 'text':
                                $html .= '<input type="text" name="'.$title.$i['inputname'].'" value="'.$i['value'].'"/>';
                                break;
                            case 'number':
                                $html .= '<input type="text" class="number" name="'.$title.$i['inputname'].'" value="'.$i['value'].'"/>';
                                break;
                            case 'image':
                                $html .= '<img src="'.$i['url'].'" onclick="$(this).next().click();"/>';
                                $html .= '<input type="file" name="'.$title.$i['inputname'].'"/>';
                                break;
                        }
                    } else {
                        $html .= '<p>'.$i['value'].'</p>';
                    }
                    $html .= '</article>';
                }
            }
            if ($open) $html .= '</div>';
            
            
            $html .= '</section>';
            return $html;
        }
        
    }

    function VerifyPostToken() {
        if (empty(PLAYERCLASS::byMe()->Id())) return false;
        return (isset($_POST['token']) && $_POST['token'] === $_SESSION['token']);
    }
    function LoggedIn() {
        if ( ! isset($_SESSION)) {
            session_start();
        }
        if ( ! isset($_SESSION['MyID']) ) {
            return false;
        }
        return true;
    }
    function isMobile() {
        return preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $_SERVER["HTTP_USER_AGENT"]);
    }
    function trim_png($path_to_png_file) {
        if (!file_exists($path_to_png_file)) {
            throw new Exception("File does not exist: $path_to_png_file");
        }
        
        // '-' makes it use stdout, required to save to $compressed_png_content variable
        // '<' makes it read from the given file path
        // escapeshellarg() makes this safe to use with any path
        shell_exec("convert ".$path_to_png_file." -trim ".$path_to_png_file);
        $size = getimagesize($path_to_png_file);
        $newsize = max($size[0],$size[1]);
        shell_exec('convert '.$path_to_png_file.' -background none -gravity center -extent '.$newsize.'x'.$newsize.' '.$path_to_png_file);
    }
    function shrink_png($path_to_png_file) {
        shell_exec('convert '.$path_to_png_file.' -resize 50% '.$path_to_png_file);
    }
    function compress_png($path_to_png_file, $max_quality = 90) {
        if (!file_exists($path_to_png_file)) {
            throw new Exception("File does not exist: $path_to_png_file");
        }

        $min_quality = 60;

        // '-' makes it use stdout, required to save to $compressed_png_content variable
        // '<' makes it read from the given file path
        // escapeshellarg() makes this safe to use with any path
        $compressed_png_content = shell_exec("pngquant --quality=$min_quality-$max_quality - < ".escapeshellarg(    $path_to_png_file));

        if (!$compressed_png_content) {
            throw new Exception("Conversion to compressed PNG failed. Is pngquant 1.8+ installed on the server?");
        }

        return $compressed_png_content;
    }
    
    function hex2RGB($hexStr, $returnAsString = false, $seperator = ',') {
        $hexStr = preg_replace("/[^0-9A-Fa-f]/", '', $hexStr); // Gets a proper hex string
        $rgbArray = array();
        if (strlen($hexStr) == 6) { //If a proper hex code, convert using bitwise operation. No overhead... faster
            $colorVal = hexdec($hexStr);
            $rgbArray['red'] = 0xFF & ($colorVal >> 0x10);
            $rgbArray['green'] = 0xFF & ($colorVal >> 0x8);
            $rgbArray['blue'] = 0xFF & $colorVal;
        } elseif (strlen($hexStr) == 3) { //if shorthand notation, need some string manipulations
            $rgbArray['red'] = hexdec(str_repeat(substr($hexStr, 0, 1), 2));
            $rgbArray['green'] = hexdec(str_repeat(substr($hexStr, 1, 1), 2));
            $rgbArray['blue'] = hexdec(str_repeat(substr($hexStr, 2, 1), 2));
        } else {
            return false; //Invalid hex color code
        }
        return $returnAsString ? implode($seperator, $rgbArray) : $rgbArray; // returns the rgb string or the associative array
    }
    function dechexpad($value) { return str_pad(dechex($value), 2, '0', STR_PAD_LEFT);}

    function rgb2Hex($r,$g,$b) {
        return '#'.dechexpad($r).dechexpad($g).dechexpad($b);
    }
    function hsl2Rgb( $h, $s, $l ){
            $c = ( 1 - abs( 2 * $l - 1 ) ) * $s;
            $x = $c * ( 1 - abs( fmod( ( $h / 60 ), 2 ) - 1 ) );
            $m = $l - ( $c / 2 );

            if ( $h < 60 ) {
                    $r = $c;
                    $g = $x;
                    $b = 0;
            } else if ( $h < 120 ) {
                    $r = $x;
                    $g = $c;
                    $b = 0;			
            } else if ( $h < 180 ) {
                    $r = 0;
                    $g = $c;
                    $b = $x;					
            } else if ( $h < 240 ) {
                    $r = 0;
                    $g = $x;
                    $b = $c;
            } else if ( $h < 300 ) {
                    $r = $x;
                    $g = 0;
                    $b = $c;
            } else {
                    $r = $c;
                    $g = 0;
                    $b = $x;
            }

            $r = ( $r + $m ) * 255;
            $g = ( $g + $m ) * 255;
            $b = ( $b + $m  ) * 255;

        return array( floor( $r ), floor( $g ), floor( $b ) );
    }