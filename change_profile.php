<!--Guest-->
<?php
    require_once 'inc/autoloadClass.php';

    $formatHelper = new formatHelper();

    //form request
    if($_SERVER['REQUEST_METHOD'] == "POST"){
        $user = new userController();
        $message = $user->updateProfile($_COOKIE['user_login'], $_FILES, $_POST);
        $display = "style='display: block; text-align: center'";
    }

    //điều hướng
    if(!isset($_COOKIE['user_login'])){
        header('Location: index.php');
    }
?>

<?= $formatHelper->addHeader($_COOKIE['user_login']); ?>

<?= $formatHelper->addFixMenu(); ?>

<div class="main">
    <div class="content">
        <div class="alert alert-info" <?= @$display ?: "style='display: block; text-align: center'"?>>
            <center><?= @$message ?: "" ?></center>
        </div>
        <!--form cập nhật thông tin-->
        <form class="frmUpdate" action="" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="phone">Số điện thoại: </label>
                <input type="text" name="user_phone" class="form-control">
            </div>
            <div class="form-group">
                <label for="realname">Họ tên: </label>
                <input type="text" name="user_displayname" class="form-control">
            </div>
            <div class="form-group">
                <label for="avatar">Ảnh đại diện: </label>
                <input type="file" name="user_avatar" class="form-control">
            </div>
            <div class="submit-group">
                <button type="submit" class="btn btn-primary">Cập nhật</button>
            </div>
        </form>
    </div>
    <?= $formatHelper->listFriendIndex($_COOKIE['user_login']); ?>
</div>

<?= $formatHelper->closeFooter(); ?>