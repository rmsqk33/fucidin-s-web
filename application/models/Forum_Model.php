<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Forum_Model extends CI_Model{

    public function __contruct(){
        parent::__contruct();
        $this->load->dbforge();
    }

    public function add_forum_category($categoryName):int{
        $this->db->insert("forumCategory", array('name'=>$categoryName));
        return $this->db->insert_id();
    }

    public function remove_forum_category($categoryDbId){
        $this->db->delete("forumcategory", array('dbid'=>$categoryDbId));
    }

    public function add_forum($categoryDbId, $name, $tablePrefix, $forumOrder){
        $insertData = array(
            'name' => $name,
            'categorydbid' => $categoryDbId,
            'tableprefix' => $tablePrefix,
            'forumorder' => $forumOrder,
        );

        $this->db->insert("forum", $insertData);
    }

    public function remove_forum($dbid){
        $this->db->delete("forum", array('dbid'=>$dbid));
    }

    public function get_forum_category_list(){
        return $this->db->get("forumCategory")->result();
    }

    public function get_forum_list(){
        return $this->db->get("forum")->result();
    }

    public function get_forum_and_category_list(){
		$forumCategories = $this->get_forum_category_list();
		$forums = $this->get_forum_list();

		$forumCategoryList = array();
		foreach($forumCategories as $category){
			$forumCategoryList[$category->id] = (object)array(
														'title' => $category->title,
														'titleColor' => $category->color,
														'forumList' => array()
													);
		}

		foreach($forums as $forum){
			$forumCategoryList[$forum->categoryDbId]->forumList[$forum->id] = $forum;
		}

		return $forumCategoryList;
    }

    public function get_forum_title($tablePrefix){
        $this->db->select("title");
        $this->db->where("tablePrefix", $tablePrefix);

        $result = $this->db->get("forum")->result();
        if(empty($result)){
            return false;
        }

        return $result[0]->title;
    }

    public function get_recent_topic_list($count){
        $this->db->select("title, tablePrefix");
        $this->db->order_by("id");
        $forumList = $this->db->get("forum")->result();
        if(empty($forumList)){
            return false;
        }

        foreach($forumList as $forum){
            $tableName = $forum->tablePrefix."_topic";
            $this->db->select("title, created, id");
            $this->db->order_by("id", "DESC");
            $this->db->limit($count);
            $topicList = $this->db->get($tableName)->result();
            $forum->topicList = $topicList;
        }

        return $forumList;
    }

    private function create_topic_comment_table($tablePrefix){
        $this->dbforge->add_field("id");
        $this->dbforge->add_field("content VARCHAR(500) NOT NULL");
        $this->dbforge->add_field("userDbId INT NOT NULL");
        $this->dbforge->add_field("recommend INT DEFAULT 0");
        $this->dbforge->add_field("unrecommend INT DEFAULT 0");
        $this->dbforge->add_field("commentDbId INT NOT NULL");
        $this->dbforge->add_foreign_key("commentDbId", $tablePrefix."_comment", "id", "CASCADE", "CASCADE");
        $this->dbforge->add_field("topicDbid INT NOT NULL");
        $this->dbforge->add_foreign_key("topicDbId", $tablePrefix."_topic", "id", "CASCADE", "CASCADE");
        $this->dbforge->create_table($tablePrefix."_comment", true);
    }
}
