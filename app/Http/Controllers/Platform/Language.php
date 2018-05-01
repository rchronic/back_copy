<?php

namespace App\Http\Controllers\Platform;

use App\Http\Controllers\Controller;

class Language extends Controller
{

    protected $messages = array(
        "content_user_list"        => array("en" => "User List"),
        "content_user_permissions" => array("en" => "User Permissions"),

        "error_link_expired"       => array("_err" => "E3101", "en" => "Link Expired"),
        "error_login_failed"       => array("_err" => "E2001", "en" => "User or Password Wrong"),
        "error_not_authorized"     => array("_err" => "E1001", "en" => "Access Forbidden"),
        "error_token_expired"      => array("_err" => "E1002", "en" => "Token Expired"),
        "error_property_already_exist" => array("_err" => "E3003", "en" => "Property Already Existing"),
        "error_property_not_exist"     => array("_err" => "E3002", "en" => "Property Not Existing"),
        "error_user_already_exist" => array("_err" => "E3003", "en" => "User Already Existing"),
        "error_user_not_exist"     => array("_err" => "E3002", "en" => "User Not Existing"),
        "error_user_permission_failed" => array("_err" => "E3201", "en" => "Updating User's Permission Failed"),
        "error_user_suspended"     => array("_err" => "E2002", "en" => "User is Suspended"),
        "error_wrong_email"        => array("_err" => "E2003", "en" => "Wrong Email"),
        "error_wrong_password"     => array("_err" => "E3001", "en" => "Wrong Password"),
        "error_wrong_var"          => array("_err" => "E3001", "en" => "Wrong Parameters"),

        "login_success"            => array("en" => "Login Successful"),
        "request_success"          => array("en" => "Request Success"),
        "reset_success"            => array("en" => "Reset Success"),
        "user_already_loggedin"    => array("en" => "User already logged in"),
        "user_created"             => array("en" => "User Created"),
        "user_edited"              => array("en" => "User Edited"),
        "user_permission_updated"  => array("en" => "User Permissions Updated"),
        "user_removed"             => array("en" => "User Removed")
    );

    protected $logMessages = array(
        "create_new_property"   => array("en" => "%n created a new property"),
        "create_new_role"       => array("en" => "%n created a new role"),
        "create_new_user"       => array("en" => "%n created a new user"),
        "edit_property"         => array("en" => "%n edited information of a property"),
        "edit_role"             => array("en" => "%n edited information of a role"),
        "edit_user"             => array("en" => "%n edited a user's information"),
        "login"                 => array("en" => "%n logged in"),
        "remove_property"       => array("en" => "%n removed a property"),
        "remove_role"           => array("en" => "%n removed a role"),
        "remove_user"           => array("en" => "%n removed a user"),
        "reset_password_request"  => array("en" => "%n requested to reset his password"),
        "update_user_permissions" => array("en" => "%n updated the permissions of a user"),
        "user_suspended"        => array("en" => "%n got suspended"),
        "wrong_password"        => array("en" => "%n used a wrong password")
    );

    public function __construct()
    {

    }

    /**
     * Login system.
     *
     * @param  int  $uid
     * @param  str  $action
     * @return none
     */

    public function getText ($target, $lang='en'){ // target=message
        if (array_key_exists($target, $this->messages)){ // target=message
            $result = $this->messages[$target][$lang]; // target=message,lang=en,return: 'Login Successfully'
        }
        else{
            $result = false;
        }

        return $result;
    }

    public function getErrorCode ($target){
        if (array_key_exists($target, $this->messages)){
            $result = $this->messages[$target]['_err'];
        }
        else{
            $result = false;
        }

        return $result;
    }

    public function getLogText ($target, $lang='en'){
        $target = strtolower(str_replace(' ', '_', substr($target, 10)));

        if (array_key_exists($target, $this->logMessages)){
            $result = $this->logMessages[$target][$lang];
        }
        else{
            $result = false;
        }

        return $result;
    }
 }
