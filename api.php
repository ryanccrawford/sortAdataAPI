<?php
session_start();

require_once("includes.php");
//Check for api key
if(get_param("apiKey") !== API_KEY){
    
    $error = array("error" => array( "message" => "Invalid or missing Api Key"));
    respond($error);
}

$api = get_param("api");
$action = get_param("action");
$data = get_param("data", true);
If(!strlen($action)){
    $response["error"] = array("message" => "missing action");
    respond($response);
}

doaction($action,$api,$data);


function doaction($_action,$_api,$_data){
global $response;
global $dbase;
    switch ($_api) {
        case "users":
        users($_action,$_data);
        case "lists":
        lists($_action,$_datab);                                
        case "listsItems":
        listsItems($_action,$_data);
        case "items":
        items($_action,$_data);
        case "custom":
        custom($_action,$_data);
        default:
            $response["error"] = array("message"=>"api value missing");
            respond($response);
    }
}

function custom($_action,$_data){
    global $response, $dbase;
    switch ($_action){
        case "select":
        $fields = implode(',', $_data->fields);
        $sql = "SELECT " .  $fields . " FROM " . $_data->table;
        if(isset($_data->where)){
                
         }
            break;
            case "update":
            break;
        case "insert":
        break;
        case "delete":
        break;
        
        
    }
    $dbase->query($_action);
    
    
}