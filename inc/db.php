<?php

class db
{
    /*
     *
     * Nếu xây dựng các query trong class db thì chuyển $connection vể private
     * Hiện tại để public là để các class khác có thể truy cập và thực thi
     * query từ các class đó.
     *
     * */
    private static $option;
    public static $connection;

    public static function connect()
    { 
        $host = 'localhost';
        $dbname = 'doan_web1';
        $username = 'root';
        $password = '';

        self::$option = [
            //set chế độ xử lý lỗi
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            //set kiểu dữ liệu trả về mặc định là FETCH_ASSOC -> trả dữ liệu về dạng mảng với key là tên của cột
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            //tắt chế độ emulate của PDO
            PDO::ATTR_EMULATE_PREPARES => false
        ];

        try{
            if (!isset(self::$connection)){
                self::$connection = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password, self::$option);
            }
        } catch(PDOException $ex){
            //throw để lớp kế thừa có thể gọi được
            throw new PDOException($ex->getMessage());
        }
    }
}
?>

