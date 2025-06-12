<?php
include_once 'html/_php/class-monsters.php';
include_once 'html/_php/class-like.php';
include_once 'html/_php/class-regions.php';
include_once 'html/_php/class-monsters-learnset.php';

$_GET['o'] = (isset($_GET['o'])?$_GET['o']:0);

$conversions = array();
$conversions['text'] = function($string) {
    global $results,$arguments;
    if (count($results) > 0) {
        $arguments['SEARCHDESCRIPTION'] .= ' \' & Contain '.$string.'\'';
        foreach($results as $key=>$i) {
            if (strpos(strtolower($i->Name()),strtolower($string)) === false) {
                unset($results[$key]);
            }
        }
    } else {
        $arguments['SEARCHDESCRIPTION'] .= ' \' Contain '.$string.'\'';
        $string = str_replace('+', ' ', $string);
        $string = '%' . str_replace('%', '', $string) . '%';
        $results = array_merge($results, TABLETEMPLATE::_search(['species'], [$string], 'CREATEMONSTERCLASS'));
    }
};
$conversions['all'] = function($string) {
    global $results,$arguments;
    if (count($results) > 0) return;
    
    $_GET['o'] = ( isset($_GET['o']) ? intval($_GET['o']) : 0);
    $_GET['l'] = ( isset($_GET['l']) ? intval($_GET['l']) : 30);
    $addon = ' ORDER BY id DESC LIMIT '.$_GET['o'].','.$_GET['l'];
    
    $results = TABLETEMPLATE::_all('CREATEMONSTERCLASS',$addon);
};
$conversions['starts_with'] = function($string) {
    global $results, $arguments;
    if (count($results) > 0) {
        $arguments['SEARCHDESCRIPTION'] .= ' & Start With \''.$string.'\'';
        foreach($results as $key=>$i) {
            if (strpos(strtolower($i->Name()),strtolower($string)) !== 0) {
                unset($results[$key]);
            }
        }
    } else {
        $arguments['SEARCHDESCRIPTION'] .= ' Start With \''.$string.'\'';
        $string = str_replace('%', '', $string) . '%';
        $results = array_merge($results, TABLETEMPLATE::_search(array('species'), array($string), 'CREATEMONSTERCLASS'));
    }
};
$conversions['ends_with'] = function($string) {
    global $results, $arguments;
    if (count($results) > 0) {
        $arguments['SEARCHDESCRIPTION'] .= ' & End With \''.$string.'\'';
        foreach($results as $key=>$i) {
            if (strcmp(substr(strtolower($i->Name()),-strlen($string)),strtolower($string)) !== 0) {
                unset($results[$key]);
            }
        }
    } else {
        $arguments['SEARCHDESCRIPTION'] .= ' End With \''.$string.'\'';
        $string = '%' . str_replace('%', '', $string);
        $results = array_merge($results, TABLETEMPLATE::_search(array('species'), array($string), 'CREATEMONSTERCLASS'));
    }
};
$conversions['region'] = function($string) {
    global $results, $arguments;
    $region = CREATEREGIONCLASS::byId($string);
    
    if (count($results) > 0) {
        $arguments['SEARCHDESCRIPTION'] .= ' & Regional Dex: \''.$region->Name().'\'';
        foreach($results as $key=>$i) {
            if ($i->AppearanceRegion() !== $region->Id()) {
                unset($results[$key]);
            }
        }
    } else {
        $arguments['SEARCHDESCRIPTION'] .= ' Regional Dex: \''.$region->Name().'\'';
        $string = $region->Id();
        $results = TABLETEMPLATE::_get('CREATEMONSTERCLASS', ['appearance_region'], [$string], 'ORDER BY number LIMIT '.$_GET['o'].',30');
    }
};
$conversions['user'] = function($string) {
    global $results, $arguments;
    $user = PLAYERCLASS::byUsername($string);
    if (count($results) > 0) {
        $arguments['SEARCHDESCRIPTION'] .= ' & Created By \''.$string.'\'';
        foreach($results as $key=>$i) {
            if ($i->UserId() !== $user->Id()) {
                unset($results[$key]);
            }
        }
    } else {
        $arguments['SEARCHDESCRIPTION'] .= ' Created By \''.$string.'\'';
        $string = $user->Id();
        $results = TABLETEMPLATE::_search(array('uid'), array($string), 'CREATEMONSTERCLASS');
    }
};
function print_result($objects,$twigfile) {
    $results = array();
    foreach ($objects as $i) {
        if (!empty($i->Id())) {
            $data = $i->data;
            $data['visible'] = true;
            $data['imageUrl'] = $i->Render()->imageUrl();
            $data['likes'] = $i->Likes() || 0;
            $data['liked'] = $i->Liked();
            $data['caught'] = PLAYERCLASS::byMe()->Caught($i->Id());
            if (PLAYERCLASS::byMe()->Id() !== 1 && 
                PLAYERCLASS::byMe()->Id() !== $i->UserId() && 
                PLAYERCLASS::byMe()->Caught($i->Id()) === false) {
                $data['visible'] = PLAYERCLASS::byMe()->Seen($i->Id());
                $data['species'] = '???';
                $data['number'] = '???';
                
                $data['description'] = "This Pokemon is not in your pokedex!";
            }
            $results[] = TWIG()->render($twigfile, array('data' => $data, 'PLAYERID' => PLAYERCLASS::byMe()->Id()));
        }
    }
    return $results;
}

$results = array();


if (isset($_GET['by']) && strlen(str_replace('%', '', $_GET['by'])) >= 2) {
    $search = explode(' ', strtolower($_GET['by']) . ' ');
    $arguments['SEARCHDESCRIPTION'] = '';
    foreach($search as $index=>$i) {
        if (strpos($i, 'page:') !== false) {
            $key = explode(':', $i);
            $_GET['o'] = intval($key[1]*30);
            unset($search[$index]);
        }
        
    }
    
    foreach ($search as $i) {
        if (strcmp($i, ' ') === 0)
            continue;
        if (strcmp($i, '') === 0)
            continue;
        if (strpos($i, ':') !== false) { 
            $key = explode(':', $i);
            if (isset($conversions[$key[0]])) {
                $conversions[$key[0]](str_replace('+',' ',$key[1]));
            }
        } else {
            if (isset($conversions[$i])) {
                $conversions[$i]($i);
            } else {
                $conversions['text']($i);
            }
        }

    }
    
    $arguments['RESULTS'] = array();
    $arguments['RESULTS'] = print_result($results,"html/_templates/Search/CREATEMONSTERCLASS.twig");
    $arguments['SEARCH'] = $_GET['by'];
    $arguments['SUBJECT'] = $_GET['g'];
    $page = floor($_GET['o']/30);
    if (substr_count($_GET['by'],'page:') === 0) $_GET['by'] = 'page:0 '.$_GET['by'];
    $arguments['NEXTPAGEURL'] = 'http://PokeWorlds.com/play.php?g='.$_GET['g'].'&by='.preg_replace('/page:[^ ]*/','page:'.($page+1),$_GET['by']);
    $arguments['PREVPAGEURL'] = 'http://PokeWorlds.com/play.php?g='.$_GET['g'].'&by='.preg_replace('/page:[^ ]*/','page:'.($page-1),$_GET['by']);

} else {
    $arguments['SEARCH'] = $_GET['by'];
    $arguments['SUBJECT'] = $_GET['g'];
    $arguments['SEARCHDESCRIPTION'] = '<center>Minimum of 2 letters required.</center>';
}

$arguments['OFFSET'] = intval($_GET['o']);

