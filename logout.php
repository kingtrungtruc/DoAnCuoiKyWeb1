<?php
    if(isset($_COOKIE['user_login'])){
        setcookie('user_login', '', time() - 3600);
    }

    header('Location: index.php');
?>