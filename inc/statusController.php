<?php
    include_once 'autoloadClass.php';

    date_default_timezone_set('Asia/Ho_Chi_Minh');

    /*===============================
        Class control status
    ===============================*/
    class statusController{
        protected $request;

        public function __construct(){
            db::connect();
        }

        //tạo status mới
        public function newStatus($status_user_id, ...$mang){
            $this->request = $mang[0];

            $content = htmlspecialchars($this->request['status_content']);

            if(isset($this->request['status_image'])){
                //tải lên và lấy đường dẫn của file ảnh
                $token = '';
                $stringRandom = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
                $lenStringRandom = strlen($stringRandom) - 1;
                //tạo chuỗi token random có 20 ký tự
                for($char = 0; $char < 20; $char++){
                    $token .= $stringRandom[rand(0, $len)];
                }

                $file_name = $this->request['status_image']['name'];
                //strrchr tìm kiếm vị trí cuối cùng của kí tự xuất hiện trong chuỗi nguồn, không có trả về false
                $ext = strrchr($file_name, '.');

                $target_path_local = __DIR__ . '/upload/' . $status_user_id . $token . $ext;

                $target_path_db = 'inc/upload/' . $status_user_id . $token . $ext;

                move_uploaded_file($this->request['status_image']['tmp_name'], $target_path_local);
            }
            else{
                $target_path_db = '';
            }

            try{
                $sqlInsert = "INSERT INTO status(status_user_id, status_content, status_role, status_image, status_created) VALUES(?, ?, ?, ?, now())";
                $data = db::$connectionstring->prepare($sqlInsert);
                if($data->execute(array($status_user_id, $content, $this->request['status_role'], $target_path_db))){
                    return db::$connectionstring->lastInsertId();
                }

                return 0;
            }
            catch (PDOException $ex) {
                throw new PDOexception($ex->getMessage());
            }
        }

        //tìm status theo id, giới hạn 10 status
        public function statusById($status_user_id, $limit = 10){
            try{
                $sqlSelect = "SELECT * FROM status WHERE status_user_id = ? ORDER BY status_created DESC LIMIT ?";
                $data = db::$connectionstring->prepare($sqlSelect);
                if($data->execute(array($status_user_id, $limit))){      
                    return $data->fetchAll(PDO::FETCH_ASSOC);
                }

                return null;
            }
            catch (PDOException $ex) {
                throw new PDOexception($ex->getMessage());
            }
        }

        //tìm status theo keyword và id của user
        public function statusByKeywordAndId($keyword, $user_id){
            try{
                $sqlSelect = "SELECT * FROM status WHERE status_content LIKE ? AND (status_user_id = ? OR status_role = 'Công khai') ORDER BY status_created DESC";
                $data = db::$connectionstring->prepare($sqlSelect);
                if($data->execute(array('%' . $keyword . '%', $user_id))){
                    return $data->fetchALL(PDO::FETCH_ASSOC);
                }

                return null;
            }
            catch (PDOException $ex) {
                throw new PDOexception($ex->getMessage());
            }
        }

        //tìm status bằng keyword và id của bạn bè
        public function statusByFriendId($keyword, $user_id, $friend_id){
            try{
                $sqlSelect = "SELECT * FROM status as stt, users as u WHERE stt.status_content LIKE ? AND (stt.status_id = u.user_id AND u.user_id = ? AND u.user_followed LIKE ? AND stt.status_role IN ('Bạn bè')) ORDER BY stt.status_created";
                $data = db::$connectionstring->prepare($sqlSelect);
                if($data->execute(array('%' . $keyword . '%', $friend_id, '%' . $user_id . '%'))){
                    return $data->fetchAll(PDO::FETCH_ASSOC);
                }

                return null;
            }
            catch (PDOException $ex) {
                throw new PDOexception($ex->getMessage());
            }
        }

        //lấy status random, giới hạn 10 status
        public function statusRandom(){
            try{
                $sqlSelect = "SELECT DISTINCT(status_user_id), status_content, status_created, status_id, status_role FROM status WHERE status_role = 'Công khai' ORDER BY status_created LIMIT 10";
                $data = db::$connectionstring->prepare($sqlSelect);
                if($data->execute()){
                    return $data->fetchAll(PDO::FETCH_ASSOC);
                }

                return null;
            }
            catch (PDOException $ex) {
                throw new PDOexception($ex->getMessage());
            }
        }

        //lấy status của user2 bất kỳ khi user1 vào xem tường của user2
        public function statusPublic($user_id_2){
            try{
                $sqlSelect = "SELECT * FROM status WHERE status_user_id = ? AND status_role = 'Công khai' ORDER BY status_created DESC";
                $data = db::$connectionstring->prepare($sqlSelect);
                if($data->execute(array($user_id_2))){
                    return $data->fetchAll(PDO::FETCH_ASSOC);
                }

                return null;
            }
            catch (PDOException $ex) {
                throw new PDOexception($ex->getMessage());
            }
        }

        //lấy status của user2 là bạn khi user1 vào xem tường của user2
        public function statusFriend($user_id_2){
            try{
                $sqlSelect = "SELECT * FROM status WHERE status_user_id = ? AND status_role = 'Bạn bè' ORDER BY status_created DESC";
                $data = db::$connectionstring->prepare($sqlSelect);
                if($data->execute(array($user_id_2))){
                    return $data->fetchAll(PDO::FETCH_ASSOC);
                }

                return null;
            }
            catch (PDOException $ex) {
                throw new PDOexception($ex->getMessage());
            }
        }

        //user1 vào xem tường của user2
        public function showStatusWithRelationship($user_id_1, $user_id_2){
            try{
                if($user_id_1 == $user_id_2){
                    return $this->statusById($user_id_1);
                }

                $user = new userController();
                $user_1 = $user->getUser('', $user_id_1);
                if($user_1['user_id'] != $user_id_1){
                    /*test
                    echo "Id $user_id_1: ";
                    var_dump($user_id_1);
                    echo "Id $user_id_1['user_id']";
                    var_dump($user_id_1['user_id']);
                    die();*/
                    return "Id không tồn tại!";
                }
                $user_2 = $user->getUser('', $user_id_2);
                if($user_2['user_id'] != $user_id_2){
                    return "Id không tồn tại!";
                }

                $arrayStatus = [];
                $statusPublic = $this->statusPublic($user_id_2);
                if($statusPublic != null){
                    $arrayStatus = array_merge($arrayStatus, $statusPublic);
                }

                //kiểm tra user1 và user2 có phải bạn bè
                $followed_1 = !empty($user_1['user_followed']) ? unserialize($user_1['user_followed']) : [];
                $followed_2 = !empty($user_2['user_followed']) ? unserialize($user_2['user_followed']) : [];

                if(in_array($user_id_1, $followed_2) && in_array($user_id_2, $followed_1)){
                    $statusFriend = $this->statusFriend($user_id_2);
                    if($statusFriend != null){
                        $arrayStatus = array_merge($arrayStatus, $statusFriend);
                    }
                }

                return $arrayStatus;
            }
            catch (PDOException $ex) {
                throw new PDOexception($ex->getMessage());
            }
        }

        //thêm những người đã like status
        public function addWhoLiked($row, $user_id, $status_id){
            try{
                if(empty($row['status_wholiked'])){
                    $arr = [];
                    array_push($arr, $user_id);
                    $convert = serialize($arr);
                    $sqlUpdate = "UPDATE status SET status_wholiked = ? WHERE status_id = ?";
                    $data = db::$connectionstring->prepare($sqlUpdate);
                    if($data->execute(array($convert, $status_id))){
                        return "Thêm người like thành công";
                    }

                    return "Thêm người like thất bại!";
                }

                $wholiked = unserialize($row['status_wholiked']);
                if(in_array($user_id, $wholiked, true)){
                    return "Đã like rồi!";
                }

                array_push($wholiked, $user_id);
                $convert = serialize($wholiked);
                $sqlUpdate = "UPDATE status SET status_wholiked = ? WHERE status_id = ?";
                $data = db::$connectionstring->prepare($sqlUpdate);
                if($data->execute(array($convert, $status_id))){
                    return "Thêm người like thành công";
                }

                return "Thêm người like thất bại!";
            }
            catch (PDOException $ex) {
                throw new PDOexception($ex->getMessage());
            }
        }

        //like cho status nào
        public function likeForStatus($user_id, $status_id){
            try{
                $sqlSelect = "SELECT * FROM status WHERE status_id = ?";
                $data = db::$connectionstring->prepare($sqlSelect);
                if($data->execute(array($status_id))){
                    $row = $data->fetch(PDO::FETCH_ASSOC);
                    return $this->addWhoLiked($row, $user_id, $status_id);
                }

                return "Không thể like!";
            }
            catch (PDOException $ex) {
                throw new PDOexception($ex->getMessage());
            }
        }

        //xóa like trong database
        public function removeWhoLiked($row, $user_id, $status_id){
            try{
                if(empty($row['status_wholiked'])){
                    return "Xóa thành công";
                }

                $wholiked = unserialize($row['status_wholiked']);
                foreach($wholiked as $key => $value){
                    if($value == $user_id){
                        unset($wholiked[$key]);
                    }
                }

                $convert = serialize($wholiked);
                $sqlUpdate = "UPDATE status SET status_wholiked = ? WHERE status_id = ?";
                $data = db::$connectionstring->prepare($sqlUpdate);
                if($data->execute(array($convert, $status_id))){
                    return "Xóa like thành công";
                }

                return "Xóa like thất bại!";
            }
            catch (PDOException $ex) {
                throw new PDOexception($ex->getMessage());
            }
        }

        //bỏ like status
        public function unLikeForStatus($user_id, $status_id){
            try{
                $sqlSelect = "SELECT * FROM status WHERE status_id = ?";
                $data = db::$connectionstring->prepare($sqlSelect);
                if($data->execute(array($status_id))){
                    $row = $data->fetch(PDO::FETCH_ASSOC);
                    return $this->removeWhoLiked($row, $user_id, $status_id);
                }

                return "Bỏ like thất bại!";
            }
            catch (PDOException $ex) {
                throw new PDOexception($ex->getMessage());
            }
        }

        //số lượng like cho 1 status
        public function amountOfLiked($status_id){
            try{
                $sqlSelect = "SELECT * FROM status WHERE status_id = ?";
                $data = db::$connectionstring->prepare($sqlSelect);
                if($data->execute(array($status_id))){
                    $row = $data->fetch(PDO::FETCH_ASSOC);
                    if(empty($row['status_wholiked'])){
                        return 0;
                    }

                    $arr = unserialize($row['status_wholiked']);
                    $count = count($arr);

                    return $count;
                }

                return 0;
            }
            catch (PDOException $ex) {
                throw new PDOexception($ex->getMessage());
            }
        }

        //kiểm tra đã like hay chưa
        public function isLiked($user_id, $status_id){
            try{
                $sqlSelect = "SELECT * FROM status WHERE status_id = ?";
                $data = db::$connectionstring->prepare($sqlSelect);
                if($data->execute(array($status_id))){
                    $row = $data->fetch(PDO::FETCH_ASSOC);

                    if(!empty($row['status_wholiked'])){
                        $wholiked = unserialize($row['status_wholiked']);
                        foreach($wholiked as $key => $value){
                            if($value == $user_id){
                                return true;
                            }
                        }
                    }

                    return false;
                }

                return false;
            }
            catch (PDOException $ex) {
                throw new PDOexception($ex->getMessage());
            }
        }
    }
?>