<!--Member-->
<?php
    require_once 'inc/autoloadClass.php';

    $formatHelper = new formatHelper();

    //form request
    if($_SERVER['REQUEST_METHOD'] == "GET"){
        $user = new userController();
        $message = $user->confirm($_GET);

        if($message == 1){
            header('Location: login.php');
        }
        else{
            header('Location: register.php');
        }
    }
?>