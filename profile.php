<?php
    require_once 'inc/autoloadClass.php';

    $formatHelper = new formatHelper();
    $user = new userController();
    $status = new statusController();

    if(!isset($_COOKIE['user_login'])){
        header('Location: index.php');
    }

    $id_user2 = $_GET['user_id'];
    $user2 = $user->getUser('', $id_user2);
    $user1 = $user->getUser($_COOKIE['user_login']);
    $id_user1 = $user1['user_id'];

    //Xác định mối quan hệ
    $noRelationship = false;
    $following = false;
    $follows = false;
    $followed = false;

    $followsA = !empty($user1['user_follows']) ? unserialize($user1['user_follows']) : [];
    $followsB = !empty($user2['user_follows']) ? unserialize($user2['user_follows']) : [];

    $followedA = !empty($user1['user_followed']) ? unserialize($user1['user_followed']) : [];
    $followedB = !empty($user2['user_followed']) ? unserialize($user2['user_followed']) : [];

    $followingA = !empty($user1['user_following']) ? unserialize($user1['user_following']) : [];
    $followingB = !empty($user2['user_following']) ? unserialize($user2['user_following']) : [];

    //nếu là chính mình
    if($id_user1 == $id_user2){

    }
    //nếu là bạn bè
    else if(in_array($id_user1, $followedB) && in_array($id_user2, $followedA)){
        $followed = true;
    }
        //nếu A đang theo dõi B
        else if(in_array($id_user1, $followsB) || in_array($id_user2, $followingA)){
            $following = true;
        }
            //nếu B đang theo dõi A
            else if(in_array($id_user1, $followingB) || in_array($id_user2, $followsA)){
                $follows = true;
            }
                //mặc định
                else{
                    $noRelationship = true;
                }

    $user2_avatar = !empty($user2['user_avatar']) ? 'data:image;base64,' . $user2['user_avatar'] : "default/image/default-avatar.jpg";
    
    $statusOfUserB = $status->statusWithRelationship($user1['id'], $id_user2);
?>

<?= $formatHelper->addHeader($_COOKIE['user_login']); ?>

<?= $formatHelper->addFixMenu(); ?>

<div class="main">
    <div class="content">
        <div class="user-info">
            <div class="info-title">
                <span>
                    <img src="<?= $user2_avatar?>"/>
                </span>
                <span id="name">
                    <?= $user2['user_displayname']?>
                </span>
            </div>

            <div class="info-body">
                <form action="friends.php" method="POST">
                    <input type="hidden" name="user_email" value="<?= $user2['user_email']?>">
                    <?php
                    if($id_user2 != $id_user1){
                        if($noRelationship){
                            ?>
                            <button type="submit" class="btn btn-primary" name="addFriend">Thêm bạn bè</button>
                            <?php
                        }
                        if($follows){
                            ?>
                            <button type="submit" class="btn btn-success" name="acceptFriend">Chấp nhận</button>
                            <button type="submit" class="btn btn-danger" name="declineFriend">Từ chối</button>
                            <?php
                        }
                        if($followed){
                            ?>
                            <button type="submit" class="btn btn-danger" name="unFriend">Hủy kết bạn</button>
                            <?php
                        }
                        if($following){
                            ?>
                            <button type="submit" class="btn btn-primary" name="delete-friend">Bỏ theo dõi</button>
                            <?php
                        }
                    }
                    else{
                        ?>
                        <select id="options_setting" class="form-control" onchage="location = this.value">
                            <option value="settings" selected>Cài đặt</option>
                            <option disabled>---------------</option>
                            <option value="change_profile.php">Đổi thông tin</option>
                            <option value="change_password.php">Đổi mật khẩu</option>
                            <option disanled>---------------</option>
                            <option value="logout.php">Đăng xuất</option>
                        </select>
                        <?php
                    }
                    ?>
                </form>
            </div>
        </div>
        <?= $formatHelper->addNewsFeed($statusOfUserB, $user1['user_email']); ?>
    </div>
    <?= $formatHelper->listFriendIndex($_COOKIE['user_login']); ?>
</div>

<?= $formatHelper->closeFooter(); ?>