<?php
if (!LoggedIn()) {
    die('<script>window.location.href="http://PokeWorlds.com/register.php";</script>');
}
include_once 'html/_php/class-regions.php';
include_once 'html/_php/class-region-maps.php';
include_once 'html/_php/class-region-maps-wild.php';
include_once 'html/_php/class-monsters.php';
include_once 'html/_php/class-html-code-table.php';

function SaveLocationData($location) {
    if ($location->UserId() !== PLAYERCLASS::byMe()->Id()) die('You do not have permissions to edit this Location.');
    $location->Name($_POST['name']);
    $location->Description($_POST['description']);
    $location->MinimapIcon($_POST['minimap_icon']);
    $location->MinimapX($_POST['minimap_x']);
    $location->MinimapY($_POST['minimap_y']);
    $location->Reference($_POST['ref_id']);
    $location->Start($_POST['start']);
    $location->Region($_POST['region']);
    $location->RegionX($_POST['region_x']);
    $location->RegionY($_POST['region_y']);
    
    if (!empty($_FILES['jsonfile']['name'])) {
        $tmptilemap = json_decode(file_get_contents($_FILES['jsonfile']['tmp_name']),true);
        $tilemap = [];
        $tilemap['width'] = $tmptilemap['width'];
        $tilemap['height'] = $tmptilemap['height'];
        $tilemap['tilewidth'] = $tmptilemap['tilewidth'];
        $tilemap['tileheight'] = $tmptilemap['tileheight'];
        $tilemap['layers'] = $tmptilemap['layers'];

        // Figure out the bare minimum tiles
        $tilesUsed = [];
        $tilesNewIndex = 1;
        foreach($tilemap['layers'] as $layerNumber=>$i) {
            foreach($i['data'] as $tileIndex=>$ii) {
                if ($ii === 0) {continue;}
                if (!$i['visible']) {
                    unset($tilemap['layers'][$layerNumber]);
                    continue;
                    
                }
                if (!isset($tilesUsed[$ii])) {
                    $tilesUsed[$ii] = $tilesNewIndex;
                    $tilemap['layers'][$layerNumber]['data'][$tileIndex] = $tilesUsed[$ii];
                    $tilesNewIndex += 1;
                } else {
                    $tilemap['layers'][$layerNumber]['data'][$tileIndex] = $tilesUsed[$ii];
                }
            }
        }

        // Gather Tileset Image Data
        $tilesets = [];
        foreach($tmptilemap['tilesets'] as $ii) {
            foreach($_FILES['tilesets']['name'] as $key=>$i) {
                if (strcmp($i,basename($ii['image']))===0) {
                    $image = imagecreatefrompng($_FILES['tilesets']['tmp_name'][$key]);
                    if (isset($ii['transparentcolor'])) {imagecolortransparent($image,hexdec($ii['transparentcolor']));}
                    $tilesets[] = ['data'=>$image,
                                   'margin'=>$ii['margin'],
                                   'spacing'=>$ii['spacing'],
                                   'imagewidth'=>floor(($ii['imagewidth']-$ii['margin'])/($tilemap['tilewidth']+$ii['spacing'])),
                                   'firstgid'=>$ii['firstgid']];
                }
            }
        }

        // Render new tileset
        $newImage = imagecreatetruecolor(8*$tilemap['tilewidth'],
                                        ceil($tilesNewIndex/8)*$tilemap['tileheight']);
        $trans_layer_overlay = imagecolorallocatealpha($newImage, 255, 0, 255, 127);
        imagecolortransparent($newImage, $trans_layer_overlay);
        imagefill($newImage, 0, 0, $trans_layer_overlay);
        
        $xpos = 0; $ypos = 0;
        foreach($tilesUsed as $tileIndex=>$value) {
            for($ii=0;isset($tilesets[$ii]);$ii++) {
                if (!isset($tilesets[$ii+1]) || $tileIndex < $tilesets[$ii+1]['firstgid']) {
                    $localindex = $tileIndex-$tilesets[$ii]['firstgid'];
                    if ($localindex !== 0) {
                        $yfrom = floor($localindex/$tilesets[$ii]['imagewidth']);
                        $xfrom = $localindex-($yfrom*$tilesets[$ii]['imagewidth']);

                        $yspacing = $yfrom*$tilesets[$ii]['spacing'];
                        $xspacing = $xfrom*$tilesets[$ii]['spacing'];

                        $yfrom *= $tilemap['tileheight'];
                        $xfrom *= $tilemap['tilewidth'];
                    } else {
                        $xfrom = 0; $yfrom = 0; $xspacing = 0; $yspacing = 0;
                    }
                    
                    imagecopy($newImage,$tilesets[$ii]['data'],
                            $xpos,$ypos,
                            $xfrom+$xspacing+$tilesets[$ii]['margin'],
                            $yfrom+$yspacing+$tilesets[$ii]['margin'],
                            $tilemap['tilewidth'],$tilemap['tileheight']);

                    $xpos += $tilemap['tilewidth'];
                    if ($xpos >= 8*$tilemap['tilewidth']) {
                        $xpos = 0;
                        $ypos += $tilemap['tileheight'];
                    }
                    $ii = 9999;
                }
            }
        }
        imagesavealpha($newImage, true);
        imagepng($newImage,'/var/www/html/img/location/tileset/'.$location->Id().'.png');

        if ($tilemap['tilewidth'] === 16) {
            shell_exec('convert '.'/var/www/html/img/location/tileset/'.$location->Id().'.png'.' -scale 200% '.'/var/www/html/img/location/tileset/'.$location->Id().'.png');
            $tilemap['tilewidth'] = 32;
            $tilemap['tileheight'] = 32;
            imagedestroy($newImage);
            $newImage = imagecreatefrompng('/var/www/html/img/location/tileset/'.$location->Id().'.png');
            imagecolortransparent($newImage, imagecolorallocate ($newImage, 220, 220, 220));
            
        }
        // Render map preview
        $preview = imagecreatetruecolor($tilemap['width']*$tilemap['tilewidth'],
                                        $tilemap['height']*$tilemap['tileheight']);
        $xpos = 0; $ypos = 0;
        foreach($tilemap['layers'] as $i) {
            if (!$i['visible']) continue;
            foreach($i['data'] as $tileIndex) {
                if ($tileIndex !== 0) {
                    $localindex = $tileIndex-1;
                    $yfrom = floor($localindex/8);
                    $xfrom = $localindex-($yfrom*8);
                    $yfrom *= $tilemap['tileheight'];
                    $xfrom *= $tilemap['tilewidth'];
                    imagecopy($preview,$newImage,
                              $xpos,$ypos,$xfrom,$yfrom,
                              $tilemap['tilewidth'],$tilemap['tileheight']);
                    
                }
                $xpos += $tilemap['tilewidth'];
                if ($xpos >= ($tilemap['width']*$tilemap['tilewidth'])) {
                    $xpos = 0;
                    $ypos += $tilemap['tileheight'];
                }

                
            }
            $xpos = 0;
            $ypos = 0;
        }
        imagepng($preview,'/var/www/html/img/location/previews/'.$location->Id().'.png');
        foreach($tilesets as $i) {
            imagedestroy($i['data']);
        }
        imagedestroy($preview);
        imagedestroy($newImage);
        
        shrink_png('/var/www/html/img/location/previews/'.$location->Id().'.png');
        
        $image = compress_png('/var/www/html/img/location/previews/'.$location->Id().'.png');
        file_put_contents('/var/www/html/img/location/previews/'.$location->Id().'.png',$image);
        $image = compress_png('/var/www/html/img/location/tileset/'.$location->Id().'.png');
        file_put_contents('/var/www/html/img/location/tileset/'.$location->Id().'.png',$image);
        
    } else {$tilemap = json_decode($location->TileData(),true);}
    $tilemap['playerlayer'] = (isset($_POST['playerlayer'])?intval($_POST['playerlayer']):0);
    $location->Width($tilemap['width']*$tilemap['tilewidth']);
    $location->Height($tilemap['height']*$tilemap['tileheight']);
    
    $location->TileData(json_encode($tilemap));

    /**
     * Save WildSet
     */
    $wildmonsters = HTMLLISTABLETABLECLASS::byNew('WildMonsterTable');
    $wildmonsters->addLabel('Method');
    $wildmonsters->addLabel('Pokemon');
    $wildmonsters->addLabel('Level Min');
    $wildmonsters->addLabel('Level Max');
    $oldwildmonsters = CREATEREGIONMAPCLASSWILD::byMap($location->Id());
    foreach($wildmonsters->renderArray() as $i) {
        $add = true;
        $match = [];
        preg_match('#\((.*?)\)#', $i['Pokemon'], $match);
        $monster = CREATEMONSTERCLASS::byId($match[1]);
        
        foreach($oldwildmonsters as $key=>$ii) {
            if ($ii->Species() == $monster->Id() && 
                $ii->Method() == $i['Method'] && 
                $ii->MinLevel() == $i['Level Min'] &&
                $ii->MaxLevel() == $i['Level Max']) {
                    array_splice($oldwildmonsters,$key,1);
                    $add = false;
            }      
        }
        if ($add) {
            if (!empty($monster->Id())) {
                preg_match('#\((.*?)\)#', $i['Method'], $match);
                CREATEREGIONMAPCLASSWILD::byNew($location->Id(),$match[1],$monster->Id(),$i['Level Min'],$i['Level Max']);
            }
        }
    }
    foreach($oldwildmonsters as $key=>$ii) { $ii->_delete(); }
    
    $location->_save();
}

