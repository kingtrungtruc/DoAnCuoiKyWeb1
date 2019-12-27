<!-- GUEST -->
<?php
require_once 'inc/autoload.php';

// Format Helper
$formatHelper = new FormatHelper();

if (!isset($_COOKIE['login'])) {
    header('Location: index.php');
}
?>

<?= $formatHelper->addHeader($_COOKIE['login']) ?>
<?= $formatHelper->addFixMenu() ?>
<?= $formatHelper->addLeftMenu($_COOKIE['login'],'turquoise') ?>
<div class="row">
    <h3 class="text-center">Có thể bạn biết</h3>
    <div class="tab-content">
        <ul class="global">
            <?php if($formatHelper->ListUsersAll($_COOKIE['login']) == ""){
                echo "Tất cả đều là bạn bè hoặc bạn chưa chấp nhận lời mời kết bạn!";
            } else{ 
            ?>
                <?= $formatHelper->ListUsersAll($_COOKIE['login']);} ?>
        </ul>
    </div>
</div>
</div>
<?= $formatHelper->ListFriendIndex($_COOKIE['login']) ?>
<?= $formatHelper->closeFooter() ?>


