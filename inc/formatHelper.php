<?php
ini_set("display_errors", 1);
error_reporting(E_ALL);
include_once 'autoload.php';

/*
 * Class Help define
 *
 *  template HTML
 */
class FormatHelper
{
    private $header;
    private $footer;
    private $fixmenu;
    private $leftMenu;
    private $rightMenu;
    private $status;
    private $newsfeed;
    private $friend;
    private $users;
    private $posts;
    private $frmResetPassword;
    private $frmNewPassword;

    /**
     * Header
     * @param [type] $title [description]
     */
    public function addHeader($title)
    {
        $realname = "";
        $login = "";

        if (!isset($_COOKIE['realname'])) {
            $realname = "Đồ án Web 1 | MXH";
        } else {
            $realname = $_COOKIE['realname'];
        }

        if (!isset($_COOKIE['login'])) {
            $login = "Đồ án Web 1 | MXH";
        } else {
            $login = $_COOKIE['login'];
        }

        $path_string = $_SERVER['PHP_SELF'];
        $mang_path = explode("/", $path_string);
        $name_page = $mang_path[count($mang_path)-1];
        if($name_page == "login.php" || $name_page == "register.php" || $name_page == "forgot_password.php"){
            $this->header =<<<HEADER
<!DOCTYPE html>
<html lang="vn">
<head>
    <title> $realname </title>
    <link rel="icon" href="favicon.ico" type="image/x-icon">
    <meta charset="utf-8">
    <meta name="username" value="$login">

    <link href="https://app.infinityfree.net/css/clients-material.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,700&subset=latin,cyrillic-ext" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" type="text/css">
    
    <script src="//code.jquery.com/jquery-1.11.1.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.1.1.min.js">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
</head>
<body>
<div class="container-fluid">
HEADER;
        }else{
            $this->header =<<<HEADER
<!DOCTYPE html>
<html lang="vn">
<head>
    <title> $realname </title>
    <link rel="icon" href="favicon.ico" type="image/x-icon">
    <meta charset="utf-8">
    <meta name="username" value="$login">
    
    <link rel="stylesheet" type="text/css" href="asset/style.css">
    <link rel="stylesheet" type="text/css" href="asset/my_style_fix.css">
    <link rel="stylesheet" type="text/css" href="asset/message.css">
    <link rel="stylesheet" type="text/css" href="asset/search/search.css">
    <link rel="stylesheet" type="text/css" href="plugins/bootstrap/css/bootstrap.css">
    
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.1/css/all.css" integrity="sha384-gfdkjb5BdAXd+lj+gudLWI+BXq4IuLW5IT+brZEZsLFm++aCMlF1V92rMkPaX4PP" crossorigin="anonymous">
    <script src="//code.jquery.com/jquery-1.11.1.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src="https://code.jquery.com/jquery-3.1.1.min.js">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
</head>
<body>
<div class="container-fluid">
HEADER;
        }
        
        return $this->header;
    }

    /**
     * Footer
     * @return [type] [description]
     */
    public function closeFooter()
    {
        $this->footer =<<<FOOTER
</div>
<script src="asset/js/hamburgerMenu.js" defer></script>
<script src="asset/js/dashboard.js" defer></script>
<script src="asset/js/status.js" defer></script>
<script src="asset/js/linkpreview.js" defer></script>
<script src="asset/search/search.js" defer></script>
</body>
</html>
FOOTER;
        return $this->footer;
    }

    /**
     * Footer
     * @return [type] [description]
     */
    public function closeFooterMessage()
    {
        $this->footer =<<<FOOTER
</div>
<script src="asset/js/hamburgerMenu.js" defer></script>
<script src="asset/js/dashboard.js" defer></script>
<script src="asset/js/status.js" defer></script>
<script src="asset/js/linkpreview.js" defer></script>
<script src="asset/search/search.js" defer></script>
<script>
    $(function(){
        //load lại khung khi có tin nhắn mới
        setInterval(function(){
            $(".wrap").load("message.php .mess", function(){
                $(".mess").scrollTop($(".mess")[0].scrollHeight);
            })                    
        }, 3000);

        $(".mess").scrollTop($(".mess")[0].scrollHeight);
    })         
</script>
</body>
</html>
FOOTER;
        return $this->footer;
    }

