<!--Guest-->
<?php
    require_once 'inc/autoload.php';

    // Format Helper
    $formatHelper = new FormatHelper();

    if ($_SERVER['REQUEST_METHOD'] == "POST") {
        $user = new userController();
        $message = $user->changePassword($_COOKIE['user_login'], $_POST);
        $display = "style='display: block; text-align: center'";
    }

    // DIRECTION
    if (!isset($_COOKIE['user_login'])) {
        header('Location: index.php');
    }
?>

<?= $formatHelper->addHeader($_COOKIE['user_login']); ?>
<?= $formatHelper->addFixMenu(); ?>

<div class="main">
    <div class="content">
        <div class="alert alert-info" <?= @$display ?: "style='display:none; text-align: center'"?>>
            <center><?= @$message?: "" ?></center>
        </div>

        <!--form đổi mật khẩu-->
        <form class="frmUpdate" action="" method="POST">
            <div class="form-group">
                <label for="old-password">Mật khẩu cũ</label>
                <input type="text" name="user_password_old" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="new-password">Mật khẩu mới:</label>
                <input type="text" name="user_password_new" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="renew-password">Nhập lại mật khẩu mới:</label>
                <input type="text" name="user_password_renew" class="form-control" required>
            </div>
            <div class="submit-group">
                <button type="submit" class="btn btn-primary">Cập nhật</button>
            </div>
        </form>
    </div>

    <?= $formatHelper->listFriendIndex($_COOKIE['user_login']); ?>
</div>
<?= $formatHelper->closeFooter(); ?>