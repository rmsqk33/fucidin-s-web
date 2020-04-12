<?php
$forumUrl = "/forum/page?".$_SERVER['QUERY_STRING'];
$writeCommentUrl = "";
$commentSubmitBtnImg = "";
$userId = null;

if(empty($_SESSION['userId'])){
    $commentSubmitBtnImg = "/data/image/gotoLogin.png";
    $writeCommentUrl = "/user/login/";
} else{
    $writeCommentUrl = "/topic/write_comment";
    $commentSubmitBtnImg = "/data/image/commentSubmit.png";
    $userId = $_SESSION['userId'];
}

function generate_nearTopicInfo($topicInfo){
    $html = "";
    $html .= "<td width='*'>";

    if(empty($topicInfo)){
        $html .= "다음글이 없습니다.</td>";
    } else{
        $url = replaceGetParamInUrl($_SERVER['REQUEST_URI'], "id", $topicInfo->id);
        $created = date("Y-m-d [H:i]", strtotime($topicInfo->created));

        $html .= "<a href='$url'>$topicInfo->title</a></td>";
        $html .= "<td>";
        $html .= "  <img width='10' src='/data/image/recommend_hand.png'/>";
        $html .= "  <span>$topicInfo->recommend</span>";
        $html .= "</td>";
        $html .= "<td>";
        $html .= "  <img width='10' src='/data/image/unrecommend_hand.png'/>";
        $html .= "  <span>$topicInfo->unrecommend</span>";
        $html .= "</td>";
        $html .= "<td>";
        $html .= "  <img width='10' src='/data/image/topic_view_count.png'/>";
        $html .= "  <span>$topicInfo->viewcount</span>";
        $html .= "</td>";
        $html .= "<td align='center'>$created</td>";
    }

    echo $html;
}
?>

