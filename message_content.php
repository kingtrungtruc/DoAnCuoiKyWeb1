<!-- GUEST -->
<?php
require_once 'inc/autoload.php';

// Format Helper
$formatHelper = new FormatHelper();
$user = new UserController();
$message = new MessageController();

// DIRECTION
if (!isset($_COOKIE['login'])) {
    header('Location: index.php');
}

$users = $user->ListFriends($_COOKIE['login'], 'user_followed');
foreach($users as $usr){
    $id_user_from = $usr['user_id'];
    break;
}
if(isset($_POST['user_friend_id'])){
    $id_user_from = $_POST['user_friend_id'];
}

?>

<?= $formatHelper->addHeader($_COOKIE['login']) ?>
<?= $formatHelper->addFixMenu() ?>
<?= $formatHelper->addLeftMenu($_COOKIE['login'],'turquoise') ?>
<div class="row">
    <div class="col-12 col-sm-12 col-md-4 col-lg-4 col-xl-4">
        <div style="border-right: solid">
            <ul class="list-group list-group-flush" style="padding-right: 10px">
                <?php foreach ($users as $usr) {
                        // real-name & avatar
                        $name = !empty($usr['user_displayname']) ? $usr['user_displayname'] : $usr['user_email'];
                        $src = !empty($usr['user_avatar']) ? 'data:image;base64,'.$usr['user_avatar'] : "asset/img/non-avatar.png";
                        $id = $usr['user_id'];
                ?>
                <li class="list-group-item item-mess-hover">
                    <form action="" method="POST" href="message_content.php?id=$id">
                        <div class="new-title">
                            <input name="user_friend_id" value="<?= $id?>" hidden/>
                            <img src="<?= $src ?>" alt="<?= $name ?>" title="<?= $name ?>"> 
                            <h4 id="user"><?= $name ?></h4>
                        </div>
                    </a>
                </li>
                <?php } ?>
            </ul>
        </div>
    </div>
    <div class="col-12 col-sm-12 col-md-8 col-lg-8 col-xl-8">
        <div>
            <div class="wrap">
                <div  class="mess">
                    <?php
                        $current_user = $user->GetUser($_COOKIE['login']);
                        $current_user_id = $current_user['user_id'];
                        
                        $mang_mess = $message->GetAllMessage($current_user_id, $id_user_from);
                        foreach($mang_mess as $mess){
                            if($mess['message_user_id'] == 2){
                    ?>
                            <div class="may1"><?= $mess['message_content'] ?></div>
                    <?php
                            } elseif($mess['message_user_id'] == 1){
                    ?>
                            <div class="may2"><?= $mess['message_content'] ?></div>
                    <?php                    
                            }
                        }
                    ?>
                </div>
            </div>            

            <form id="chatbox" action="" method="POST">
                <table border="3" cellpadding="10" style="border-collapse: collapse; margin: 0px auto; width: 395px">
                    <tr>
                        <td>
                            <input type="text" name="may" value="2" hidden/>
                            <input type="text" name="tin_nhan" id="tin_nhan" style="width: 100%" autofocus/> 
                        </td>

                        <td style="text-align: center">
                            <input type="submit" value="Send"/>
                        </td>
                    </tr>
            </form>
        </div>
    </div>
</div>
</div>
<?= $formatHelper->ListFriendIndex($_COOKIE['login']) ?>
<?= $formatHelper->closeFooterMessage() ?>