<?php

$wrongPlace = array(
    'error' => array('message' => 'cannot access this server directly. API access only'));
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
echo json_encode($wrongPlace);
die;