<script src='/javascript/topic_page.js'></script>
<div id='wrap_topicPage'>
    <input id='userId' type='hidden' value='<?=$userId?>'/>
    <input id='table' type='hidden' value='<?=$tablePrefix?>'/>
    <input id='csrf_hash' type='hidden' value='<?=$this->security->get_csrf_hash()?>'/>
    <div id='wrap_topicTitle' class='bgColor_gray'>
        <table>
            <tbody>
                <tr>
                    <td height='26' class='topicInfo'>
                        <span>제목</span>
                        <span class='text_bold bp_right_tri'><?=$currentTopic->title?></span>
                    </td>
                </tr>
                <tr>
                    <td height='26' class='topicInfo'>
                        <span>작성자</span>
                        <img class='bp_right_tri' width='15' height='15' src='<?=$userInfo->iconpath?>' />
                        <span><?=$userInfo->nickname?></span>
                    </td>
                </tr>
                <tr>
                    <td height='52' class='topicInfo'>
                        <div>
                            <span>번호</span>
                            <span class='bp_right_tri text_bold'><?=$currentTopic->id?></span>
                            <span>추천</span>
                            <span id='topic_recommend' class='color_red bp_right_tri'><?=$currentTopic->recommend?></span>
                            <span>반대</span>
                            <span id='topic_unrecommend' class='color_blue bp_right_tri'><?=$currentTopic->unrecommend?></span>
                            <span>답글</span>
                            <span class='color_red bp_right_tri'><?=$commentCount?></span>
                            <span>조회</span>
                            <span class='text_bold bp_right_tri'><?=$currentTopic->viewcount?></span>
                            <div>
                                작성시간
                                <span class='text_bold bp_right_tri'><?=$currentTopic->created?></span>
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td height='26' id='topicBtnList'>
                        <img id='prevTopicBtn' src='/data/image/prevTopicBtn.png' />
                        <img id='nextTopicBtn' src='/data/image/nextTopicBtn.png' />
                        <a href='<?=replaceGetParamInUrl($forumUrl, "id", null)?>'><img src='/data/image/gotoTopicListBtn.png' /></a>
                        <a href='javascript:on_recommend_topic(<?=$currentTopic->id?>)'><img src='/data/image/recommendBtnBig.png' /></a>
                        <a href='javascript:on_unrecommend_topic(<?=$currentTopic->id?>)'><img src='/data/image/unrecommendBtnBig.png' /></a>
                        <?php
                        if($userId == $currentTopic->userdbid){
                            echo "<a href='/topic/edit_topic?table=$tablePrefix&id=$currentTopic->id'><img src='/data/image/topicEditBtn.png'/></a>";
                            echo "<a href='/topic/delete_topic?table=$tablePrefix&id=$currentTopic->id'><img src='/data/image/topicDelBtn.png'/></a>";
                        }
                        ?>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <div id='wrap_topicContent'>
        <?=$currentTopic->content?>
    </div>
    <div id='wrap_nearTopicInfo'>
        <table>
            <colgroup>
                <col width='15' />
                <col width='60' />
                <col width='*' />
                <col width='40' />
                <col width='40' />
                <col width='50' />
                <col width='100' />
            </colgroup>
            <tbody>
                <tr>
                    <td><img width='15' src='/data/image/up_arrow.png'/></td>
                    <td>다음글</td>
                    <?=generate_nearTopicInfo($nextTopic)?>
                </tr>
                <tr>
                    <td><img width='15' src='/data/image/down_arrow.png'/></td>
                    <td>이전글</td>
                    <?=generate_nearTopicInfo($prevTopic)?>
                </tr>
            </tbody>
        </table>
    </div>
    <div id='wrap_commentAll'>
        <div id='wrap_commentTitle' class='comment_bottom'>
            <table>
                <tbody>
                    <tr>
                        <td width='18'>
                            <img class='float_left' src='/data/image/right_arrow.png'/>
                        </td>
                        <td>답글(<?=count($commentList)?>)</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div id='wrap_commentList'>
            <table cellspacing='0'>
                <tbody>
                <?php
                foreach($commentList as $comment){
                    $isRecomment = $comment->commentdbid != null;
                    $trClass = $isRecomment ? "topicRecomment" : "topicComment";
                ?>
                    <tr height='30' class='<?=$trClass?>'>
                        <td width='150' class='comment_bottom'>
                            <?php
                            if($isRecomment){
                                echo "<img class='float_left' src='/data/image/recommentBP.png' width='21' height='8' />";
                            }
                            ?>

                            <img class='float_left' src='<?=$comment->iconpath?>' width='20' height='20'/>
                            <div class='float_left'><?=$comment->nickname?></div>
                        </td>
                        <td width='*' class='comment_bottom color_gray01'><?=$comment->content?></td>
                        <td width='50' class='comment_bottom'>
                            <span id='comment_recommend_<?=$comment->cid?>' class='color_red'><?=$comment->recommend?></span>
                            <span id='comment_unrecommend_<?=$comment->cid?>' class='color_blue'><?=$comment->unrecommend?></span>
                        </td>
                        <td width='80' class='comment_bottom'>
                            <a href='javascript:on_recommend_comment(<?=$comment->cid?>)'>
                                <img src='/data/image/recommendBtn.png' width='15' height='15'/>
                            </a>
                            <a href='javascript:on_unrecommend_comment(<?=$comment->cid?>)'>
                                <img src='/data/image/unrecommendBtn.png' width='15' height='15'/>
                            </a>
                            <?php
                            if(!$isRecomment){
                            ?>
                            <a href='javascript:on_toggle_recomment_input(<?=$comment->cid?>)'>
                                <img src='/data/image/recommentBtn.png' width='15' height='15'/>
                            </a>
                            <?php
                            }

                            if($comment->userdbid == $userId){
                                echo form_open("/topic/delete_comment", array('onsubmit' => "return on_del_comment_submit($comment->userdbid);", 'style' => "display:inline;"));
                            ?>
                                    <input type='hidden' name='table' value='<?=$tablePrefix?>'/>
                                    <input type='hidden' name='commentId' value='<?=$comment->cid?>'/>
                                    <input type='image' src='/data/image/commentDel.png' width='15' height='15'/>
                                </form>
                            <?php
                            }
                            ?>
                        </td>
                        <td width='100' class='comment_bottom' align='center'><?=$comment->comCreated?></td>
                    </tr>
                    <?php
                    if(!$isRecomment){
                    ?>
                    <tr id='recomment_<?=$comment->cid?>' style='display: none;'>
                        <td class='auto_colspan'>
                            <?=form_open($writeCommentUrl, array('onsubmit' => "return on_comment_submit(this)", 'autocomplete' => "off"))?>
                                <input type='hidden' name='commentId' value='<?=$comment->cid?>'/>
                                <input type='hidden' name='table' value='<?=$tablePrefix?>'/>
                                <input type='hidden' name='topicId' value='<?=$currentTopic->id?>' />
                                <table class='commentInput'>
                                    <tbody>
                                        <tr>
                                            <td class='bp_right_tri' width='75'>
                                                <p>답글쓰기</p>
                                                <p class='text_font_12'>한글 <?=$commentInputLength/2?>자</p>
                                            </td>
                                            <td width='*' align='center'>
                                                <input class='input_text' type='text' name='content' maxlength='<?=$commentInputLength?>' size='73'/>
                                            </td>
                                            <td align='right'>
                                                <input type='image' width='70' height='20' src='<?=$commentSubmitBtnImg?>' />
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </form>
                        </td>
                    </tr>
                <?php
                    }
                }
                ?>
                </tbody>
            </table>
        </div>
        <div id='wrap_commentInput'>
            <div id='commentMsg'>
                <div class='bp_right_tri'>
                    상대방에 대한 배려는 네티켓의 기본입니다.게시물에 상관없는 댓글이나 추천유도성 댓글을 달지 마세요.
                </div>
                <div class='bp_right_tri'>
                    스포일러성 답글이 신고되거나 발견되면 이유불문 삭제 혹은 정학처리 됩니다. 유의 부탁 드립니다.
                </div>
            </div>
            <?=form_open($writeCommentUrl, array('onsubmit' => "return on_comment_submit(this)", 'autocomplete' => "off"))?>
                <input type='hidden' name='table' value='<?=$tablePrefix?>'/>
                <input type='hidden' name='topicId' value='<?=$currentTopic->id?>' />
                <table class='commentInput'>
                    <tbody>
                        <tr>
                            <td class='bp_right_tri' width='75'>
                                <p>답글쓰기</p>
                                <p class='text_font_12'>한글 <?=$commentInputLength/2?>자</p>
                            </td>
                            <td align='center'>
                                <input type='text' name='content' maxlength='<?=$commentInputLength?>' size='73'/>
                            </td>
                            <td align='right'>
                                <input type='image' width='70' height='20' src='<?=$commentSubmitBtnImg?>' />
                            </td>
                        </tr>
                    </tbody>
                </table>
            </form>
        </div>
    </div>
</div>

<script>
if("<?=isset($nextTopic)?>"){
    var url = "<?=replaceGetParamInUrl($_SERVER['REQUEST_URI'], 'id', isset($nextTopic) ? $nextTopic->id : null)?>";
    $('#nextTopicBtn').wrap("<a href='" + url + "'></a>");
}

if("<?=isset($prevTopic)?>"){
    var url = "<?=replaceGetParamInUrl($_SERVER['REQUEST_URI'], 'id', isset($prevTopic) ? $prevTopic->id : null)?>";
    $('#prevTopicBtn').wrap("<a href='" + url + "'></a>");
}
</script>
