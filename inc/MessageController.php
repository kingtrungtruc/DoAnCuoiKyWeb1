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

    public function AddMessage($user_id, $user_id_from, $content){
        try{
            $sqlSelect = "INSERT INTO message(message_user_id, message_from_user_id, message_content, message_created) VALUES (?, ?, ?, now())";
            $data = db::$connection->prepare($sqlSelect);
            if($data->execute([$user_id, $user_id_from, $content])){
                return db::$connection->lastInsertId();
            }

            return "Có lỗi xảy ra";
        }
        catch(PDOException $ex){
            throw new PDOException($ex->getMessage());
        }
    }
}