    /**
     * Giao diện Navbar
     */
    public function addFixMenu()
    {
        $user = new UserController();
        $usr = $user->GetUser($_COOKIE['login'], '');
        $id =$usr['user_id'];
        $name = empty($usr['user_displayname']) ? $usr['user_email'] : $usr['user_displayname'];

        // new request friend
        $follows = !empty($usr['user_follows']) ? unserialize($usr['user_follows']) : [];
        $count = count($follows);
        $req = $count > 0 ? "<span id='new-request'>+$count</span>" : "";
        
        //Here doc viết html vẫn giữ format
        $this->fixmenu =<<<FIXMENU
<div class="container-fluid">            
    <nav class="navbar navbar-inverse navbar-fixed-top">
        <div class="container-fluid">
            <div class="row">
                <div class="navbar-header col-12 col-sm-12 col-md-1 col-lg-1 col-xl-1"></div>
                
                <div class="navbar-header col-12 col-sm-12 col-md-3 col-lg-3 col-xl-3">
                    <a class="navbar-brand" href="dashboard.php" id="trangchu-hover">Trang chủ</a>
                </div>
                <div class="col-12 col-sm-12 col-md-4 col-lg-4 col-xl-4" style="text-align: center;">
                    <form class="navbar-form" action="search.php" method="GET">
                        <div class="form-group">
                            <input type="search" class="form-control" placeholder="Nhập tên hoặc email..." name="keyword">
                        </div>
                        <button type="submit" class="btn btn-info"><span class="glyphicon glyphicon-search"></span> Tìm</button>
                    </form>
                </div>

                <div class="col-12 col-sm-12 col-md-3 col-lg-3 col-xl-3">
                    <ul class="nav navbar-nav navbar-right">
                        <li class="dangxuat-hover"><a href="logout.php"><span class="glyphicon glyphicon-log-in"></span> Đăng xuất</a></li>
                    </ul>
                </div>  
                
                <div class="navbar-header col-12 col-sm-12 col-md-1 col-lg-1 col-xl-1"></div>
            </div>
        </div>
    </nav>
</div>
<div class="container-fluid">
    <div class="body-website">
        <div class="row">
FIXMENU;
        return $this->fixmenu;
    }

    /**
     * Giao diện Menu bên trái
     */
    public function addLeftMenu($username,$bg_color)
    {
        $user = new UserController();
        $message = new MessageController();
        $usr = $user->GetUser($username);
        $id = $usr['user_id'];
        $name = $usr['user_displayname'];

        $countFollowing = $user->CountListFriends($username, 'user_following');
        $countFollows = $user->CountListFriends($username, 'user_follows');
        $countFollowed = $user->CountListFriends($username, 'user_followed');

        $countAllUser = 0;
        $users = $user->ListUsers();

        // get list follow of user
        $info = $user->GetUser($username);
        $followed = !empty($info['user_followed']) ? unserialize($info['user_followed']) : [];
        $following = !empty($info['user_following']) ? unserialize($info['user_following']) : [];
        $follows = !empty($info['user_follows']) ? unserialize($info['user_follows']) : [];

        foreach ($users as $usr) {
            if ($username === $usr['user_email']) continue;

            if (in_array($usr['user_id'], $followed) || in_array($usr['user_id'], $follows) || in_array($usr['user_id'], $following)) continue;

            $countAllUser ++;
        }

        $checkfollows = "";
        if($countFollows > 0){
            $checkfollows = "color: red";
        }

        $checkmessage = "";
        $countmessagenoseen = $message->CountAllMessageFalse($id);
        if($countmessagenoseen > 0){
            $checkmessage = "color: red";
        }
        $this->leftMenu =<<<LEFTMENU
        <div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">
            <div class="row">
                <div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">
                    <div class="fix-left-menu">
                        <h4 class="text-center"><a href="profile.php?id=$id">$name</a></h4>                
                        <ul class="list-group list-group-flush"> 
                            <a href="change_profile.php">
                                <li class="list-group-item menu-hover"><span class="glyphicon glyphicon-pencil"></span> Đổi thông tin </li>
                            </a>   
                            <a href="change_password.php">
                                <li class="list-group-item menu-hover"><span class="glyphicon glyphicon-wrench"></span> Đổi mật khẩu </li>
                            </a>   
                            <a href="message.php">
                                <li class="list-group-item menu-hover"><span class="glyphicon glyphicon-envelope"></span> Tin nhắn <span class="badge" style="$checkmessage">$countFollowed</span></li>
                            </a>
                            <a href="following.php">
                                <li class="list-group-item menu-hover"><span class="glyphicon glyphicon-eye-open"></span> Đang theo dõi <span class="badge">$countFollowing</span></li>
                            </a>
                            <a href="follows.php">
                                <li class="list-group-item menu-hover"><span class="glyphicon glyphicon-user"></span> Lời mời kết bạn <span class="badge" style="$checkfollows">$countFollows</span></li>
                            </a>
                            <a href="all_user.php">
                                <li class="list-group-item menu-hover"><span class="glyphicon glyphicon-search"></span> Có thể bạn biết <span class="badge">$countAllUser</span></li>
                            </a>               
                        </ul> 
                    </div>  
                </div>
            </div>             
        </div>
        <div class="col-3 col-sm-2 col-md-2 col-lg-2 col-xl-2"></div>
        <div class="col-9 col-sm-8 col-md-8 col-lg-8 col-xl-8" style="background-color: $bg_color">
            <br/>
LEFTMENU;
        return $this->leftMenu;
    }

