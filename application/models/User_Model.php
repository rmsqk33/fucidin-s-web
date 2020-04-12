<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_Model extends CI_Model{
    public function __construct(){
        parent::__construct();

    }

    public function get_user_info($id){
        return $this->get_user_info_inner($id, "*");
    }

    public function get_user_nickname($id){
        $userInfo = $this->get_user_info_inner($id, "nickname");
        if($userInfo)
            return $userInfo->nickname;

        return "";
    }

    public function get_user_dbid($id, $password){
        $this->db->select("id, password");
        $this->db->from("user");
        $this->db->where("loginId", $id);

        $result = $this->db->get()->result();
        if(empty($result))
            return null;

        if(!password_verify($password, $result[0]->password))
            return null;

        return $result[0]->id;
    }

    public function get_login_id_max_length(){
        return $this->get_column_max_len("user", "loginId");
    }

    public function get_nickname_max_length(){
        return $this->get_column_max_len("userinfo", "nickname");
    }

    public function get_profil_max_length(){
        return $this->get_column_max_len("userinfo", "profil");
    }

    public function add_user($id, $password, $nickname){
        $password = password_hash($password, PASSWORD_BCRYPT);

        $insertUserData = array(
            'loginId' => $id,
            'password' => $password,
        );

        if(!$this->db->insert("user", $insertUserData)){
            return false;
        }

        $insertId = $this->db->insert_id();
        $insertUserInfoData = array(
            'id' => $insertId,
            'nickname' => $nickname,
            'created' => date("Y-m-d H:i:s"),
            'iconpath' => "/data/image/user_default_icon.png",
        );

        if($this->db->insert("userinfo", $insertUserInfoData) === false){
            $this->db->delete("user", array( 'id' => $insertId ));
            return false;
        }

        return $insertId;
    }

    public function update_user_info($id, $updateDatas){
        $data = array();
        if(!empty($updateDatas['iconpath'])){
            $data['iconpath'] = $updateDatas['iconpath'];
        }

        if(!empty($updateDatas['nickname'])){
            $data['nickname'] = $updateDatas['nickname'];
        }

        if(isset($updateDatas['profil'])){
            $data['profil'] = $updateDatas['profil'];
        }

        if(empty($data))
            return false;

        $this->db->where("id", $id);
        return $this->db->update("userinfo", $data);
    }

    private function get_user_info_inner($id, $columns){
        $this->db->select($columns);
        $this->db->from("userinfo");
        $this->db->where("id", $id);

        $result = $this->db->get()->result();
        if(empty($result))
            return null;

        return $result[0];
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
