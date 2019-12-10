<!--Guest--> 
<?php
    require_once 'inc/autoloadClass.php';

    //Tạo các đối tượng format Helper, user Controller và comment Controller
    $formatHelper = new formatHelper();
    $user = new userController();
    $comment = new commentController();

    //tạo các biến để tìm theo post hoặc user
    $currentTab = "All";
    $postEntities = null;
    $posts = null;
    $users = null;

    if($_SERVER['REQUEST_METHOD'] == "POST"){
        $message = "";

        if(isset($_POST['keyword']) || isset($_POST['search'])){
            //gán giá trị cho users nếu keyword tìm được trong user
            $users = $formatHelper->searchUser((!isset($_POST['keyword']) ? mull : $_POST['keyword']));
            //gán giá trị cho postEntities nếu keyword tìm được trong post
            $postEntities = $user->searchPosts($_COOKIE['user_login'], (!isset($_POST['keyword']) ? null : $_POST['keyword']));
            //thêm newsFeed
            $posts = $formatHelper->addNewsFeed($postEntities, $_COOKIE['user_login']);
        }

        if(count($posts) == 0 && count($users) == 0){
            $message = "Không tìm thấy dữ liệu nào khớp với nội dung cần tìm!";
            $display = "style='display: block; text-align: center'";
        }
    }

    //điều hướng
    if(!isset($_COOKIE['user_login'])){
        header('Location: index.php');
    }
?>

<?= $formatHelper->addHeader($_COOKIE['user_login']); ?>

<?= $formatHelper->addFixMenu(); ?>

<!--Tìm kiếm-->
<div class="main">
    <div class="content">
        <div class="alert alert-info" <?= @$display ? : "style='display:none; text-align: center;'" ?>><center>
                <?= @$message ? : "" ?>
            </center>
        </div>
        <div id="btnFilters">
            <button class="btn active" onclick="filterSelection('all')"> Show all</button>
            <button class="btn" onclick="filterSelection('users')"> Users</button>
            <button class="btn" onclick="filterSelection('status')"> Status</button>
        </div>
    </div>

    <div class="content" id="users" style="padding: 20px;">
        <?php if ($users != null) {
            echo $users;
        } ?>
    </div>

    <div class="content" id="status" style="padding: 20px;">
        <?php if ($posts != null) {
            echo $posts;
        } ?>
    </div>

</div>

</div>
<?= $formatHelper->listFriendIndex($_COOKIE['user_login']) ?>
</div>
<?= $formatHelper->closeFooter() ?>