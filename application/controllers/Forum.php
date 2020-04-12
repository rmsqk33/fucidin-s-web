<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Forum extends MY_Controller{
    private $tablePrefix = "";
    private $maxPage = 0;
    private $currentPage = 0;
    private $topicList = array();

    public function page(){
        $this->tablePrefix = $this->input->get('table');
        if(empty($this->tablePrefix)){
            error_show_log("fail forum page : invalid tablePrefix");
        }

        $this->currentPage = $this->input->get('page');
        if(empty($this->currentPage)){
            $this->currentPage = 1;
        }

        $this->load->model("IniDataReader");

        $searchDatas = array();
        $searchKeyword = $this->input->get("keyword");
        if(isset($searchKeyword)){
            $searchDatas['keyword'] = $searchKeyword;
            $searchDatas['searchFilter'] = $this->get_valid_filter($this->input->get("searchFilter"), $this->IniDataReader->get_search_topic_filter_list());
            $searchDatas['dateFilter'] = $this->get_valid_filter($this->input->get("dateFilter"), $this->IniDataReader->get_search_topic_date_filter_list());
        }

        $maxTopicOnce = $this->IniDataReader->get_topic_data("maxTopicOnce");

        $this->load->model("Topic_Model");
        $topicInfo = $this->Topic_Model->get_topic_info_list($this->tablePrefix, $this->currentPage, $maxTopicOnce, $searchDatas);
        $this->topicList = $topicInfo->topicList;
        $this->maxPage = $topicInfo->maxPage;

        $this->load_topic_all();
	}

    private function get_valid_filter($filter, $filterList){
        if(isset($filterList[$filter])){
            return $filter;
        }

        $firstKey = array_key_first($filterList);
        return $filterList[$firstKey];
    }

    private function load_topic_all(){
        $topicListView = $this->load_topic_list();
        $pagingView = $this->load_paging();
        $searchView = $this->load_search();
		$forumView = ($this->load->view("common/wrap_view", array(
			'id' => "wrap_forumPage",
            'class' => null,
            'viewList' => array( $topicListView, $pagingView, $searchView),
		), true));

        $this->load_common_view($forumView);
    }

    private function load_topic_list(){
		$topicListView = $this->load->view("forum/topic_list", array(
			'topicList' => $this->topicList,
		), true);

        return $topicListView;
    }

    private function load_paging(){
        $maxPage = $this->maxPage;
        $currentPage = $this->currentPage;

        $maxPageOnce = $this->IniDataReader->get_topic_data("maxPageOnce");
        $pageStartNum = max(1, (int)(($currentPage-1) / $maxPageOnce) * $maxPageOnce + 1);
        $pageEndNum = min($maxPage, $pageStartNum + $maxPageOnce - 1);
        $enablePrev = $maxPageOnce < $currentPage;
        $enableNext = $maxPageOnce < $maxPage && $currentPage <= $maxPage - ($maxPage % $maxPageOnce);

        $pagingView = $this->load->view("forum/paging", array(
			'pageStartNum' => $pageStartNum,
			'pageEndNum' => $pageEndNum,
			'currentPage' => $currentPage,
			'enablePrev' => $enablePrev,
			'enableNext' => $enableNext,
		), true);

        return $pagingView;
    }

    private function load_search(){
        $searchView = $this->load->view("forum/search", array(
            'searchFilterList' => $this->IniDataReader->get_search_topic_filter_list(),
            'dateFilterList' => $this->IniDataReader->get_search_topic_date_filter_list(),
            'tablePrefix' => $this->tablePrefix,
        ), true);
        return $searchView;
    }
}
