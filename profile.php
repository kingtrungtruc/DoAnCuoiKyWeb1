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

$id_user2 = $_GET['id'];
$user2 = $user->GetUser('',$id_user2);
$user1 =$user->GetUser($_COOKIE['login']);
$id_user1 = $user1['user_id'];

//Xác định mối quan hệ gì
$noRelationship = false;
$following= false;
$follows= false;
$followed= false;

$followedA = !empty($user1['user_followed']) ? unserialize($user1['user_followed']) : [];
$followedB = !empty($user2['user_followed']) ? unserialize($user2['user_followed']) : [];

$followingA = !empty($user1['user_following']) ? unserialize($user1['user_following']) : [];
$followsA = !empty($user1['user_follows']) ? unserialize($user1['user_follows']) : [];

$followsB = !empty($user2['user_follows']) ? unserialize($user2['user_follows']) : [];
$followingB = !empty($user2['user_following']) ? unserialize($user2['user_following']) : [];

//nếu là chính mình
if($id_user1==$id_user2)
{

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

<div class="user-info">
    <div class="info-title">
        <span><img src="<?= $user2_avatar?>"/></span>
        <span id="name"><?=$user2['user_displayname']?></span>
    </div>
</div> 
<?php if($id_user1 == $id_user2): { ?>
<?= $formatHelper->addStatus() ?>
<?php }endif; ?>
<?= $formatHelper->addNewsfeed($statusOfUserB,$user1['user_email'])?>
<?= $formatHelper->ListFriendIndex($_COOKIE['login']) ?>
<?= $formatHelper->closeFooter() ?>