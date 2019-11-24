<?php
    //nạp namespace với use
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

    //require Khi được cung cấp một chuỗi chứa path tới file đã cho, hàm này sẽ trả về tên của thư mục
    require dirname(dirname(__FILE__)) . '/vendor/autoload.php';
    //include autoload.php trong vendor để gửi mail
    include_once 'autoload.php';

    //đặt giá trị timezone mặc định cho hệ thống, tất cả các hàm về xử lí thời gian sẽ sử dụng timezone này
    date_default_timezone_set('Asia/Ho_Chi_Minh');

    //class control User
    class userController{
        private $request;

        //kết nối db
        public function __construct(){
            //cách gọi hàm static <class name>::<tên hàm()>
            db::connect();
        }

        private function setCookie($username, $realname, $remember = "on"){
            if($remember == 'on'){
                $time = 3600 * 24; //24 giờ
            } else{
                $time = 60*10; //10 phút
            }

            //lưu cookie với setcookie(tên cookie, giá trị cookie, time() + thời gian lưu, <đường dẫn lưu trữ>, <tên của domain>)
            setcookie('login', $username, time() + $time);
            setcookie('realname', $realname, time() + $time);

            return 1;
        }

        //hàm trả về user theo id và username
        public function getUser($username = '', $id = ''){
            //kiểm tra giá trị có hợp lệ không
            if(((int)$id < 1 || empty($id)) && empty($username)){
                return "Tài khoản không hợp lệ!";
            }

            try{
                //truy vấn theo id và username
                $sqlSelect = "SELECT * FROM users WHERE id = ? OR username = ?";

                //dùng biến static của class <class name>::<tên biến>
                $data = db::$connectionstring->prepare($sqlSelect);

                //nếu truy vấn được thì trả về user
                if($data->execute([$id, $username])){
                    return $data->fetch(PDO::FETCH_ASSOC);
                }
                //mặc định sẽ có lỗi
                return "Có lỗi xảy ra!";
            } catch(PDOException $ex){
                //throw để lớp kế thừa có thể gọi được
                throw new PDOException($ex->getMessage());
            }
        }

        //... : truyền vào mảng đối số mà chưa biết số lượng
        public function login(...$mang){
            //set giá trị cho biến $request
            $this->request = $mang[0];

            //kiểm tra giá trị có hợp lệ không
            if(empty($this->request['username']) || empty($this->request['password'])){
                return "Tên đăng nhập và Mật khẩu không được để trống!";
            }

            //kiểm tra đầu vào username (email) có đúng định dạng hay không
            /*kiểm tra so khớp chuỗi preg_match ( $pattern , $subject, <&$matches>), trong đó:
                $pattern là biểu thức Regular Expression
                $subject là chuỗi cần kiểm tra
                $matches là kết quả trả về, đây là một tham số truyền vào ở dạng tham chiếu*/
            if(!preg_match('/^[0-9a-zA-Z._]+\@[a-zA-Z]+\..*$/', $this->request['username'])){
                return "Tên đăng nhập phải đúng định dạng email!";
            }

            try{
                $userSelect = $this->getUser($this->request['username']);
                //so khớp tài khoản
                if($userSelect['username'] != $this->request['username']){
                    return "Không khớp tài khoản!";
                }

                //so khớp password
                if(!password_verify($this->request['password'], $userSelect['password'])){
                    return "Không khớp mật khẩu!";
                }
                
                //cập nhật thời gian đăng nhập lần cuối
                $sqlUpdate = "UPDATE users SET last_login = now() WHERE username = ?";
                //dùng biến static của class <class name>::<tên biến>
                $data = db::$connectionstring->prepare($sqlUpdate);
                //nếu update được thì trả về 1 
                if($data->execute([$userSelect['username']])){
                    //gọi hàm setCookie bên trên để đặt lại thời gian cho cookie
                    if($this->setCookie($userSelect['username'], $userSelect['realname'])){
                        return 1;
                    }
                }
                //mặc định trả về thất bại
                return "Đăng nhập thất bại!";
            } catch(PDOException $ex){
                //throw để lớp kế thừa có thể gọi được
                throw new PDOException($ex->getMessage());
            }
        }

        //gửi email : token là vái verify với database
        public function sendEmail($email, $token){
            $mail = new PHPMailer(true);
            try{
                //Server settings
                $mail->isSMTP(); // Set mailer to use SMTP
                $mail->Host = 'smtp.gmail.com'; // Specify main and backup SMTP servers
                $mail->SMTPAuth = true; // Enable SMTP authentication
                $mail->Username = 'testwebltt@gmail.com'; // SMTP username
                $mail->Password = 'testwebltt97@'; // SMTP password
                $mail->SMTPSecure = 'tls'; // Enable TLS encryption, `ssl` also accepted
                $mail->Port = 587; // TCP port to connect to

                //Recipients
                $mail->setFrom('testwebltt@gmail.com', 'Web 1');
                $mail->addAddress($email); //
                //Content
                $mail->isHTML(true);
                $mail->Subject = 'Confirm Register account from website';
                //định dạng đường dẫn
                $mail->Body    = "Nhấn vào đường dẫn sau để xác nhận đăng ký tài khoản: <a href='http://$_SERVER[HTTP_HOST]/confirm.php?email=$email&token=$token'>http://$_SERVER[HTTP_HOST]/confirm.php?email=$email&token=$token</a>";

                $mail->send();
                return 1;
            } catch(Exception $e){
                return "Không thể gửi mail. Mailser Error: " . $mail->ErrorInfo;
            }
        }

        //đăng ký
        public function register(...$mang){
            $this->request = $mang[0];

            //kiểm tra xem email có trống không
            if(empty($this->request['username'])){
                return "Email không được để trống!";
            }

            //kiểm tra xem tên người dùng có trống không
            if(empty($this->request['realname'])){
                return "Tên người dùng không được để trống!";
            }

            //kiểm tra xem password có trống không
            if(empty($this->request['password'])){
                return "Password không được để trống!";
            }

            //kiểm tra xem password nhập lại có trống không
            if(empty($this->request['re-password'])){
                return "Nhập lại password không được để trống!";
            }

            //kiểm tra đầu vào username (email) có đúng định dạng hay không
            if(!preg_match('/^[0-9a-zA-Z._]+\@[a-zA-Z]+\..*$/', $this->request['username'])){
                return "Tên đăng nhập phải đúng định dạng email!";
            }

            //so sánh password và re-password có phân biệt hoa thường
            if(strcmp($this->request['password'], $this->request['re-password']) != 0){
                return "Mật khẩu không khớp!";
            }

            //kiểm tra độ dài của mật khẩu
            if(strlen($this->request['password']) < 8){
                return "Mật khẩu phải có độ dài tối thiểu là 8 ký tự!";
            }

            try{
                $userSelect = $this->getUser($this->request['username']);

                //kiểm tra xem email nhập vào có trong database chưa
                if($userSelect['username'] == $this->request['username']){
                    return "Email đã tồn tại!";
                }

                //tạo chuỗi token ngẫu nhiên từ chuỗi prepare để xác thực đăng nhập
                $token = '';
                $prepare = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
                $len = strlen($prepare) - 1;
                for($c = 0; $c < 25; $c++){
                    $token .= $prepare[rand(0, $len)];
                }
                
                //hash password
                $passwordHash = password_hash($this->request['password'], PASSWORD_DEFAULT);

                $strInsert = "INSERT INTO register (username, password, token, realname) VALUES(?, ?, ?, ?)";
                //dùng biến static của class <class name>::<tên biến>
                $data = db::$connectionstring->prepare($strInsert);

                //nếu execute thành công thì gửi mail
                if($data->execute(array($this->request['username'], $passwordHash, $token, $this->request['realname']))){
                    return $this->sendEmail($this->request['username'], $token);
                }
            } catch(PDOException $ex){
                //throw để lớp kế thừa có thể gọi được
                throw new PDOException($ex->getMessage());
            }
        }
    }
?>