<script>
window.onload = function(){
    var prevPageUrl = "<img src='/data/image/prevBtn.png' width='40'/>";
    if("<?=$enablePrev?>"){
        var url = "<?=replaceGetParamInUrl($_SERVER['REQUEST_URI'], "page", $pageStartNum - 1)?>";
        prevPageUrl = "<a href='" + url + "'>" + prevPageUrl + "</a>";
    }
    $('#wrap_paging').prepend(prevPageUrl);

    var nextPageUrl = "<img src='/data/image/nextBtn.png' width='40'/>";
    if("<?=$enableNext?>"){
        var url = "<?=replaceGetParamInUrl($_SERVER['REQUEST_URI'], "page", $pageEndNum + 1)?>";
        nextPageUrl = "<a href='"+ url +"'>" + nextPageUrl + "</a>";
    }
    $('#wrap_paging').append(nextPageUrl);
}
</script>

<div id='wrap_paging'>
    <?php
    for($i = $pageStartNum; $i <= $pageEndNum; ++$i){
        $url = replaceGetParamInUrl($_SERVER['REQUEST_URI'], "page", $i);
        if($i == $currentPage){
            echo "<a href='$url'><span class='page current_page'>{$i}</span></a>";
        } else{
            echo "<a href='$url'><span class='page'>{$i}</a>";
        }
    }
    ?>
</div>
