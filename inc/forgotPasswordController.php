<?php
    use PMPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

    require dirname(dirname(__FILE__)) . '/vendor/autoload.php';
    include_once 'autoloadClass.php';

    date_default_timezone_set('Asia/Ho_Chi_Minh');

    /*===============================
      Class control forgot password
    ===============================*/
    class forgotPasswordController{
        private $request;

        public function __construct(){
            db::connect();
        }

        //tạo token xác thực để đặt lại password
        public function generalToken($user_email){
            //tạo token 25 ký tự, phân biệt hoa thường
            $token = '';
            $stringRandom = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
            $lenStringRandom = strlen($stringRandom) - 1;
            for($char = 0; $char < 25; $char++){
                $token .= $stringRandom[rand(0, $len)];
            }

            try{
                //lưu vào bảng forgot_password
                $sql = "INSERT INTO forgot_password(forgot_password_email, forgot_password_token, forgot_password_experied) VALUES(?, ?, now() + INTERVAL 1 DAY)";
                $sqlSelect = "SELECT user_email FROM forgot_password WHERE forgot_password_email = ?";

                //kiểm tra nếu từng forgot rồi thì cập nhật lại hạn dùng và token mới
                $data = db::$connectionstring->prepare($sqlSelect);
                if($data->execute(array($user_email))){
                    $row = $data->fetch(PDO::FETCH_ASSOC);
                    if($row){
                        $sql = "UPDATE forgot_password SET forgot_password_token =?, forgot_password_experied = now() + INTERVAL 1 DAY WHERE forgot_password_email = ?";
                    }
                }

                //cập nhật token
                $data = db::$connectionstring->prepare($sql);
                if($data->execute(array($token, $user_email))){
                    return $token;
                }

                return false;
            }
            catch (PDOException $ex) {
                throw new PDOexception($ex->getMessage());
            }
        }

        //gửi token qua link để forgot password
        public function sendPasswordToEmail($user_email){
            //tạo token
            $token = $this->generalToken($user_email);
            if(!$token){
                return "Có lỗi xảy ra, vui lòng thử lại!";
            }

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
                $mail->Subject = 'Lấy lại mật khẩu';
                $mail->Body    = "Nhấn vào đường dẫn sau để lấy lại mật khẩu: <a href='http://$_SERVER[HTTP_HOST]$_SERVER[PHP_SELF]?token=$token</a>.<br/>Có hiệu lực trong 1 ngày, kể từ thời điểm nhận được email này.<br/><br/>Nếu không phải là bạn, vui lòng không thực hiện điều này và hãy đổi mật khẩu cho tài khoản của mình!";

                $mail->send();
                return "Gửi mail thành công, vui lòng kiểm tra lại mail và làm theo hướng dẫn";
            } catch (Exception $e) {
                return "Không thể gửi mail. Mailser Error: " . $mail->ErrorInfo;
            }
        }

        //xác thực token forgot password
        public function validateToken($token){
            $experied = '';
            $message = 'Token không hợp lệ hoặc đã hết hạn!';

            try{
                //kiểm tra token trong database và hạn dùng của nó
                $sqlSelect = "SELECT * FROM forgot_password WHERE forgot_password_token = ?";
                $data = db::$connectionstring->prepare($sqlSelect);
                if($data->execute(array($token))){
                    $row = $data->fetch(PDO::FETCH_ASSOC);

                    //không tồn tại token
                    if(!$row){
                        return $message;
                    }

                    //token hết hạn
                    $experied = strtotime($row['forgot_password_expried']);
                    $now = strtotime(date("Y-m-d H:i:s"));
                    if($now > $experied){
                        return $message;
                    }
                }

                return true;
            }
            catch (PDOException $ex) {
                throw new PDOexception($ex->getMessage());
            }
        }

        //đổi password
        public function changePassword($token, $password){
            $isNotExperied = $this->validateToken($token);
            if($isNotExperied !== true){
                return $isNotExperied;
            }

            try{
                $sqlSelect = "SELECT * FROM forgot_password WHERE forgot_password_token = ?";
                $data = db::$connectionstring->prepare($sqlSelect);
                if($data->execute(array($token))){
                    $row = $data->fetch(PDO::FETCH_ASSOC);
                    $user_email = $row['forgot_password_email'];
                }

                if(!isset($user_email)){
                    return "Token không hợp lệ!";
                }

                //hashpass
                $passwordHash = password_hash($password, PASSWORD_DEFAULT);

                $sqlUpdate = "UPDATE users SET user_password = ? WHERE user_email = ?";
                $data = db::$connectionstring->prepare($sqlUpdate);
                if($data->execute(array($passwordHash, $user_email))){
                    return "Đổi mật khẩu thành công, mật khẩu mới của bạn là: $password";
                }

                return "Có lỗi xảy ra, vui lòng thử lại!";
            }
            catch (PDOException $ex) {
                throw new PDOexception($ex->getMessage());
            }
        }
    }
?>