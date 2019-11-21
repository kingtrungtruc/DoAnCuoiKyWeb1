<?php
    //load phpmailler
    require_once 'vendor/autoload.php';
    //tải các function
    require_once 'functions.php';

    //hiển thị tất cả các lỗi lên màn hình
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    //bật secssion để sử dụng
    session_start();

    //lấy page hiện tại
    $page = detectPage();

    //chuỗi kết nối db mysql
    $db = new PDO('mysql:host=localhost;dbname=qlusers;charset=utf8', 'root', '');

    //user đăng nhập hiện tại
    $currentUser = null;
    if (isset($_SESSION['userId'])){
        $currentUser = findUserById($_SESSION['userId']);
        if($currentUser['User_status'] != 'accept'){
            $currentUser = null;
        }
    }
    $image_post = null;
?>