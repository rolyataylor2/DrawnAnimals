<?php


// DONE WITH ENTIRE FILE?
include_once 'include/SQL-Var.php';
include_once 'include/GLSS-Var.php';
include_once 'include/NODE-Var.php';
include_once 'include/PKMN-Class.php';
include_once 'include/ITEM-Class.php';

include_once 'include/PLYRACHIEVMENT-Class.php';
include_once 'include/PLYRBATTLE-Class.php';
include_once 'include/PLYRFRIENDS-Class.php';
include_once 'include/PLYRINVENTORY-Class.php';
include_once 'include/PLYRMESSAGE-Class.php';
include_once 'include/PLYRNODE-Class.php';
include_once 'include/PLYRPARTY-Class.php';
include_once 'include/PLYRSPECIES-Class.php';
include_once 'include/PLYRVARIABLE-Class.php';
include_once 'include/PLYRQUEST-Class.php';
include_once 'include/PLYRTRAINER-Class.php';
$GLOBALS['PLYR_LOADED'] = array();

function PLYROBJ($uid, $newusername = null, $newpassword = null, $newemail = null) {
    if (!is_numeric($uid)) {
        $STMT = SQL()->prepare('SELECT uid FROM user_accounts WHERE username=?');
        $STMT->bind_param('s', strtolower($uid));
        $STMT->execute();
        if (($result = $STMT->get_result()) === false || $result->num_rows === 0) {
            $STMT->close();
            return false;
        }
        $STMT->close();
        $row = $result->fetch_row();
        $uid = $row[0];
    }
    if ($uid < 0) {
        return new PLYRCLASS($uid, strtolower($newusername), $newpassword, $newemail);
    }
    if (!isset($GLOBALS['PLYR_LOADED'][$uid])) {
        $GLOBALS['PLYR_LOADED'][$uid] = new PLYRCLASS($uid);
    }
    return $GLOBALS['PLYR_LOADED'][$uid];
}

class PLYRPRINTERCLASS {

    function __construct($uid) {
        $this->parent = PLYROBJ($uid);
    }

    function SmallSummery() {
        
    }

    function LargeSummery() {
        
    }

    function TrainerCard() {
        
    }

    function ExperianceSmall() {
        $variables = array();
        $i = 1;
        $variables['Type'] = array();
        $variables['TypeName'] = array();
        $variables['TypeColor'] = array();
        while (isset(GLSS()->TypeNames[$i])) {
            $variables['Type'][] = $this->parent->Exp($i) + 300;
            $variables['TypeName'][] = GLSS()->TypeNames[$i];
            $variables['TypeColor'][] = GLSS()->TypeColors[$i];
            $i++;
        }
        $variables['PLYR'] = $this->parent->id;
        return TWIG()->render('/_plugins/plyr/' . __FUNCTION__ . '.twig', $variables);
    }

    function ExperianceLarge() {
        $variables = array();
        $i = 1;
        $variables['Type'] = array();
        $variables['TypeName'] = array();
        $variables['TypeColor'] = array();
        while (isset(GLSS()->TypeNames[$i])) {
            $variables['Type'][] = $this->parent->Exp($i) + 300;
            $variables['TypeName'][] = GLSS()->TypeNames[$i];
            $variables['TypeColor'][] = GLSS()->TypeColors[$i];
            $i++;
        }
        $variables['PLYR'] = $this->parent->id;
        return TWIG()->render('/_plugins/plyr/' . __FUNCTION__ . '.twig', $variables);
    }

    function Username() {
        $username = $this->parent->Username();
        return "<a class='PlyrUsername' href='javascript:'>$username</a>";
    }

