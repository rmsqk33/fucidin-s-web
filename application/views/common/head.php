<?php
if(!isset($_SERVER['PATH_INFO']) || !str_search($_SERVER['PATH_INFO'], $this->config->item("ignore_save_prev_url"))){
    $_SESSION['prevUrl'] = $_SERVER["REQUEST_URI"];
}
?>

<DOCTYPE html>
<html>
    <head>
        <title><?=$headTitle?></title>
        <meta charset='utf-8' />
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel='stylesheet' type='text/css' href='/data/css/defaultstyle.css' />
        <script src="https://ajax.aspnetcdn.com/ajax/jQuery/jquery-3.4.1.min.js"></script>
        <script src='/javascript/util.js'></script>
    </head>
    <body>
        <div id='wrap_all'>
