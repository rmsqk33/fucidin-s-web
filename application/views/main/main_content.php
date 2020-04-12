<div id='wrap_mainContent'>
    <?php
    foreach($recentContentList as $content){
    ?>
    <div class='recentTopicList'>
        <table cellspacing='0' cellpadding='0'>
            <colgroup>
                <col width='*' />
                <col width='85' />
            </colgroup>
            <tbody>
                <tr class='rt_title'>
                    <td colspan='2'><?=$content->title?></td>
                </tr>
            <?php
            if(empty($content->topicList)){
            ?>
                <tr>
                    <td colspan='2'>최근에 올라온 게시물이 없습니다.</td>
                </tr>
            <?php
            } else{
                foreach($content->topicList as $topic){
                    $diffTimeStr = time_diff_to_str(time() - strtotime($topic->created));
            ?>
                <tr>
                    <td><a href='<?="/topic/page?table={$content->tablePrefix}&page=1&id={$topic->id}"?>'><?=$topic->title?></a></td>
                    <td align='right'><?=$diffTimeStr?></td>
                </tr>
            <?php
                }
            }
            ?>
            </tbody>
        </table>
    </div>
    <?php
    }
    ?>
</div>
