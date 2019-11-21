<?php
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\SMTP;
    use PHPMailer\PHPMailer\Exception;

    require_once 'vendor/autoload.php';

    function detectPage(){
        $script_name = $_SERVER['SCRIPT_NAME'];
        $parts = explode('/', $script_name);
        $fileName = $parts[4];
        $parts = explode('.', $fileName);
        $page = $parts[0];

        return $page;
    }

    function findUserByEmail($email){
        global $db;
        $stmt = $db->prepare("SELECT * FROM users WHERE User_email = ?");
        $stmt->execute(array($email));

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    function findUserById($id){
        global $db;
        $stmt = $db->prepare("SELECT * FROM users WHERE User_id = ?");
        $stmt->execute(array($id));

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    function resizeImage($filename, $max_width, $max_height){

        list($orig_width, $orig_height) = getimagesize($filename);

        $width = $orig_width;
        $height = $orig_height;

        # taller
        if ($height > $max_height) {
            $width = ($max_height / $height) * $width;
            $height = $max_height;
        }

        # wider
        if ($width > $max_width) {
            $height = ($max_width / $width) * $height;
            $width = $max_width;
        }

        $image_p = imagecreatetruecolor($width, $height);

        $image = imagecreatefromjpeg($filename);

        imagecopyresampled($image_p, $image, 0, 0, 0, 0, $width, $height, $orig_width, $orig_height);

        return $image_p;
    }  

    function createUser($name, $email, $password){
        global $db;

        //đọc file dạng binary
        $avatar_file = '../../public/image/default-avatar.jpg';
        $fb = fopen($avatar_file, "rb");
        $avatar = fread($fb, filesize($avatar_file));
        fclose($fb);

        $code = generateRandomString(16);
        $hashPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $db->prepare("INSERT INTO users (User_displayName, User_email, User_password, User_avatar, User_status) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute(array($name, $email, $hashPassword, $avatar, $code));

        //lấy id user vừa đăng ký
        $newUserId = $db->lastInsertId();

        sendEmail($email, $name, 'Kích hoạt tài khoản', "Mã kích hoạt của bạn là: $code");
        return $newUserId;
    }

    function changePassword($password, $userid){
        global $db;

        $hashPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $db->prepare("UPDATE users SET User_password = ? WHERE User_id = ?");
        $stmt->execute(array($hashPassword,  $userid));
    }

    function generateRandomString($length) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    function sendEmail($to_email, $to_name, $subject, $content){
        // Instantiation and passing `true` enables exceptions
        $mail = new PHPMailer(true);

        //Server settings
        //$mail->SMTPDebug = SMTP::DEBUG_SERVER;                      // Enable verbose debug output
        $mail->isSMTP();  
        $mail->CharSet = 'UTF-8';                                        // Send using SMTP
        $mail->Host       = 'smtp.gmail.com';                    // Set the SMTP server to send through
        $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
        $mail->Username   = 'testwebltt@gmail.com';                     // SMTP username
        $mail->Password   = 'testwebltt97@';                               // SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` also accepted
        $mail->Port       = 587;                                    // TCP port to connect to

        //Recipients
        //email gửi
        $mail->setFrom('testwebltt@gmail.com', 'LT - Web 1');
        //email nhận
        $mail->addAddress($to_email, $to_name);     // Add a recipient

        // Content
        $mail->isHTML(true);                                  // Set email format to HTML
        $mail->Subject = $subject;
        $mail->Body    = $content;
        $mail->AltBody = $content;

        $mail->send();
        echo 'Message has been sent';
    }

    function acceptUser($code, $id){
        global $db;
        $stmt = $db->prepare("SELECT * FROM users WHERE User_id = ?");
        $stmt->execute(array($id));
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && $user['User_status'] == $code){
            $stmt = $db->prepare("UPDATE users SET User_status = ? WHERE User_id = ?");
            $stmt->execute(array('accept', $id));

            return true;
        }

        return false;
    }

    function forgotPassword($email){
        global $db;
        $stmt = $db->prepare("SELECT * FROM users WHERE User_email = ?");
        $stmt->execute(array($email));
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user){
            $pass_new = generateRandomString(16);
            $hashPassword = password_hash($pass_new, PASSWORD_DEFAULT);

            $stmt = $db->prepare("UPDATE users SET User_password = ? WHERE User_email = ?");
            $stmt->execute(array($hashPassword, $email));

            sendEmail($email, $user['User_displayName'], 'Khôi phục mật khẩu', "Mật khẩu mới của bạn là: $pass_new");

            return true;
        }

        return false;
    }

    function getAllPosts($id){
        global $db;
        $stmt = $db->prepare("SELECT * FROM posts WHERE Post_user = ?");
        $stmt->execute(array($id));

        return $stmt;
    }
?>