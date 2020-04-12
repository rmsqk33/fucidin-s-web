<?php
$writeTopicBtnUrl = "";
if(isset($_SESSION['userId'])){
    $writeTopicBtnUrl = "/topic/write_topic?table=$tablePrefix";
} else{
    $writeTopicBtnUrl = "/user/login";
}
?>

<script>
function on_search_submit(){
    if(is_empty_str(document.searchForm.keyword.value)){
        alert("검색어를 입력해주세요.");
        return false;
    }

    return true;
}
</script>

<div id='wrap_topicSearch'>
    <div class='writeTopicBtn'>
        <a class='align_middle' href='<?=$writeTopicBtnUrl?>'>
            <img src='/data/image/write_topic_btn.png' width='57'/>
        </a>
    </div>
    <div class='searchForm'>
        <?=form_open("/forum/page", array('name' => "searchForm", 'method' => "GET", 'onsubmit' => "return on_search_submit();"))?>
            <input type='hidden' name='table' value='<?=$tablePrefix?>' />
            <input type='hidden' name='page' value='1' />
            <select name='searchFilter' value='<?=$this->input->get('searchFilter')?>'>
                <?php
                $selectedOption = $this->input->get('searchFilter');
                $html = "";
                foreach($searchFilterList as $key => $value){
                    if(isset($selectedOption) && $selectedOption == $key){
                        $html .= "<option value='$key' selected='selected'>";
                    } else{
                        $html .= "<option value='$key'>";
                    }
                    $html .= $value;
                    $html .= "</option>";
                }
                echo $html;
                ?>
            </select>
            <input type='text' name='keyword' maxlength='50' value='<?=$this->input->get('keyword')?>'/>
            <select name='dateFilter'>
                <?php
                $selectedOption = $this->input->get('dateFilter');
                $html = "";
                foreach($dateFilterList as $key => $value){
                    if(isset($selectedOption) && $selectedOption == $key){
                        $html .= "<option value='$key' selected='selected'>";
                    } else{
                        $html .= "<option value='$key'>";
                    }
                    $html .= $value;
                    $html .= "</option>";
                }
                echo $html;
                ?>
            </select>
            <input class='align_middle' type='image' src='/data/image/search.png' width='34'/>
        </form>
    </div>
</div>
