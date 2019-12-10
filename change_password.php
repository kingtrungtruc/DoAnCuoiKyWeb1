<!-- MEMBER -->
<?php
    require_once 'inc/autoloadClass.php';

    $display = 'display: none';
    $message = '';
    $style = 'danger';

    //định dạng cho html
    $formatHelper = new formatHelper();
    $contentHTML = $formatHelper->addResetPassword();

    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['user_email'])) {
        // gửi yêu cầu đặt lại mật khẩu
        $display = "display: block; text-align: center;";

        //kiểm tra xem có tồn tại user
        $users = new UserController();
        $user = $users->GetUser($_POST['user_email']);

        if (!$user) {
            $message = "Không tồn tại email trên hệ thống!";
        } 
        else {
            $work = new forgotPasswordController();
            $message = $work->sendPasswordToEmail($_POST['user_email']);
            $style = 'success';
            $contentHTML = '';
        }
    } else if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['token'])) {
            //yêu cầu từ token
            $token = filter_input(INPUT_GET, 'token');

            $work = new ForgotPasswordController();
            $message = $work->validateToken($token);

            if ($message === true) {
                $contentHTML = $formatHelper->addNewPassword();
                $display = 'display: none';
            } else {
                $contentHTML = '';
                $display = "display: block; text-align: center;";
                $style = "warning";
            }
        } else if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['user_password']) && isset($_GET['token'])) {
            //yêu cầu password mới
            $work = new forgotPasswordController();
            $message = $work->changePassword($_GET['token'], $_POST['user_password']);

            header('Location: login.php');
            die();
    }
    
    //điều hướng
    if (isset($_COOKIE['user_login'])) {
        header('Location: dashboard.php');
    }
?>

<?=
    $formatHelper->addHeader('Quên mật khẩu');
    echo $formatHelper->addAlert($display, $style, $message);
    echo $contentHTML;
    $formatHelper->closeFooter();
?>