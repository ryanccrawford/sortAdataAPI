<?php 

    function lists($action,$data){
        global $response;
        $listid = intval($data->listid);
        $userid =  intval($_SESSION["userid"]);
        if(!$userid){
            $userid = intval($data->userid);
        }
        if(!$userid){
             $response["auth_error"] = array("message" => "user not logged in");
             respond($response);
        }
            if($action === "add_list"){
                $name = $data->listname;
                addlist($userid,$name);
            }
            if($action === "get_list"){
                getUserList($userid,$listid);
            }
            if($action === "get_lists"){
                getUserLists($userid);
            }
            if($action === "update_list_name"){
                $new_name = $data->listname;
                updateUserListName($userid,$listid,$new_name);
            }
            if($action === "remove_list"){
                removeUserList($userid, $listid);
            }
            if($action === "remove_lists"){
                removeUsersLists($userid);
            }  
            if($action === "getAllListsForUser"){
                getAllListsForUser($userid);
            }  
            if($action === "get_list_items"){
                get_list_items($userid, $listid);
            }  
         respond($response);
        }
           
    
    // Add new list to database
    function addlist($userid, $listname){
        global $response;
        global $dbase;
        $sql = "INSERT INTO ". LISTS ." (list_id, user_id, name, created_on) VALUES (NULL, $userid, '". $listname ."', CURDATE())";
        $dbase->query($sql);
        $response["list_added"] = $dbase->getInsertedId();
        respond($response);
    }
    // Get List from database kinda point less to have this
    function getUserList($userid, $listid){
        global $response;
        global $dbase;
        $sql = "SELECT * FROM ". LISTS ." WHERE user_id=$userid AND list_id=$listid";
        $dbase->query($sql);
        $result = $dbase->getResults();
        $response["list"] = $result;
        respond($response);
    }
    function get_list_items($userid, $listid){
         global $response;
        global $dbase;
        $sql = "SELECT * FROM ". ITEMS ." WHERE user_id=$userid AND list_id=$listid";
        $dbase->query($sql);
        $result = $dbase->getResults();
        $response["list_items"] = $result;
        respond($response);
    }
    // Get all users list
    function getUserLists($userid){
        global $response;
        global $dbase;
        $sql = "SELECT * FROM ". LISTS ." WHERE user_id=$userid";
        $dbase->query($sql);
        $result = $dbase->getResults();
        $response["lists"] = $result;
        respond($response);
    }
    // Update List from database
    function updateUserListName($userid, $listid, $listname){
        global $response;
        global $dbase;
        $sql = "UPDATE ". LISTS ." SET name='".$listname."' WHERE user_id=$userid AND list_id=$listid";
        $dbase->query($sql);
        $result = $dbase->getResults();
        $response["updated"] = $result;
        respond($response);

    }
    // Remove List from database
    function removeUserList($userid, $listid){
        global $response;
        global $dbase;
        $sql1 = "DELETE FROM ". LISTS ." WHERE user_id=$user_id AND list_id=$listid";
        $dbase->query($sql1);
        $result1 = $dbase->getResults();
        if($result1){
            $sql2 = "DELETE FROM ". ITEMS ." WHERE user_id=$user_id AND list_id=$listid";
            $dbase->query($sql2);
            $result2 = $dbase->getResults();
            if($result2){
                $response["removed"] = array($result2,$result2);
            }
        }else{
            $response["error"][] = array("message"=>"delete faild");
        }
        respond($response);
    }
    function getAllListsForUser($userid){
        global $response;
        global $dbase;
        $sql = "SELECT * FROM users_lists JOIN list_items on users_lists.user_id=list_items.user_id WHERE users_lists.user_id=$userid";
        $dbase->query($sql);
        $data_lists = $dbase->getResults();
        if(count($data_lists) > 0){
            $response["lists"] = $data_lists;
            respond($response);
        }
        $response["list"] = array("message" =>"No List");
        respond($response);
    }
    // Remove all users Lists and items from database
    function removeUsersLists($userid){
        global $response;
        global $dbase;
        $sql1 = "DELETE FROM ". LISTS ." WHERE user_id=$userid";
        $dbase->query($sql1);
        $result1 = $dbase->getResults();
        if($result1){
            $sql2 = "DELETE FROM ". ITEMS ." WHERE user_id=$userid";
            $dbase->query($sql2);
            $result2 = $dbase->getResults();
            if($result2){
                $response["removed"] = array($result2,$result2);
            }
        }else{
            $response["error"][] = array("message"=>"delete faild");
        }
    }