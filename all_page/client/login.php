<?php
    require_once '../server/init.php';
?>
<?php 
    if(isset($_POST['email']) && isset($_POST['userpassword'])):
?>

    <?php 
        $email = $_POST['email'];
        $userpassword = $_POST['userpassword'];
        $success = false;

        $user = findUserByEmail($email);
        
        if ($user && password_verify($userpassword, $user['User_password'])){
            if($user['User_status'] == 'accept'){
                $success = true;
                $_SESSION['userId'] = $user['User_id'];
            }
            else{
                header('Location: accept_user.php');
            }
            
        }
    ?>

    <?php if($success): ?>
        <?php header('Location: ../../index.php'); ?>
    <?php else: ?>
        <div class="alert alert-danger" role="alert">
            Đăng nhập thất bại!
        </div>
    <?php endif; ?>

<?php else : ?>
<!doctype html>
<html lang="en">
    <head>
        <!-- Required meta tags -->
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

        <!-- Bootstrap CSS -->
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
        
        <link rel="stylesheet" href="../../public/css/login-register.css">

        <!--Fontawesome CDN-->
	    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css" integrity="sha384-mzrmE5qonljUremFsqc01SB46JvROS7bZs3IO2EmfFsd15uHvIt+Y8vEf7N7fWAU" crossorigin="anonymous">

        <title>Bài Tập Web 1</title>

    </head>
    <body>
        <div class="container-fluid login-background">
            <a class="btn login_btn" href="../../index.php">Trang chủ</a>
            <div class="d-flex justify-content-center h-100">
                <div class="card card-top-login">
                    <img src="../../public/image/iconLogin.png" alt="Không load được ảnh" class="center">
                    <div class="card-header text-center">
                        <h3>ĐĂNG NHẬP</h3>
                    </div>
                    <div class="card-body">
                        <form action="login.php" method="POST" >
                            <div class="input-group form-group">
                                <div class="input-group-prepend radius">
                                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                </div>
                                <input type="email" name="email" id="email" class="form-control radius" placeholder="Nhập vào email">						
                            </div>
                            <div class="input-group form-group radius">
                                <div class="input-group-prepend radius">
                                    <span class="input-group-text"><i class="fas fa-key"></i></span>
                                </div>
                                <input type="password" name="userpassword" id="userpassword" class="form-control radius" placeholder="Mật khẩu">
                            </div>
                            <div class="form-group">
                                <input type="submit" value="Đăng nhập" class="btn login_btn">
                            </div>
                        </form>
                    </div>
                    <div class="card-footer">
                        <div class="d-flex justify-content-center links">Bạn chưa có tài khoản?<a href="register.php">Đăng ký</a></div>
                        <div class="d-flex justify-content-center">
                            <a href="forgot_password.php">Quên mật khẩu?</a>
                        </div>
                        <div class="d-flex justify-content-center links">
                                    
                        </div>	
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
<?php endif; ?>