    function Avatar() {
        $avatar = $this->parent->AvatarForum();
        return "<img class='avatar' src='res/img/avatars/$avatar.png'/>";
    }
    function PartySize() {
        $string = '<div class="partysize">';
        $i = 6;
        $ii = $this->parent->Party()->Size();
        $iii = $this->parent->Party()->Alive();
        while($i--) {
            $string .= '<img src="http://img.drawnimals.com/icons/';

            if ($iii>0) {
                $string .= 'drawnimal-alive-icon.png';
            } elseif ($ii>0) {
                $string .= 'drawnimal-dead-icon.png';
            } else {
                $string .= 'drawnimal-empty-icon.png';
            }
            $string .= '"/>';
            $iii--; $ii--;
        }
        $string .= '</div>';
        return $string;
    }
    function Summery($onclick = '', $items = '') {
        $arguments = array();
        $arguments['ONCLICK'] = $onclick;
        $arguments['USERNAME'] = $this->parent->Username();
        $items = explode('|', $items);
        foreach ($items as $item) {
            switch ($item) {
                case 'location': $arguments['LOCATION'] = $this->parent->Location();
                    break;
                case 'expchart': $arguments['EXPGUAGE'] = $this->ExperianceLarge();
                    break;
                case 'avatar': $arguments['AVATAR'] = $this->parent->AvatarForum();
                    break;
                case 'country': $arguments['COUNTRY'] = $this->parent->Country();
                    break;
                case 'created': $arguments['CREATED'] = $this->parent->Created('M jS, Y');
                    break;
                case 'lastseen': $arguments['LASTSEEN'] = $this->parent->LastSeen(true);
                    break;
                case 'birthday': $arguments['BIRTHDAY'] = $this->parent->Birthday('M jS');
                    break;
                case 'level': $arguments['LEVEL'] = $this->parent->Level();
                    break;
                case 'gender': $arguments['GENDER'] = $this->parent->Gender();
                    break;
                case 'age': $arguments['AGE'] = $this->parent->Age();
                    break;
                case 'theme': $arguments['THEME'] = $this->parent->SiteTheme();
                    break;
                case 'alignment': $arguments['ALIGNMENT'] = $this->parent->Alignment();
                    break;
                case 'party': $arguments['PARTY'] = $this->parent->Party()->All(); //@todo work on this
                    break;
                case 'partysize': $arguments['PARTYSIZE'] = $this->PartySize();
                    break;
            }
        }
        return TWIG()->render('/_plugins/plyr/' . __FUNCTION__ . '.twig', $arguments);
    }
}

class PLYRCLASS {

    var $id = 0;

    /** PLYRCLASS - Get Object that represents a player on the website.
     * 
     * @param string/int $id Username/ID of a player on the site.
     * @return PLYRCLASS/FALSE false if player doesnt exist.
     */
    function __construct($id, $newusername = null, $newpassword = null, $newemail = null) {
        if ($id < 0) {
            // Create Bug Tracking Profile
            $sql = new mysqli("localhost", "root", "0309289DB00B44E407D92E42CC9D10112D","bugtracker");
            $stmt = $sql->prepare('INSERT INTO mantis_user_table (username,email,password,cookie_string,access_level) VALUES (?,?,?,UUID(),25)');
            $stmt->bind_param('sss',$_POST['username'], $_POST['email'], md5($_POST['password']));
            $stmt->execute();
            
            // Hash password
            $newpassword .= substr($newusername, -2);
            $newpassword .= substr($newusername, -3);
            $newpassword .= substr($newusername, 0, 2);
            $newpassword .= substr($newusername, 1, 2);
            $newpassword = md5($newpassword);
            // Create account
            $STMT = SQL()->prepare('INSERT INTO user_accounts (username,password,datecreated,datelastseen,status) VALUES (?,?,?,?,?)');
            $STMT->bind_param('ssiis', $newusername, $newpassword, time(), time(), uniqid());
            $STMT->execute();
            $STMT->close();

            $id = SQL()->insert_id;
            $this->id = intval($id);
            $this->Email($newemail);
            // Create other tables
            SQL()->query("INSERT INTO user_status (uid, location) VALUES ($id,'Introduction')");
            SQL()->query("INSERT INTO user_settings (uid) VALUES ($id)");
            $GLOBALS['PLYR_LOADED'][$id] = $this;
        }
        $this->id = intval($id);

        // For __destruct method, needs a special reference.... LAME...
        $this->destructdb = SQL();
    }

