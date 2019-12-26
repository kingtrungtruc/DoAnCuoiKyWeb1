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
$current_user = $user->GetUser($_COOKIE['login']);
$current_user_id = $current_user['user_id'];
$id_user_from = -1;
$users = $user->ListFriends($_COOKIE['login'], 'user_followed');
foreach($users as $usr){
    $id_user_from = $usr['user_id'];
    break;
}
if(isset($_COOKIE['message_friend_id'])){
    if(isset($_POST['user_friend_id'])){
        if($_POST['user_friend_id'] != $_COOKIE['message_friend_id']){
            $id_user_from = $_POST['user_friend_id'];
        }else{
            $id_user_from = $_COOKIE['message_friend_id'];
        }
    }else{
        foreach($users as $usr){
            $id_user_from = $usr['user_id'];
            break;
        }
        if($_COOKIE['message_friend_id'] != $id_user_from){
            $id_user_from = $_COOKIE['message_friend_id'];
        }
    }
    setcookie('message_friend_id', '', time() - 3600);
    setcookie('message_friend_id', $id_user_from, time() + 4);
}else{
    foreach($users as $usr){
        $id_user_from = $usr['user_id'];
        break;
    }
    if(isset($_POST['user_friend_id'])){
        if($_POST['user_friend_id'] != $id_user_from){
            $id_user_from = $_POST['user_friend_id'];
        }
    }
    setcookie('message_friend_id', '', time() - 3600);
    setcookie('message_friend_id', $id_user_from, time() + 4);
}
if($id_user_from == -1){
    foreach($users as $usr){
        $id_user_from = $usr['user_id'];
        break;
    }
    setcookie('message_friend_id', '', time() - 3600);
    setcookie('message_friend_id', $id_user_from, time() + 4);
}

if(isset($_POST['tin_nhan']) && !empty($_POST['tin_nhan'])){
    $tin_nhan = $_POST['tin_nhan']; 
    if($id_user_from != -1){
        $message->AddMessage($current_user_id, $id_user_from, $tin_nhan);
    }       
}
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    if(isset($_POST['deleteMessage'])){
        $message_nof = $message->DeleteMessage($_POST['message_id']);
        header('Location: '.$_SERVER['PHP_SELF']);
    }
}
?>

<?= $formatHelper->addHeader($_COOKIE['login']) ?>
<?= $formatHelper->addFixMenu() ?>
<?= $formatHelper->addLeftMenu($_COOKIE['login'],'turquoise') ?>
<div class="row">
    <div class="col-4 col-sm-4 col-md-4 col-lg-4 col-xl-4">
        <div style="border-right: solid">
            <ul class="list-group list-group-flush" style="padding-right: 10px">
                <?php foreach ($users as $usr) {
                        // real-name & avatar
                        $name = !empty($usr['user_displayname']) ? $usr['user_displayname'] : $usr['user_email'];
                        $src = !empty($usr['user_avatar']) ? 'data:image;base64,'.$usr['user_avatar'] : "asset/img/non-avatar.png";
                        $id = $usr['user_id'];
                ?>
                <li class="list-group-item item-mess-hover">
                    <form action="" method="POST">
                        <div class="new-title">
                            <input name="user_friend_id" value="<?= $id?>" hidden/>
                            <img src="<?= $src ?>" alt="<?= $name ?>" title="<?= $name ?>"> 
                            <h4 id="user"><?= $name ?></h4>
                        </div>
                        <button type="submit" class="btn btn-default">Nhắn tin</button>
                    </form>
                </li>
                <?php } ?>
            </ul>
        </div>
    </div>
    <div class="col-8 col-sm-8 col-md-8 col-lg-8 col-xl-8">
        <div>
            <div class="wrap">
                <div  class="mess">
                    <?php                        
                        $mang_mess = $message->GetAllMessage($current_user_id, $id_user_from);
                        foreach($mang_mess as $mess){
                            if($mess['message_user_id'] == $current_user_id){
                    ?>
                            <form action="" method="POST">
                                <input name="message_id" value="<?= $mess['message_id']?>" hidden> 
                                <div class="may1" title="<?= date('H:i:s - d/m/Y', strtotime($mess['message_created']))?>"><?= $mess['message_content'] ?><br/><button type="submit" name="deleteMessage" style="border: 1px solid #d43f3a; background-color: #d9534f; font-size: 10px; float: right">Xóa</button></div>
                                
                                <br/>
                            </form>
                    <?php
                            } elseif($mess['message_user_id'] == $id_user_from){
                    ?>
                                <input name="message_id" value="$mess['message_id']" hidden> 
                                <div class="may2" title="<?= date('H:i:s - d/m/Y', strtotime($mess['message_created']))?>"><?= $mess['message_content'] ?></div>                                
                                <br/>
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
                        <?php
                            $friend = $user->GetUser('', $id_user_from);
                            $friendname = $friend['user_displayname'];
                        ?>
                            <input type="text" name="tin_nhan" id="tin_nhan" style="width: 100%"  placeholder="Gửi tin nhắn cho <?= $friendname?>" autofocus/> 
                        </td>

                        <td style="text-align: center">
                            <input type="submit" value="Send"/>
                        </td>
                    </tr>
                </table>
            </form>
        </div>
    </div>
</div>
</div>
<?= $formatHelper->ListFriendIndex($_COOKIE['login']) ?>
<?= $formatHelper->closeFooterMessage() ?>