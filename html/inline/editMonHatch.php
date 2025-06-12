<?php
    /**
     *  Headers
     */
    session_start();
    include_once 'html/_php/mysqli.php';
    include_once 'html/_php/class-player.php';
    include_once 'html/_php/class-monsters.php';
    include_once 'html/_php/class-updates.php';
    /**
     * Variables
     */
    if (isset($_POST['id'])) {
        if (VerifyPostToken()) {
            $monster = MONSTERCLASS::byId($_POST['id']);
            if ($monster->EggHatch()) {
                $monster->_save();
                PLAYERCLASS::byMe()->Caught($monster->Id(),true);
?>
    <h1>Your Egg Hatched!</h1>
    <sub>Your Egg Hatched into a <?php echo ucwords($monster->Species()->Name()); ?></sub><br/>
    <img src="<?php echo $monster->Render()->imageUrl();?>"/>
    <script>
        inlinePopupClose = function() {window.location.reload(true);}
    </script>
<?php
                die();
            }
            
        }
    }
    $monster = MONSTERCLASS::byId($_GET['arguments'][0]);
    $token = $_SESSION['token'] = uniqid();
?>
<h1>Your Egg Is Hatching!</h1>
<sub>** Twiddle Twiddle ** Twiddle Twiddle **</sub>
<div style="position:relative; width:300px; height:300px; display:inline-block;">
    <img id="arm" src="http://PokeWorlds.com/img/mon/eggCracked.png"/>
</div><br/>

<br/>
<a href='javascript:' onclick='inlinePopupSubmit({"id":<?php echo $_GET['arguments'][0]; ?>,"token":"<?php echo $token; ?>"},"editMonHatch");'>Continue</a>
<style>
@-webkit-keyframes arm {
  from { -webkit-transform: rotate(6deg); -moz-transform: rotate(6deg); -o-transform: rotate(6deg); }
  60% { -webkit-transform: rotate(0deg); -moz-transform: rotate(0deg); -o-transform: rotate(0deg); }
  80% { -webkit-transform: rotate(-6deg); -moz-transform: rotate(-6deg); -o-transform: rotate(-6deg); }
  to { -webkit-transform: rotate(0deg); -moz-transform: rotate(0deg); -o-transform: rotate(0deg); }
}
 
#arm {
  position:absolute;
  top:0;
  left:0;
  
  -webkit-transform-origin:150px 300px;
  -webkit-animation:arm 2.0s ease-in-out infinite alternate;
  -moz-transform-origin:150px 300px;
  -o-transform-origin:150px 300px;

}
</style>