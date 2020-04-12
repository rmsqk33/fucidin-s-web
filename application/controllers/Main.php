<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Main extends My_Controller {
	public function index(){
		$this->load->model("Forum_Model");

		$viewData = array(
			'recentContentList' => $this->Forum_Model->get_recent_topic_list(5),
		);

		$mainContentView = $this->load->view("main/main_content", $viewData, true);
		$this->load_common_view($mainContentView);
	}
}
