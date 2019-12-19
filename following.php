<!-- GUEST -->
<?php
require_once 'inc/autoload.php';

// Format Helper
$formatHelper = new FormatHelper();

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $user = new UserController();
    $message = $user->UpdateProfile($_COOKIE['login'], $_FILES, $_POST);
    $display = "style='display: block; text-align: center;'";
}


// DIRECTION
if (!isset($_COOKIE['login'])) {
    header('Location: index.php');
}
?>

<?= $formatHelper->addHeader($_COOKIE['login']) ?>
<?= $formatHelper->addFixMenu() ?>
<?= $formatHelper->addLeftMenu($_COOKIE['login'],'turquoise') ?>
<div class="row">
    <div class="tab-content">
        <ul class="global">
            <?php if($formatHelper->ListFollowing($_COOKIE['login']) == ""){
                echo "Bạn chưa theo dõi ai cả!";
            } else{ 
            ?>
                <?= $formatHelper->ListFollowing($_COOKIE['login']);} ?>
        </ul>
    </div>
</div>
</div>
<?= $formatHelper->ListFriendIndex($_COOKIE['login']) ?>
<?= $formatHelper->closeFooter() ?>
