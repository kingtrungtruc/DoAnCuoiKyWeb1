<!-- GUEST -->
<?php
require_once 'inc/autoload.php';

// Format Helper
$formatHelper = new FormatHelper();
$user = new UserController();
$comment = new CommentController();

$currentTab = "All";
$postEntities = null;
$posts = null;
$users = null;

if ($_SERVER['REQUEST_METHOD'] == "GET") {
    $message = "";

    if (isset($_GET['keyword']) || isset($_GET['search'])) {
        $users = $formatHelper->SearchUser((!isset($_GET['keyword']) ? null : $_GET['keyword']));
        $postEntities = $user->SearchPosts($_COOKIE['login'], (!isset($_GET['keyword']) ? null : $_GET['keyword']));
        $posts = $formatHelper->addNewsfeed($postEntities, $_COOKIE['login']);
    }

    if ($posts == "" && $users == "") {
        $message = "Không tìm thấy kết quả nào phù hợp!";
        $display = "style='display: block; text-align: center;'";
    }
}


// DIRECTION
if (!isset($_COOKIE['login'])) {
    header('Location: index.php');
}
?>

<?= $formatHelper->addHeader($_COOKIE['login']) ?>
<?= $formatHelper->addFixMenu() ?>
<?= $formatHelper->addLeftMenu($_COOKIE['login'],'lightgreen') ?>

<div>
    <div class="content">
    <div class="alert alert-info" <?= @$display ? : "style='display:none; text-align: center;'" ?>><center><?= @$message ? : "" ?></center></div>
        <div id="btnFilters">
            <button class="btn active" onclick="filterSelection('all')"> Tất cả</button>
            <button class="btn" onclick="filterSelection('users')"> Người dùng</button>
            <button class="btn" onclick="filterSelection('status')"> Bài đăng</button>
        </div>
    </div>

    <div class="content" id="users" style="padding: 20px;">
        <?php 
            echo $users;
         ?>
    </div>

    <div class="content" id="status" style="padding: 20px;">
        <?php 
            echo $posts;
        ?>
    </div>
</div>

<?= $formatHelper->ListFriendIndex($_COOKIE['login']) ?>
<?= $formatHelper->closeFooter() ?>
