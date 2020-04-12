<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class IniDataReader extends CI_Model{
    private $metaDataList = null;

    public function __construct(){
        $this->metaDataList = parse_ini_file(__DIR__."/../../data/ini/metaData.ini", true);
    }

    public function get_title_data($key){
        return $this->get_data('Title', $key);
    }

    public function get_topic_data($key){
        return $this->metaDataList['Topic'][$key];
        return $this->get_data('Topic', $key);
    }

    public function get_sign_up_data($key){
        return $this->get_data('SignUp', $key);
    }

    public function get_user_info_edit_data($key){
        return $this->get_data('UserInfoEdit', $key);
    }

    public function get_search_topic_filter_list(){
        return $this->metaDataList['SearchTopicFilter'];
    }

    public function get_search_topic_date_filter_list(){
        return $this->metaDataList['SearchTopicDateFilter'];
    }

    public function get_data($key, $subKey){
        return $this->metaDataList[$key][$subKey];
    }
}
