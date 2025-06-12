<?php

/* 
 * Copyright (c) 2014 User.
 * All rights reserved. This program and the accompanying materials
 * are made available under the terms of the Eclipse Public License v1.0
 * which accompanies this distribution, and is available at
 * http://www.eclipse.org/legal/epl-v10.html
 *
 * Contributors:
 *    User - initial API and implementation and/or initial documentation
 */
if (!LoggedIn()) {
    die('<script>window.location.href="http://PokeWorlds.com/register.php";</script>');
}
include_once 'html/_php/class-regions.php';
include_once 'html/_php/class-region-maps.php';

function SaveLocationData($location,$jsonfilecontents) {
    $tmptilemap = json_decode($jsonfilecontents,true);
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
    $trans_layer_overlay = imagecolorallocatealpha($newImage, 220, 220, 220, 127);
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
    
    $location->TileData(json_encode($tilemap));
    $location->_save();
}

if (VerifyPostToken()) {
    foreach($_FILES['jsonfile']['name'] as $key=>$value) {
        $location = CREATEREGIONMAPCLASS::byNew(PLAYERCLASS::byMe()->Id(),$value);
        SaveLocationData($location,file_get_contents($_FILES['jsonfile']['tmp_name'][$key]));
    }
    die('<script>history.go(-1);</script>');
}
$arguments['TOKEN'] = $_SESSION['token'] = uniqid();
