<?php
define("USERS","users");
define("LISTS","users_lists");
define("ITEMS", "list_items");
define("API_KEY", "q98ejf-fqwefj-8wefqw8w");
require_once("database.php");
$response = array();
global $response;
$dbase = new database();
global $dbase;
require_once("paramaters.php");
require_once("usersApi.php");
require_once("listsItemsApi.php");
require_once("listsApi.php");
function respond($data){
    session_start();
    global $dbase;
    $dbase->disconnect();
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    echo json_encode($data);
    die;
}



