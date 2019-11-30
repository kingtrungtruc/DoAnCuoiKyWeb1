<?php
    include_once 'autoloadClass.php';

    //đặt giá trị timezone mặc định cho hệ thống, tất cả các hàm về xử lí thời gian sẽ sử dụng timezone này
    date_default_timezone_set('Asia/Ho_Chi_Minh');

    /*===============================
        Class control status
    ===============================*/
    class statusController{
        protected $request;

        //kết nối db
        public function __construct(){
            //cách gọi hàm static <class name>::<tên hàm()>
            db::connect();
        }

        //tạo bài đăng mới
        public function newStatus($id_user, ...$mang){
            $this->request = $mang[0];

            //chuyển đổi ký tự thành thực thể html
            $content = htmlspecialchars($this->request['content']);

            if(isset($this->request['image'])){
                //tải lên và lấy đường dẫn của file ảnh
                $token = '';
                $prepare = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
                $len = strlen($prepare) - 1;

                //tạo chuỗi token ngẫu nhiên từ dãy prepare
                for($c = 0; $c < 20; $c++){
                    $token .= $prepare[rand(0, $len)];
                }

                $file_name = $this->request['image']['name'];

                //Hàm strrchr trả về chuỗi con bắt đầu tự vị trí tìm thấy kí tự đến vị trí cuối chuỗi nguồn. Nếu không tìm thấy hàm trả về FALSE.
                $ext = strrchr($file_name,'.');

                $target_path_local = __DIR__ . "/upload/" . $id_user . $token . $ext;
                $target_path_db = "inc/upload/" . $id_user . $token . $ext;

                move_uploaded_file($this->request['image']['tmp_name'], $target_path_local);
            }
            else{
                $target_path_db = '';
            }

            try{
                //lưu vào database
                $sqlInsert = "INSERT INTO status(id_user, content, role, image, created) VALUES(?, ?, ?, ?, now())";
                $data = db::$connectionstring->prepare($sqlInsert);
                if($data->execute(array($id_user, $content, $this->request['role'], $target_path_db))){
                    
                    //trả về id của bài đăng cuối cùng
                    return db::$connectionstring->lastInsertId();
                }

                //mặc định trả về 0
                return 0;
            }
            catch(PDOException $ex){
                throw new PDOException($ex->getMessage());
            }
        }

        //lấy bài đăng của id_user, tối đa bằng limit
        public function statusById($id_user, $limit = 10){
            try{
                $sqlSelect = "SELECT * FROM status WHERE id_user = ? ORDER BY created DESC LIMIT ?";
                $data = db::$connectionstring->prepare($sqlSelect);
                if($data->execute(array($id_user, $limit))){
                    //trả về toàn bộ bản ghi lấy được
                    $row = $data->fetchAll(PDO::FETCH_ASSOC);
                    return $row;
                }

                //mặc định trả về null
                return null;
            }
            catch(PDOException $ex){
                throw new PDOException($ex->getMessage());
            }
        }

        //lấy bài đăng bằng keyword và id
        public function statusByKeyWordAndId($keyword, $id){
            try{
                $sqlSelect = "SELECT * FROM status WHERE status.content LIKE ? AND (status.id_user = ? OR status.role = 'Công khai') ORDER BY status.created DESC";
                $data = db::$connectionstring->prepare($sqlSelect);

                //truyền chuỗi keyword có dấu % trước sau để so sánh LIKE
                if($data->execute(array('%' . $keyword . '%'. $id))){

                    //trả về toàn bộ bản ghi tìm được
                    $row = $data->fetchAll(PDO::FETCH_ASSOC);
                    return $row;
                }

                //mặc định trả về null
                return null;
            }
            catch(PDOException $ex){
                throw new PDOException($ex->getMessage());
            }
        }

        //lấy bài đăng của bạn bè qua id bạn bè
        public function statusByFriendId($keyword, $userId, $friendId){
            try{
                $sqlSelect = "SELECT * FROM status, users WHERE status.content LIKE ? AND (status.id_user = users.id AND users.id = ? AND users.followed LIKE ? AND status.role IN ('Bạn bè')) ORDER BY status.created";
                $data = db::$connectionstring->prepare($sqlSelect);
                if($data->execute(array('%' . $keyword . '%', $friendId, '%' . $userId . '%'))){

                    //trả về toàn bộ bản ghi tìm được
                    $row = $data->fetchAll(PDO::FETCH_ASSOC);
                    return $row;
                }

                //mặc định trả về null
                return null;
            }
            catch(PDOException $ex){
                throw new PDOException($ex->getMessage());
            }
        }

        //bài đăng ngẫu nhiên
        public function statusRandom(){
            try{
                $sqlSelect = "SELECT DISTINCT(id_user), content, created, id, role FROM status WHERE role = 'Công khai' ORDER BY created LIMIT 10";
                $data = db::$connectionstring->prepare($sqlSelect);
                if($data->execute()){

                    //trả về toàn bộ bản ghi tìm được
                    $row = $data->fetchAll(PDO::FETCH_ASSOC);
                    return $row;
                }

                //mặc định trả về null
                return null;
            }
            catch(PDOException $ex){
                throw new PDOException($ex->getMessage());
            }
        }

        //tương tác giữa user và userfriend
        //user và xem trang cá nhân của userfriend, lấy các bài đăng công khai của userfriend chế độ công khai
        public function statusPublic($friendId){
            try{
                $sqlSelect = "SELECT * FROM status WHERE id_user = ? AND role = 'Công khai' ORDER BY created DESC";
                $data = db::$connectionstring->prepare($sqlSelect);
                if($data->execute(array($friendId))){
                    
                    //trả về toàn bộ bản ghi tìm được
                    $row = $data->fetchAll(PDO::FETCH_ASSOC);
                    return $row;
                }

                //mặc định trả về null;
                return null;
            }
            catch(PDOException $ex){
                throw new PDOException($ex->getMessage());
            }
        }

        //user và xem trang cá nhân của userfriend, lấy các bài đăng công khai của userfriend chế độ bạn bè
        public function statusFriend($friendId){
            try{
                $sqlSelect = "SELECT * FROM status WHERE id_user = ? AND role = 'Bạn bè' ORDER BY created DESC";
                $data = db::$connectionstring->prepare($sqlSelect);
                if($data->execute(array($friendId))){
                    
                    //trả về toàn bộ bản ghi tìm được
                    $row = $data->fetchAll(PDO::FETCH_ASSOC);
                    return $row;
                }

                //mặc định trả về null;
                return null;
            }
            catch(PDOException $ex){
                throw new PDOException($ex->getMessage());
            }
        }

        //hiển thị bài đăng của userfriend với user đang xét
        public function statusWithRelationship($userId, $friendId){
            try{
                if($userId == $friendId){
                    
                    //trả về bài đăng của user xét qua Id
                    return $this->statusById($userId);
                }

                $user = new userController();
                $usr_user = $user->getUser('', $userId);
                if($usr_user['id'] != $userId){
                    /*check hàm
                    echo "Id user: ";
                    var_dump($userId);
                    echo "ID get from db: ";
                    var_dump($usr_user['id']);*/

                    die();
                    return "Id user không tồn tại!";
                }

                $usr_friend = $user->getUser('', $friendId);
                if($usr_friend['id'] != $friendId){
                    
                    return "Id friend không tồn tại!";
                }

                $arrayStatus = [];
                $statusPublic = $this->statusPublic($friendId);
                if($statusPublic != null){
                    //hàm array_merge để nối hai hay nhiều mảng lại thành một mảng. Nếu trong các mảng truyền vào có những phần tử có cùng khóa, phần tử của mảng cuối cùng được truyền vào sẽ được chọn để nối vào mảng kết quả.
                    $arrayStatus = array_merge($arrayStatus, $statusPublic);
                }

                //kiểm tra user và friend có phải bạn bè
                //hàm unserialize sẽ chuyển đổi chuỗi đã được hàm serialize() chuyển đổi trước đó về chuỗi ban đầu. Nếu truyền vào hàm unserialize() một chuỗi nguyên bản chưa được chuyển đổi, hàm sẽ báo lỗi.
                $followedUser = !empty($usr_user['followed']) ? unserialize($usr_user['followed']) : [];

                $followedFriend = !empty($usr_friend['followed']) ? unserialize($usr_friend['followed']) : [];

                if(in_array($userId, $followedFriend) && in_array($friendId, $followedUser)){
                    $statusFriend = $this->statusFriend($friendId);
                    $arrayStatus = array_merge($arrayStatus, $statusFriend);
                }

                //trả về mảng status lấy được
                return $arrayStatus;
            }
            catch(PDOException $ex){
                throw new PDOException($ex->getMessage());
            }
        }

        //thêm những người đã like status
        public function addWhoLike($row, $userId, $id_status){
            try{
                if(empty($row['wholiked'])){
                    $array = [];
                    array_push($array, $userId);
                    $convert = serialize($array);
                    $sqlUpdate = "UPDATE status SET wholiked = ? WHERE id = ?";
                    $data = db::$connectionstring->prepare($sqlUpdate);
                    if($data->execute(array($convert, $id_status))){
                        return "Thêm thành công";
                    }

                    return "Thêm thất bại, đã có lỗi xảy ra!";
                }

                $wholiked = unserialize($row['wholiked']);
                if(in_array($userId, $wholiked, true)){
                    return "Đã like rồi!";
                }

                array_push($wholiked, $userId);
                $convert = serialize($wholiked);
                $sqlUpdate = "UPDATE status SET wholiked = ? WHERE id = ?";
                $data = db::$connectionstring->prepare($sqlUpdate);
                if($data->execute(array($convert, $id_status))){
                    return "Thành công";
                }

                return "Thất bại!";
            }
            catch(PDOException $ex){
                throw new PDOException($ex->getMessage());
            }
        }

        //like cho status
        public function likeForStatus($userId, $id_status){
            try{
                $sqlSelect = "SELECT * FROM status WHERE id = ?";
                $data = db::$connectionstring->prepare($sqlSelect);
                if($data->execute(array($id_status))){

                    //add like này bằng hàm addWhoLike
                    $row = $data->fetch(PDO::FETCH_ASSOC);
                    return $this->addWhoLike($row, $userId, $id_status);
                }

                return "Thất bại!";
            }
            catch(PDOException $ex){
                throw new PDOException($ex->getMessage());
            }
        }

        //xóa like trong database
        public function removeWhoLiked($row, $userId, $id_status){
            try{
                if(empty($row['wholiked'])){
                    return "Thành công";
                }

                $wholiked = unserialize($row['wholiked']);
                foreach($wholiked as $key => $value){
                    if($value == $userId){
                        //loại bỏ biến key có giá trị value bằng userId khỏi mảng wholike
                        unset($wholiked[$key]);
                    }
                }

                $convert = serialize($wholiked);
                $sqlUpdate = "UPDATE status SET wholiked = ? WHERE id = ?";
                $data = db::$connectionstring->prepare($sqlUpdate);
                if($data->execute(array($convert, $id_status))){
                    return "Thành công";
                }

                return "Thất bại, đã xảy ra lỗi!";
            }
            catch(PDOException $ex){
                throw new PDOException($ex->getMessage());
            }
        }

        //bỏ like khỏi status
        public function unLikeForStatus($userId, $id_status){
            try{
                $sqlSelect = "SELECT * FROM status WHERE id = ?";
                $data = db::$connectionstring->prepare($sqlSelect);
                if($data->execute(array($id_status))){

                    //gọi hàm removeWhoLiked
                    $row = $data->fetch(PDO::FETCH_ASSOC);
                    return $this->removeWhoLiked($row, $userId, $id_status);
                }

                return "Thất bại, đã xảy ra lỗi!";
            }
            catch(PDOException $ex){
                throw new PDOException($ex->getMessage());
            }
        }

        //đếm số like cho status
        public function countOfLiked($id_status){
            try{
                $sqlSelect = "SELECT * FROM status WHERE id = ?";
                $data = db::$connectionstring->prepare($sqlSelect);
                if($data->execute(array($id_status))){
                    $row = $data->fetch(PDO::FETCH_ASSOC);
                    if(empty($row['wholiked'])){
                        return 0;
                    }

                    $array = unserialize($row['wholiked']);
                    $count = count($array);

                    return $count;
                }

                return "Có lỗi xảy ra!";
            }
            catch(PDOException $ex){
                throw new PDOException($ex->getMessage());
            }
        }

        //kiểm tra xem đã like hay chưa
        public function isLiked($userId, $id_status){
            try{
                $sqlSelect = "SELECT * FROM status WHERE id = ?";
                $data = db::$connectionstring->prepare($sqlSelect);
                if($data->execute(array($id_status))){
                    $row = $data->fetch(PDO::FETCH_ASSOC);

                    if(!empty($row['wholiked'])){
                        $wholiked = unserialize($row['wholiked']);
                        foreach($wholiked as $key => $value){
                            if($value == $userId){
                                return true;
                            }
                        }
                    }

                    return false;
                }

                return false;
            }
            catch(PDOException $ex){
                throw new PDOException($ex->getMessage());
            }
        }
    }
?>