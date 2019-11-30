<?php
    /*===============================
        Class control comment
    ===============================*/
    class commentController{
        public function __construct(){

        }

        //lưu comment xuống database
        public function newComment($id_status, $userId, $content){
            try{
                $sqlInsert = "INSERT INTO comments(id_status, id_user_comment, content, created) VALUES (?, ?, ?, now())";
                $data = db::$connectionstring->prepare($sqlInsert);
                if($data->execute(array($id_status, $userId, $content))){

                    //trả về id của comment
                    return db::$connectionstring->lastInsertId();
                }

                return 0;
            }
            catch (PDOException $ex) {
                throw new PDOexception($ex->getMessage());
            }
        }

        //tìm các comment của status
        public function commentWithIdStatus($id_status){
            try{
                $sqlSelect = "SELECT * FROM comments WHERE id_status = ? ORDER BY created DESC";
                $data = db::$connectionstring->prepare($sqlSelect);
                if($data->execute(array($id_status))){

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