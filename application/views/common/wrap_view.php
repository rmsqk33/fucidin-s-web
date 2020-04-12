<?php
if(empty($tag))
    $tag = "div";
?>

<<?=$tag?>
<?php
if(!empty($id)){
    echo " id='$id'";
}

if(!empty($class)){
    echo " class='$class'";
}
?>
>

<?php
foreach($viewList as $view){
    echo $view;
}
?>


</<?=$tag?>>
