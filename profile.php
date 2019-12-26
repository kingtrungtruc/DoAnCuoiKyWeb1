<?php
    require_once 'inc/autoload.php';
    //Trang
    // Format Helper
    $formatHelper = new FormatHelper();
    $user = new UserController();
    $status = new StatusController();

    if (!isset($_COOKIE['login'])) {
        header('Location: index.php');
    }

    if ($_SERVER['REQUEST_METHOD'] == "POST") {

        if (isset($_POST['addStatus'])) {
            $message = $user->NewStatus($_COOKIE['login'], $_FILES, $_POST);
            header('Location: '.$_SERVER['PHP_SELF']);
        }
        if (isset($_POST['changeStatus'])){
            $messagechange = $user->ChangeStatus($_COOKIE['login'], $_POST);
            header('Location: '.$_SERVER['PHP_SELF']);
        }
    }

    $style = 'danger';
    $message = "";//Thông báo KQ từ server trả về
    if ($_SERVER['REQUEST_METHOD'] == "POST") {
        $user = new UserController();
        
        if (isset($_POST['unFriend'])) {
            $message = $user->DeleteFriend($_COOKIE['login'], $_POST['name']);
            $style = 'info';
        } 
        $display = "class='alert alert-$style' style='display: block; text-align: center;'";
    }
    //Xác định mối quan hệ gì
    $noRelationship = false;
    $following= false;
    $follows= false;
    $followed= false;
    $user1 =$user->GetUser($_COOKIE['login']);
    $id_user1 = $user1['user_id'];
    $id_user2 = $user1['user_id'];
    $user2 = $user->GetUser('',$id_user2);
    if(isset($_GET['id'])){
        $id_user2 = $_GET['id'];
        $user2 = $user->GetUser('',$id_user2);
        $followedA = !empty($user1['user_followed']) ? unserialize($user1['user_followed']) : [];
        $followedB = !empty($user2['user_followed']) ? unserialize($user2['user_followed']) : [];

        $followingA = !empty($user1['user_following']) ? unserialize($user1['user_following']) : [];
        $followsA = !empty($user1['user_follows']) ? unserialize($user1['user_follows']) : [];

        $followsB = !empty($user2['user_follows']) ? unserialize($user2['user_follows']) : [];
        $followingB = !empty($user2['user_following']) ? unserialize($user2['user_following']) : [];
    }

    //nếu là chính mình
    if($id_user1==$id_user2)
    {
        $user2 = $user1;
    }
    //Nếu là bạn bè
    else if (in_array($id_user1, $followedB) && in_array($id_user2, $followedA)) {
        $followed=true;
    }
    //Nếu A đang theo dõi B
    else if (in_array($id_user2, $followingA) || in_array($id_user1, $followsB)) {
        $following = true;
    }
    //Nếu B đang theo dõi A
    else if(in_array($id_user1, $followingB) || in_array($id_user2, $followsA))
    {
        $follows=true;
    }
    else{
        $noRelationship=true;
    }

    $user2_avatar = !empty($user2['user_avatar']) ? 'data:image;base64,'.$user2['user_avatar'] : "asset/img/non-avatar.png";
    $statusOfUserB = $status->ShowStatusWithRelationship($user1['user_id'],$id_user2);
?>
<?= $formatHelper->addHeader($_COOKIE['login']) ?>
<?= $formatHelper->addFixMenu() ?>
<?= $formatHelper->addLeftMenu($_COOKIE['login'], 'gold') ?>
<?php if($message != ""){ ?>
    <div <?= @$display ?: "class='alert alert-$style' style='display:none;text-align: center;'"?>> <?= @$message?: "" ?> </div>
<?php } ?>
<div class="user-info">
    <div class="info-title">
        <span><img src="<?= $user2_avatar?>"/></span>
        <span id="name"><?=$user2['user_displayname']?></span>
        <?php if($user2['user_phone'] == null){?>
            <div>Số điện thoại: Chưa cập nhật</div>
        <?php }else{?>
            <div>Số điện thoại: <?= $user2['user_phone']?></div>
        <?php } ?>
        <div>Năm sinh: <?= $user2['user_birthday']?></div>
        <?php if($id_user1 != $id_user2){?>
            <div>Đăng nhập lần cuối: <?php echo date('H:i:s - d/m/Y', strtotime($user2['user_lastlogin']));?></div>
        <?php }?>
    </div>
    <div class="info-body">
        <?php
            if($id_user2 != $id_user1){
                if($noRelationship){
        ?>
        <form action="following.php" method="POST">
            <input type="hidden" name="name" value="<?= $user2['user_email']?>">
                <button type="submit" class="btn btn-success" name="addFriend">Thêm bạn bè</button>
        <?php
                }
                if($follows){
        ?>
        <form action="follows.php" method="POST">
            <input type="hidden" name="name" value="<?= $user2['user_email']?>">
                <button type="submit" class="btn btn-success" name="acceptFriend">Chấp nhận</button>
                <button type="submit" class="btn btn-danger" name="declineFriend">Từ chối</button>
        <?php
                }
                if($followed){
        ?>
        <form action="" method="POST">
            <input type="hidden" name="name" value="<?= $user2['user_email']?>">
                <button type="submit" class="btn btn-danger" name="unFriend">Hủy kết bạn</button>
        <?php
                }
                if($following){
        ?>
        <form action="following.php" method="POST">
            <input type="hidden" name="name" value="<?= $user2['user_email']?>">
                <button type="submit" class="btn btn-warning" name="delete-friend">Bỏ theo dõi</button>
        <?php
                }
            }
        ?>
        </form>
    </div>
</div> 

<?php if($id_user1 == $id_user2): { ?>
<?= $formatHelper->addStatus() ?>
<?php }endif; ?>
<?= $formatHelper->addNewsfeed($statusOfUserB,$user1['user_email'])?>
<?= $formatHelper->ListFriendIndex($_COOKIE['login']) ?>
<?= $formatHelper->closeFooter() ?>