    function __destruct() {
        if (!isset($this->newdata)) {
            return;
        }
        foreach ($this->newdata as $tablename => $table) {

            $types = '';
            $params = array();
            $query = 'UPDATE user_' . $tablename . ' SET uid=uid';
            foreach ($table as $variable => $value) {
                $query .= ', ' . $variable . '=?';
                $params[] = &$table[$variable];
                if (is_numeric($value)) {
                    $types .= 'i';
                } else {
                    $types .= 's';
                }
            }
            $query .= ' WHERE uid=' . $this->id;
            // SAVE VALUES
            if (count($params) === 0) {
                continue;
            }
            $STMT = $this->destructdb->prepare($query) or die($this->destructdb->error);
            call_user_func_array('mysqli_stmt_bind_param', array_merge(array($STMT, $types), $params));
            $STMT->execute();
            $STMT->close();
        }
    }

    private function _load($table) {
        if (!isset($this->data)) {
            $this->data = array();
        }
        if (isset($this->data[$table])) {
            return;
        }
        $result = SQL()->query("SELECT * FROM user_$table WHERE uid=$this->id");
        $this->data[$table] = $result->fetch_assoc();
        $this->newdata[$table] = array();
    }

    function _var($table, $name, $set = null, $relative = null) {
        $this->_load($table);
        if (!isset($this->data[$table])) {
            return false;
        }
        if (!isset($this->data[$table][$name])) {
            return false;
        }
        if ($set != null) {
            if ($relative === true) {
                $set+=$this->_var($table, $name);
            }
            $this->newdata[$table][$name] = $set;
            $this->Node()->Variable($name, $set);
        }
        if (isset($this->newdata[$table][$name])) {
            return $this->newdata[$table][$name];
        }
        if (isset($this->data[$table][$name])) {
            return $this->data[$table][$name];
        }
    }

    function Username() {
        return $this->_var('accounts', 'username');
    }

    function Password($oldpassword, $newpassword = null, $override=false) {
        $username = $this->Username();
        $oldpassword .= substr($username, -2);
        $oldpassword .= substr($username, -3);
        $oldpassword .= substr($username, 0, 2);
        $oldpassword .= substr($username, 1, 2);
        $oldpassword = md5($oldpassword);
        if ($oldpassword === $this->_var('accounts', 'password') || $override === true) {
            if ($newpassword !== null) {
                $newpassword .= substr($username, -2);
                $newpassword .= substr($username, -3);
                $newpassword .= substr($username, 0, 2);
                $newpassword .= substr($username, 1, 2);
                $newpassword = md5($newpassword);
                $this->_var('accounts', 'password', $newpassword);
            }
            return true;
        }
        return false;
    }

    function Email($value = null) {
        return $this->_var('accounts', 'email', $value);
    }

    function Country($value = null) {
        return $this->_var('accounts', 'country', $value);
    }

    function Timezone($value = null) {
        return $this->_var('accounts', 'timezone', $value);
    }

    function Created($datestring) {
        return date($datestring, $this->_var('accounts', 'datecreated'));
    }

    function LastSeen($datestring = null) {
        if ($datestring == null) {
            return $this->_var('accounts', 'datelastseen', time());
        }
        if ($datestring===true) {
            $length = $this->_var('accounts', 'datelastseen')-time();
            if ($length < 60*60) {
                return 'A few minutes ago';
            } elseif ($length < 60*60*24) {
                return 'A few Hours ago...';
            } elseif ($length < 60*60*24*7) {
                return 'A few days ago...';
            } elseif ($length < 60*60*24*7*4) {
                return 'A few months ago...';
            } elseif ($length < 60*60*24*7*4*3) {
                return 'A really long time ago...';
            }
        } else {
            return $this->_var('accounts', 'datelastseen');
        }
    }

