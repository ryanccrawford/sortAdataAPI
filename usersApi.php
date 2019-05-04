<?php
    function users($action,$data){
        $email = $data->email;
        $password = $data->password;
        $zip = $data->zip;
        $is_auth = is_authenticated();
        if($action === "insert"){
            addUser($email,$password,$zip);
        }
        if($action === "update_password"){
            $new_password = $data->newpassword;
            $old_password = $data->password;
            updateUserPassword($email,$old_password,$new_password);
        }
        if($action === "auth_user"){
            authenticate($email,$password);
        }
        if($action === "update_email"){
            $oldEmail = $data->email;
            $newEmail = $data->newemail;
            updateUserEmail($oldEmail,$newEmail,$password);
        }
        if($action === "update_zip"){
            updateUserZip($email,$password,$zip);
        }
        if($action === "get_zip" && strlen($is_auth)){
            $userid = $is_auth;
            getUserZip($userid,$zip);
        }
         if($action === "get_email" && strlen($is_auth)){
            $userid = intval($is_auth);
            getUserEmail($userid);
        }
    }
    // Add a new user to the database, sends the client a JSON message with results.
    function addUser($email, $password, $zip){
        global $response;
        global $dbase;
        if(strlen($zip) != 5 || strlen($email) < 5 || strlen($password) < 4){
            if(strlen($email) < 5){
                $response["error"][] = array("message"=>"bad email address"); 
            }
            if(strlen($zip) != 5){
                 $response["error"][] = array("message"=>"zip code invalid");
            }
            if(strlen($password) < 4){
                 $response["error"][] = array("message"=>"password too short");
            }
            respond($response);
        }
        
        if(!checkUserExisit($email)){
            $encrypt_password = md5($password);
            $sql = "INSERT INTO ". USERS ." (email, password, created_on, zip) VALUES ('" . $email . "', '" . $encrypt_password . "', CURDATE(),'" . $zip . "')";
            $dbase->query($sql);
            $response["user_added"] = $dbase->getInsertedId();
            respond($response);
        }
        $response["error"][] = array("message"=>"user exist");
        respond($response);
    }
    function getUserZip($userid){
        global $response;
        global $dbase;
        $sql = "SELECT zip FROM " . USERS . " WHERE user_id=$userid";
        $dbase->query($sql);
        $result = $dbase->getResults();
        if($result == 0){
           $response["zip"] = array("message"=>"faild");
        }else{
            $_SESSION["zip"] = $result[0]["zip"];
            $response["zip"] = $result[0]["zip"];
        }
       respond($response);
    }
    function getUserEmail($userid){
         global $response;
        global $dbase;
        $sql = "SELECT email FROM " . USERS . " WHERE user_id=$userid";
        $dbase->query($sql);
        $result = $dbase->getResults();
        if($result == 0){
           $response["error"] = array("message"=>"faild");
        }else{
            $_SESSION["email"]= $result[0]["email"];
            $response["email"] = $result[0]["email"];
        }
       respond($response);
    }
    function updateUserZip($email, $password, $zip){
        global $response;
        global $dbase;
        if(strlen($zip) != 5 || strlen($email) < 5 || strlen($password) < 4){
            if(strlen($email) < 5){
                $response["error"][] = array("message"=>"bad email address"); 
            }
            if(strlen($zip) != 5){
                 $response["error"][] = array("message"=>"zip code invalid");
            }
            if(strlen($password) < 4){
                 $response["error"][] = array("message"=>"password too short");
            }
            respond($response);
        }
        $encrypt_password = md5($password);
        $sql = "UPDATE ". USERS ." SET zip=$zip WHERE email=$email AND password=$encrypt_password";
        $dbase->query($sql);
        $response["user_zip"] = array("message"=> $zip);
        respond($response);
    }
    function authenticate($email, $password){
        global $response;
        global $dbase;
        $sql = "SELECT user_id, email FROM " . USERS . " WHERE email='".$email."' AND password='".md5($password)."'";
        $dbase->query($sql);
        $result = $dbase->getResults();
        if($result == 0){
           $response["auth_faild"] = array("message"=>"faild");
        }else{
            $_SESSION["userid"] = $result[0]["user_id"];
            $_SESSION["email"] = $email;
            
            $response["email"] = $email;
            $response["auth_passed"] = array( "email" => $email, "userid" => $result[0]["user_id"]);
        }
       respond($response);

    }
    function is_authenticated(){
        return isset($_SESSION["userid"]) ? $_SESSION["userid"] : false;
    }
    function updateUserEmail($old_email,$new_email,$password){
        global $response;
        global $dbase;
        if(authenticate($old_email, $password, $database)){
            $sql = "UPDATE users SET email='".$new_email."' WHERE email='".$old_email."' AND `password`='".$password."'";
            $dbase->query($sql);
            $result = $dbase->getResults();
            if($result == 0){
                $response["error"][] = array("message"=>"Could Not Update Email");
            }else{
               $response["email_updated"] = $result;
            }
        }else{
            $response["error"][] = array("code",401);
        }
        respond($response);
    }
    function updateUserPassword($email,$old_password,$new_password){
        global $response;
        global $dbase;
        if(authenticate($email, $old_password)){
            $old = md5($old_password);
            $new = md5($new_password);
            $sql = "UPDATE users SET `password`='".$new."' WHERE email='".$email."' AND `password`='".$old."'";
            $dbase->query($sql);
            $result = $dbase->getResults();
            if($result == 0){
                $response["error"][] = array("message"=>"Could Not Update Password");
            }else{
               $response["password_updated"] = $result;
            }
        }else{
            $response["error"][] = array("code",401);
        }
        respond($response);

    }
    function checkUserExisit($email){
        global $dbase;
        $sql = "SELECT email FROM " . USERS . " WHERE email='".$email."' ";
        $dbase->query($sql);
        $result = $dbase->getResults();
        if($result == 0){
            return false;
        }
        return true;
    }
    function resetPassword($email){
        //TODO: create php script for password reset 
        // need to verify an encryption string saved to datbase then sent in email to verify the email account
        // right now this just sends the email 
        $link = "https://fe41a14.online-server.cloud/emailrecovery.php?email=$email&v=$pword";
        $html_message = createEmail($link);
        sendEmail($email, "Password Recovery", $html_message);

    }
    function sendEmail($email, $subject, $html_message){
      
        $mail_headers = "MIME-Version: 1.0" . "\r\n";
        $mail_headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $mail_headers .= "From: <admin@fe41a14.online-server.cloud>" . "\r\n";
        mail($email,$subject,$html_message,$mail_headers);

    }
    function createEmail($link){
        return "
            <html>  
                <head>
                <title>Password Recovery</title>
                </head>
                <body>
                    <h1>Recover your password</h1>
                    <p>Click the following link to reset your password</p>
                    <p><a href='".$link."'>RESET PASSWORD</a></p>
                </body>
            </html>";
    }