<?php require_once 'server/init.php'; ?>
<!doctype html>
<html lang="en">
    <head>
        <!-- Required meta tags -->
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

        <!-- Bootstrap CSS -->
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
        
        <?php if($page != 'index'){ echo "<link rel='stylesheet' href='../../public/css/style.css'>"; } else { echo "<link rel='stylesheet' href='./public/css/style.css'>"; } ?>

        <!--Fontawesome CDN-->
	    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css" integrity="sha384-mzrmE5qonljUremFsqc01SB46JvROS7bZs3IO2EmfFsd15uHvIt+Y8vEf7N7fWAU" crossorigin="anonymous">

        <title>Bài Tập Web 1</title>

    </head>
    <body>
        <div class="container-fluid">
            <header >
                <nav class="navbar navbar-expand-lg navbar-light bg-light">
                    <a class="navbar-brand" href="index.php">LTT-659</a>
                    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>

                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                        <ul class="navbar-nav mr-auto">
                        <li class="nav-item <?php echo $page == 'index' ? 'active' : ''; ?>">
                            <a class="nav-link" href="<?php if($page != 'index') { echo "../../";} else { echo "";} ?>index.php">Trang chủ</a>
                        </li>
                    <?php if ($currentUser): ?>
                        <li class="nav-item <?php echo $page == 'wall' ? 'active' : ''; ?>">
                            <a class="nav-link" href="<?php if($page != 'index') { echo "";} else { echo "all_page/client/";} ?>wall.php">Đăng bài viết</a>
                        </li>
                        <li class="nav-item <?php echo $page == 'profile' ? 'active' : ''; ?>">
                            <a class="nav-link" href="<?php if($page != 'index') { echo "";} else { echo "all_page/client/";} ?>profile.php">Trang cá nhân</a>
                        </li>                        
                    <?php endif; ?>
                    </div> 
                    <?php if(!$currentUser): ?>
                        <a class="btn btn-success radius" href="<?php if($page != 'index') { echo "";} else { echo "all_page/client/";} ?>login.php">Đăng nhập</a>
                        <a class="btn btn-warning radius" href="<?php if($page != 'index') { echo "";} else { echo "all_page/client/";} ?>register.php">Đăng ký</a>
                    <?php else: ?>
                        <a class="btn btn-info radius" href="<?php if($page != 'index') { echo "";} else { echo "all_page/client/";} ?>logout.php">Đăng xuất <?php echo $currentUser ? ' (' . $currentUser['User_displayName'] . ')' : '' ?></a>
                        <?php if($page != 'change_password'): ?>
                            <?php if($page != 'index') { echo "<a class='btn btn-danger radius' href='change_password.php'>Đổi mật khẩu</a>";} else { echo "<a class='btn btn-danger radius' href='all_page/client/change_password.php'>Đổi mật khẩu</a>";}?> 
                        <?php endif; ?>

                    <?php endif; ?> 

                </nav>
            </header>