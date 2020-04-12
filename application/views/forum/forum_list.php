
<div id='wrap_forum'>
<?php
foreach($forumCategoryList as $forumCategory){
?>
    <dl>
        <dt class='text_bold' style='color:#<?=dechex($forumCategory->titleColor)?>'><?=$forumCategory->title?></dt>
        <?php
        foreach($forumCategory->forumList as $forum){
            $html = "";
            $html .= "<dd>";

            if(!empty($forum->iconPath)){
                $html .= "<img width='15' height='15' src='=$forum->iconPath'/>";
            }

            $html .= "<a href='/forum/page?table={$forum->tablePrefix}&page=1'>$forum->title</a>";
            $html .= "</dd>";
            echo $html;
        }
        ?>
    </dl>
<?php
}
?>
</div>
