<?php
    include '_include/SQL-Var.php';
    session_start();
    if (isset($_GET['token']) && $_SESSION['token']===$_GET['token']) {
        SQL()->query(file_get_contents('./TB_user_accounts.sql')) or die(SQL()->error);
        SQL()->query(file_get_contents('./TB_user_drawnimals.sql')) or die(SQL()->error);
        SQL()->query(file_get_contents('./TB_user_achievments.sql')) or die(SQL()->error);
        SQL()->query(file_get_contents('./TB_user_friends.sql')) or die(SQL()->error);
        SQL()->query(file_get_contents('./TB_user_items.sql')) or die(SQL()->error);
        SQL()->query(file_get_contents('./TB_user_messages.sql')) or die(SQL()->error);
        SQL()->query(file_get_contents('./TB_user_settings.sql')) or die(SQL()->error);
        SQL()->query(file_get_contents('./TB_user_species.sql')) or die(SQL()->error);
        SQL()->query(file_get_contents('./TB_user_status.sql')) or die(SQL()->error);
        SQL()->query(file_get_contents('./TB_user_variables.sql')) or die(SQL()->error);
//        SQL()->query(file_get_contents('./TB_system_aitrainers.sql'));
        SQL()->query(file_get_contents('./TB_system_attacks.sql')) or die(SQL()->error);
        SQL()->query(file_get_contents('./TB_system_attacks.bulk.sql')) or die(SQL()->error);
        SQL()->query(file_get_contents('./TB_system_drawnimals.sql')) or die(SQL()->error);
        SQL()->query(file_get_contents('./TB_system_drawnimals.bulk.sql')) or die(SQL()->error);
        SQL()->query(file_get_contents('./TB_system_drawnimals_learnset.sql')) or die(SQL()->error);
        SQL()->query(file_get_contents('./TB_system_drawnimals_learnset.bulk.sql')) or die(SQL()->error);
        SQL()->query(file_get_contents('./TB_system_items.sql')) or die(SQL()->error);
//        SQL()->query(file_get_contents('./TB_social_comments.sql'));
//        SQL()->query(file_get_contents('./TB_social_likes.sql'));
//        SQL()->query(file_get_contents('./TB_social_timeline.sql'));
//        SQL()->query(file_get_contents('./TB_temp_battle.sql'));
        die('Finished setting up databases.');
    }
    $_SESSION['token'] = uniqid();
?>
<h1>Install Databases</h1>
<a href='index.php?token=<?php echo $_SESSION['token']; ?>'>Click here</a> to install the System databases.