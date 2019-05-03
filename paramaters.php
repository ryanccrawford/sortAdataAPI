<?php

function get_param($parma_name,$is_json = false){
    if($is_json){
        
        $json = file_get_contents("php://input");
        //echo $json;
        $data = json_decode($json);
        return $data;
        
    }
    return is_null($_GET[$parma_name]) ? false: $_GET[$parma_name];
}