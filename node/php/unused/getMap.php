<?php 
    include_once 'include/Core/index.php';
    include_once 'include/Core/Catalog/class-catalog-location.php';
    $map = CATALOGLOCATIONCLASS::byId(1);
    
?>
{
    "xindex":<?php echo $argv[1]; ?>,
    "yindex":<?php echo $argv[2]; ?>,
    "data":{
        "tiles":<?php echo $map->Data();?>
    }
}