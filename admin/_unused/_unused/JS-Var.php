<?php
    
    function JsSetVariable($name, $value) {
        echo $name;
        echo ' = ';
        if (is_string($value)) 
            echo '"'.$value.'"';
        else echo $value;
        echo ';';
    };
    function UpdateSelf() {
        JsSetVariable('server.Username',$GLOBALS['Myself']->GetUsername());
        JsSetVariable('server.Userid',$GLOBALS['Myself']->userId);
        JsSetVariable('server.Coins',$GLOBALS['Myself']->GetCoins());
        JsSetVariable('server.Cash',$GLOBALS['Myself']->GetCash());
        JsSetVariable('server.Location',$GLOBALS['Myself']->GetLocationName());
        JsSetVariable('server.NetworkKey',$_SESSION['NetworkKey']);
    };
    
    function ClearAllMessages() {
        echo "server.Messages = [];";
    }
    function AddMessage($subject,$content,$sender,$attachment, $read, $starred, $paper,$date) {
        echo "server.Messages.push(new ClassMessage('$subject','$content','$sender','$attachment', $read, $starred, '$paper',$date));\n";
    }
    function UpdateMessages() {
        ClearAllMessages();
        $result = mysql_query('SELECT * FROM usr_letter WHERE userid='.$GLOBALS['Myself']->userId);
        while($message = mysql_fetch_array($result)) 
            AddMessage(mysql_escape_string ($message['subject']),
                        mysql_escape_string ($message['content']),
                        $message['senderid'],
                        $message['item'],
                        $message['isread'],
                        $message['starred'],
                        $message['paper'],
                        $message['sent_date']);
    };
    
    function ClearAllFriends() {
        echo "server.Friends = [];";
    };
    function AddFriend($username,$userid, $confirmed,$tag) {
        echo "server.Friends.push(new ClassFriend('$username',$userid,$confirmed, $tag));";
    };
    function UpdateFriends() {
        ClearAllFriends();
        $result = mysql_query('SELECT * FROM usr_friend WHERE userid='.$GLOBALS['Myself']->userId);
        while($friend = mysql_fetch_array($result)) 
            AddFriend(mysql_escape_string ($friend['friendname']), $friend['friendid'], $friend['confirmed'], $friend['tag']);
    };
    
    function ClearAllItems() {
        echo "server.Inventory = [];";
    };
    function AddItem($type, $ids, $cat) {
        echo "server.Inventory.push(new ClassItem('$type',$ids, '$cat'));";
    };
    function UpdateInventory() {
        ClearAllItems();
        $result = mysql_query('SELECT * FROM usr_item WHERE userid='.$GLOBALS['Myself']->userId.' ORDER BY name');
        $name = '';
        
        while($item = mysql_fetch_array($result)) {
            if ($name === '') {
                $name = $item['name'];
                $cat = $item['catagory'];
                $ids = '['.$item['id'];
            } elseif ($name !== $item['name']) {
                $ids .= ']';
                AddItem(mysql_escape_string ($name), $ids, $cat);
                $ids = '['.$item['id'];
                $name = $item['name'];
                $cat = $item['catagory'];
            } else {
                $name = $item['name'];
                $cat = $item['catagory'];
                $ids .= ','.$item['id'];
            }
        }
    };
    
    function ClearMyTeam() {};
    function AddMyTeam() {};
    function UpdateMyTeam() {};
    UpdateSelf();
    UpdateMessages();
    UpdateFriends();
    UpdateInventory();
    
?>
