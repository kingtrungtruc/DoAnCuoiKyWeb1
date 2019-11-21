<?php
    require_once '../server/init.php';
?>
<?php
    header("Content-type: image/jpeg");
    echo $currentUser['User_avatar'];
?>