<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Controller extends CI_Controller{

    protected function load_common_view($view = null){
        $this->load_head();
        $this->load_title();
        $this->load_left_side();

        if($view != null)
		      $this->load->view("common/view_display", array('view' => $view));

		$this->load_footer();
    }

    protected function load_head(){
        $this->load->model("IniDataReader");
        $this->load->view("common/head", array(
            'headTitle' => $this->IniDataReader->get_title_data("titleBar"),
        ));
    }

    protected function load_title(){
        $this->load->model("IniDataReader");
        $this->load->view("common/main_title", array('mainDoorTitle' => $this->IniDataReader->get_title_data("mainDoorTitle")));
    }

    protected function load_left_side(){
        $this->load->view("common/wrap_view", array(
            'id' => null,
            'class' => "float_left",
            'viewList' => array(
                $this->load_user_info(),
                $this->load_forum_list(),
            ),
        ));
    }

    protected function load_user_info(){
        $this->load->model("User_Model");

        $data = array();
        if(isset($_SESSION['userId'])){
            $data['userId'] = $_SESSION['userId'];
            $data['nickname'] = $this->User_Model->get_user_nickname($_SESSION['userId']);
        }

        return $this->load->view("user/user_info", $data, true);
    }

    protected function load_forum_list(){
        $this->load->model("Forum_Model");
        return $this->load->view("forum/forum_list", array('forumCategoryList' => $this->Forum_Model->get_forum_and_category_list(),), true);
    }

    protected function load_footer(){
        $this->load->view("common/footer");
    }
}
