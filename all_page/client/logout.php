<?php
    require_once '../server/init.php';

    unset($_SESSION['userId']);
    header('Location: ../../index.php');
?>