<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Topic_Model extends CI_Model{
    public function __construct(){
        parent::__construct();

    }

    public function add_topic($tablePrefix, $userDbId, $topicDatas){
        $tableName = $this->combine_table_name($tablePrefix);
        if(!$this->has_table($tableName)){
            error_show_log("fail add topic : not found topic table");
        }

        $insertData = array(
            'userdbid' => $userDbId,
            'created' => date("Y-m-d H:i:s"),
        );
        $insertData = array_merge($insertData, $topicDatas);

        if(!$this->db->insert($tableName, $insertData)){
            return false;
        }

        return $this->db->insert_id();
    }

    public function update_topic($tablePrefix, $topicId, $userId, $updateDatas){
        $tableName = $this->combine_table_name($tablePrefix);
        if(!$this->has_table($tableName)){
            error_show_log("fail update topic : not found topic table");
        }

        $this->db->select("userdbid");
        $this->db->where("id", $topicId);
        $tableInfo = $this->db->get($tableName)->result();
        if($tableInfo === false){
            error_show_log("fail update topic : not found topic");
        }

        if($tableInfo[0]->userdbid != $userId){
            error_show_log("fail update topic : not have authority");
        }

        $this->db->where("id", $topicId);
        return $this->db->update($tableName, $updateDatas);
    }

    public function get_topic_info_list($tablePrefix, $page, $countOncePage, $searchInfo = array()){
        $tableName = $this->combine_table_name($tablePrefix);
        if(!$this->has_table($tableName)){
            return array();
        }

        $result = (object) array();

        $this->db->select("$tableName.id as topicid, title, $tableName.created, viewcount, recommend, unrecommend, userinfo.id as userid, nickname, profil, iconpath");
        $this->db->join("userinfo", "$tableName.userdbid = userinfo.id");
        if(!empty($searchInfo)){
            $keyword = $searchInfo['keyword'];
            switch($searchInfo['searchFilter']){
                case "content":
                $this->db->like("content", $keyword);
                break;

                case "title_content":
                $this->db->like("title", $keyword);
                $this->db->or_like("content", $keyword);
                break;

                case "user":
                $this->db->like("nickname", $keyword);
                break;

                default:
                $this->db->like("title", $keyword);
                break;
            }

            $dateFilter = $searchInfo['dateFilter'];
            if(0 < $dateFilter){
                $dateFilter = date("Y-m-d H:i:s", strtotime(-$searchInfo['dateFilter']." month"));
                $this->db->where("$tableName.created>", $dateFilter);
            }
        }

        $result->maxPage = $this->db->count_all_results($tableName, false) / $countOncePage + 1;

        $this->db->order_by("$tableName.id DESC");
        $this->db->limit($countOncePage, ($page - 1) * $countOncePage);

        $result->topicList = $this->db->get()->result();

        return $result;
    }

    public function get_topic_prev_next($tablePrefix, $topicId){
        $tableName = $this->combine_table_name($tablePrefix);
        if(!$this->has_table($tableName))
            return null;

        $topicId = $this->db->escape($topicId);

        $query = "SELECT * FROM $tableName
                    WHERE id IN(
                        $topicId,
                        (SELECT id FROM $tableName
                        WHERE id < $topicId
                        ORDER BY id DESC
                        LIMIT 1),
                        (SELECT id FROM $tableName
                        WHERE $topicId < id
                        ORDER BY id
                        LIMIT 1))
                    ORDER BY id";

        return $this->db->query($query)->result();
    }

    public function get_topic_info($tablePrefix, $id){
        $tableName = $this->combine_table_name($tablePrefix);
        if(!$this->has_table($tableName))
            return null;

        $this->db->from($tableName);
        $this->db->where("id", $id);

        $result = $this->db->get()->result();
        if(empty($result))
            return null;

        return $result[0];
    }

    public function create_topic_table($tablePrefix){
        if($this->db->table_exists($this->combine_table_name($tablePrefix)))
        {
            error_show_log("fail create topic table by exist");
            return;
        }

        $this->dbforge->add_field("id");
        $this->dbforge->add_field("userdbid INT NOT NULL");
        $this->dbforge->add_field("title VARCHAR(30) NOT NULL");
        $this->dbforge->add_field("content VARCHAR(2000)");
        $this->dbforge->add_field("created DATETIME NOT NULL");
        $this->dbforge->add_field("viewcount INT DEFAULT 0");
        $this->dbforge->add_field("recommend INT DEFAULT 0");
        $this->dbforge->add_field("unrecommend INT DEFAULT 0");
        $this->dbforge->create_table($this->combine_table_name($tablePrefix));
    }

    public function recommend_comment($tablePrefix, $topicId, $userId, $isRecommend){
        $tableName = $this->combine_table_name($tablePrefix);
        if(!$this->has_table($tableName)){
            error_show_log("fail check recommended : not exists table");
        }

        $columnName = "";
        if($isRecommend == true){
            $columnName = "recommend";
        } else{
            $columnName = "unrecommend";
        }

        $this->db->select($columnName);
        $this->db->where("id", $topicId);
        $recommendCount = $this->db->get($tableName)->result();
        if(empty($recommendCount)){
            return false;
        }
        $recommendCount = $recommendCount[0]->$columnName;

        $topicEscapeId = $this->db->escape($topicId);
        $userEscapeId = $this->db->escape($userId);
        $result = $this->db->query("INSERT INTO topicRecommend
                    (tablePrefix, topicId, userId, $columnName)
                    VALUES('$tablePrefix', $topicEscapeId, $userEscapeId, true)
                    ON DUPLICATE KEY UPDATE $columnName=true");

        if(!$result){
            return false;
        }

        $newCount = $recommendCount + 1;
        $this->db->where("id", $topicId);
        $this->db->update($tableName, array( $columnName => $newCount));

        return $newCount;
    }

    public function is_recommended($tablePrefix, $topicId, $userId, $isRecommend){
        if(!$this->has_table($this->combine_table_name($tablePrefix))){
            error_show_log("fail check topic recommended : not exists table");
        }

        $this->db->where("tablePrefix", $tablePrefix);
        $this->db->where("topicId", $topicId);
        $this->db->where("userId", $userId);

        $result = $this->db->get("topicRecommend")->result();
        if(empty($result))
            return false;

        return $isRecommend == true ? $result[0]->recommend : $result[0]->unrecommend;
    }

    public function increase_view_count($tablePrefix, $topicId){
        $tableName = $this->combine_table_name($tablePrefix);
        if(!$this->has_table($tableName)){
            return false;
        }


        $this->db->select("viewcount");
        $this->db->where("id", $topicId);
        $result = $this->db->get($tableName)->result();
        if(!$result)
            return false;

        $cookieName = $this->get_view_cookie($tablePrefix, $topicId);
        setCookie($cookieName, true);

        $this->db->where("id", $topicId);
        return $this->db->update($tableName, array("viewcount" => $result[0]->viewcount + 1));
    }

    public function is_aleady_view($tablePrefix, $topicId){
        $cookieName = $this->get_view_cookie($tablePrefix, $topicId);
        return isset($_COOKIE[$cookieName]);
    }

    public function get_topic_title_max_length($tablePrefix){
        $tableName = $this->combine_table_name($tablePrefix);
        return $this->get_column_max_len($tableName, "title");
    }

    public function get_topic_content_max_length($tablePrefix){
        $tableName = $this->combine_table_name($tablePrefix);
        return $this->get_column_max_len($tableName, "content");
    }

    public function get_topic_user_id($tablePrefix, $id){
        $tableName = $this->combine_table_name($tablePrefix);
        if(!$this->has_table($tableName)){
            error_show_log("fail delete topic : not found topic table");
        }

        $this->db->select("userdbid");
        $this->db->where("id", $id);
        $result = $this->db->get($tableName)->result();
        if(empty($result)){
            return false;
        }

        return $result[0]->userdbid;
    }

    public function delete_topic($tablePrefix, $id){
        $tableName = $this->combine_table_name($tablePrefix);
        if(!$this->has_table($tableName)){
            error_show_log("fail delete topic : not found topic table");
        }

        return $this->db->delete($tableName, array( 'id' => $id ));
    }

    private function get_view_cookie($tablePrefix, $topicId){
        return $tablePrefix.'_view_'.$topicId;
    }

    private function has_table($tableName){
        return $this->db->table_exists($tableName);
    }

    private function combine_table_name($tablePrefix){
        return $tablePrefix."_topic";
    }

    private function get_column_max_len($tableName, $columnName){
        $this->db->select("character_maximum_length");
        $this->db->from("information_schema.COLUMNS");
        $this->db->where("TABLE_NAME", $tableName);
        $this->db->where("COLUMN_NAME", $columnName);

        $result = $this->db->get()->result();
        if(empty($result))
            return 0;

        return $result[0]->character_maximum_length;
    }
}
