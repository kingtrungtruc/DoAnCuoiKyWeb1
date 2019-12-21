<!-- GUEST -->
<?php
require_once 'inc/autoload.php';

// Format Helper
$formatHelper = new FormatHelper();
$user = new UserController();

// DIRECTION
if (!isset($_COOKIE['login'])) {
    header('Location: index.php');
}

$users = $user->ListFriends($_COOKIE['login'], 'user_followed');

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
                    <div class="new-title">
                        <img src="<?= $src ?>" alt="<?= $name ?>" title="<?= $name ?>"> 
                        <h4 id="user"><?= $name ?></h4>
                    </div>
                </li>
                <?php } ?>
            </ul>
        </div>
    </div>
    <div class="col-12 col-sm-12 col-md-8 col-lg-8 col-xl-8">
        <div>
            Nội dung tin nhắn
        </div>
    </div>
</div>
</div>
<?= $formatHelper->ListFriendIndex($_COOKIE['login']) ?>
<?= $formatHelper->closeFooter() ?>
