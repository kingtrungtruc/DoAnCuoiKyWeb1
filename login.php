<!-- MEMBER -->
<?php
require_once 'inc/autoload.php';

// Format HTML
$formatHelper = new FormatHelper();

// Form REQUEST
if ($_SERVER['REQUEST_METHOD'] == 'POST')  {
    $user = new UserController();
    $message = $user->login($_POST);
    $display = "style='display: block; text-align: center;'";

    if ($message == 1) header('Location: dashboard.php');
}

// DIRECTION
if (isset($_COOKIE['login'])) {
    header('Location: dashboard.php');
}
?>

<?= $formatHelper->addHeader('Đăng nhập') ?>
<div class="alert alert-danger" <?= @$display ?: "style='display:none; text-align: center;'"?>><center><?= @$message?: "" ?></center></div>

<!-- LOGIN -->
<div class="login-page">
    <div class="login-box">
        <div class="logo">
            <a><b>ĐĂNG NHẬP</b></a>
        </div>
        <div class="card">
            <div class="body">
                <form method="POST" action="">
                    <div class="input-group form-float">
                        <span class="input-group-addon">
                            <i class="material-icons">email</i>
                        </span>
                        <div class="form-line">
                            <input type="email" class="form-control" name="username" placeholder="Email đăng nhập..." required autofocus>
                        </div>
                    </div>
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="material-icons">lock</i>
                        </span>
                        <div class="form-line">
                            <input type="password" class="form-control" name="password" placeholder="Mật khẩu..." required>
                        </div>
                    </div>
                    <div class="p-t-5">
                        <input type="checkbox" name="remember" id="remember" class="filled-in chk-col-pink">
                        <label for="remember">Ghi nhớ</label>
                    </div>
                    <div class="row">
                        <div class="col-xs-4"></div>
                        <div class="col-xs-4">
                            <button type="submit" class="btn btn-block bg-pink waves-effect">Đăng nhập</button>
                        </div>
                        <div class="col-xs-4"></div>
                    </div>
                    <div class="row text-center">
                        <div class="col-xs-12">
                            Chưa có tài khoản?
                            <a href="register.php" title="Đăng ký tài khoản" target="_parent"> Đăng ký</a>
                        </div>
                        <div class="col-xs-12">
                            <a href="forgot_password.php" title="Quên mật khẩu" target="_parent">Quên mật khẩu?</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $formatHelper->closeFooter() ?>