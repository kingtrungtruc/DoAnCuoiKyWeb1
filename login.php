<!--Member-->
<?php
    require_once 'inc/autoloadClass.php';

    $formatHelper = new formatHelper();

    //form request
    if($_SERVER['REQUEST_METHOD'] == "POST"){
        $user = new userController();
        $message = $user->login($_POST);
        $display = "style='display: block; text-align: center'";

        if($message == 1){
            header('Location: dashboard.php');
        }
    }

    //điều hướng
    if(isset($_COOKIE['user_login'])){
        header('Location: dashboard.php');
    }
?> 

<?= $formatHelper->addHeader('Đăng nhập'); ?>

<div class="alert alert-danger" <?= @$display ?: "style='display:none; text-align: center;'"?>><center><?= @$message?: "" ?></center></div>

<!-- LOGIN -->
<form class="frmLogin" action="" method="POST">
    <div class="form-group">
        <label for="usename">Email:</label>
        <input type="email" name="user_email" class="form-control" maxlength="255" required>
    </div>
	<div class="form-group">
		<label for="password">Mật khẩu:</label>
		<input type="password" name="user_password" class="form-control" required>
	</div>
	<div class="form-group">
		<input type="checkbox" name="user_remember" class="form-check-input" id="user_remember">
		<label for="remember">Nhớ mật khẩu</label>
	</div>
       <div class="submit-group">
           <button type="submit" class="btn btn-success">Đăng nhập</button>
           <a href="register.php" title="Đăng ký tài khoản" target="_parent">Đăng ký</a> <br>
           <a href="forgot_password.php" title="Quên mật khẩu" target="_parent">Quên mật khẩu ?</a>
       </div>
</form>
<?= $formatHelper->closeFooter() ?>