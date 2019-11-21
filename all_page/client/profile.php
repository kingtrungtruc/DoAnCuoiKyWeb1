<?php
    require_once '../server/init.php';
?>
<?php include '../header.php'; ?>
<?php if($_SESSION == null): ?>
    <?php echo 'Vui lòng đăng nhập!'; ?>
<?php else: ?>
    <h1 class="text-center">Quản lý thông tin cá nhân</h1>
    <?php
        if(isset($_POST['submit'])){
            $file = $_FILES['file'];
            var_dump($file);
            $fileName = $_FILES['file']['name'];
            $fileType = $_FILES['file']['type'];
            if($fileType == 'image/jpeg'){
                $fileTmpName = $_FILES['file']['tmp_name'];
                $fileSize = $_FILES['file']['size'];
                $fileError = $_FILES['file']['error'];

                $fileExt = explode('.', $fileName);
                $fileActualExt = strtolower(end($fileExt));

                $allowed = array('jpg', 'jpeg');

                if (in_array($fileActualExt, $allowed)){
                    if($fileError === 0){
                        if($fileSize < 16777215){
                            
                            //đọc file dạng binary
                            $fb = fopen($fileTmpName, "rb");
                            $avatar = fread($fb, filesize($fileTmpName));
                            fclose($fb);

                            $stmt = $db->prepare("UPDATE users SET User_avatar = ? WHERE User_id = ?");
                            $stmt->execute(array($avatar,  $currentUser['User_id']));                        

                            header('Location: profile.php');
                        }
                        else {
                            echo "Ảnh quá lớn (>16MB)";
                        }
                    }
                    else{
                        echo "Đã có lỗi xảy ra khi tải ảnh lên!";
                    }
                }
                else{
                    echo "Định dạng tệp tin không đúng! Chỉ sử dụng dạng mở rộng .jpg hoặc .jpeg";
                }
                
            }
        }
    ?>
    <?php 
        if(isset($_POST['username'])):
    ?>
        <?php 
            $username = $_POST['username'];
            $success = false;

            $user = findUserById($currentUser['User_id']);
            if ($user){
                $success = true;
                $stmt = $db->prepare("UPDATE users SET User_displayName = ? WHERE User_id = ? ");
                $stmt->execute(array($username, $currentUser['User_id']));
            }        
        ?>

        <?php if($success): ?>
            <?php header('Location: profile.php'); ?>
        <?php else: ?>
        <div class="alert alert-danger" role="alert">
            Cập nhật không thành công!
        </div>
        <?php endif; ?>

    <?php else : ?>
        <div class="row text-center">
            <div class="col-sm-3 col-md-3 col-lg-3 col-xl-3"></div>
            <div class="col-sm-6 col-md-6 col-lg-6 col-xl-6">
                <img class="img-style" src="image.php" alt="<?php echo $currentUser['User_displayName']; ?>">
                <br/>
                
                <button type="button" class="btn btn-primary radius" data-toggle="modal" data-target="#myModal">Thay đổi</button>
                <!-- Modal -->
                <div class="modal fade" id="myModal" role="dialog">
                    <div class="modal-dialog">
                    
                        <!-- Modal content-->
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal">&times;</button>                    
                            </div>
                            <form action="profile.php" method="POST" enctype="multipart/form-data">
                                <div class="modal-body">                        
                                    <input type="file" name="file">                        
                                </div>
                                <div class="modal-footer">
                                    <button type="submit" name="submit" class="btn btn-primary radius">Tải ảnh lên</button>
                                </div>
                            </form>
                        </div>
                    
                    </div>
                </div>

                <form class="fix-form" action="profile.php" method="POST">
                    <div class="input-group form-group radius">
                        <div class="input-group-prepend radius text-center">
                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                        </div>
                        <input type="text" class="form-control radius" id="username" name="username" placeholder="<?php echo $currentUser['User_displayName']; ?>">
                    </div>
                    <button type="submit" class="btn btn-primary radius">Cập nhật</button>
                </form>
            </div>
            <div class="col-sm-3 col-md-3 col-lg-3 col-xl-3"></div>
        </div>
    <?php endif; ?>
<?php endif; ?>

<?php include '../footer.php'; ?>