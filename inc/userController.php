<?php
    use PMPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

    //autoloadClass + PHPMailer
    include_once 'autoloadClass.php';
    require dirname(dirname(__FILE__)) . '/vendor/autoload.php';

    //đặt timezone mặc định
    date_default_timezone_set('Asia/Ho_Chi_Minh');

    /*===============================
        Class control user
    ===============================*/
    class userController{
        private $request;

        //kết nối database
        public function __construct(){
            db::connect();
        }

        //đặt tên và thời gian cho cookie quản lý đăng nhập của user
        private function setCookie($user_email, $user_displayname, $remember = "on"){
            if($remember == "on"){
                $time = 3600 * 24; //24 giờ
            }
            else{
                $time = 60 * 10; //10 phút
            }

            setcookie('user_login', $user_email, time() + $time);
            setcookie('user_displayname', $user_displayname, time() + $time);

            return 1;
        }

        //tìm user theo id hoặc email
        public function getUser($user_email = '', $user_id = ''){
            //kiểm tra dữ liệu
            if(((int)$user_id < 1 || empty($user_id)) && empty($user_email)){
                return "Tài khoản không hợp lệ!";
            }

            try{
                //truy xuất database
                $sqlSelect = "SELECT * FROM users WHERE user_id = ? OR user_email = ?";
                $data = db::$connectionstring->prepare($sqlSelect);
                if($data->execute(array($user_id, $user_email))){
                    return $data->fetch(PDO::FETCH_ASSOC);
                }

                return "Có lỗi xảy ra!";
            }
            catch(PDOException $ex){
                throw new PDOException($ex->getMessage());
            }
        }

        //đăng nhập, ... => truyền vào mảng đối số mà không biết trước số lượng phần tử
        public function login(...$mang){
            $this->request = $mang[0];

            //kiểm tra dữ liệu
            if(empty($this->request['user_email']) || empty($this->request['user_password'])){
                return "Nhập đầy đủ dữ liệu!";
            }

            //kiểm tra email có đúng định dạng
            if(!preg_match('/^[0-9a-zA-Z._]+\@[a-zA-Z]+\..*$/', $this->request['user_email'])){
                return "Tên đăng nhập không đúng định dạng email!";
            }

            try{
                //kiểm tra email và password có tồn tại trong database
                $user = $this->getUser($this->request['user_email']);
                if($user['user_email'] != $this->request['user_email']){
                    return "Không tồn tại tài khoản hoặc sai mật khẩu!";
                }

                if(!password_verify($this->request['user_password'], $user['user_password'])){
                    return "Không tồn tại tài khoản hoặc sai mật khẩu!";
                }

                //cập nhật thời gian đăng nhập cuối và đặt thời gian cho cookie
                $sqlUpdate = "UPDATE users SET user_lastlogin = now() WHERE user_email = ?";
                $data = db::$connectionstring->prepare($sqlUpdate);
                if($data->execute(array($user['user_email']))){
                    if($this->setCookie($user['user_email'], $user['user_displayname'])){
                        return 1;
                    }
                }

                return "Đăng nhập thất bại!";
            }
            catch(PDOException $ex){
                throw new PDOException($ex->getMessage());
            }
        }

        //gửi email xác nhận đăng ký tài khoản bằng link
        public function sendEmail($user_email, $token){
            $mail = new PHPMailer(true);
            try {
                //Server settings
                $mail->isSMTP();                                      // Set mailer to use SMTP
                $mail->Host = 'smtp.gmail.com';  // Specify main and backup SMTP servers
                $mail->SMTPAuth = true;                               // Enable SMTP authentication
                $mail->Username = 'testwebltt@gmail.com';                 // SMTP username
                $mail->Password = 'testwebltt97@';                           // SMTP password
                $mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
                $mail->Port = 587;                                    // TCP port to connect to
            
                //Recipients
                $mail->setFrom('testwebltt@gmail.com', 'Web 1');
                $mail->addAddress($email);     //
                //Content
                $mail->isHTML(true);
                $mail->Subject = 'Xác nhận đăng ký tài khoản';
                $mail->Body    = "Nhấn vào đường dẫn sau để xác nhận đăng ký tài khoản: <a href='http://$_SERVER[HTTP_HOST]/confirm.php?register_email=$user_email&register_token=$token'>http://$_SERVER[HTTP_HOST]/confirm.php?email=$user_email&token=$token</a>";

                $mail->send();
                return 1;
            } catch (Exception $e) {
                return "Không thể gửi mail. Mailser Error: " . $mail->ErrorInfo;
            }
        }

        //đăng ký tài khoản
        public function register(...$mang){
            $this->request = $mang[0];

            //kiểm tra các trường dữ liệu có để trống
            if(empty($this->request['user_email'])){
                return "Tên đăng nhập không được để trống!";
            }
            if(empty($this->request['user_displayname'])){
                return "Tên người dùng không được để trống!";
            }
            if(empty($this->request['user_password'])){
                return "Mật khẩu không được để trống!";
            }
            if(empty($this->request['user_repassword'])){
                return "Nhập lại mật khẩu không được để trống!";
            }

            //kiểm tra định dạng email
            if(!preg_match('/^[0-9a-zA-Z._]+\@[a-zA-Z]+\..*$/', $this->request['user_email'])){
                return "Tên đăng nhập không đúng định dạng email!";
            }

            //so khớp password và nhập lại password
            if(strcmp($this->request['user_password'], $this->request['user_repassword']) != 0){
                return "Mật khẩu nhập lại không khớp!";
            }

            //kiểm tra số lượng ký tự tối thiểu của password (>= 6 ký tự)
            if(strlen($this->request['user_password']) < 6){
                return "Mật khẩu tối thiểu phải có 6 ký tự!";
            }

            try{
                //kiểm tra xem email đã tồn tại trong database
                $user = $this->getUser($this->request['user_email']);
                if($user['user_email'] == $this->request['user_email']){
                    return "Tài khoản đã tồn tại!";
                }

                //tạo token xác thực (25 ký tự, phân biệt hoa thường)
                $token = '';
                $stringRandom = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
                $lenStringRandom = strlen($stringRandom) - 1;
                for($char = 0; $char < 25; $char++){
                    $token .= $stringRandom[rand(0, $len)];
                }

                //lưu dữ liệu đăng ký vào database
                $passwordHash = password_hash($this->request['user_password'], PASSWORD_DEFAULT);
                $sqlInsert = "INSERT INTO register(register_email, register_password, register_token, register_displayname) VALUES(?, ?, ?, ?)";
                $data = db::$connectionstring->prepare($sqlInsert);

                //nếu lưu thành công thì gửi mail để xác thực
                if($data->execute(array($this->request['user_email'], $passwordHash, $token, $this->request['user_displayname']))){                    
                    return $this->sendEmail($this->request['user_email'], $token);
                }
            }
            catch (PDOException $ex) {
                throw new PDOException($ex->getMessage());
            }
        }

        //tìm tài khoản đăng ký trong bảng register bằng email và token
        public function findRegisterByEmailAndToken($register_email, $register_token){
            //kiểm tra dữ liệu trống
            if(empty($register_email) || empty($register_token)){
                return null;
            }

            try{
                //lấy dữ liệu từ bảng register
                $sqlSelect = "SELECT * FROM register WHERE register_email = ? AND register_token = ?";
                $data = db::$connectionstring->prepare($sqlSelect);

                //nếu có trả về bản ghi lấy được
                if($data->execute(array($register_email, $register_token))){
                    return $data->fetch(PDO::FETCH_ASSOC);
                }

                //mặc định trả về null
                return null;
            }
            catch (PDOException $ex) {
                throw new PDOException($ex->getMessage());
            }
        }

        //xóa bản ghi trong bảng register bằng email
        public function deleteRegisterByEmail($register_email){
            try{
                $sqlDelete = "DELETE FROM register WHERE register_email = ?";
                $data = db::$connectionstring->prepare($sqlDelete);
                $data->execute(array($register_email));
            }
            catch (PDOException $ex) {
                throw new PDOException($ex->getMessage());
            }
        }

        //xác thực tài khoản
        public function confirm(...$mang){
            $this->request = $mang[0];
            $token = $this->request['register_token'];
            $email = $this->request['register_email'];
            if(isset($token) && isset($email)){
                $check = $this->findRegisterByEmailAndToken($email, $token);

                if($check){
                    try{
                        //lưu tài khoản vào bảng users
                        $sqlInsert = "INSERT INTO users(user_email, user_password, user_displayname, user_created, user_lastlogin) VALUES(?, ?, ?, now(), now())";
                        $data = db::$connectionstring->prepare($sqlInsert);

                        //nếu insert vào bảng users thành công thì xóa bản ghi ở bảng register
                        if($data->execute(array($email, $check['register_password'], $check['register_displayname']))){
                            $this->deleteRegisterByEmail($email);
                            return 1;
                        }
                    }
                    catch (PDOException $ex) {
                        throw new PDOException($ex->getMessage());
                    }
                }
            }
            return "Xác thực tài khoản thất bại!";
        }

        //đổi mật khẩu
        public function changePassword($user_email, ...$mang){
            $this->request = $mang[0];

            //kiểm tra dữ liệu rỗng
            if(empty($this->request['user_password_old'])){
                return "Chưa nhập mật khẩu cũ!";
            }

            if(empty($this->request['user_password_new'])){
                return "Chưa nhập mật khẩu mới!";
            }

            if(empty($this->request['user_password_renew'])){
                return "Chưa nhập lại mật khẩu mới!";
            }

            if(strcmp($this->request['user_password_new'], $this->request['user_password_renew'])){
                return "Mật khẩu mới không khớp với nhau!";
            }

            if(!strcmp($this->request['user_password_old'], $this->request['user_password_new'])){
                return "Mật khẩu mới giống mật khẩu hiện tại!";
            }

            try{
                //kiểm tra tài khoản có tồn tại
                $user = $this->getUser($user_email);
                if($user['user_email'] != $user_email){
                    return "Tài khoản không tồn tại!";
                }

                //kiểm tra mật khẩu hiện tại có đúng
                if(!password_verify($his->require['user_password_old'], $user['user_password'])){
                    return "Mật khẩu không chính xác!";
                }

                //hashpass và cập nhật lại vào database
                $passwordHash = password_hash($this->require['user_password_new'], PASSWORD_DEFAULT);

                $sqlUpdate = "UPDATE users SET user_password = ? WHERE user_email = ?";
                $data = db::$connectionstring->prepare($sqlUpdate);
                if($data->execute(array($passwordHash, $user_email))){
                    return "Đổi mật khẩu thành công";
                }

                //mặc định là không thành công
                return "Không thành công, có lỗi xảy ra!";
            }
            catch (PDOException $ex) {
                throw new PDOException($ex->getMessage());
            }
        }

        //cập nhật thông tin cá nhân
        public function updateProfile($user_email, $user_avatar, ...$mang){
            $this->request = $mang[0];
            $phone = $name = $image = '';
            
            //cập nhật số điện thoại (test)
            /*if (!empty($this->request['phone'])){
                if (!preg_match('/0{1}[0-9]{9}$|\+[0-9]{2}[0-9]{9}$/', $this->request['phone'])) {
                    return "Định dạng số điện thoại không chính xác";
                }
                $phone = $this->request['phone'];
            }*/

            //cập nhật tên
            if(!empty($this->request['user_displayname'])){
                $temp_name = htmlspecialchars($this->request['user_diaplayname']);

                //tên không dài quá 50 ký tự
                if(strlen($temp_name) > 50 || strlen($temp_name) < 2){
                    return "Tên dài từ 2 đến 50 ký tự!";
                }

                $name = $temp_name;
            }

            //cập nhật avatar
            if(!empty($avatar['user_avatar']['name']) && $avatar['user_avatar']['size'] > 0){
                if(getimagesize($avatar['user_avatar']['tmp_name']) === false){
                    return "Không đúng định dạng hình ảnh!";
                }

                $image = base64_encode(file_get_contents($avatar['user_avatar']['tmp_name']));
            }

            try{
                $user = $this->request->getUser($user_email);
                if($user['user_email'] != $user_email){
                    return "Tài khoản không tồn tại!";
                }

                if(empty($phone)){
                    $phone = $user['user_phone'];
                }

                if(empty($name)){
                    $name = $user['user_displayname'];
                }

                if(empty($image)){
                    $image = $user['user_avatar'];
                }

                $sqlUpdate = "UPDATE users SET user_displayname = ?, user_phone = ?, user_avatar = ? WHERE user_email = ?";
                $data = db::$connectionstring->prepare($sqlUpdate);
                if($data->execute(array($name, $phone, $image, $user_email))){
                    return "Cập nhật thông tin thành công";
                }

                //mặc định thất bại
                return "Cập nhật thất bại, có lỗi xảy ra!";
            }
            catch (PDOException $ex) {
                throw new PDOException($ex->getMessage());
            }
        }

        //tạo status mới
        public function newStatus($user_email, $attach, ...$mang){
            $this->request = $mang[0];

            //xử lý ảnh đính kèm
            if(!empty($attach['status_image']['name']) && $attach['status_image']['size'] > 0){
                if(getimagesize($attach['status_image']['tmp_name']) === false){
                    return "Không đúng định dạng hình ảnh!";
                }

                $this->request['status_image'] = $attach['status_image'];
            }

            //xử lý nội dung status
            if(!isset($this->request['image']) && empty($this->request['status_content'])){
                return "Chưa có nội dung!";
            }

            try{
                $user = $this->getUser($user_email);
                if($user['user_email'] != $user_email){
                    return "Tài khoản không tồn tại!";
                }

                $status = new statusController();
                $status_id = $status->newStatus($user['user_id'], $this->request);

                return $status_id ? $status_id : "Đăng bài thất bại, đã có lỗi xảy ra!";
            }
            catch (PDOException $ex) {
                throw new PDOException($ex->getMessage());
            }
        }

        //tạo comment mới
        public function newComment($comment_status_id, $comment_user_email, $comment_content){
            //kiểm tra dữ liệu rỗng
            if(empty($comment_content)){
                return "Chưa viết gì cả!";
            }   

            try{
                $user = $this->getUser($comment_user_email);
                if($user['user_email'] != $comment_user_email){
                    return "Tài khoản không tồn tại!";
                }
                $comment = new commentController();
                $comment_id = $comment->newComment($comment_status_id, $user['user_id'], $comment_content);

                return $comment_id ? $comment_id : "Comment thất bại, có lỗi xảy ra!";
            }
            catch (PDOException $ex) {
                throw new PDOException($ex->getMessage());
            }
        }

        //load các newfeed
        public function loadNewsFeed($user_email){
            try{
                $user = $this->getUser($user_email);
                if($user['user_email'] != $user_email){
                    return "Tài khoản không tồn tại!";
                }

                $user_id = $user['user_id'];
                $following = $user['user_following'];
                $arrayStatus = [];

                $status = new statusController();

                $stt = $status->statusById($user_id);
                if($stt != null){
                    $arrayStatus = array_merge($arrayStatus, $stt);
                }

                if(!empty($following)){
                    $friend_id = unserialize($following);
                    foreach($friend_id as $f_id){
                        $stt = $status->showStatusWithRelationship($friend_id, $f_id);
                        if($stt != null){
                            $arrayStatus = array_merge($arrayStatus, $stt);
                        }
                    }
                }

                if(count($arrayStatus) < 1){
                    $arrayStatus = $status->statusRandom();
                }

                return $arrayStatus;
            }
            catch (PDOException $ex) {
                throw new PDOException($ex->getMessage());
            }
        }

        //danh sách bạn bè
        public function listFriends($user_email, $follow = 'followed'){
            //kiểm tra dữ liệu rỗng
            if(empty($user_email)){
                return "Nhập đầy đủ thông tin!";
            }

            try{
                $user = $this->getUser($user_email);
                if($user['user_email'] != $user_email){
                    return "Tài khoản không tồn tại!";
                }

                $follows_id = !empty($user[$follow]) ? unserialize($user[$follow]) : [];
                $friends = [];

                //lấy thông tin bạn bè bằng id
                foreach($follows_id as $f_id){
                    $sqlSelect = "SELECT * FROM users WHERE user_id = ?";
                    $data = db::$connectionstring->prepare($sqlSelect);
                    if($data->execute(array($f_id))){
                        $row = $data->fetchAll(PDO::FETCH_ASSOC);
                        $friends = array_merge($friends, $row);
                    }
                }

                return $friends;
            }
            catch (PDOException $ex) {
                throw new PDOException($ex->getMessage());
            }
        }

        //thêm bạn bè
        public function addFriend($user_email_A, $user_email_B){
            //A gửi lời mời kết bạn cho B
            //kiểm tra dữ liệu rỗng
            if(empty($user_email_A) || empty($user_email_B)){
                return "Tài khoản rỗng!";
            }

            if(strcmp($user_email_A, $user_email_B) == 0){
                return "Không thể gửi yêu cầu cho chính mình!";
            }

            try{
                $user_A = $this->getUser($user_email_A);
                $user_B = $this->getUser($user_email_B);
                if($user_A['user_email'] != $user_A || $user_B['user_email'] != $user_B){
                    return "Tài khoản không tồn tại!";
                }

                //xét lời mời tồn tại chưa
                $following_A = !empty($user_A['user_following']) ? unserialize($user_A['user_following']) : [];
                $following_B = !empty($user_B['user_following']) ? unserialize($user_B['user_following']) : [];
                $follows_A = !empty($user_A['user_follows']) ? unserialize($user_A['user_follows']) : [];
                $follows_B = !empty($user_B['user_follows']) ? unserialize($user_B['user_follows']) : [];
                $id_A = $user_A['user_id'];
                $id_B = $user_B['user_id'];

                //nếu A đang theo dõi B hoặc B đang theo dõi A thì không thể kết bạn
                if(in_array($id_B, $following_A) || in_array($id_B, $follows_A) || in_array($id_A, $following_B) || in_array($id_A, $follows_B)){
                    return "Không thể gửi yêu cầu kết bạn!";
                }

                //lưu lại A đang theo dõi B
                array_push($following_A, $id_B);
                array_push($follows_B, $id_A);

                $sqlUpdate_A = "UPDATE users SET user_following = ? WHERE user_email = ?";
                $sqlUpdate_B = "UPDATE users SET user_following = ? WHERE user_email = ?";

                $data = db::$connectionstring->prepare($sqlUpdate_A);
                if(!$data->execute(array(serialize($following_A), $user_A))){
                    return "Không thể gửi yêu cầu kết bạn, đã có lỗi xảy ra!";
                }
                $data = db::$connectionstring->prepare($sqlUpdate_B);
                if(!$data->execute(array(serialize($follows_B), $user_B))){
                    return "Không thể gửi yêu cầu kết bạn, đã có lỗi xảy ra!";
                }

                return "Gửi lời mời kết bạn thành công";
            }
            catch (PDOException $ex) {
                throw new PDOException($ex->getMessage());
            }
        }

        //chấp nhận lời mời kết bạn
        public function acceptFriend($user_A, $user_B){
            //A chấp nhận lời mời kết bạn với B
            //kiểm tra dữ liệu rỗng
            if(empty($user_A) || empty($user_B)){
                return "Tài khoản rỗng!";
            }

            if(strcmp($user_A, $user_B) == 0){
                return "Không thể kết bạn với chính mình!";
            }

            try{
                $userA = $this->getUser($user_A);
                $userB = $this->getUser($user_B);
                if($userA['user_email'] != $user_A || $userB['user_email'] != $userB){
                    return "Không tồn tại tài khoản!";
                }

                //kiểm tra xem có tồn tại lời mời
                $followed_B = !empty($userB['user_followed']) ? unserialize($userB['user_followed']) : [];
                $follows_A = !empty($userA['user_follows']) ? unserialize($userA['user_follows']) : [];
                $followed_A = !empty($userA['user_followed']) ? unserialize($userA['user_followed']) : [];
                $following_A = !empty($userA['user_following']) ? unserialize($userA['user_following']) : [];

                $userA_id = $userA['user_id'];
                $userB_id = $userB['user_id'];

                if(in_array($userA_id, $followed_B) || in_array($userB_id, $followed_A) || !in_array($userB_id, $follows_A)){
                    return "Không thể chấp nhận lời mời kết bạn!";
                }

                //thêm id user vào các danh sách tương ứng
                array_push($followed_A, $userB_id);
                array_push($followed_B, $userA_id);
                array_push($following_A, $userB_id);

                //xóa id B khỏi danh sách lời mời kết bạn của A
                $follows_A = array_filter($follows_A, function($e) use ($userB_id){
                    return ($e !== $userB_id);
                });

                //cập nhật vào database
                $sqlUpdate_A = "UPDATE users SET user_followed = ?, user_follows = ?, user_following = ? WHERE user_email = ?";
                $sqlUpdate_B = "UPDATE users SET user_followed = ? WHERE user_email = ?";

                $data = db::$connectionstring->prepare($sqlUpdate_A);
                if(!$data->execute(array(serialize($followed_A), serialize($follows_A), serialize($following_A), $user_A))){
                    return "Không thể chấp nhận lời mời, đã có lỗi xảy ra!";
                }
                $data = db::$connectionstring->prepare($sqlUpdate_B);
                if(!$data->execute(array(serialize($followed_B), $user_B))){
                    return "Không thể chấp nhận lời mời, đã có lỗi xảy ra!";
                }

                return "Thêm bạn bè thành công";
            }
            catch (PDOException $ex) {
                throw new PDOException($ex->getMessage());
            }
        }

        //từ chối kết bạn
        public function declineFriend($user_A, $user_B){
            //A từ chối lời mời kết bạn với B
            //kiểm tra dữ liệu rỗng
            if(empty($user_A) || empty($user_B)){
                return "Tài khoản rỗng!";
            }

            if(strcmp($user_A, $user_B) == 0){
                return "Không thể gửi yêu cầu với chính mình!";
            }

            try{
                $userA = $this->getUser($user_A);
                $userB = $this->getUser($user_B);
                if($userA['user_email'] != $user_A || $userB['user_email'] != $userB){
                    return "Không tồn tại tài khoản!";
                }

                //kiểm tra xem A và B có là bạn bè
                $follows_A = !empty($userA['user_follows']) ? unserialize($userA['user_follows']) : [];
                $following_B = !empty($userB['user_following']) ? unserialize($userB['user_following']) : [];

                $userA_id = $userA['user_id'];
                $userB_id = $userB['user_id'];
                
                if(!in_array($userA_id, $following_B) || !in_array($userB_id, $follows_A)){
                    return "Không thể từ chối yêu cầu!";
                }

                //xóa id B khỏi danh sách lời mời
                $follows_A = array_filter($follows_A, function($e) use ($userB_id){
                    return ($e !== $userB_id);
                });

                $following_B = array_filter($following_B, function($e) use ($userA_id){
                    return ($e !== $userA_id);
                });

                //cập nhật lại database
                $sqlUpdate_A = "UPDATE users SET user_follows = ? WHERE user_email = ?";
                $sqlUpdate_B = "UPDATE users SET user_following = ? WHERE user_email = ?";
                
                $data = db::$connectionstring->prepare($sqlUpdate_A);
                if(!$data->execute(array(serialize($follows_A), $user_A))){
                    return "Không thể từ chối, đã có lỗi xảy ra!";
                }
                $data = db::$connectionstring->prepare($sqlUpdate_B);
                if(!$data->execute(array(serialize($following_B), $user_B))){
                    return "Không thể từ chối, đã có lỗi xảy ra!";
                }

                return "Đã từ chối lời mời";
            }
            catch (PDOException $ex) {
                throw new PDOException($ex->getMessage());
            }
        }
        
        //xóa(hủy) bạn bè
        public function deleteFriend($user_A, $user_B){
            //A xóa bạn bè với B
            //kiểm tra dữ liệu rỗng
            if(empty($user_A) || empty($user_B)){
                return "Tài khoản rỗng!";
            }

            if(strcmp($user_A, $user_B) == 0){
                return "Không thể xóa kết bạn với chính mình!";
            }

            try{
                $userA = $this->getUser($user_A);
                $userB = $this->getUser($user_B);
                if($userA['user_email'] != $user_A || $userB['user_email'] != $userB){
                    return "Không tồn tại tài khoản!";
                }

                //kiểm tra có là bạn bè
                $followed_A = !empty($userA['user_followed']) ? unserialize($userA['user_followed']) : [];
                $followed_B = !empty($userB['user_followed']) ? unserialize($userB['user_followed']) : [];
                $following_A = !empty($userA['user_following']) ? unserialize($userA['user_following']) : [];
                $following_B = !empty($userB['user_following']) ? unserialize($userB['user_following']) : [];

                $userA_id = $userA['user_id'];
                $userB_id = $userB['user_id'];

                if(!in_array($userA_id, $followed_B) || !in_array($userB_id, $followed_A)){
                    return "Không thể hủy vì chưa là bạn bè!";
                }

                //xóa khỏi id A khỏi danh sách của B và ngược lại
                $followed_A = array_filter($followed_A, function($e) use ($userB_id){
                    return ($e !== $userB_id);
                });
                $followed_B = array_filter($followed_B, function($e) use ($userA_id){
                    return ($e !== $userA_id);
                });
                $following_A = array_filter($following_A, function($e) use ($userB_id){
                    return ($e !== $userB_id);
                });
                $following_B = array_filter($following_B, function($e) use ($userA_id){
                    return ($e !== $userA_id);
                });

                //cập nhật lại database
                $sqlUpdate = "UPDATE users SET user_followed = ?, user_following = ? WHERE user_email = ?";
                $data = db::$connectionstring->prepare($sqlUpdate);
                if(!$data->execute(array(serialize($followed_A), serialize($following_A), $user_A))){
                    return "Không thể hủy, đã có lỗi xảy ra!";
                }
                $data = db::$connectionstring->prepare($sqlUpdate);
                if(!$data->execute(array(serialize($followed_B), serialize($following_B), $user_B))){
                    return "Không thể hủy, đã có lỗi xảy ra!";
                }

                return "Hủy kết bạn thành công";
            }
            catch (PDOException $ex) {
                throw new PDOException($ex->getMessage());
            }
        }

        //bỏ theo dõi
        public function unFollowing($user_A, $user_B){
            //A bỏ theo dõi B
            //kiểm tra dữ liệu rỗng
            if(empty($user_A) || empty($user_B)){
                return "Tài khoản rỗng!";
            }

            if(strcmp($user_A, $user_B) == 0){
                return "Không thể bỏ theo dõi với chính mình!";
            }

            $this->declineFriend($user_B, $user_A);
        }

        //danh sách user (giới hạn 100 user)
        public function listUsers(){
            try{
                $sqlSelect = "SELECT user_id, user_email, user_displayname, user_avatar, user_following, user_followed, user_follows, user_created FROM users LIMIT 100";
                $data = db::$connectionstring->prepare($sqlSelect);
                if($data->execute()){
                    return $data->fetchAll(PDO::FETCH_ASSOC);
                }

                return "Đã có lỗi xảy ra!";
            }
            catch (PDOException $ex) {
                throw new PDOException($ex->getMessage());
            }
        }

        //tìm user theo tên (email hoặc tên hiển thị, giới hạn 100 user)
        public function searchUsersByName($user_name){
            try{
                $sqlSelect = "SELECT * FROM users WHERE user_email LIKE ? OR user_displayname LIKE ? LIMIT 100";
                $data = db::$connectionstring->prepare($sqlSelect);
                if($data->execute(array('%' . $user_name . '%', '%' . $user_name . '%'))){
                    return $data->fetchAll(PDO::FETCH_ASSOC);
                }

                return "Đã có lỗi xảy ra!";
            }
            catch (PDOException $ex) {
                throw new PDOException($ex->getMessage());
            }
        }

        //tìm bài đăng bằng keyword
        public function searchPosts($user_email, $keyword){
            try{
                $user = $this->getUser($user_email);
                if($user['user_email'] != $user_email){
                    return "Tài khoản không tồn tại!";
                }

                $user_id = $user['user_id'];
                $following = $user['user_following'];
                $resultStatus = [];

                $status = new statusController();

                $stt = $status->statusByKeywordAndId($keyword, $user_id);
                $list_friend_id = unserialize($following);

                if($stt != null){
                    $resultStatus = array_merge($resultStatus, $stt);
                }

                if(!empty($list_friend_id)){
                    foreach($list_friend_id as $id_friend){
                        $stt = $status->statusByFriendId($keyword, $user_id, $id_friend);
                        if($stt != null){
                            $resultStatus = array_merge($resultStatus, $stt);
                        }
                    }
                }

                return $resultStatus;
            }
            catch (PDOException $ex) {
                throw new PDOException($ex->getMessage());
            }
        }
    }
?>