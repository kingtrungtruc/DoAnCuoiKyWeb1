<?php
    require_once '../server/init.php';
?>
<?php 
    if(isset($_GET['usercode_reg'])):
?>

    <?php 
        $code = $_GET['usercode_reg'];
        $success = false;

        $success = acceptUser($code, $_SESSION['userId']);
    ?>

    <?php if($success): ?>
        <?php header('Location: ../../index.php'); ?>
    <?php else: ?>
        <div class="alert alert-danger" role="alert">
            Kích hoạt tài khoản thất bại!
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
                <div class="card card-top-accept">
                    <img src="../../public/image/iconLogin.png" alt="Không load được ảnh" class="center">
                    <div class="card-header text-center">
                        <h3>XÁC NHẬN ĐĂNG KÝ</h3>
                    </div>
                    <div class="card-body text-center">
                        <form action="accept_user.php" method="GET">
                            <div class="input-group form-group">
                                <div class="input-group-prepend radius">
                                    <span class="input-group-text"><i class="fas fa-keyboard"></i></span>
                                </div>
                                <input type="text" class="form-control" id="usercode_reg" name="usercode_reg" placeholder="Nhập mã code đã gửi đến mail của bạn">
                            </div>

                            <input type="submit" value="Xác nhận đăng ký" class="btn login_btn">
                        </form>                        
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
    
<?php endif; ?>