if (isset($_GET['id'])) {
    if ($_GET['id'] == -1) {
        if (VerifyPostToken()) {
            $location = CREATEREGIONMAPCLASS::byNew(PLAYERCLASS::byMe()->Id(),$_POST['name']);
            SaveLocationData($location);
            die('<script>history.go(-2);</script>');
        }
    }
    
    $location= CREATEREGIONMAPCLASS::byId($_GET['id']);
    $location->_load();
    
    if (VerifyPostToken()) {
        SaveLocationData($location);
        die('<script>history.go(-2);</script>');
    }
    $arguments['LOCATION'] = (isset($location->data)?$location->data:array());
    
    
    $tilemap = json_decode($location->TileData(),true);
    if (isset($tilemap['layers'])) {
        $arguments['PLAYERLAYER'] = (isset($tilemap['playerlayer'])?$tilemap['playerlayer']:0);
        $arguments['LAYERS'] = $tilemap['layers'];
    } else {
        $arguments['PLAYERLAYER'] =0;
        $arguments['LAYERS'] = 0;
    }
    
    /**
     * Pull Users Regions
     */
    $regions = CREATEREGIONCLASS::byUserId(PLAYERCLASS::byMe()->Id());
    $arguments['REGIONS'] = array();
    foreach($regions as $i) {
        $arguments['REGIONS'][] = array('name'=>$i->Name(),'id'=>$i->Id());
    }
    
    /**
     * Wild POkemon
     */
    $wildmonsters = HTMLLISTABLETABLECLASS::byNew('WildMonsterTable');
    $environments = [];
    $environments[] = array('value'=>'<b>SurfingWater Set 0</b>', 'data'=>'(0)SurfingWater Set 0');
    $environments[] = array('value'=>'<b>SurfingWater Set 1</b>', 'data'=>'(1)SurfingWater Set 1');
    $environments[] = array('value'=>'<b>Grass Set 0</b>', 'data'=>'(2)Grass Set 0');
    $environments[] = array('value'=>'<b>Grass Set 1</b>', 'data'=>'(3)Grass Set 1');
    $environments[] = array('value'=>'<b>Grass Set 2</b>',  'data'=>'(4)Grass Set 2');
    $environments[] = array('value'=>'<b>TallGrass Set 0</b>', 'data'=>'(5)TallGrass Set 0');
    $environments[] = array('value'=>'<b>TallGrass Set 1</b>', 'data'=>'(6)TallGrass Set 1');
    $environments[] = array('value'=>'<b>TallGrass Set 2</b>', 'data'=>'(7)TallGrass Set 2');
    $environments[] = array('value'=>'<b>FishingWater Set 0</b>', 'data'=>'(8)FishingWater Set 0');
    $environments[] = array('value'=>'<b>FishingWater Set 1</b>', 'data'=>'(9)FishingWater Set 1');
    $wildmonsters->addLabel('Method', $environments);
    
    $monsters = [];
    foreach(CREATEMONSTERCLASS::byAll(' ') as $i) {
        $form = $i->Form().' ';
        if (strcmp($i->Form(),'Basic') === 0) {$form = '';}
        $monsters[] = array('value'=>'<b>'.$form.ucwords($i->Name()).'</b>', 'data'=>$form.ucwords($i->Name()).' ID('.$i->Id().')');
    }
    $wildmonsters->addLabel('Pokemon',$monsters);
    $wildmonsters->addLabel('Level Min');
    $wildmonsters->addLabel('Level Max');
    
    foreach(CREATEREGIONMAPCLASSWILD::byMap($location->Id()) as $i ) {
        if (!empty($i->Id())) {
            $monster = CREATEMONSTERCLASS::byId($i->Species());
            $form = $monster->Form();
            if (strcmp($form,'Basic')===0) {$form = '';}
            $wildmonsters->addRow(array($environments[$i->Method()]['data'], $form.ucwords($monster->Name()).' ID('.$monster->Id().')', $i->MinLevel(), $i->MaxLevel()), true);
        }
    }
    $arguments['WILDPOKEMONLIST'] = $wildmonsters->renderHTML();
    
    
    
    $arguments['EDIT'] = ($location->UserId() === PLAYERCLASS::byMe()->Id());
    $arguments['TOKEN'] = $_SESSION['token'] = uniqid();
}