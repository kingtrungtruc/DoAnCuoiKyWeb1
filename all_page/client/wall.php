<?php
    require_once '../server/init.php';
?>
<?php include '../header.php'; ?>
<?php
    if(isset($_POST['submit'])){
        if(isset($_FILES['file'])){
            $file = $_FILES['file'];

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
                            $image_status = fread($fb, filesize($fileTmpName));
                            fclose($fb);
                            
                            if (isset($_POST['message'])){
                                $content = $_POST['message'];
                                var_dump($content);
                                $stmt = $db->prepare("INSERT INTO posts (Post_user, Post_content, Post_image) VALUES (?, ?, ?)");
                                $stmt->execute(array($currentUser['User_id'], $content, $image_status));
                                header('Location: wall.php');
                            } 
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
        else{
            if (isset($_POST['message'])){
                $content = $_POST['message'];

                $stmt = $db->prepare("INSERT INTO posts (Post_user, Post_content) VALUES (?, ?)");
                $stmt->execute(array($currentUser['User_id'], $content));
                header('Location: wall.php');
            }
        }
        
    }
?>
<div class="row">  
    <div class="col-sm-3 col-md-3 col-lg-3 col-xl-3"></div>
    <div class="col-sm-6 col-md-6 col-lg-6 col-xl-6">  
        <form action="wall.php" method="POST" enctype="multipart/form-data">        
            <div class="form-group mb-3 text-center">
                <textarea class="form-control" name="message" placeholder="Bạn đang nghĩ gì!" required="required"></textarea>
            </div>
            <input type="file" name="file">
            <button class="btn btn-success radius" type="submit" name="submit">Đăng</button>
        </form>
    </div>
    <div class="col-sm-3 col-md-3 col-lg-3 col-xl-3"></div>
</div>
<div class="row">  
    <div class="col-sm-3 col-md-3 col-lg-3 col-xl-3"></div>
    <div class="col-sm-6 col-md-6 col-lg-6 col-xl-6 text-center">  
        <?php if(getAllPosts($currentUser['User_id'])): ?>
            <?php  
                $all_posts = getAllPosts($currentUser['User_id']);
                while($row = $all_posts->fetch(PDO::FETCH_ASSOC)) {
                    $image_post = $row['Post_image'];
            ?>
                <div class="card card-top">
                    <div class="card-header">
                        <img class="info-image" src="image.php" alt="<?php echo $currentUser['User_displayName']; ?>">
                        <p class="info-name"><?php echo $currentUser['User_displayName']; ?></p>
                    </div>
                    <div class="card-body">
                        <p><?php echo $row['Post_content']; ?></p>
                        <?php $image_post = 'data:image/jpeg;base64,' . base64_encode($row['Post_image']); ?>
                        <img class="img-style" src="<?= $image_post ?>" alt="">
                    </div>
                    <div class="card-footer reaction radius">
                        <ul>
                            <li style="display: none" class="reaction-like" id="reaction-like-48"> 
                                &nbsp;Đã thích 
                                <span id="numlike-48"></span>
                            </li>
                            <li style="display: table-cell" class="reaction-nonlike" id="reaction-nonlike-48"> 
                                &nbsp;Thích 
                                <span id="numnonlike-48"></span>
                            </li>
                            <li class="reaction-comment" id="reaction-comment-48">
                                &nbsp;Bình luận 
                                <span id="numcom-48"></span>
                            </li>
                            <li class="reaction-share" id="reaction-share-48">
                                &nbsp;Chia sẽ
                            </li>
                        </ul>
                    </div>
                </div>
            <?php } ?>
        <?php endif; ?>
    </div>
    <div class="col-sm-3 col-md-3 col-lg-3 col-xl-3"></div>
</div>

<?php include '../footer.php'; ?>