<!DOCTYPE html>
<html lang="vn">
    <head>
        <title>Đồ án Web 1 | MXH</title>
        <meta charset="utf-8">
        <link rel="stylesheet" type="text/css" href="default/style.css">
        <link rel="stylesheet" type="text/css" href="plugin/bootstrap/css/bootstrap.css">
        <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    </head>

    <body>
        <div class="container-fluid">
            <!--Điều hướng-->
            <?php
                if(isset($_COOKIE['user_login'])){
                    header('Location: dashboard.php');
                }
                else{
                    header('Location: login.php');
                }
            ?>
        </div>
    </body>
</html>