    /**
     * Giao diện Menu bên phải
     */
    public function addRightMenu()
    {
        $this->rightMenu = <<<RIGHTMENU
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
    public function addStatus()
    {
            // <label for="image">Hình ảnh: </label>
        $this->status =<<<STATUS
            <div class="status">
                <form action="" method="POST" enctype="multipart/form-data">
                    <textarea rows='6' placeholder='Hôm nay của bạn thế nào...' class="content" name="content"></textarea>

                    <div class="status-extra-content">
                        <hr>
                        <input type="file" name="image" class="form-control" id="status-image">                       

                        <select class="form-control" id="sel1" name = "role">
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
    public function addNewsfeed($contents,$username)
    {

        $user = new UserController();
        $comment = new CommentController();
        $status = new StatusController();
        $currentUser = $user->GetUser($username);

        $this->newsfeed = "";

        foreach ($contents as $content) {

            // real-name & avatar
            $usr = $user->GetUser('', $content['status_user_id']);
            $id_user = $usr['user_id'];
            $name = empty($usr['user_displayname']) ? $usr['user_email'] : $usr['user_displayname'];
            $src = !empty($usr['user_avatar']) ? 'data:image;base64,'.$usr['user_avatar'] : "asset/img/non-avatar.png";

            // image attach in status
            $imageAttach = !empty($content['status_image']) ? "<img src=$content[status_image] class='image_status'><br>" : "";

            //Comment form (Trang)
            $id_status = $content['status_id'];

            // avatar comment
            $currentAvatar = !empty($currentUser['user_avatar']) ? 'data:image;base64,'.$currentUser['user_avatar'] : "asset/img/non-avatar.png";

            // role status
            $role = "fas fa-globe-asia";
            if (strcmp($content['status_role'], 'Công khai') == 0)
                $role = '<span class="fas fa-globe-asia"></span>';
            if (strcmp($content['status_role'], 'Bạn bè') == 0)
                $role = '<span class="fas fa-user"></span>';
            if (strcmp($content['status_role'], 'Chỉ mình tôi') == 0)
                $role = '<span class="far fa-eye-slash"></span>';

            // like or unlike
            $amountLike = $status->AmountOfLiked($content['status_id']);
            $amountLike = $amountLike > 0 ? $amountLike : "" ;

            $like = "";
            $nonlike = "";
            $userIsLike = $status->IsLiked($currentUser['user_id'], $content['status_id']);
            if ($userIsLike) {
                $like = "table-cell";
                $nonlike = "none";
            } else {
                $like = "none";
                $nonlike = "table-cell";
            }

            $comments = $comment->CommentWithIdStatus($id_status);
            $amountComment = count($comments);
            $amountComment = $amountComment > 0 ? $amountComment : "";


            // content status html
            $this->newsfeed .=<<<NEWSFEED
            <!--Status-->
            <div class="card">
                <a name="$content[status_id]"></a>
                <div id="$content[status_id]">
                    <div class="card-header">
                        <div class="new-title">
                            <a href="profile.php?id=$id_user">
                                <img src='$src' alt="logo" title='$name'> 
                            </a>
                            <h4 id="user">
                                <a href="profile.php?id=$id_user">$name</a> 
                                <span title="$content[status_role]">&nbsp;&nbsp;$role</span>               
                            </h4>                                
                            <span title='$content[status_created]'><i>$content[status_created]</i></span>
                        </div>
NEWSFEED;
if($id_user == $currentUser['user_id']){
    $this->newsfeed .=<<<NEWSFEED
            <div class="new-title-role">
                <!-- Trigger the modal with a button -->
                <button id="btn-modal" type="button" class="btn btn-default" data-toggle="modal" data-target="#myModal">Sửa bài đăng</button>
                                    
                <!-- Modal -->
                <form action="" method="POST">
                <div class="modal fade" id="myModal" role="dialog">
                    
                        <div class="modal-dialog modal-my-style">                                
                        <!-- Modal content-->
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                    <h4 class="modal-title">Chỉnh sửa bài đăng</h4>
                                </div>
                                <div class="modal-body">
                                    <input name="status_id_change" value='$content[status_id]' hidden> 
                                    <textarea rows='6' placeholder='$content[status_content]' name="new_content"></textarea>                                            
                                    <select class="form-control" id="sel1" name="new_role">
                                        <option>Công khai</option>
                                        <option>Bạn bè</option>
                                        <option>Chỉ mình tôi</option>
                                    </select>                                            
                                </div>
                                <div class="modal-footer">
                                    <button type="submit" class="btn btn-success" name="changeStatus">Cập nhật</button>
                                </div>
                            </div>                                    
                        </div>
                    
                </div> 
                </form>               
            </div>
NEWSFEED;
}

            $this->newsfeed .=<<<NEWSFEED
            </div>
                    <div class="card-body">
                        <div class="new-content">$content[status_content]</div>
                        $imageAttach
                    </div>
                    <!--Reaction Button-->
                    <div class="card-footer">
                        <div class="reaction">
                            <ul>
                                <li style="display: $like" class='reaction-like' id="reaction-like-$content[status_id]"> &nbsp;Đã thích <span id="numlike-$content[status_id]">$amountLike</span></li>

                                <li style="display: $nonlike" class='reaction-nonlike' id="reaction-nonlike-$content[status_id]"> &nbsp;Thích <span id="numnonlike-$content[status_id]">$amountLike</span></li>

                                <li class="reaction-comment" id="reaction-comment-$content[status_id]">&nbsp;Bình luận <span id="numcom-$content[status_id]">$amountComment</span></li>

                                <li class="reaction-share" id="reaction-share-$content[status_id]">&nbsp;Chia sẽ</li>
                            </ul> 
                            <!--Comment-->
                            <div class="hide-comment-status" id="comment-status-$content[status_id]">
                                <div class="new-comment">
                                    <img src="$currentAvatar" alt="logo" title="$name">
                                    <div class="content">
                                        <form action="#" method="POST" class="frmComment" id="frmComment-$content[status_id]">
                                            <input name="username" value="$_COOKIE[login]" hidden>
                                            <input name="type" value="new_status" hidden>
                                            <input name="id_status" value="$content[status_id]" hidden>
                                            <input type="text" name="content_comment" class="content_comment" placeholder=" Viết bình luận ..." value="" id="content_comment_$content[status_id]">
                                            <button name="addComment" class="btn btn-primary center-block" style="display: none">Đăng</button>
                                        </form>
                                    </div>
                                </div>
                                <div class="show-comment" id="show-comment-$content[status_id]">
NEWSFEED;

            //show comment
            foreach ($comments as $row)
            {
                $userComment = $user->GetUser('', $row['comment_user_id']);
                $id_user_comment = $userComment['user_id'];
                $contentComment = $row['comment_content'];
                $avatarUserComment = !empty($userComment['user_avatar']) ? 'data:image;base64,'.$userComment['user_avatar'] : "asset/img/non-avatar.png";
                $nameComment = $userComment['user_displayname'];


                // content html comment
                $this->newsfeed .=<<<COMMENTS
                                    <div class="detail-comment">
                                        <div id="icon">
                                            <img src='$avatarUserComment' alt="icon" title='$nameComment'>
                                        </div>
                                        <div class="content">
                                            <span id="user-comment">
                                                <a href="profile.php?id=$id_user_comment"> $nameComment </a>
                                            </span>
                                            <span id="content-commment">
                                                $contentComment
                                            </span> 
                                        </div>
                                    </div>
                                    <br/>
COMMENTS;
            }

            $this->newsfeed.= "</div></div></div></div></div></div><br/>";
        }

        $this->newsfeed.="</div>";
        return $this->newsfeed;
    }

    public function ListFriendIndex($username)
    {
        $this->friend = "";
        $user = new UserController();
        $users = $user->ListFriends($username, 'user_followed');


        $this->friend .=<<<FRIENDSINDEX
        <div class="col-3 col-sm-0 col-md-0 col-lg-0 col-xl-0"></div>
        <div class="col-9 col-sm-2 col-md-2 col-lg-2 col-xl-2">
            <div class="row">
                <div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">
                    <div class="fix-right-menu">
                        <h4 class="text-center">Danh sách bạn bè </h4>
                        <div>
                            <ul class="list-group list-group-flush">
FRIENDSINDEX;

        foreach ($users as $usr) {
            // real-name & avatar
            $name = !empty($usr['user_displayname']) ? $usr['user_displayname'] : $usr['user_email'];
            $src = !empty($usr['user_avatar']) ? 'data:image;base64,'.$usr['user_avatar'] : "asset/img/non-avatar.png";
            $id = $usr['user_id'];

            $this->friend .=<<<FRIENDSINDEX
                                <li class="list-group-item">
                                    <a href="profile.php?id=$id">
                                        <span id="ficon"><img src=$src alt="."></span>
                                        <span>$name</span>
                                        <span id="onoff"></<span></span>
                                    </a>
                                </li>
FRIENDSINDEX;
        }

        $this->friend .=<<<FRIENDSINDEX
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
FRIENDSINDEX;


        return $this->friend;
    }


    /**
     * Giao diện liệt kê tất cả user hiện có
     * @param [type] $username [description]
     */
    public function ListUsers($username)
    {
        $this->friend = "";
        $user = new UserController();
        $users = $user->ListUsers();

        // get list follow of user
        $info = $user->GetUser($username);
        $followed = !empty($info['user_followed']) ? unserialize($info['user_followed']) : [];
        $following = !empty($info['user_following']) ? unserialize($info['user_following']) : [];
        $follows = !empty($info['user_follows']) ? unserialize($info['user_follows']) : [];

        foreach ($users as $usr) {
            if ($username === $usr['user_email']) continue;

            if (in_array($usr['user_id'], $followed) || in_array($usr['user_id'], $follows) || in_array($usr['user_id'], $following)) continue;

            // real-name & avatar
            $name = !empty($usr['user_displayname']) ? $usr['user_displayname'] : $usr['user_email'];
            $src = !empty($usr['user_avatar']) ? 'data:image;base64,'.$usr['user_avatar'] : "asset/img/non-avatar.png";
            $id = $usr['user_id'];
            //content list user html
            $this->friend .=<<<LISTUSER
<li>
    <form action="" method="POST">
        <img src=$src alt="avatar">
        <h2><a href="profile.php?id=$id">$name</a></h2>
        <input name="name" value=$usr[user_email] hidden>
        <button class='btn btn-primary' name='addFriend'>Thêm bạn bè</button>
    </form>
</li>
LISTUSER;
        }

        return $this->friend;
    }

    /**
     * Giao diện liệt kê tất cả user hiện có
     * @param [type] $username [description]
     */
    public function ListUsersAll($username)
    {
        $this->friend = "";
        $user = new UserController();
        $users = $user->ListUsers();

        // get list follow of user
        $info = $user->GetUser($username);
        $followed = !empty($info['user_followed']) ? unserialize($info['user_followed']) : [];
        $following = !empty($info['user_following']) ? unserialize($info['user_following']) : [];
        $follows = !empty($info['user_follows']) ? unserialize($info['user_follows']) : [];

        foreach ($users as $usr) {
            if ($username === $usr['user_email']) continue;

            if (in_array($usr['user_id'], $followed) || in_array($usr['user_id'], $follows) || in_array($usr['user_id'], $following)) continue;

            // real-name & avatar
            $name = !empty($usr['user_displayname']) ? $usr['user_displayname'] : $usr['user_email'];
            $src = !empty($usr['user_avatar']) ? 'data:image;base64,'.$usr['user_avatar'] : "asset/img/non-avatar.png";
            $id = $usr['user_id'];
            //content list user html
            $this->friend .=<<<LISTUSERALL
<li>
    <form action="profile.php?id=$id" method="POST">
        <img src=$src alt="avatar" title="$name">
        <h2>$name</h2>
        <input name="name" value=$usr[user_email] hidden>
        <a href="profile.php?id=$id"><button class='btn btn-primary'>Trang cá nhân</button></a>
    </form>
</li>
LISTUSERALL;
        }

        return $this->friend;
    }


    /**
     * Giao diện liệt kê tất cả Friend hiện có
     * @param [type] $username [description]
     */
    public function ListFriends($username)
    {
        $this->friend = "";
        $user = new UserController();
        $userfriends = $user->ListFriends($username, 'user_followed');

        foreach ($userfriends as $usr) {
            // real-name & avatar
            $name = !empty($usr['user_displayname']) ? $usr['user_displayname'] : $usr['user_email'];
            $src = !empty($usr['user_avatar']) ? 'data:image;base64,'.$usr['user_avatar'] : "asset/img/non-avatar.png";
            $id = $usr['user_id'];
            //content list friends html
            $this->friend .=<<<LISTFRIEND
<li>
    <form action="" method="POST">
        <img src=$src alt="avatar">
        <h2><a href="profile.php?id=$id">$name</a></h2>
        <input name="name" value=$usr[user_email] hidden>
        <button class='btn btn-danger center-block' name='unFriend'>Bỏ kết bạn</button>
    </form>
</li>
LISTFRIEND;
        }

        return $this->friend;
    }

    /**
     * Giao diện liệt kê tất cả Người đang theo dõi mình
     * @param [type] $username [description]
     */
    public function ListFollows($username)
    {
        $this->friend = "";
        $user = new UserController();
        $users = $user->ListFriends($username, 'user_follows');

        foreach ($users as $usr) {
            // real-name & avatar
            $name = !empty($usr['user_displayname']) ? $usr['user_displayname'] : $usr['user_email'];
            $src = !empty($usr['user_avatar']) ? 'data:image;base64,'.$usr['user_avatar'] : "asset/img/non-avatar.png";
            $id = $usr['user_id'];
            //content list Follows html
            $this->friend .=<<<LISTFRIEND
<li>
    <form action="" method="POST">
        <img src=$src alt="avatar">
        <h2><a href="profile.php?id=$id">$name</a></h2>
        <input name="name" value=$usr[user_email] hidden>
        <div class='submit-group'>
            <button class='btn btn-success btn-block' name='acceptFriend'>Chấp nhận</button>
            <button class='btn btn-danger btn-block' name='declineFriend'>Từ chối</button>
        </div>
    </form>
</li>
LISTFRIEND;
        }

        return $this->friend;
    }

    /**
     * Giao diện liệt kê tất cả người mà mình đang theo dõi
     * @param [type] $username [description]
     */
    public function CheckId($id, $username){
        $check = false;
        $user = new UserController();
        $userfriends = $user->ListFriends($username, 'user_followed');        
        foreach($userfriends as $usrf){
            if($usrf['user_id'] == $id){
                $check = true;
                break;
            }
        }
        return $check;
    }
    public function ListFollowing($username)
    {
        $this->friend = "";
        $user = new UserController();
        $userfollowing = $user->ListFriends($username, 'user_following');

        foreach ($userfollowing as $usr) {
            // real-name & avatar
            $name = !empty($usr['user_displayname']) ? $usr['user_displayname'] : $usr['user_email'];
            $src = !empty($usr['user_avatar']) ? 'data:image;base64,'.$usr['user_avatar'] : "asset/img/non-avatar.png";
            $id = $usr['user_id'];
            //content list Following html
            $this->friend .=<<<LISTFRIEND
<li>
    <form action="" method="POST">
        <img src=$src alt="avatar">
        <h2><a href="profile.php?id=$id">$name</a></h2>
        <input name="name" value=$usr[user_email] hidden>
LISTFRIEND;
            if($this->CheckId($usr['user_id'], $username)){
                $this->friend .=<<<LISTFRIEND
                <button type="submit" class="btn btn-danger" name="unFriend">Hủy kết bạn</button>
LISTFRIEND;
            }else{
                $this->friend .=<<<LISTFRIEND
                <button class='btn btn-warning' name='unFollowing'>Bỏ theo dõi</button>
LISTFRIEND;
            } 
            $this->friend .=<<<LISTFRIEND
    </form>
</li>
LISTFRIEND;
        }

        return $this->friend;
    }

    /**
     * Giao diện Lấy lại mật khẩu
     */
    public function addResetPassword()
    {
        $this->frmResetPassword =<<<FORM_RESET_PASSWORD
<!--RESET PASSWORD-->
<div class="login-page">
    <div class="login-box">
        <div class="logo">
            <a><b>LẤY LẠI MẬT KHẨU</b></a>
        </div>
        <div class="card">
            <div class="body">
                <form method="post" action="">
                    <div class="input-group form-float">
                        <span class="input-group-addon">
                            <i class="material-icons">email</i>
                        </span>
                        <div class="form-line">
                            <input type="email" class="form-control" name="username" placeholder="Địa chỉ Email..."
                                    required autofocus>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-4"></div>
                        <div class="col-xs-4">
                            <button type="submit" class="btn btn-block bg-pink waves-effect">Gửi mã</button>
                        </div>
                        <div class="col-xs-4"></div>
                    </div>
                    <div class="row text-center">
                        <div class="col-xs-12">
                            Chưa có tài khoản?
                            <a href="register.php" title="Đăng nhập" target="_parent"> Đăng ký</a>
                        </div>
                        <div class="col-xs-12">
                            Đã nhớ mật khẩu!
                            <a href="login.php" title="Đăng nhập" target="_parent"> Đăng nhập</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

FORM_RESET_PASSWORD;
        return $this->frmResetPassword;
    }

    /**
     * Giao diện nhập mật khẩu mới khi Lấy lại mật khẩu
     */
    public function addNewPassword()
    {
        $this->frmNewPassword =<<<FORM_NEW_PASSWORD
<!-- NEW PASSWORD -->
<div class="login-page">
    <div class="login-box">
        <div class="logo">
            <a><b>MẬT KHẨU MỚI</b></a>
        </div>
        <div class="card">
            <div class="body">
                <form method="POST" action="">
                    <div class="input-group form-float">
                        <span class="input-group-addon">
                            <i class="material-icons">lock</i>
                        </span>
                        <div class="form-line">
                            <input type="password" class="form-control" name="password" placeholder="Mật khẩu mới..."
                                required autofocus>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-3"></div>
                        <div class="col-xs-6">
                            <button type="submit" class="btn btn-block bg-pink waves-effect">Đổi mật khẩu</button>
                        </div>
                        <div class="col-xs-3"></div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

FORM_NEW_PASSWORD;
        return $this->frmNewPassword;
    }

    /**
     * Giao diện để show thông báo
     * @param string $display [description]
     * @param string $style   [description]
     * @param string $message [description]
     */
    public function addAlert($display = 'none', $style = 'danger', $message = '')
    {
        return "<div class='alert alert-$style' style='$display'><center>$message</center></div>";
    }

    /**
     * Giao diện Tìm kiếm 1 tài khoản
     * @param [type] $nameKey [description]
     */
    public function SearchUser($name) {
        $user = new UserController();
        $listUser = $user->SearchUsersByName($name);
        if(count($listUser) == 0) {
            return "";
        }
        $this->users .=<<<SEARCHUSER
<div class="tab-content">
    <ul class="global">
SEARCHUSER;
        foreach ($listUser as $usr) {

            // real-name & avatar
            $name = !empty($usr['user_displayname']) ? $usr['user_displayname'] : $usr['user_email'];
            $src = !empty($usr['user_avatar']) ? 'data:image;base64,'.$usr['user_avatar'] : "asset/img/non-avatar.png";
            $id = $usr['user_id'];

            $this->users .=<<<SEARCHUSER
        <!-- Status -->
        <li>
            <form action="profile.php?id=$id" method="POST">
                <img src=$src alt="avatar" title="$name">
                <h2>$name</h2>
                <input name="name" value=$usr[user_email] hidden>
                <a href="profile.php?id=$id"><button class='btn btn-primary'>Trang cá nhân</button></a>
            </form>
        </li>
SEARCHUSER;
        }
        $this->users .=<<<SEARCHUSER
    </ul>
</div>
SEARCHUSER;
        
        return $this->users;
    }
}