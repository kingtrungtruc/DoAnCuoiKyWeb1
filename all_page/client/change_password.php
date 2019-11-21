<?php
    require_once '../server/init.php';
?>
<?php include '../header.php'; ?>

<?php 
    if(isset($_POST['userpassword_old']) && isset($_POST['userpassword_new']) && isset($_POST['userpassword_new2'])):
?>

    <?php 
        $userpassword_old = $_POST['userpassword_old'];
        $userpassword_new = $_POST['userpassword_new'];
        $userpassword_new2 = $_POST['userpassword_new2'];
        $success = false;

        if (($userpassword_new == $userpassword_new2) && password_verify($userpassword_old,  $currentUser['User_password'])){
            $success = true;
            changePassword($userpassword_new, $currentUser['User_id']);
        }
   
        
    ?>

    <?php if($success): ?>
        <?php header('Location: ../../index.php'); ?>
    <?php else: ?>
    <div class="alert alert-danger" role="alert">
        Đổi mật khẩu không thành công!
    </div>
    <?php endif; ?>

<?php else : ?>
<div class="row">
    <div class="col-sm-3 col-md-3 col-lg-3 col-xl-3"></div>
    <div class="col-sm-6 col-md-6 col-lg-6 col-xl-6 text-center">
        <h1>Đổi mật khẩu</h1>
        <form action="change_password.php" method="POST">
            <div class="input-group form-group radius">
                <div class="input-group-prepend radius">
                    <span class="input-group-text"><i class="fas fa-key"></i></span>
                </div>
                <input type="password" class="form-control" id="userpassword_old" name="userpassword_old" placeholder="Nhập mật khẩu hiện tại">
            </div>
            <div class="input-group form-group radius">
                <div class="input-group-prepend radius">
                    <span class="input-group-text"><i class="fas fa-key"></i></span>
                </div>
                <input type="password" class="form-control" id="userpassword_new" name="userpassword_new" placeholder="Nhập mật khẩu mới">
            </div>
            <div class="input-group form-group radius">
                <div class="input-group-prepend radius">
                    <span class="input-group-text"><i class="fas fa-key"></i></span>
                </div>
                <input type="password" class="form-control" id="userpassword_new2" name="userpassword_new2" placeholder="Nhập lại mật khẩu mới">
            </div>
            <button type="submit" class="btn btn-danger radius">Đổi mật khẩu</button>
        </form>
    </div>
    <div class="col-sm-3 col-md-3 col-lg-3 col-xl-3"></div>
</div>
<?php endif; ?>

<?php include '../footer.php'; ?>