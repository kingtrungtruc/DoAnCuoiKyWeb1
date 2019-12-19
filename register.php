<!-- MEMBER -->
<?php
require_once 'inc/autoload.php';

// Format Helper
$formatHelper = new FormatHelper();
$style = 'danger';

// Form Request
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user = new UserController();
    $message = $user->register($_POST);

    if ($message == 1) {
        $message = 'Gửi email xác nhận thành công, vui lòng kiểm tra email và làm theo hướng dẫn';
        $style = 'success';
    }

    $display = "class='alert alert-$style' style='display: block; text-align: center;'";
}

// DIRECTION
if (isset($_COOKIE['login'])) {
    header('Location: dashboard.php');
}
?>

<?= $formatHelper->addHeader('Đăng ký'); ?>
<div <?= @$display ?: "class='alert alert-$style' style='display:none;text-align: center;'"?>> <?= @$message?: "" ?> </div>

<!-- REGISTER -->
<div class="login-page">
    <div class="login-box">
        <div class="logo">
            <a><b>ĐĂNG KÝ</b></a>
        </div>
        <div class="card">
            <div class="body">
                <form method="POST" action="">
                    <div class="input-group form-float">
                        <span class="input-group-addon">
                            <i class="material-icons">people</i>
                        </span>
                        <div class="form-line">
                            <input type="text" class="form-line" name="realname" placeholder="Họ và tên..." required autofocus>
                        </div>
                    </div>
                    <div class="input-group form-float">
                        <span class="input-group-addon">
                            <i class="material-icons">email</i>
                        </span>
                        <div class="form-line">
                            <input type="email" class="form-control" name="username" placeholder="Email..." required
                                autofocus>
                        </div>
                    </div>
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="material-icons">lock</i>
                        </span>
                        <div class="form-line">
                            <input type="password" class="form-control" name="password" placeholder="Mật khẩu..."
                                required>
                        </div>
                    </div>
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="material-icons">lock</i>
                        </span>
                        <div class="form-line">
                            <input type="password" class="form-control" name="re-password" placeholder="Nhập lại mật khẩu..."
                                required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-4"></div>
                        <div class="col-xs-4">
                            <button type="submit" class="btn btn-block bg-pink waves-effect">Đăng ký</button>
                        </div>
                        <div class="col-xs-4"></div>
                    </div>
                    <div class="row m-t-15 m-b--20 text-center">
                        <div class="col-xs-12">
                            Đã có tài khoản! 
                            <a href="login.html">Đăng nhập</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $formatHelper->closeFooter(); ?>