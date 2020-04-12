<?php

function error_show_log($error_message, $log_message = ""){
    if(empty($log_message))
        $log_message = $error_message;

    $fullLog = "error_meesage : ".$error_message;
    $fullLog = "log_message : ".$log_message;

    $backtracelist = debug_backtrace();
    for($i = 0; $i < count($backtracelist); ++$i){
        $fullLog .= "backtrace[i] => {\n";
        $fullLog .= "\t\tfile => ".$backtracelist[$i]['file']."\n";
        $fullLog .= "\t\tline => ".$backtracelist[$i]['line']."\n";

        if(isset($backtracelist[$i]['class']))
            $fullLog .= "\t\tclass => ".$backtracelist[$i]['class']."\n";

        if(isset($backtracelist[$i]['function']))
            $fullLog .= "\t\tfunction => ".$backtracelist[$i]['function']."\n";

        if(isset($backtracelist[$i]['args'])){
            $fullLog .= "\t\targs => ".var_export($backtracelist[$i]['args'], true)."\n";
        }

        $fullLog .= "}";
    }

    log_message("error", $fullLog);
    show_error("$error_message");
}

function replaceGetParamInUrl($url, $key, $value){
    $pathInfo = parse_url($url);
    $queryString = $pathInfo['query'];
    parse_str($queryString, $queryArray);
    $queryArray[$key] = $value;
    return $pathInfo['path']."?".http_build_query($queryArray);
}

function str_search($haystack, $needle){
    if(is_array($needle)){
        foreach($needle as $str){
            if(strpos($haystack, $str) === 0){
                return true;
            }
        }
    } else{
        return strpos($haystack, $needle) === 0;
    }
    return false;
}

function time_diff_to_str($diffSec){
    if(0 < (int)($diffSec / 217728000)){
        return (int)($diffSec / 217728000)."년전";
    } else if(0 < (int)($diffSec / 18144000)){
        return (int)($diffSec / 18144000)."달전";
    } else if(0 < (int)($diffSec / 604800)){
        return (int)($diffSec / 604800)."주전";
    } else if(0 < (int)($diffSec / 86400)){
        return (int)($diffSec / 86400)."일전";
    } else if(0 < (int)($diffSec / 3600)){
        return (int)($diffSec / 3600)."시간전";
    } else if(0 < (int)($diffSec / 60)){
        return (int)($diffSec / 60)."분전";
    }

    return ($diffSec % 60)."초전";
}
