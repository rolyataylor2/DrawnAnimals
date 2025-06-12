<?php

class NODEMAPCLASS {

    function AttackTree() {
        
    }

    function AdminTree() {
        
    }

    function AttackGrass() {
        
    }

    function AdminGrass() {
        
    }

    function SetObject() {
        
    }

    function AddNPC() {
        
    }

}

class NODESENDEVERYONECLASS {

    function Queue() {
        
    }

    function Chat() {
        
    }

    function NewsFeed() {
        
    }

    function Weather() {
        
    }

}

class NODECLASS {

    function _send($action, $data) {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'http://127.0.0.1');

        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:'));
        curl_setopt($ch, CURLOPT_PORT, 8080);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);

        curl_setopt($ch, CURLOPT_POST, true);

        $data['action'] = $action;
        $data = json_encode($data);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(array('data'=>$data)));

        curl_exec($ch);
        curl_close($ch);
    }

    function Everyone() {
        return new NODESENDEVERYONECLASS();
    }

    function Map() {
        
    }

}

$GLOBALS['NODECLASS'] = new NODECLASS();

function NODE() {
    return $GLOBALS['NODECLASS'];
}
