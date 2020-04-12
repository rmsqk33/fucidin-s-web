<div class='float_left'>
    <div class='wrap_listHead'>
        <table cellpadding='0'>
            <tbody>
                <tr>
                    <td width='70'>번호</td>
                    <td width='*'>제목</td>
                    <td width='160'>작성자</td>
                    <td width='70'>등록일</td>
                    <td width='60'>조회수</td>
                    <td width='80'>추천/반대</td>
                </tr>
            </tbody>
        </table>
    </div>
    <div id='wrap_topicList'>
    <?php
    if(count($topicList) == 0)
    {
    ?>
        <P align='center'>
            게시글이 없습니다.
        </P>
    <?php
    } else{
    ?>
        <table cellpadding='0' >
            <tbody>
    <?php
        $i = 0;
        foreach($topicList as $topic){
    ?>
                <tr>
                    <td width='70' class='text_center'><?=$topic->topicid?></td>
                    <td width='*'>
                        <a href='<?="/topic/page?{$_SERVER['QUERY_STRING']}&id={$topic->topicid}"?>'><?=$topic->title?></a>
                    </td>
                    <td width='160'><img width='15' height='15' src='<?=$topic->iconpath?>'/><?=$topic->nickname?></td>
                    <td width='70' class='text_center'><?=$topic->created?></td>
                    <td width='60' class='text_center'><?=$topic->viewcount?></td>
                    <td width='80' class='text_center'><?=$topic->recommend."/".$topic->unrecommend?></td>
                </tr>
    <?php
            ++$i;
        }
    ?>
            </tbody>
        </table>
    <?php
    }
    ?>
    </div>
</div>
