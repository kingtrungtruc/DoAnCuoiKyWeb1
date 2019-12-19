<!-- GUEST -->
<?php
require_once 'inc/autoload.php';

// Format Helper
$formatHelper = new FormatHelper();

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $user = new UserController();
    $message = $user->UpdateProfile($_COOKIE['login'], $_FILES, $_POST);
    $display = "style='display: block; text-align: center;'";
}


// DIRECTION
if (!isset($_COOKIE['login'])) {
    header('Location: index.php');
}
?>

<?= $formatHelper->addHeader($_COOKIE['login']) ?>
<?= $formatHelper->addFixMenu() ?>
<?= $formatHelper->addLeftMenu($_COOKIE['login'],'turquoise') ?>
<div class="row">
    <div class="col-12 col-sm-12 col-md-3 col-lg-3 col-xl-3">
        <div style="border-right: solid">
            <ul>
                <li> Bạn 1 </li>
                <li> Bạn 2 </li>
                <li> Bạn 3 </li>
                <li> Bạn 4 </li>
                <li> Bạn 5 </li>
                <li> Bạn 6 </li>
                <li> Bạn 7 </li>
            </ul>
        </div>
    </div>
    <div class="col-12 col-sm-12 col-md-9 col-lg-9 col-xl-9">
        <div>
            Nội dung tin nhắn
        </div>
    </div>
</div>
</div>
<?= $formatHelper->ListFriendIndex($_COOKIE['login']) ?>
<?= $formatHelper->closeFooter() ?>
