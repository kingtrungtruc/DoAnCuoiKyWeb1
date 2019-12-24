<?php
include_once 'autoload.php';

date_default_timezone_set('Asia/Ho_Chi_Minh');

/*
 * Class control Status
 */

class StatusController
{
    protected $request;
    
    public function __construct()
    {
        db::connect();
    }

    public function NewStatus($id_user, ...$args)
    {
        $this->request = $args[0];

    	$content = htmlspecialchars($this->request['content']);


        if (isset($this->request['image'])) {

            // upload and get path file
            $token = '';
            $prepare = "0123456789qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM";
            $len = strlen($prepare) - 1;
            for ($c = 0; $c < 20; $c++)
            {
                $token .= $prepare[rand(0, $len)];
            }
            $file_name = $this->request['image']['name'];
            $ext = strrchr($file_name,'.');
            $target_path_local = __DIR__."/upload/". $id_user . $token . $ext;
            $target_path_db = "inc/upload/". $id_user . $token . $ext;
            move_uploaded_file($this->request["image"]["tmp_name"], $target_path_local);
        } else {
            $target_path_db = '';
        }

        try {
            // prepare string insert status
            $sqlInsert = "INSERT INTO status(status_user_id, status_content, status_role, status_image, status_created) VALUES(?, ?, ?, ?, now())";
            $data = db::$connection->prepare($sqlInsert);
            if ($data->execute([$id_user, $content, $this->request['role'], $target_path_db])){
                return db::$connection->lastInsertId();
            }
            return 0;
        } catch (PDOException $ex) {
            throw new PDOexception($ex->getMessage());
        }
    }

    public function StatusById($id_user, $limit = 10)
    {
        try {
            $sqlSelect = "SELECT * FROM status WHERE status_user_id = ? ORDER BY status_created DESC LIMIT ?";
            $data = db::$connection->prepare($sqlSelect);
            if ($data->execute([$id_user, $limit])) {
                $row = $data->fetchAll(PDO::FETCH_ASSOC);
                return $row;
            }
            return null;
        } catch (PDOException $ex) {
            throw new PDOException($ex->getMessage());
        }
    }


    public function StatusByKeyWordAndId($keyword, $id)
    {
        try {
            $sqlSelect = "SELECT * FROM status WHERE status_content LIKE ? AND (status_user_id = ? OR status_role = 'Công khai') ORDER BY status_created DESC";
            $data = db::$connection->prepare($sqlSelect);
            if ($data->execute(array('%' . $keyword . '%', $id))) {
                $row = $data->fetchAll(PDO::FETCH_ASSOC);
                return $row;
            }
            return null;
        } catch (PDOException $ex) {
            throw new PDOException($ex->getMessage());
        }
    }

    public function StatusByFriendId($keyword, $userId, $friendId)
    {
        try {
            $sqlSelect = "SELECT * FROM status as stt, users as u WHERE stt.status_content LIKE ? AND (stt.status_id = u.user_id AND u.user_id = ? AND u.user_followed LIKE ? AND stt.status_role IN ('Bạn bè')) ORDER BY stt.status_created";
            $data = db::$connection->prepare($sqlSelect);
            if ($data->execute(array('%' . $keyword . '%', $friendId, '%' . $userId . '%'))) {
                $row = $data->fetchAll(PDO::FETCH_ASSOC);
                return $row;
            }
            return null;
        } catch (PDOException $ex) {
            throw new PDOException($ex->getMessage());
        }
    }

