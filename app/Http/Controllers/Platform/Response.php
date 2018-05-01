<?php

namespace App\Http\Controllers\Platform;

use App\Http\Controllers\Controller,
    App\Http\Controllers\Platform\Language;

class Response extends Controller
{
    public static function send ($message = '', $type = 'error', $data = array()) // message, 'data', data
    {
          $result = array();

          if($type == 'error'){
              $status = false;
              $result['error_code'] = (new Language)->getErrorCode($message);
          }
          else{$status = true;} // status=true

          if((new Language)->getText($message)){ // message
              $message = (new Language)->getText($message); // 'Login Successfully'
          }

          $result["status"]  = $status; // true
          $result["type"]    = $type; // 'data'
          $result["data"]    = $data; // data()
          $result["message"] = $message; // message
          $result["title"]   = "";

          if($status) {
            return response()->json($result); 
          }
          else{
            response()->json($result)->send();
            die;
          }
    }

    public static function invalidRequest ($access=true, $params=true){
        if($access && !$params){
            return self::send('error_wrong_var');
        }
        else{
            return self::send('error_not_authorized');
        }
    }
}
