<?php
    ini_set("display_errors", 1);
    error_reporting(E_ALL);
    include_once 'autoloadClass.php';

    /*===============================
       Class control format helper
       -->>> template HTML <<<--
    ===============================*/
    class formatHelper{
        private $header;
        private $footer;
        private $fixMenu;
        private $rightMenu;
        private $status;
        private $newsFeed;
        private $friend;
        private $users;
        private $posts;
        private $formResetPassword;
        private $formNewPassword;

        /**
         * Header
         * @param [type] $title [description]
         */
        public function addHeader($title){
            $displayname = '';
            $login = '';

            if(!isset($_COOKIE['user_displayname'])){
                $displayname = 'Đồ án Web 1 | MXH';
            }
            else{
                $displayname = $_COOKIE['user_displayname'];
            }

            if(!isset($_COOKIE['user_login'])){
                $login = 'Đồ án Web 1 | MXH';
            }
            else{
                $login = $_COOKIE['user_login'];
            }

            $this->header =<<<HEADER
<!DOCTYPE html>
    <html lang="vn">
        <head>
            <title> $displayname </title>
            <meta charset="utf-8">
            <meta name="username" value="$login">
            <link rel="stylesheet" type="text/css" href="default/style.css">
            <link rel="stylesheet" type="text/css" href="default/search/search.css">
            <link rel="stylesheet" type="text/css" href="plugin/bootstrap/css/bootstrap.css">
            <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
            <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.1/css/all.css" integrity="sha384-gfdkjb5BdAXd+lj+gudLWI+BXq4IuLW5IT+brZEZsLFm++aCMlF1V92rMkPaX4PP" crossorigin="anonymous">

            <script src="//code.jquery.com/jquery-1.11.1.min.js"></script>
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
            <script src="https://code.jquery.com/jquery-3.1.1.min.js">
            <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
            <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
        </head>
    <body>
        <div class="container-fluid">
HEADER;
            return $this->header;
        }

        /**
         * Footer
         * @return [type] [description]
         */
        public function closeFooter(){
            $this->footer =<<<FOOTER
    </div>
    <div class="copyright">
        <span>Đồ án Web 1 | MXH</span>       
    </div>
    <script src="default/javascript/menu.js" defer></script>
    <script src="default/javascript/dashboard.js" defer></script>
    <script src="default/javascript/status.js" defer></script>
    <script src="default/javascript/linkpreview.js" defer></script>
    <script src="default/search/search.js" defer></script>
</body>
</html>
FOOTER;
            return $this->footer;
        }

        /**
         * Giao diện Navbar
         */
        public function addFixMenu(){
            $user = new userController();
            $user_login = $user->getUser($_COOKIE['user_login'], '');
            $user_id = $user_login['user_id'];
            $displayname = empty($user_login['user_displayname']) ? $user_login['user_email'] : $user_login['user_displayname'];

            //yêu cầu kết bạn mới
            $follows = !empty($user_login['user_follows']) ? unserialize($user_login['user_follows']) : [];
            $count = count($follows);
            $req = $count > 0 ? "<span id='new-request'>+$count</span>" : "";

            $this->fixMenu =<<<FIXMENU
<div class="fix-menu">
    <div id="icon">
        <a href="index.php"><img src="default/image/home.png" alt="home"></a>
    </div>
        
    <ul id="nav" class="nav-info">
        <li><a href="profile.php?user_id=$user_id">( $displayname )</a></li>
        <li><a href="friends.php">Bạn bè $req </a></li>
        <li><a href="logout.php">Đăng xuất</a></li>
    </ul>
        
    <ul id="nav">
        <div class="d-flex justify-content-center h-100">
            <form class="form" action="search.php" method="POST">
                <div class="searchbar">
                    <input class="search_input " type="search" name="keyword" placeholder="Tìm status, bạn bè...">
                    <a href="search.php" class="search_icon "><i class="fas fa-search"></i></a>
                </div>
            </form>
        </div>
    </ul>
    <span id="hamburger">
        <span></span>
        <span></span>
        <span></span>
    </span>
    <div class="hamburger-menu">
        <ul>
            <li><i class="fas fa-caret-right"></i><a href="dashboard.php">Trang chủ</a></li>
            <li><i class="fas fa-caret-right"></i><a href="profile.php?user_id=$user_id"><i>$displayname<i></a></li>
            <li><i class="fas fa-caret-right"></i><a href="friends.php">Bạn bè $req </a></li>
            <li><i class="fas fa-caret-right"></i><a href="logout.php">Đăng xuất</a></li>
        </ul>
    </div>  
        
        
    <div class="clear"></div>
</div>
FIXMENU;
            return $this->fixMenu;
        }

        /**
         * Giao diện Menu bên phải
         */
        public function addRightMenu(){
            $this->rightMenu =<<<RIGHTMENU
<div class="right-menu">
    <ul class="sub-menu">
        <strong>Cá nhân</strong>
        <li><span id="licon"></span><a href="change_profile.php">Đổi thông tin</a></li>
        <li><span id="licon"></span><a href="change_password.php">Đổi mật khẩu</a></li>
        <li><span id="licon"></span><a href="logout.php">Đăng xuất</a></li>
    </ul>
</div>
RIGHTMENU;
            return $this->rightMenu;
        }

        /**
         * Giao diện from Viết status
         */
        public function addStatus(){
            $this->status =<<<STATUS
<div class="status">
    <form action="" method="POST" enctype="multipart/form-data">
        <textarea rows='6' placeholder='Viết gì đó ...' class="content" name="content"></textarea>
        
        <div class="status-extra-content">
            <hr>
            <input type="file" name="status_image" class="form-control" id="status-image">
            <button class="btn btn-default" id="status-image-btn"><i class="far fa-image fa-2x"></i></button>
        
            <select class="form-control" id="sel1" name = "status_role">
                <option>Công khai</option>
                <option>Bạn bè</option>
                <option>Chỉ mình tôi</option>
            </select>
            <button name="addStatus" class="btn btn-primary center-block" value="" id="btnSubmit">Đăng</button>
        </div>
    </form>
</div>
STATUS;
            return $this->status;
        }

        /**
         * Thêm newsfeed (status + comment)
         * @param [type] $contents [description]
         * @param [type] $username [description]
         */
        public function addNewsFeed($contents, $user_email){
            $user = new userController();
            $comment = new commentController();
            $status = new statusController();
            $currentUser = $user->getUser($user_email);

            foreach($contents as $content){
                //displayname và avatar
                $user_content = $user->getUser('', $content['status_user_id']);
                $user_id = $user_content['user_id'];
                $user_displayname = empty($user_content['user_displayname']) ? $user_content['user_email'] : $user_content['user_displayname'];
                $user_avatar_src = !empty($user_content['user_avatar']) ? 'data:image;base64,' . $user_content['user_avatar'] : 'default/image/default-avatar.jpg';

                //ảnh kèm theo status
                $imageAttachStatus = !empty($content['status_image']) ? "<img src=$content[status_image] class='image-status'<br>" : "";

                //comment của status
                $status_id = $content['status_id'];

                //avatar user comment
                $currentAvatar = !empty($currentUser['user_avatar']) ? 'data:image;base64,' . $currentUser['user_avatar'] : 'default/image/default-avatar.jpg';

                //role status
                $status_role = '';
                if(strcmp($content['status_role'], 'Công khai') == 0){
                    $status_role = "<span class='fas fa-globe-asia'></span>";
                }
                if(strcmp($content['status_role'], 'Bạn bè') == 0){
                    $status_role = "<span class='fas fa-user'></span>";
                }
                if(strcmp($content['status_role'], 'Chỉ mình tôi') == 0){
                    $status_role = "<span class='fas fa-eye-slash'></span>";
                }

                //like hoặc bỏ like
                $amountLike = $status->amountOfLiked($content['status_id']);
                $amountLike = $amountLike > 0 ? $amountLike : '';

                $like = '';
                $nonlike = '';
                $userIsLike = $status->isLiked($currentUser['user_id'], $content['status_id']);
                if($userIsLike){
                    $like = 'table-cell';
                    $nonlike = 'none';
                }
                else{
                    $like = 'none';
                    $nonlike = 'table-cell';
                }

                $comments = $comment->commentWithIdStatus($status_id);
                $amountComment = count($comments);
                $amountComment = $amountComment > 0 ? $amountComment : '';

                //nội dung status html
                $this->newsFeed .=<<<NEWSFEED
<div class="newsfeed">
    <a name="$content[status_id]"></a>
    <div class="new" id="$content[status_id]">
        <!-- Status -->
        <div class='new-title'>
            <img src='$user_avatar_src' alt='logo'> 
            <h4 id='user'><a href="profile.php?user_id=$user_id">$user_displayname</a></h4>
            <span>&nbsp;&nbsp;$status_role</span>
            <span title="$content[status_created]"><i>$content[status_created]</i></span>
        </div>
        <div class='new-content'>$content[status_content]</div>
        $imageAttachStatus

        <!-- Reaction Button -->
        <hr style="width: 97%">
        <div class="reaction">
            <ul>
                <li style="display: $like" class='reaction-like' id=reaction-like-$content[status_id]> &nbsp;Liked <span id=numlike-$content[status_id]>$amountLike</span>
                </li>
                <li style="display: $nonlike" class='reaction-nonlike' id=reaction-nonlike-$content[status_id]> &nbsp;Like <span id=numnonlike-$content[status_id]>$amountLike</span>
                </li>
                <li class="reaction-comment" id="reaction-comment-$content[status_id]">&nbsp;Comment <span id=numcom-$content[status_id]>$amountComment</span></li>
                <li class="reaction-share" id="reaction-share-$content[status_id]">&nbsp;Share</li>
            </ul>
        </div>

        <!-- Comment -->
        <hr>
        <div class="hide-comment-status" id="comment-status-$content[status_id]">
            <div class="new-comment">
                <span id="icon"><img src='$currentAvatar' alt='logo'></span>
                <span id="comment">
                    <form action="#" method="POST" class="frmComment" id="frmComment-$content[status_id]">
                        <input name='username' value='$_COOKIE[user_login]' hidden>
                        <input name='type' value='new_status' hidden>
                        <input name='id_status' value='$content[status_id]' hidden>
                        <input type="text" name="content_comment" class="content_comment" placeholder="Viết bình luận ..." value="" id="content_comment_$content[status_id]">
                        <button name="addComment" class="btn btn-primary center-block" style="display: none">Đăng</button>
                    </form>
                </span>
            </div>
            <div class="show-comment" id="show-comment-$content[status_id]">
NEWSFEED;

                //hiển thị comment
                foreach($comments as $row){
                    $userComment = $user->getUser('', $row['comment_user_id']);
                    $user_id = $userComment['user_id'];
                    $contentComment = $row['comment_content'];
                    $avatarUserComment = !empty($userComment['user_avatar']) ? 'data:image;base64,' . $userComment['user_avatar'] : 'default/image/default-avatar.jpg';
                    $displaynameUserComment = $userComment['user_displayname'];

                    $this->newsFeed .=<<<COMMENTS
<div class="detail-comment">
    <span id="icon">
        <img src='$avatarUserComment' alt='icon'>
    </span>
    <span id="content">
        <span id="user-comment">
            <a href="profile.php?user_id=$user_id"> $displaynameUserComment </a>
        </span>
        <span id="content-commment">$contentComment</span> 
    </span>
</div>   
COMMENTS;
                    $this->newsFeed .= "</div></div></div></div>";
                }
            }

            return $this->newsFeed;
        }

        public function listFriendIndex($user_email){
            $this->friend = '';
            $user = new userController();
            $users = $user->listFriends($user_email, 'followed');

            $this->friend .=<<<FRIENDSINDEX
<div class="listfriend">
    <div class="content">
        <ul>  
FRIENDSINDEX;

            foreach($users as $usr){
                $displayname = !empty($usr['user_displayname']) ? $usr['user_displayname'] : $usr['user_email'];
                $src = !empty($usr['user_avatar']) ? 'data:image;base64,' . $usr['user_avatar'] : 'default/image/default-avatar.jpg';
                $user_id = $usr['user_id'];

                $this->friend .=<<<FRIENDSINDEX
<li>
    <span id="ficon"><img src=$src alt="."></span>
    <span><a href="profile.php?user_id=$user_id">$displayname</a></span>
    <span id="onoff"></a></span>
</li>     
FRIENDSINDEX;
            }

            $this->friend .=<<<FRIENDSINDEX
        </ul>
    </div>
</div>
FRIENDSINDEX;

            return $this->friend;
        }

        /**
         * Giao diện liệt kê tất cả user hiện có
         * @param [type] $username [description]
         */
        public function listUsers($user_email){
            $this->friend = '';
            $user = new userController();
            $users = $user->listUsers();

            $info = $user->getUser($user_email);
            $follows = !empty($info['user_follows']) ? unserialize($info['user_follows']) : [];
            $followed = !empty($info['user_followed']) ? unserialize($info['user_followed']) : [];
            $following = !empty($info['user_following']) ? unserialize($info['user_following']) : [];

            foreach($users as $usr){
                if($user_email === $usr['user_email']){
                    continue;
                }

                if(in_array($usr['user_id'], $follows) || in_array($usr['user_id'], $followed) || in_array($usr['user_id'], $following)){
                    continue;
                }

                $displayname = !empty($usr['user_displayname']) ? $usr['user_displayname'] : $usr['user_email'];
                $src = !empty($usr['user_avatar']) ? 'data:image;base64,' . $usr['user_avatar'] : 'default/image/default-avatar.jpg';
                $user_id = $usr['user_id'];

                $this->friend .=<<<LISTUSER
<li>
    <form action="" method="POST">
        <img src=$src alt="avatar">
        <h2><a href="profile.php?user_id=$user_id">$displayname</a></h2>
        <input name="user_email" value=$usr[user_email] hidden>
        <button class='btn btn-primary' name='addFriend'>Thêm bạn bè</button>
    </form>
</li>
LISTUSER;
            }

            return $this->friend;
        }

        /**
         * Giao diện liệt kê tất cả Friend hiện có
         * @param [type] $username [description]
         */
        public function listFriends($user_email){
            $this->friend = '';
            $user = new userController();
            $users = $user->listFriends($user_email, 'followed');

            foreach($users as $usr){
                $displayname = !empty($usr['user_displayname']) ? $usr['user_displayname'] : $usr['user_email'];
                $src = !empty($usr['user_avatar']) ? 'data:image;base64,' . $usr['user_avatar'] : 'default/image/default-avatar.jpg';
                $user_id = $usr['user_id'];

/*Đoạn này có lỗi chưa giải quyết được
                $this->friend .=<<<LISTFRIEND
<li>
    <form action="" method="POST">
        <img src=$src alt="avatar">
        <h2><a href="profile.php?user_id=$user_id">$displayname</a></h2>
        <input name="name" value=$usr[user_email] hidden>
        <button class='btn btn-danger center-block' name='unFriend'>Bỏ kết bạn</button>
    </form>
</li>
LISTFRIEND;  */    
            }

            return $this->friend;
        }

        /**
         * Giao diện liệt kê tất cả Người đang theo dõi mình
         * @param [type] $username [description]
         */
        public function listFollows($user_email){
            $this->friend = '';
            $user = new userController();
            $users = $user->listFriends($user_email, 'follows');

            foreach($users as $usr){
                $displayname = !empty($usr['user_displayname']) ? $usr['user_displayname'] : $usr['user_email'];
                $src = !empty($usr['user_avatar']) ? 'data:image;base64,' . $usr['user_avatar'] : 'default/image/default-avatar.jpg';
                $user_id = $usr['user_id'];
/*Đoạn này có lỗi chưa giải quyết được
                $this->friend .=<<<LISTFRIEND
<li>
    <form action="" method="POST">
        <img src=$src alt="avatar">
        <h2><a href="profile.php?user_id=$user_id">$displayname</a></h2>
        <input name="name" value=$usr[user_email] hidden>
        <div class='submit-group'>
            <button class='btn btn-success btn-block' name='acceptFriend'>Chấp nhận</button>
            <button class='btn btn-danger btn-block' name='declineFriend'>Từ chối</button>
        </div>
    </form>
</li>
LISTFRIEND; */              
            }

            return $this->friend;
        }

        /**
         * Giao diện liệt kê tất cả người mà mình đang theo dõi
         * @param [type] $username [description]
         */
        public function listFollowing($user_email){
            $this->friend = '';
            $user = new userController();
            $users = $user->listFriends($user_email, 'following');

            foreach($users as $usr){
                $displayname = !empty($usr['user_displayname']) ? $usr['user_displayname'] : $usr['user_email'];
                $src = !empty($usr['user_avatar']) ? 'data:image;base64,' . $usr['user_avatar'] : 'default/image/default-avatar.jpg';
                $user_id = $usr['user_id'];

                $this->friend .=<<<LISTFRIEND
<li>
    <form action="" method="POST">
        <img src=$src alt="avatar">
        <h2><a href="profile.php?user_id=$user_id">$displayname</a></h2>
        <input name="name" value=$usr[user_email] hidden>
        <button class='btn btn-warning' name='unFollowing'>Bỏ theo dõi</button></form></li>
    </form>
</li>
LISTFRIEND;
            }

            return $this->friend;
        }

        /**
         * Giao diện Lấy lại mật khẩu
         */
        public function addResetPassword(){
            $this->formResetPassword =<<<FORM_RESET_PASSWORD
<form class="frmLogin" action="" method="POST">
    <div class="form-group">
        <label for="usename">Gửi mật khẩu qua mail:</label>
        <input type="email" name="user_email" class="form-control" maxlength="50" required>
    </div>
    <div class="submit-group">
        <button type="submit" class="btn btn-primary">Gửi</button>
        <a href="login.php" title="Đăng nhập" target="_parent">Đăng nhập</a>
    </div>
</form>
FORM_RESET_PASSWORD;
            return $this->formResetPassword;
        }

        /**
         * Giao diện nhập mật khẩu mới khi Lấy lại mật khẩu
         */
        public  function addNewPassword(){
            $this->formNewPassword =<<<FORM_NEW_PASSWORD
<!-- NEW PASSWORD -->
<form class="frmLogin" action="" method="POST" name="new">
    <div class="form-group">
        <label for="pasword">Mật khẩu mới:</label>
        <input type="password" name="user_password" class="form-control" maxlength="255" required>
    </div>
    <div class="submit-group">
        <button type="submit" class="btn btn-primary">Đổi mật khẩu</button>
    </div>
</form>
FORM_NEW_PASSWORD;
            return $this->formNewPassword;           
        }

        /**
         * Giao diện để show thông báo
         * @param string $display [description]
         * @param string $style   [description]
         * @param string $message [description]
         */
        public function addAlert($display = 'none', $style = 'danger', $message = ''){
            return "<div class='alert alert-$style' style='$display'><center>$message</center></div>";
        }

        /**
         * Giao diện Tìm kiếm 1 tài khoản
         * @param [type] $nameKey [description]
         */
        public function searchUser($name){
            $user = new userController();
            $listUser = $user->searchUsersByName($name);
            if(count($listUser) == 0){
                return null;
            }

            foreach($listUser as $usr){
                $displayname = !empty($usr['user_displayname']) ? $usr['user_displayname'] : $usr['user_email'];
                $src = !empty($usr['user_avatar']) ? 'data:image;base64,' . $usr['user_avatar'] : 'default/image/default-avatar.jpg';

                $this->users .=<<<SEARCHUSER
<div class="user">
    <div class="user-item" id="38">
    <!-- Status -->
    <div class="new-title">
        <img src="$src" alt="logo"> 
        <h4 id="user"><a href="profile.php?user_id=$usr[user_id]">$displayname</a></h4>
        <span>&nbsp;&nbsp;</span>
        <span title="$usr[user_created]"><i>$usr[user_created]</i></span>
    </div>
</div>
</div>
SEARCHUSER;
            }

            return $this->users;
        }
    }
?>