    function Birthday($datestring, $set = null) {
        if ($set != null) {
            $set = strtotime($set);
        }
        return date($datestring, $this->_var('accounts', 'datebirth', $set));
    }

    function Age($datestring) {
        return date($datestring, time() - $this->_var('accounts', 'datebirth'));
    }

    function IpAddress($value = null) {
        return $this->_var('accounts', 'ipaddress', $value);
    }

    function SessionId($value = null) {
        return $this->_var('accounts', 'sessionid', $value);
    }

    function AccountStatus($value = null) {
        return $this->_var('accounts', 'status', $value);
    }

    function AccountType($value = null) {
        return $this->_var('accounts', 'type', $value);
    }

    function IsAdmin($value) {
        // values = [admincontent,adminconsole,adminforum,adminmoderator];
        return (strpos($this->AccountType(),$value)===false?false:true);
    }


    function Coins($value = null) {
        return $this->_var('status', 'coins', $value, true);
    }

    function Cash($value = null) {
        return $this->_var('status', 'cash', $value, true);
    }

    function Alignment($value = null) {
        return $this->_var('status', 'alignment', $value, true);
    }

    function Exp($type, $value = null) {
        if (!is_numeric($type)) {
            $type = array_search($type, GLSS()->TypeNames);
        }
        $type = max(min($type, 18), 1);
        return $this->_var('status', 'xp' . intval($type), $value, true);
    }

    function Level() {
        return 0;
    }

    function LocationDefault($value = null) {
        return $this->_var('settings', 'location_default', $value);
    }

    function Location($value = null) {
        if ($value === false) {
            $value = $this->_var('settings', 'location_default');
        }
        return $this->_var('settings', 'location', $value);
    }

    function LocationX($value = null) {
        return $this->_var('settings', 'location_x', intval($value));
    }

    function LocationY($value = null) {
        return $this->_var('settings', 'location_y', intval($value));
    }

    function Avatar($value = null) {
        return explode('|', $this->_var('settings', 'avatar', $value));
    }

    function AvatarForum($value = null) {
        return $this->_var('settings', 'avatar_forums', $value);
    }

    function Gender($value = null) {
        return $this->_var('settings', 'gender', $value);
    }

    function AboutMe($value = null) {
        return $this->_var('settings', 'profile_aboutme', $value);
    }

    function SiteTheme($value = null) {
        return $this->_var('settings', 'profile_theme', $value);
    }

    // classes
    function Party() {
        return PLYRPARTYOBJ($this->id);
    }

    function Inventory() {
        return PLYRINVENTORYOBJ($this->id);
    }

    function Messages() {
        return PLYRMESSAGESOBJ($this->id);
    }

    function Friends() {
        return PLYRFRIENDSOBJ($this->id);
    }

    function Battles() {
        return PLYRBATTLESOBJ($this->id);
    }

    function Node() {
        return PLYRNODEOBJ($this->id);
    }

    function Species() {
        return PLYRSPECIESOBJ($this->id);
    }

    function Achievment() {
        return PLYRACHIEVMENTOBJ($this->id);
    }

    function Variable() {
        return PLYRVARIABLEOBJ($this->id);
    }

    function Quests() {
        return new PLYRQUESTCLASS($this->id);
    }
    
    function Trainer() {
        return new PLYRTRAINERCLASS($this->id);
    }
    function Printer() {
        if (isset($this->printer)) {
            return $this->printer;
        }
        return $this->printer = new PLYRPRINTERCLASS($this->id);
    }

}


if (!isset($_SESSION)) {
    ini_set("session.cookie_domain", ".drawnimals.com"); 
    session_start();
}

function PLYR() {
    if (!isset($_SESSION['MyID'])) {
        return false;
    }

    return PLYROBJ($_SESSION['MyID']);
}
