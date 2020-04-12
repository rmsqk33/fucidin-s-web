<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User extends MY_Controller{
    private $uploadUserIconPath = "";

    public function login(){
        $this->load->model("User_Model");

        $this->load_head();
        $this->load->view("user/login");
        $this->load_footer();
    }

    public function login_confirm(){
        $id = $this->input->post('id');
        if(empty($id)){
            error_show_log("fail login: empty id");
        }

        $password = $this->input->post('password');
        if(empty($password)){
            error_show_log("fail login: empty password");
        }

        $this->load->model("User_Model");

        $userDbId = $this->User_Model->get_user_dbid($id, $password);
        if($userDbId == null){
            redirect("user/login?errorCode=1");
        }

        $_SESSION['userId'] = $userDbId;
        if(empty($_SESSION['prevUrl'])){
            redirect("");
        } else{
            redirect($_SESSION['prevUrl']);
        }
    }

    public function sign_up(){
        $this->load->library("form_validation");
        $this->load->model("IniDataReader");
        $this->load->model("User_Model");

        $idMaxLength = $this->User_Model->get_login_id_max_length();
        $this->form_validation->set_rules("id", "아이디", "required|min_length[1]|max_length[$idMaxLength]|is_unique[user.loginid]|is_alpha_numeric_special");

        $passwordMinLength = $this->IniDataReader->get_sign_up_data("passwordMinLength");
        $passwordMaxLength = $this->IniDataReader->get_sign_up_data("passwordMaxLength");
        $this->form_validation->set_rules("password", "비밀번호", "required|min_length[$passwordMinLength]|max_length[$passwordMaxLength]|is_alpha_numeric_special");
        $this->form_validation->set_rules("passwordConfirm", "비밀번호 확인", "required|matches[password]");

        $nickNameMaxLength = $this->User_Model->get_nickname_max_length();
        $this->form_validation->set_rules("nickname", "닉네임", "required|is_unique[userinfo.nickname]|min_length[1]|max_length[$nickNameMaxLength]");

        if($this->form_validation->run()){
            $id = $this->input->post('id');
            $password = $this->input->post('password');
            $nickname = $this->input->post('nickname');

            $result = $this->User_Model->add_user($id, $password, $nickname);
            if($result !== false){
                $_SESSION['userId'] = $result;

                $view = $this->load->view("common/success_page", array( 'message' => "가입 완료되었습니다."), true);
                $this->load_common_view($successView);
            } else{
                error_show_log("fail edit info: db error");
            }
        } else{
            $this->form_validation->set_error_delimiters("<span>", "</span>");
            $signUpView = $this->load->view("user/sign_up", array(
                'idMaxLength' => $idMaxLength,
                'passwordMinLength' => $passwordMinLength,
                'passwordMaxLength' => $passwordMaxLength,
                'nickNameMaxLength' => $nickNameMaxLength,
            ), true);
            $this->load_common_view($signUpView);
        }
    }

    public function logout(){
        $_SESSION['userId'] = null;
        if(empty($_SESSION['prevUrl'])){
            redirect();
        } else{
            redirect($_SESSION['prevUrl']);
        }
    }

    public function edit_info(){
        if(empty($_SESSION['userId'])){
            error_show_log("fail edit info: empty id");
        }

        $this->load->model("User_Model");
        $userInfo = $this->User_Model->get_user_info($_SESSION['userId']);
        if(!$userInfo){
            error_show_log("fail edit info: db error");
        }

        $this->load->library("form_validation");

        $nickNameMaxLength = $this->User_Model->get_nickname_max_length();
        $this->form_validation->set_rules("nickname", "닉네임", "is_unique[userinfo.nickname]|min_length[1]|max_length[$nickNameMaxLength]");

        $profilMaxLen = $this->User_Model->get_profil_max_length();
        $this->form_validation->set_rules("profil", "프로필", "max_length[$profilMaxLen]");
        $this->form_validation->set_rules("icondata", "아이콘", "callback_upload_icon_check");

        $this->load->model("IniDataReader");
        $iconAllowTypes = $this->IniDataReader->get_user_info_edit_data("iconAllowTypes");

        if($this->form_validation->run()){
            $updateDatas = array(
                 'nickname' => $this->input->post('nickname'),
                'profil' => $this->input->post('profil'),
                'iconpath' => $this->uploadUserIconPath,
            );

            if($this->User_Model->update_user_info($_SESSION['userId'], $updateDatas)){
                $view = $this->load->view("common/success_page", array( 'message' => "수정 완료되었습니다."), true);
                $this->load_common_view($view);
            }
        } else{
            $editInfoView = $this->load->view("user/edit_info", array(
                'userInfo' => $userInfo,
                'iconAllowTypes' => $iconAllowTypes,
                'nickNameMaxLength' => $nickNameMaxLength,
                'profilMaxLength' => $profilMaxLen,
                'iconMaxSize' => $this->IniDataReader->get_user_info_edit_data("maxSize"),
            ), true);

            $this->load_common_view($editInfoView);
        }
    }

    public function upload_icon_check(){
        if(empty($_FILES))
            return true;

        if(empty($_FILES['icondata']['name']))
            return true;

        $this->load->model("IniDataReader");
        $iconAllowTypes = $this->IniDataReader->get_user_info_edit_data("iconAllowTypes");

        $iconAllowTypes = str_replace(array(".", " ", ","), array("", "", "|"), $iconAllowTypes);

        $uploadPath = $this->IniDataReader->get_user_info_edit_data("uploadPath");
        $uploadDocRootPath = $_SERVER['DOCUMENT_ROOT'].$this->IniDataReader->get_user_info_edit_data("uploadPath");
        $fileExt = explode(".", $_FILES['icondata']['name']);
        $fileExt = ".".end($fileExt);
        $uploadFileName = tempnam($uploadDocRootPath, "");
        $uploadFileName = explode("/", $uploadFileName);
        $uploadFileName = end($uploadFileName).$fileExt;

        $config['upload_path'] = $uploadDocRootPath;
        $config['max_size'] = $this->IniDataReader->get_user_info_edit_data("maxSize");
        $config['allowed_types'] = $iconAllowTypes;
        $config['file_name'] = $uploadFileName;
        $this->uploadUserIconPath = $uploadPath.$uploadFileName;

        $this->load->library('upload', $config);
        if(!$this->upload->do_upload('icondata')){
            $this->form_validation->set_message("upload_icon_check", $this->upload->display_errors());
            return false;
        }

        return true;
    }
}
