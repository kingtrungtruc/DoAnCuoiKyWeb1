<!--Guest-->
<?php
    require_once 'inc/autoloadClass.php';

    $formatHelper = new formatHelper();
    $user = new userController();
    $comment = new commentController();

    //điều hướng
    if(!isset($_COOKIE['user_login'])){
        header('Location: index.php');
    }

    if($_SERVER['REQUEST_METHOD'] == "POST"){
        if(isset($_POST['addStatus'])){
            $user->newStatus($_COOKIE['user_login'], $_FILES, $_POST);
            //$_SERVER['PHP_SELF'] trả về tên file của file đang đượ chạy
            header('Location: ' . $_SERVER['PHP_SELF']);
        }
    }

    $postEntities = $user->searchPosts($_COOKIE['user_login'], '');
?> 

<?= $formatHelper->addHeader($_COOKIE['user_login']); ?>

<?= $formatHelper->addFixMenu(); ?>

<div class="main">
    <div class="content">
        <?= $formatHelper->addStatus(); ?>
        <?= $formatHelper->addNewsFeed($postEntities, $_COOKIE['user_login']); ?>
    </div>
    <?= $formatHelper->listFriendIndex($_COOKIE['user_login']); ?>
</div>

<?= $formatHelper->closeFooter(); ?>