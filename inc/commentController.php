<?php
    /*===============================
        Class control comment
    ===============================*/
    class commentController{
        public function __construct(){

        }

        //lưu comment xuống database
        public function newComment($status_id, $user_id, $comment_content){
            try{
                $sqlInsert = "INSERT INTO comments(comment_status_id, comment_user_id, comment_content, comment_created) VALUES (?, ?, ?, now())";
                $data = db::$connectionstring->prepare($sqlInsert);
                if($data->execute(array($status_id, $user_id, $comment_content))){

                    //trả về id của comment
                    return db::$connectionstring->lastInsertId();
                }

                return 0;
            }
            catch (PDOException $ex) {
                throw new PDOexception($ex->getMessage());
            }
        }

        //tìm các comment của status bằng id của status
        public function commentWithIdStatus($status_id){
            try{
                $sqlSelect = "SELECT * FROM comments WHERE comment_status_id = ? ORDER BY comment_created DESC";
                $data = db::$connectionstring->prepare($sqlSelect);
                if($data->execute(array($status_id))){

                    //trả về toàn bộ bản ghi tìm được
                    $row = $data->fetchALL(PDO::FETCH_ASSOC);
                    return $row;
                }

                return null;
            }
            catch (PDOException $ex) {
                throw new PDOexception($ex->getMessage());
            }
        }
    }
?>