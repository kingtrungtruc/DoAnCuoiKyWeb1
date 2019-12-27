<?php

/**
 * Created by PhpStorm.
 * User: Trang
 * Date: 12/17/18
 * Time: 11:46 AM
 */
class CommentController
{
    public function __construct()
    {}
    //Trang làm nè : lưu comment xuống database
    public function NewComment($id_status,$id_user, $content)
    {
        try {
            // prepare string insert status
            $sqlInsert = "INSERT INTO comments(comment_status_id, comment_user_id, comment_content, comment_created) VALUES(?,?,?,now())";
            $data = db::$connection->prepare($sqlInsert);
            if ($data->execute([$id_status,$id_user, $content])) {
                return db::$connection->lastInsertId();
            }
            return 0;
        } catch (PDOException $ex) {
            throw new PDOexception($ex->getMessage());
        }
    }
    public function CommentWithIdStatus($id_status)
    {
        try {
            $sqlSelect = "SELECT * FROM comments WHERE comment_status_id = ? ORDER BY comment_created DESC";
            $data = db::$connection->prepare($sqlSelect);
            if ($data->execute([$id_status])) {
                $row = $data->fetchAll(PDO::FETCH_ASSOC);
                return $row;
            }
            return null;
        } catch (PDOException $ex) {
            throw new PDOException($ex->getMessage());
        }
    }
    
}
?>