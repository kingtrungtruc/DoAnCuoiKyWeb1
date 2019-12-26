<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

//autoloader
require dirname(dirname(__FILE__)) .'/vendor/autoload.php';
include_once 'autoload.php';

date_default_timezone_set('Asia/Ho_Chi_Minh');

/*
 * Class control Message
 */
class MessageController{
    private $request;
    
    public function __construct()
    {
        db::connect();
    }

    public function GetAllMessage($user_id, $user_id_from){
        try{
            $sqlSelect = "SELECT * FROM message WHERE (message_user_id = ? and message_from_user_id = ?) or (message_user_id = ? and message_from_user_id = ?) ORDER BY message_created";
            $data = db::$connection->prepare($sqlSelect);
            if($data->execute([$user_id, $user_id_from, $user_id_from, $user_id])){
                return $data->fetchAll(PDO::FETCH_ASSOC);
            }
            
            return null;
        }
        catch(PDOException $ex){
            throw new PDOException($ex->getMessage());
        }
    }

    public function GetIdMessageFalse($user_id, $user_id_from){
        try{
            $row_id = null;
            $sqlSelect = "SELECT * FROM message WHERE (message_user_id = ? and message_from_user_id = ? and message_seen = ?) or (message_user_id = ? and message_from_user_id = ? and message_seen = ?) ORDER BY message_created";
            $data = db::$connection->prepare($sqlSelect);
            if($data->execute([$user_id, $user_id_from, 0, $user_id_from, $user_id, 0])){
                $rows = $data->fetchAll(PDO::FETCH_ASSOC);                
                foreach($rows as $row){
                    $row_id = $row['message_id'];
                    break;
                }
            }
            
            return $row_id;
        }
        catch(PDOException $ex){
            throw new PDOException($ex->getMessage());
        }
    }

    public function LastMessage($user_id, $user_id_from){
        try{
            $messagefalse = $this->GetIdMessageFalse($user_id, $user_id_from);
            if($messagefalse != null){
                $sqlUpdate = "UPDATE message SET message_seen = ? WHERE message_id = ?";
                $data = db::$connection->prepare($sqlUpdate);
                if($data->execute([1, $messagefalse])){
                    return 1;
                }
            }
            return 0;
        }
        catch(PDOException $ex){
            throw new PDOException($ex->getMessage());
        }
    }

    //gửi email khi tin nhắn
    public function SendEmailNewMessage($email,$id,$content)
    {
        $user = new UserController();
        $user_email = $user->GetUser('', $id);
        $name = $user_email['user_displayname'];
        $mail = new PHPMailer(true);
        try {
            //Server settings
            $mail->isSMTP();                                      // Set mailer to use SMTP
            $mail->CharSet = 'UTF-8';
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
            $mail->Subject = 'Có tin nhắn mới';
            $mail->Body    = "$name vừa gửi cho bạn một tin nhắn: $content <br/>Nhấp vào link để đăng nhập và kiểm tra tin nhắn: <a href='http://$_SERVER[HTTP_HOST]/Web1/LT/DoAnCuoiKyWeb1/login.php'>http://$_SERVER[HTTP_HOST]/Web1/LT/DoAnCuoiKyWeb1/login.php</a>";

            $mail->send();
            return 1;
        } catch (Exception $e) {
            return 'Không thể gửi mail. Mailser Error: '. $mail->ErrorInfo;
        }
    }

    public function AddMessage($user_id, $user_id_from, $content){
        try{
            $this->LastMessage($user_id, $user_id_from);
            $user = new UserController();
            $user_email = $user->GetUser('', $user_id_from);
            $user_email_from = $user_email['user_email'];

            $sqlSelect = "INSERT INTO message(message_user_id, message_from_user_id, message_content, message_seen, message_created) VALUES (?, ?, ?, ?, now())";
            $data = db::$connection->prepare($sqlSelect);
            if($data->execute([$user_id, $user_id_from, $content, 0])){
                $this->SendEmailNewMessage($user_email_from, $user_id, $content);
                return db::$connection->lastInsertId();
            }

            return "Có lỗi xảy ra";
        }
        catch(PDOException $ex){
            throw new PDOException($ex->getMessage());
        }
    }
}