    public function StatusRandom()
    {
        try {
            $sqlSelect = "SELECT DISTINCT(status_user_id), status_content, status_created, status_id, status_role FROM status WHERE status_role = 'Công khai' ORDER BY status_created LIMIT 10";
            $data = db::$connection->prepare($sqlSelect);
            if ($data->execute()) {
                $row = $data->fetchAll(PDO::FETCH_ASSOC);
                return $row;
            }
            return null;
        } catch (PDOException $ex) {
            throw new PDOException($ex->getMessage());
        }
    }
    //public  function Comment($)
    //Trang
    public function StatusPublic($id_user2)
    {
        //User1 vào xem profile của user2
        try {
            $sqlSelect = "SELECT * FROM status WHERE status_user_id = ? AND status_role = 'Công khai' ORDER BY status_created DESC";
            $data = db::$connection->prepare($sqlSelect);
            if ($data->execute([$id_user2])) {
                $row = $data->fetchAll(PDO::FETCH_ASSOC);
                return $row;
            }
            return null;
        } catch (PDOException $ex) {
            throw new PDOException($ex->getMessage());
        }
    }
    //Trang
    public function StatusFriend($id_user2)
    {
        //User1 vào xem profile của user2
        try {
            $sqlSelect = "SELECT * FROM status WHERE status_user_id = ? AND status_role = 'Bạn bè' ORDER BY status_created DESC";
            $data = db::$connection->prepare($sqlSelect);
            if ($data->execute([$id_user2])) {
                $row = $data->fetchAll(PDO::FETCH_ASSOC);
                return $row;
            }
            return null;
        } catch (PDOException $ex) {
            throw new PDOException($ex->getMessage());
        }
    }
    //Trang
    //userA xem profile userB
    public function ShowStatusWithRelationship($id_userA,$id_userB)
    {
        try {
            if($id_userA==$id_userB)
            {
                return $this->StatusById($id_userA);
            }

            $user = new UserController();
            $usrA = $user->GetUser('',$id_userA);
            if ($usrA['user_id'] != $id_userA) {
                return "Không tồn tại id";
            }
            $usrB = $user->GetUser('',$id_userB);
            if ($usrB['user_id'] != $id_userB) {
                return "Không tồn tại id";
            }
            $arrStatus = [];
            $sttPublic = $this->StatusPublic($id_userB);
            if ($sttPublic != null) {
                $arrStatus = array_merge($arrStatus, $sttPublic);
            }

            //Kiểm tra A và b có phải là bạn bè?
            $followedA = !empty($usrA['user_followed']) ? unserialize($usrA['user_followed']) : [];
            $followedB = !empty($usrB['user_followed']) ? unserialize($usrB['user_followed']) : [];
            if (in_array($id_userA, $followedB) && in_array($id_userB, $followedA)) {
                $sttFriend = $this->StatusFriend($id_userB);
                $arrStatus = array_merge($arrStatus, $sttFriend);
            }
            return $arrStatus;
        } catch (PDOException $ex) {
            throw new PDOException($ex->getMessage());
        }
    }
    //Add những người đã like
    public function AddWhoLiked($row,$id_user,$id_status)
    {
        try 
        {
            
            if (empty($row['status_wholiked']))
                {
                    $arr = array();
                    array_push($arr,$id_user);
                    $convert = serialize($arr);
                    $sqlUpdate = "UPDATE status SET status_wholiked = ? WHERE status_id = ?";
                    $data = db::$connection->prepare($sqlUpdate);
                    if ($data->execute([$convert,$id_status]))
                    {
                        return 'Thành công';
                    }
                    return 'Thất bại';
                }
            $wholiked = unserialize($row['status_wholiked']);
            if (in_array($id_user,$wholiked,true))
                {
                    return "Đã like rồi";
                }
            array_push($wholiked,$id_user);
            $convert = serialize($wholiked);
            $sqlUpdate = "UPDATE status SET status_wholiked = ? WHERE status_id = ?";
            $data = db::$connection->prepare($sqlUpdate);
            if ($data->execute([$convert,$id_status]))
            {
                return 'Thành công';
            }
            return 'Thất bại';
        } catch (PDOException $ex) {
            throw new PDOException($ex->getMessage());
        }
    }
//Like cho status nào
    public function LikeForStatus($id_user,$id_status)
    {
        try
        {
            $sqlSelect = "SELECT * FROM status WHERE status_id = ?";
            $data = db::$connection->prepare($sqlSelect);
            if ($data->execute([$id_status])) {
                $row = $data->fetch(PDO::FETCH_ASSOC);
                return $this->AddWhoLiked($row,$id_user,$id_status);
                
            }
        
            return 'Thất bại';
        } catch (PDOException $ex) {
            throw new PDOException($ex->getMessage());
        }
    }
//Xóa liketrong database
    public function RemoveWhoLiked($row,$id_user,$id_status)
    {
        try 
        {
            if (empty($row['status_wholiked']))
                {
                    return 'Thành công';
                }
            
            $wholiked = unserialize($row['status_wholiked']);
            foreach ($wholiked as $key => $value) 
            {
                if($value==$id_user)
                {
                   unset($wholiked[$key]);
                }
            }
            $convert = serialize($wholiked);
            $sqlUpdate = "UPDATE status SET status_wholiked = ? WHERE status_id = ?";
            $data = db::$connection->prepare($sqlUpdate);
            if ($data->execute([$convert,$id_status]))
            {
                return 'Thành công';
            }
            return 'Thất bại';
        } catch (PDOException $ex) {
            throw new PDOException($ex->getMessage());
        }
    }
//Bỏ like( người nào status nào)
    public function UnLikeForStatus($id_user,$id_status)
    {
        try
        {
            $sqlSelect = "SELECT * FROM status WHERE status_id = ?";
            $data = db::$connection->prepare($sqlSelect);
            if ($data->execute([$id_status])) 
            {
                $row = $data->fetch(PDO::FETCH_ASSOC);
                return $this->RemoveWhoLiked($row,$id_user,$id_status);
            }
        
            return 'Thất bại';
        } catch (PDOException $ex) {
            throw new PDOException($ex->getMessage());
        }
    }
    //Số lượng lượt like cho 1 status
    public function AmountOfLiked($id_status)
    {
        try
        {
            $sqlSelect = "SELECT * FROM status WHERE status_id = ?";
            $data = db::$connection->prepare($sqlSelect);
            if ($data->execute([$id_status])) {
                $row = $data->fetch(PDO::FETCH_ASSOC);
                if(empty($row['status_wholiked'])) return 0;
                
                $array = unserialize($row['status_wholiked']);
                $count = count($array);
                return $count;
            }
        
            return 0;
        } catch (PDOException $ex) {
            throw new PDOException($ex->getMessage());
        }
    }
//đã like hay chưa
    public function IsLiked($id_user, $id_status)
    {
        try 
        {
            $sqlSelect = "SELECT * FROM status WHERE status_id = ?";
            $data = db::$connection->prepare($sqlSelect);
            if ($data->execute([$id_status])) 
            {
                $row = $data->fetch(PDO::FETCH_ASSOC);
                
                if (!empty($row['status_wholiked']))
                {
                    $wholiked = unserialize($row['status_wholiked']);
                    foreach ($wholiked as $key => $value) 
                    {
                        if($value==$id_user)
                        {
                            return true;
                        }
                    }
                }
                return false;
            }
            return false;
        } catch (PDOException $ex) {
            throw new PDOException($ex->getMessage());
        }
    }
}