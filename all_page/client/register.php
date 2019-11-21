<?php
    require_once '../server/init.php';
?>
<?php 
    if(isset($_POST['username_reg']) && isset($_POST['useremail_reg']) && isset($_POST['userpassword_reg']) && isset($_POST['userpassword_reg2'])):
?>

    <?php 
        $username_reg = $_POST['username_reg'];
        $useremail_reg = $_POST['useremail_reg'];
        $userpassword_reg = $_POST['userpassword_reg'];
        $userpassword_reg2 = $_POST['userpassword_reg2'];
        $success = false;

        $user = findUserByEmail($useremail_reg);
        if (!$user){
            if ($userpassword_reg == $userpassword_reg2){
                $success = true;

                $_SESSION['userId'] = createUser($username_reg, $useremail_reg, $userpassword_reg);
            }
        }
        
    ?>

    <?php if($success): ?>
        <?php header('Location: accept_user.php'); ?>
    <?php else: ?>
        <div class="alert alert-danger" role="alert">
            Đăng ký không thành công!
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
                <div class="card card-top-register">
                    <img src="../../public/image/iconLogin.png" alt="Không load được ảnh" class="center">
                    <div class="card-header text-center">
                        <h3>ĐĂNG KÝ</h3>
                    </div>
                    <div class="card-body text-center">
                        <form action="register.php" method="POST">
                            <div class="input-group form-group">
                                <div class="input-group-prepend radius">
                                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                                </div>
                                <input type="text" class="form-control" id="username_reg" name="username_reg" placeholder="Nhập vào tên của bạn">
                            </div>

                            <div class="input-group form-group">
                                <div class="input-group-prepend radius">
                                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                </div>
                                <input type="email" class="form-control" id="useremail_reg" name="useremail_reg" placeholder="Nhập vào email đăng nhập">
                            </div>

                            <div class="input-group form-group">
                                <div class="input-group-prepend radius">
                                    <span class="input-group-text"><i class="fas fa-key"></i></span>
                                </div>
                                <input type="password" class="form-control" id="userpassword_reg" name="userpassword_reg" placeholder="Nhập vào mật khẩu">
                            </div>

                            <div class="input-group form-group">
                                <div class="input-group-prepend radius">
                                    <span class="input-group-text"><i class="fas fa-key"></i></span>
                                </div>
                                <input type="password" class="form-control" id="userpassword_reg2" name="userpassword_reg2" placeholder="Nhập lại mật khẩu">
                            </div>

                            <input type="submit" value="Đăng Ký" class="btn login_btn">
                        </form>                        
                    </div>
                    <div class="card-footer">
                        <div class="d-flex justify-content-center links">Bạn đã có tài khoản? <a href="login.php">Đăng Nhập</a> </div>
                        <div class="d-flex justify-content-center links">
                                    
                        </div>	
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
    
<?php endif; ?>