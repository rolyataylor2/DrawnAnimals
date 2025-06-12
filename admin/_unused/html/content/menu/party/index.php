<?php
include 'libvariables.php';
?>

<style>
    .partyMenu {width:90%; height:80%; margin:50px auto; padding:0px; }
    .partyMenu .partyMember {float: left; position:relative;
                            height: 25%;
                            width: 48%; padding:0px;
                            margin: 2% 1%;
                            background-color: #51427C;
                            color: white;
                            cursor: pointer;
                            text-shadow: 1px 1px 3px black;
                            border-radius: 150px 10px 10px 10px;
                            transition: 250ms all ease-in-out; -webkit-transition:  250ms all ease-in-out;}
    .mobile .partyMenu .partyMember {width:90%; height:150px;}
    .partyMenu .partyMember .picture {position: absolute; left:0px; top:-15px; height:100%; z-index:2;}
    .partyMenu .partyMember b {font-size:140%; font-weight:bolder;}
    .partyMenu .partyMember .info {position:absolute; top:0px; right:0px; left:10%; margin:10px; text-align:right;}
    .partyMenu .partyMember .info .hpmax {float:right; margin-left:20%; margin-top:10px; width:80%; height:10px; border:2px solid black; 
                                          overflow:hidden;
                                        background-color:#333; border-radius:10px; 
                                        box-shadow:0 0 10px black, inset 0 0 5px black;}
    .partyMenu .partyMember .info .hpmax .hp {float:right; width:100%; height:100%; background-color:green; box-shadow:inset 0 -5px 5px darkgreen;}
    .partyMenu .partyMember .info .status {float:right; width:90%; height:16px}
    .partyMenu .partyMember .info .status img {width:16px; float:right;}
    .partyMenu .partyMember .expmax {position: absolute;
                                    bottom: 5px;
                                    left: 5px;
                                    right: 5px;
                                    overflow: hidden;
                                    height: 10px;
                                    background-color: #333;
                                    box-shadow: inset 0 0 10px black;
                                    border-radius: 4px;}
    .partyMenu .partyMember .expmax .exp {float: left;
                                        height: 100%;
                                        background-color: blueviolet;
                                        border-radius: 0 20px 0px 0;
                                        box-shadow: inset 0px -1px 5px rgb(0, 0, 0)}

</style>
<?php function print_statusAilments(POKEMONCLASS $pet) {
    if ($pet->IS_Badpoison()) echo '<img src="../img/site/ailment-badpoison.png"/>';
    if ($pet->IS_Burn()) echo '<img src="../img/site/ailment-burn.png"/>';
    if ($pet->IS_Confused()) echo '<img src="../img/site/ailment-confused.png"/>';
    if ($pet->IS_Fainted()) echo '<img src="../img/site/ailment-fainted.png"/>';
    if ($pet->IS_Frozen()) echo '<img src="../img/site/ailment-frozen.png"/>';
    if ($pet->IS_Paralysed()) echo '<img src="../img/site/ailment-paralyse.png"/>';
    if ($pet->IS_Poison()) echo '<img src="../img/site/ailment-poison.png"/>';
    if ($pet->IS_Sleep()) echo '<img src="../img/site/ailment-sleep.png"/>';
}; 
function print_shadowStyle(POKEMONCLASS $pet) {
    
    $evil = floor(($pet->GetPersonalityEvil()/65535)*255);
    $evil = 'rgb('.$evil.','.$evil.','.$evil.')';
    $boxshadow = "box-shadow: inset 0 0 0 5px black, inset 125px 0 5px 5px $evil;";
    echo "style='$boxshadow'";
    
}

?>
<div class="partyMenu">
    <?php $_team = $GLOBALS['Myself']->GetPartyAll();?>
    <?php for ($i=0;$i<6;$i++): ?>
        <?php if (isset($_team[$i])):?>
            <div class="partyMember" <?php print_shadowStyle($_team[$i]);?> onclick="Menu.PokemonView(<?php echo $_team[$i]->id;?>);">
                <img class='picture' src='../img/pets/<?php echo $_team[$i]->GetSpecies();?>.png'/>
                <div class='expMax'><div class='exp' style='width:<?php echo $_team[$i]->GetExperianceProgress()*100;?>%;'></div></div>
                <div class='info'>
                    Lv.<?php echo $_team[$i]->GetLevel();?> <b><?php echo $_team[$i]->GetNickName();?></b> <img width='24'src='../img/site/gender<?php echo $_team[$i]->GetGender();?>.png'/><br/>
                    <div class='hpmax'><div class='hp' style='width:<?php echo $_team[$i]->GetHPPercentage()*100;?>%'></div></div>
                    <small><?php echo $_team[$i]->GetHP().'/'.$_team[$i]->GetHPMax();?></small></br>
                    <div class='status'>
                        <?php print_statusAilments($_team[$i]); ?>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="partyMember"></div>
        <?php endif;?>
    <?php endfor; ?>
</div>
<script>
    var toolbar = '<a href="javascript:Menu.hide();"><img src="img/sit/toolbar/back.png"/>Close</a>';
    Menu.addToolbar(toolbar);
</script>