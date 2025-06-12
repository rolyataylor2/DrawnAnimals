<?php
include 'libvariables.php';
?>
<style>
    .bagMenu {width: 90%; height: auto;
                margin: 50px auto;
                padding: 0px;
                background-color: white;
                border-radius: 10px 40px;
                overflow: hidden;}
    .bagMenu .title {width: 100%;
                    height: 48px;
                    background-color: #999;
                    overflow: hidden;
                    line-height: 48px;
                    font-size: 160%;
                    font-weight: bolder;
                    color: white;}
    .bagMenu .catagory {float: left;
                        height: 100%; width: 40%;
                        border-radius: 0 42px 0px 0;
                        background-color: black;
                        color: white;
                        text-align: center;
                        font-size: 70%;
                        margin-right: 20px;}
    .mobile .bagMenu .catagory {width:auto; padding:0 10px;}
    .bagMenu .itemList { width: 100%; padding: 35px 0;}
    .bagMenu .itemList .item { float: left;
                                width: 45%; margin: 0 1.5%; text-decoration: none; color: black;
                                padding: 1%; border-bottom:1px dashed #CCC;
                                font-weight: bolder;}
    .bagMenu .itemList .item:hover { border-color:#333; }
    .mobile .bagMenu .itemList .item { float:left; width:90%; margin: 0 5%; font-size:110%;}
    .bagMenu .itemList .item .quantity {float:right; }
    
</style>
<div class="bagMenu">
    <div class="title"><div class="catagory">All</div> Items</div>
    <div class="itemList">
        <?php 
        $items = $GLOBALS['Myself']->GetItemList();
        $number = 1;
        while ($item = array_shift($items)):
            if (isset($items[0]))
                if ($items[0]['name'] == $item['name']) 
                    { $number+=1; continue; } 
            echo "<a href='javascript:Menu.ItemView(".$item['id'].")' class='item itmcat-".$item['catagory']."'>".ucwords($item['name']);
            if ($number != 1) echo "<div class='quantity'>$number</div>";
            echo "</a>";
            $number = 1; 
         endwhile; 
        ?>
        <div style="clear:both;"></div>
    </div>
</div>

<script>
    function bagMenuCatagory(name) {
        if (name==='all') {
            $('.bagMenu .itemList .item').show(100);
            $('.bagMenu .title .catagory').html("All");
        } else {
            $('.bagMenu .itemList .item:not(.itemList .item.itmcat-'+name+')').hide(100);
            $('.bagMenu .itemList .item.itmcat-'+name).show(100);
            $('.bagMenu .title .catagory').html('('+name+' Pocket)');
        }
    }
    var toolbar = '<a href="javascript:Menu.hide();"><img src="img/sit/toolbar/back.png"/>Close</a>';
    toolbar += '<div class="menu" onclick="$(this).toggleClass(\'open\').siblings().removeClass(\'open\');">';
    toolbar += '<div class="container" >';
    toolbar += '<a href="javascript:bagMenuCatagory(\'all\');">Show All</a>';
    toolbar += '<a href="javascript:bagMenuCatagory(\'Healing\');">Healing</a>';
    toolbar += '<a href="javascript:bagMenuCatagory(\'Orb\');">Orbs</a>';
    toolbar += '<a href="javascript:bagMenuCatagory(\'Food\');">Food</a>';
    toolbar += '<a href="javascript:bagMenuCatagory(\'Clothing\');">Clothing</a>';
    toolbar += '<a href="javascript:bagMenuCatagory(\'Fossil\');">Fossils</a>';
    toolbar += '<a href="javascript:bagMenuCatagory(\'Key\');">Key Items</a>';
    toolbar += '</div><img src="img/sit/menu.png"/>Pocket</div>';
    
    toolbar += '<div class="menu" onclick="$(this).toggleClass(\'open\').siblings().removeClass(\'open\'); ">';
    toolbar += '<div class="container" >';
    toolbar += '<a href="javascript:Menu.A_InventoryAddItem();">Add Item</a>';
    toolbar += '<a href="javascript:;">Give Item</a>';
    toolbar += '<a href="javascript:;">View Inventory</a>';
    toolbar += '</div><img src="img/sit/toolbar/admin.png"/></div>';
                
    Menu.addToolbar(toolbar);
</script>