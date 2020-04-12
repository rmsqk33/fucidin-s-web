<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Comment_Model extends CI_Model{

    public function __contruct(){
        parent::__contruct();
    }

    public function get_comment_list($tablePrefix, $topicId){
        $tableName = $this->combine_table_name($tablePrefix);
        if(!$this->has_table($tableName)){
            return array();
        }

        $this->db->select("*, $tableName.id as cid, userinfo.id as uid, $tableName.created as comCreated, userinfo.created as userCreated");
        $this->db->from($tableName);
        $this->db->where("topicdbid", $topicId);
        $this->db->order_by("ifnull(commentdbid, $tableName.id)", "ASC", false);
        $this->db->order_by("$tableName.id", "ASC");
        $this->db->join("userinfo", "$tableName.userdbid = userinfo.id");

        return $this->db->get()->result();
    }

    public function get_comment_max_length($tablePrefix){
        $this->db->select("character_maximum_length");
        $this->db->from("information_schema.COLUMNS");
        $this->db->where("TABLE_NAME", $this->combine_table_name($tablePrefix));
        $this->db->where("COLUMN_NAME", "content");

        $result = $this->db->get()->result();
        if(empty($result)){
            return 0;
        }

        return $result[0]->character_maximum_length;
    }

    public function add_comment($tablePrefix, $topicId, $userId, $content, $commentId){
        $tableName = $this->combine_table_name($tablePrefix);
        if(!$this->has_table($tableName)){
            error_show_log("fail write comment : not exists table");
        }

        if($commentId != null && !$this->has_comment($tablePrefix, $commentId)){
            error_show_log("fail write comment : invalid commentId");
        }

        $insertData = array(
            'userdbid' => $userId,
            'topicdbid' => $topicId,
            'content' => $content,
            'commentdbid' => $commentId,
            'created' => date("Y-m-d H:m:s"),
        );

        return $this->db->insert($tableName, $insertData);
    }

    public function delete_comment($tablePrefix, $userId, $commentId){
        $tableName = $this->combine_table_name($tablePrefix);
        if(!$this->has_table($tableName)){
            error_show_log("fail delete comment : not exists table");
        }

        if(!$this->db->delete($tableName, array('id'=>$commentId, 'userdbid' => $userId))){
            return false;
        }

        return $this->db->delete($tableName, array('commentdbid'=>$commentId));
    }

    public function get_comment_userdbid($tablePrefix, $commentId){
        $tableName = $this->combine_table_name($tablePrefix);
        if(!$this->has_table($tableName)){
            error_show_log("fail write comment : not exists table");
        }

        $this->db->select("userdbid");
        $this->db->from($tableName);
        $this->db->where("id", $commentId);

        $result = $this->db->get()->result();
        if(empty($result))
            return null;

        return $result[0]->userdbid;
    }

    public function recommend_comment($tablePrefix, $commentId, $userId, $isRecommend){
        $tableName = $this->combine_table_name($tablePrefix);
        if(!$this->has_table($tableName)){
            error_show_log("fail comment recommend : not exists table");
        }

        $columnName = "";
        if($isRecommend == true){
            $columnName = "recommend";
        } else{
            $columnName = "unrecommend";
        }

        $this->db->select($columnName);
        $this->db->where("id", $commentId);
        $recommendCount = $this->db->get($tableName)->result();
        if(empty($recommendCount)){
            return false;
        }
        $recommendCount = $recommendCount[0]->$columnName;

        $commentEscapeId = $this->db->escape($commentId);
        $userEscapeId = $this->db->escape($userId);
        $result = $this->db->query("INSERT INTO commentRecommend
                    (tablePrefix, commentId, userId, $columnName)
                    VALUES('$tablePrefix', $commentEscapeId, $userEscapeId, true)
                    ON DUPLICATE KEY UPDATE $columnName=true");

        $newCount = $recommendCount + 1;
        $this->db->where("id", $commentId);
        $this->db->update($tableName, array( $columnName => $newCount));

        return $newCount;
    }

    public function is_recommended($tablePrefix, $commentId, $userId, $isRecommend){
        if(!$this->has_table($this->combine_table_name($tablePrefix))){
            error_show_log("fail check comment recommended : not exists table");
        }

        $this->db->where("tablePrefix", $tablePrefix);
        $this->db->where("commentId", $commentId);
        $this->db->where("userId", $userId);

        $result = $this->db->get("commentRecommend")->result();
        if(empty($result))
            return false;

        return $isRecommend == true ? $result[0]->recommend : $result[0]->unrecommend;
    }

    private function has_comment($tablePrefix, $commentId){
        $this->db->select("id");
        $this->db->from($this->combine_table_name($tablePrefix));
        $this->db->where("id", $commentId);

        return 0 < $this->db->get()->num_rows();
    }

    private function has_table($tablePrefix){
        return $this->db->table_exists($tablePrefix);
    }

    private function combine_table_name($tablePrefix){
        return $tablePrefix."_comment";
    }
}
