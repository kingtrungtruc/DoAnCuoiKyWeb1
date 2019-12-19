<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

//autoloader
require dirname(dirname(__FILE__)) .'/vendor/autoload.php';
include_once 'autoload.php';

date_default_timezone_set('Asia/Ho_Chi_Minh');

/**
 * Class ForgotPasswordController
 */
class ForgotPasswordController
{
    private $request;

    public function __construct()
    {
        db::connect();
    }

    public function GeneralToken($email)
    {

        // general token
        $token = '';
        $prepare = "0123456789qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM";
        $len = strlen($prepare) - 1;
        for ($c = 0; $c < 25; $c++)
        {
            $token .= $prepare[rand(0, $len)];
        }

        try{
            //lưu vào bảng forgot_password
            $sql = "INSERT INTO forgot_password(forgot_password_email, forgot_password_token, forgot_password_experied) VALUES(?, ?, now() + INTERVAL 1 DAY)";
            $sqlSelect = "SELECT forgot_password_email FROM forgot_password WHERE forgot_password_email = ?";

            //kiểm tra nếu từng forgot rồi thì cập nhật lại hạn dùng và token mới
            $data = db::$connection->prepare($sqlSelect);
            if($data->execute(array($email))){
                $row = $data->fetch(PDO::FETCH_ASSOC);
                if($row){
                    $sql = "UPDATE forgot_password SET forgot_password_token =?, forgot_password_experied = now() + INTERVAL 1 DAY WHERE forgot_password_email = ?";
                }
            }

            //cập nhật token
            $data = db::$connection->prepare($sql);
            if($data->execute(array($email, $token))){
                return $token;
            }

            return false;
        }
        catch (PDOException $ex) {
            throw new PDOexception($ex->getMessage());
        }
    }

    public function SendPasswordToEmail($email)
    {
        // general token
        $token = $this->GeneralToken($email);
        if (!$token) {
            return "Có lỗi xảy ra, vui lòng thử lại";
        }

        $mail = new PHPMailer(true);
        try {
            //Server settings
            $mail->isSMTP();
            $mail->CharSet = 'UTF-8';
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'testwebltt@gmail.com';
            $mail->Password = 'testwebltt97@';
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            //Recipients
            $mail->setFrom('testwebltt@gmail.com', 'Web 1');
            $mail->addAddress($email);

            //Content
            $mail->isHTML(true);
            $mail->Subject = 'Lấy lại mật khẩu';
            $mail->Body    = "Nhấn vào đường dẫn sau để lấy lại mật khẩu: <a href='http://$_SERVER[HTTP_HOST]$_SERVER[PHP_SELF]?token=$token'>http://$_SERVER[HTTP_HOST]$_SERVER[PHP_SELF]?token=$token</a>.\nCó hiệu lực trong 1 ngày, kể từ thời điểm nhận được email này.<br/><br/>Nếu không phải là bạn, vui lòng không thực hiện điều này và hãy đổi mật khẩu cho tài khoản của mình!";

            $mail->send();
            return 'Gửi thành công, vui lòng kiểm tra email và làm theo hướng dẫn';
        } catch (Exception $e) {
            return 'Không thể gửi mail. Mailer Error: '. $mail->ErrorInfo;
        }
    }

    public function ValidateToken($token)
    {
        $experied = '';
        $message = "Token không hợp lệ hoặc đã hết hạn";

        //query email from token in forgot_password table
        $sqlSelect = "SELECT * FROM forgot_password WHERE forgot_password_token = ?";
        $data = db::$connection->prepare($sqlSelect);
        if ($data->execute([$token])) {
            $row = $data->fetch(PDO::FETCH_ASSOC);

            // Haven't token
            if (!$row)  return $message + "";

            // token was experied
            $experied = strtotime($row['forgot_password_experied']);
            $now = strtotime(date("Y-m-d H:i:s"));
            if ($now > $experied) return $message;
        }
        return true;
    }

    public function ChangePassword($token, $password)
    {
        $isNotExperied = $this->ValidateToken($token);
        if ($isNotExperied !== true) return $isNotExperied;

        $sqlSelect = "SELECT * FROM forgot_password WHERE forgot_password_token = ?";
        $data = db::$connection->prepare($sqlSelect);
        if ($data->execute([$token])) {
            $row = $data->fetch(PDO::FETCH_ASSOC);
            $user = $row['forgot_password_email'];
        }

        if (!isset($user)) {
            return "Token không hợp lệ";
        }

        $sqlDelete = "DELETE FROM forgot_password WHERE forgot_password_email = ?";
        $data2 = db::$connection->prepare($sqlDelete);
        $data2->execute([$user]);
        // Hash password
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        $sqlUpdate = "UPDATE users SET user_password = ? WHERE user_email = ?";
        $data = db::$connection->prepare($sqlUpdate);
        if ($data->execute([$passwordHash, $user])) {
            return "Đổi mật khẩu thành công, mật khẩu mới của bạn là: $password";
        }

        
        return "Có lỗi xảy ra, vui lòng thử lại";
    }
}