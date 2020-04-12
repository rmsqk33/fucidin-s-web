<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Topic extends MY_Controller{
    public function page(){
        $tablePrefix = $this->input->get('table');
        if(empty($tablePrefix)){
            error_show_log("fail show topic page: invalid tablePrefix");
        }

        $topicId = $this->input->get('id');
        if(empty($topicId)){
            error_show_log("fail show topic page: invalid topicId");
        }

        $this->load->model("Topic_Model");
        $this->load->model("User_Model");
        $this->load->model("Comment_Model");

        $this->increase_topic_view_count($tablePrefix, $topicId);

        $topicInfoList = $this->Topic_Model->get_topic_prev_next($tablePrefix, $topicId);
        if(empty($topicInfoList)){
            show_404();
        }

        $currentTopic = $prevTopic = $nextTopic = null;
        foreach($topicInfoList as $topicInfo){
            if($topicInfo->id == $topicId){
                $currentTopic = $topicInfo;
            } else if($topicInfo->id < $topicId){
                $prevTopic = $topicInfo;
            } else{
                $nextTopic = $topicInfo;
            }
        }

        if(empty($currentTopic)){
            error_show_log("fail show topic page: invalid topicInfo");
        }

        $userInfo = $this->User_Model->get_user_info($currentTopic->userdbid);
        if(!$userInfo){
            show_404();
        }

        $commentList = $this->Comment_Model->get_comment_list($tablePrefix, $topicId);

        $topicPage = $this->load->view("topic/topic_page", array(
            'currentTopic' => $currentTopic,
            'nextTopic' => $nextTopic,
            'prevTopic' => $prevTopic,
            'userInfo' => $userInfo,
            'commentCount' => count($commentList),
            'tablePrefix' => $tablePrefix,
            'commentList' => $commentList,
            'commentInputLength' => $this->Comment_Model->get_comment_max_length($tablePrefix),
        ), true);

        $this->load_common_view($topicPage);
    }

    public function recommend_topic(){
        if(empty($_SESSION['userId'])){
            error_show_log("fail recommend comment: invalid userId");
        }

        $tablePrefix = $this->input->post('table');
        if(empty($tablePrefix)){
            error_show_log("fail recommend comment: invalid tablePrefix");
        }

        $topicId = $this->input->post('topicId');
        if(empty($topicId)){
            error_show_log("fail recommend comment: invalid topicId");
        }

        $isRecommend = (boolean)$this->input->post('recommend');

        $this->load->model("Topic_Model");
        if($this->Topic_Model->is_recommended($tablePrefix, $topicId, $_SESSION['userId'], $isRecommend)){
            echo json_encode(array('recommended' => true));
            return;
        }

        $newCount = $this->Topic_Model->recommend_comment($tablePrefix, $topicId, $_SESSION['userId'], $isRecommend);
        if($newCount){
            echo json_encode(array('recommended' => false, 'count' => $newCount));
        } else{
            error_show_log("fail recommend comment: db error");
        }
    }

    private function increase_topic_view_count($tablePrefix, $topicId){
        $this->load->model("Topic_Model");
        if($this->Topic_Model->is_aleady_view($tablePrefix, $topicId)){
            return;
        }

        if(!$this->Topic_Model->increase_view_count($tablePrefix, $topicId)){
            error_show_log("fail increase topic view count: db error");
        }
    }

    public function write_comment(){
        if(empty($_SESSION['userId'])){
            error_show_log("fail write comment: invalid userId");
        }

        $tablePrefix = $this->input->post('table');
        if(empty($tablePrefix)){
            error_show_log("fail write comment: invalid tablePrefix");
        }

        $topieId = $this->input->post('topicId');
        if(empty($topieId)){
            error_show_log("fail write comment: invalid topicId");
        }

        $content = $this->input->post('content');
        if(empty($content)){
            error_show_log("fail write comment: invalid comment");
        }

        $commentId = $this->input->post('commentId');

        $this->load->model("Comment_Model");
        if($this->Comment_Model->add_comment($tablePrefix, $topieId, $_SESSION['userId'], $content, $commentId)){
            $prevUrl = !empty($_SESSION['prevUrl']) ? $_SESSION['prevUrl'] : "";
            redirect($prevUrl);
        } else{
            error_show_log("fail write comment: db error");
        }
    }

    public function delete_comment(){
        if(empty($_SESSION['userId'])){
            error_show_log("fail delete comment: invalid userId");
        }

        $tablePrefix = $this->input->post('table');
        if(empty($tablePrefix)){
            error_show_log("fail delete comment: invalid tablePrefix");
        }

        $commentId = $this->input->post('commentId');
        if(empty($commentId)){
            error_show_log("fail delete comment: invalid commentId");
        }

        $this->load->model("Comment_Model");
        $userdbid = $this->Comment_Model->get_comment_userdbid($tablePrefix, $commentId);
        if($userdbid == null){
            error_show_log("fail delete comment: invalid comment");
        }

        if($_SESSION['userId'] != $userdbid){
            error_show_log("fail delete comment: invalid ownerUser");
        }

        if($this->Comment_Model->delete_comment($tablePrefix, $_SESSION['userId'], $commentId)){
            $prevUrl = !empty($_SESSION['prevUrl']) ? $_SESSION['prevUrl'] : "";
            redirect($prevUrl);
        } else{
            error_show_log("fail_delete_comment: db error");
        }
    }

    public function recommend_comment(){
        if(empty($_SESSION['userId'])){
            error_show_log("fail recommend comment: invalid userId");
        }

        $tablePrefix = $this->input->post('table');
        if(empty($tablePrefix)){
            error_show_log("fail recommend comment: invalid tablePrefix");
        }

        $commentId = $this->input->post('commentId');
        if(empty($commentId)){
            error_show_log("fail recommend comment: invalid commentId");
        }

        $isRecommend = (boolean)$this->input->post('recommend');

        $this->load->model("Comment_Model");
        if($this->Comment_Model->is_recommended($tablePrefix, $commentId, $_SESSION['userId'], $isRecommend)){
            echo json_encode(array('recommended' => true));
            return;
        }

        $newCount = $this->Comment_Model->recommend_comment($tablePrefix, $commentId, $_SESSION['userId'], $isRecommend);
        if($newCount){
            echo json_encode(array('recommended' => false, 'count' => $newCount));
        } else{
            error_show_log("fail recommend comment: db error");
        }
    }

    public function write_topic(){
        if(empty($_SESSION['userId'])){
            error_show_log("fail write topic page: invalid userId");
        }

        $tablePrefix = $this->input->get("table");
        if(empty($tablePrefix)){
            error_show_log("fail write topic page: invalid tablePrefix");
        }

        $this->load->model("Forum_Model");
        $forumTitle = $this->Forum_Model->get_forum_title($tablePrefix);
        if($forumTitle === false){
            error_show_log("fail write topic page: forum not found");
        }

        $this->load->model("User_Model");
        $this->load->model("Topic_Model");

        $viewData = array(
            'tablePrefix' => $tablePrefix,
            'forumTitle' => $forumTitle,
            'nickname' => $this->User_Model->get_user_nickname($_SESSION['userId']),
            'titleMaxLength' => $this->Topic_Model->get_topic_title_max_length($tablePrefix),
            'contentMaxLength' => $this->Topic_Model->get_topic_content_max_length($tablePrefix),
            'confirmUrl' => "/topic/write_topic_confirm",
        );
        $writeTopicView = $this->load->view("topic/write_topic", $viewData, true);
        $this->load_common_view($writeTopicView);
    }

    public function write_topic_confirm(){
        $userId = $_SESSION['userId'];
        if(empty($userId)){
            error_show_log("fail wirte topic confirm: invalid userId");
        }

        $tablePrefix = $this->input->post("table");
        if(empty($tablePrefix)){
            error_show_log("fail wirte topic confirm: invalid tablePrefix");
        }

        $title = $this->input->post("title");
        if(empty($title)){
            error_show_log("fail wirte topic confirm: invalid title");
        }

        $topicInfo = array(
            'title' => $title,
            'content' => $this->input->post("content"),
        );

        $this->load->model("Topic_Model");
        $topicId = $this->Topic_Model->add_topic($tablePrefix, $userId, $topicInfo);
        if($topicId !== false){
            redirect("/topic/page?table=$tablePrefix&page=1&id=$topicId");
        } else{
            error_show_log("fail wirte topic confirm: db error");
        }
    }

    public function edit_topic(){
        $userId = $_SESSION['userId'];
        if(empty($userId)){
            error_show_log("fail edit topic page: invalid userId");
        }

        $tablePrefix = $this->input->get("table");
        if(empty($tablePrefix)){
            error_show_log("fail edit topic page: invalid tablePrefix");
        }

        $topicId = $this->input->get("id");
        if(empty($topicId)){
            error_show_log("fail edit topic page: invalid topicId");
        }

        $this->load->model("Forum_Model");
        $forumTitle = $this->Forum_Model->get_forum_title($tablePrefix);
        if($forumTitle === false){
            error_show_log("fail edit topic page: forum not found");
        }

        $this->load->model("Topic_Model");
        $topicInfo = $this->Topic_Model->get_topic_info($tablePrefix, $topicId);
        if($topicInfo === false){
            error_show_log("fail edit topic page: topic not found");
        }

        if($topicInfo->userdbid != $userId){
            error_show_log("fail edit topic page: not have authority");
        }

        $this->load->model("User_Model");
        $viewData = array(
            'tablePrefix' => $tablePrefix,
            'forumTitle' => $forumTitle,
            'nickname' => $this->User_Model->get_user_nickname($_SESSION['userId']),
            'titleMaxLength' => $this->Topic_Model->get_topic_title_max_length($tablePrefix),
            'contentMaxLength' => $this->Topic_Model->get_topic_content_max_length($tablePrefix),
            'topicInfo' => $topicInfo,
            'confirmUrl' => "/topic/edit_topic_confirm",
            'topicInfo' => $topicInfo,
        );

        $writeTopicView = $this->load->view("topic/write_topic", $viewData, true);
        $this->load_common_view($writeTopicView);
    }

    public function edit_topic_confirm(){
        $userId = $_SESSION['userId'];
        if(empty($userId)){
            error_show_log("fail edit topic confirm: invalid userId");
        }

        $tablePrefix = $this->input->post("table");
        if(empty($tablePrefix)){
            error_show_log("fail edit topic confirm: invalid tablePrefix");
        }

        $topicId = $this->input->post("topicId");
        if(empty($topicId)){
            error_show_log("fail edit topic confirm: invalid topidId");
        }

        $title = $this->input->post("title");
        if(empty($title)){
            error_show_log("fail edit topic confirm: invalid title");
        }

        $topicInfo = array(
            'title' => $title,
            'content' => $this->input->post("content"),
        );

        $this->load->model("Topic_Model");
        if($this->Topic_Model->update_topic($tablePrefix, $topicId, $userId, $topicInfo)){
            redirect("/topic/page?table=$tablePrefix&page=1&id=$topicId");
        } else{
            error_show_log("fail wirte topic confirm: db error");
        }
    }

    public function delete_topic(){
        $userId = $_SESSION['userId'];
        if(empty($userId)){
            error_show_log("fail delete topic: invalid userId");
        }

        $tablePrefix = $this->input->get("table");
        if(empty($tablePrefix)){
            error_show_log("fail delete topic: invalid tablePrefix");
        }

        $topicId = $this->input->get("id");
        if(empty($topicId)){
            error_show_log("fail delete topic: invalid topidId");
        }

        $this->load->model("Topic_Model");
        $topicUserId = $this->Topic_Model->get_topic_user_id($tablePrefix, $topicId);
        if($topicUserId == false || $userId != $topicUserId){
            error_show_log("fail delete topic: not have authority");
        }

        if(!$this->Topic_Model->delete_topic($tablePrefix, $topicId)){
            error_show_log("fail delete topic: db error");
        }

        $view = $this->load->view("common/success_page", array( 'message' => "삭제되었습니다."), true);
        $this->load_common_view($view);
    }
}
