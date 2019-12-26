<!-- GUEST -->
<?php
    require_once 'inc/autoload.php';

    // Format Helper
    $formatHelper = new FormatHelper();

    // DIRECTION
    if (!isset($_COOKIE['login'])) {
        header('Location: index.php');
    }
    $style = 'danger';
    //$_SERVER['REQUEST_METHOD' => Xác định request gửi đến server con đường nào (post,get,patch,delete)
    if ($_SERVER['REQUEST_METHOD'] == "POST") {
        $user = new UserController();
        $message = "";//Thông báo KQ từ server trả về

        if (isset($_POST['addFriend'])) {
            $message = $user->AddFriend($_COOKIE['login'], $_POST['name']);
            $style = 'info';
        }else if (isset($_POST['unFollowing'])){
            $message = $user->unFollowing($_COOKIE['login'], $_POST['name']);
            $style = 'info';
        }else if (isset($_POST['unFriend'])) {
            $message = $user->DeleteFriend($_COOKIE['login'], $_POST['name']);
            $style = 'info';
        } 

        $display = "class='alert alert-$style' style='display: block; text-align: center;'";
    }
?>

<?= $formatHelper->addHeader($_COOKIE['login']) ?>
<?= $formatHelper->addFixMenu() ?>
<?= $formatHelper->addLeftMenu($_COOKIE['login'],'turquoise') ?>
<div class="row">
    <h3 class="text-center">Đang theo dõi</h3>

    <div <?= @$display ?: "class='alert alert-$style' style='display:none;text-align: center;'"?>> <?= @$message?: "" ?> </div>
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
