<script>
function on_write_topic_submit(){
    if(is_empty_str(document.writeTopicForm.title.value)){
        alert("제목을 입력해주세요.");
        return false;
    }

    return true;
}
</script>

<div id='wrap_writeTopic'>
    <img src='/data/image/right_arrow.png'/>
    <span>글쓰기</span>
    <?=form_open("$confirmUrl", array( 'onsubmit' => "return on_write_topic_submit();", 'name' => "writeTopicForm", 'autocomplete' => "off" ))?>
        <input type='hidden' name='table' value='<?=$tablePrefix?>' />
        <?php
        if(isset($topicInfo)){
            echo "<input type='hidden' name='topicId' value='$topicInfo->id' />";
        }
        ?>
        <table cellpadding='0' cellspacing='0'>
            <tbody>
                <tr>
                    <td class='t_left'>게시판</td>
                    <td><?=$forumTitle?></td>
                </tr>
                <tr>
                    <td class='t_left'>닉네임</td>
                    <td><?=$nickname?></td>
                </tr>
                <tr>
                    <td class='t_left'>제목</td>
                    <td>
                        <input class='title' type='text' name='title' maxlength='<?=$titleMaxLength?>' value='<?php isset($topicInfo) ? print($topicInfo->title) : "" ?>'/>
                        <sapn>(<?=$titleMaxLength?>자 이내)</sapn>
                    </td>
                </tr>
                <tr>
                    <td colspan='2' class='content'>
                        <textarea name='content' cols='99' rows='70' maxlength='<?=$contentMaxLength?>'><?php isset($topicInfo) ? print($topicInfo->content) : "" ?></textarea>
                    </td>
                </tr>
            </tbody>
        </table>
        <div class='writeTopicBtn'>
            <input class='anchor_button' type='submit' value='확인' />
            <a class='anchor_button' href='/'>취소</a>
        </div>
    </form>
</div>
