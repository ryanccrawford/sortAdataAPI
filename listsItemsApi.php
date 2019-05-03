<?php
    
    function listitems($action,$data){
        global $response;
        $listid = intval($data->listid);
        $userid =  intval($_SESSION["userid"]);
        if(!$userid){
            $userid = intval($data->userid);
        }
        if(!$userid){
            $response["auth_error"] = array("message"=>"user not logged in");
            respond($response);
        }else{
            
            if($action === "add_item"){
                addItem($userid, $listid);
            }
            if($action === "get_item"){
                getItem($userid, $listid, $itemid);
            }
            if($action === "get_items"){
                getItems($userid, $listid);
            }
            if($action === "remove_item"){
                removeItem($userid, $listid, $itemid);
            }
            if($action === "remove_items"){
                removeItems($userid, $listid);
            }
            if($action === "countListItems"){
                countListItems($userid, $listid);
            }  
        }
            respond($response);
    }
    // Add new item to list
    function addItem($userid, $listid, $itemname){
        global $response;
        global $dbase;
       $category = isset($data->walmartcategory)?$data->walmartcategory:null;
       $categoryid = isset($data->walmartcategoryid)?$data->walmartcategoryid:null;
       // item_id, , user_id, name, upc, walmart_id, walmart_category, walmart_category_id, walmart_price
        $sql = "INSERT INTO ". ITEMS ." (list_id, user_id, name, walmart_category_id, walmart_category) VALUES ($listid, $userid, '". $itemname ."', $categoryid, '".$category."')";
        $dbase->query($sql);
        $response["item_added"]["itemid"] = $dbase->getInsertedId();
        $response["item_added"]["listid"] = $listid;
    }
    // Get item from list 
    function getItem($userid, $listid, $itemid){
        global $response;
        global $dbase;
        $sql = "SELECT * FROM ". ITEMS ." WHERE user_id=$userid AND list_id=$listid AND item_id=$itemid";
        $dbase->query($sql);
        $result = $dbase->getResults();
        $response["item"] = $result;
    }
    // Get all items from list
    function getItems($userid, $listid){
        global $response;
        global $dbase;
        $sql = "SELECT * FROM ". ITEMS ." WHERE user_id=$userid AND list_id=$listid";
        $dbase->query($sql);
        $result = $dbase->getResults();
        $response["items"] = $result;
    }
    // Get count of all items from list
    function countListItems($userid, $listid){
        global $response;
        global $dbase;
        $sql = "SELECT COUNT(*) FROM ". ITEMS ." WHERE user_id=$userid AND list_id=$listid";
        $dbase->query($sql);
        $result = $dbase->getResults();
        $response["count"] = $result;
    }
   // Remove Item from list 
    function removeItem($userid, $listid, $itemid){
        global $response;
        global $dbase;
        $sql = "DELETE FROM ". ITEMS ." WHERE user_id=$userid AND list_id=$listid AND item_id=$itemid";
        $dbase->query($sql);
        $result = $dbase->getResults();
         if($result){
            $response["removed"] = $result;
         }else{
            $response["error"][] = array("message"=>"delete faild");
         }
    }
    // Remove all items from Lists 
    function removeItems($userid, $listid){
       global $response;
       global $dbase;
        $sql = "DELETE FROM ". ITEMS ." WHERE user_id=$userid AND list_id=$listid";
        $dbase->query($sql);
        $result = $dbase->getResults();
         if($result){
            $response["removed"] = $result;
         }else{
            $response["error"][] = array("message"=>"delete faild");
         }
    }