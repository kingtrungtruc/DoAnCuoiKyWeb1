<!--Member--> 
<?php
    require_once 'inc/autoloadClass.php';

    $formatHelper = new formatHelper();
    $style = 'danger';

    //form request
    if($_SERVER['REQUEST_METHOD'] == "POST"){
        $user = new userController();
        $message = $user->register($_POST);

        if($message == 1){
            $message = 'Đăng ký thành công, vui lòng kiểm tra email và làm theo hướng dẫn';
            $style = 'success';
        }

        $display = "class='alert alert-$style' style='display: block; text-align: center'";
    }

    //điều hướng
    if(isset($_COOKIE['user_login'])){
        header('Location: dashboard.php');
    }
?>

<?= $formatHelper->addHeader('Đăng ký'); ?>

<div <?= @$display ?: "class='alert alert-$style' style='display: block; text-align: center'"?>>
<?= @$message ?: "" ?>
</div>

<!--Đăng ký-->
<form class="frmReg" action="" method="POST">
    <div class="form-group">
        <label for="usename">Email:</label>
        <input type="email" name="user_email" class="form-control" maxlength="255" required>
    </div>
    <div class="form-group">
        <label for="realname">Username:</label>
        <input type="text" name="user_displayname" class="form-control" maxlength="255" required>
    </div>
    <div class="form-group">
        <label for="password">Mật khẩu:</label>
        <input type="password" name="user_password" class="form-control" required>
    </div>
    <div class="form-group">
        <label for="password">Nhập lại mật khẩu:</label>
        <input type="password" name="user_repassword" class="form-control" required>
    </div>
    <div class="submit-group">
        <button type="submit" class="btn btn-warning">Đăng ký</button>
        <a href="login.php" title="Đăng nhập hệ thống" target="_parent">Đăng nhập</a>
    </div>
</form>
<?= $formatHelper->closeFooter(); ?>
