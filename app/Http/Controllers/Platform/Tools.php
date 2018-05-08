<?php

namespace App\Http\Controllers\Platform;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Platform\Response;

class Tools extends Controller {
    public function setDefaultDB($type='_vps') {
        if($type == 'local') {
            $type = '_local';
        } else {
            $type = '_vps';
        }

        Config::set('database.default', 'general'.$type); // db.default = general_vps
        DB::reconnect('general'.$type); // general_vps
        return $type;
    }

    public function setDB($db, $type='_vps') { // company_fnb_test, _vps
        if($type == 'local') {
            $type = '_local';
        } elseif($type == 'breez') {
            $type = '_breez';
        } else {
            $type = '_vps';
        }

        Config::set('database.connections.company'.$type.'.database',$db); // database company_vps di-replace dengan company
        Config::set('database.default','company'.$type); // db.default = company_vps
        DB::reconnect('company'.$type); // company_vps
    }

    public function generateToken($db) { // 4r1fk
        return $this->generateString(32).'-'.$db; // randomString-4r1fk
    }

    public function generateString($length) { // 32
        $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';

        $string = '';
        $max = strlen($characters) - 1;
        for($i = 0; $i < $length; $i++) {
            $string .= $characters[mt_rand(0,$max)];
        }
        
        return $string;
    }

    public static function params($request, $params=[]){
        $result = [];
        $valid = true;
        if(count($params)>0){
            foreach ($params as $row) {
                $param = $row[0];
                $rules  = explode("|",$row[1]);
  
                if($request->has($param)){
                    // Check if format is valid.
                    // If so, it will then format it to the needed format
                    $value = $request->input($param);
                    if(self::matchingFormat($value,$rules)){$result[$param]=self::format($value,$rules);}
                    else{$valid=false;}
                }
                else{
                    // Check if Required
                    if(self::isMandatory($rules)){$valid = false;}
                    else{$result[$param] = false;}
                }
            }
        }
        if($valid){
          return (object)self::addHeaders($result,$request);
        }
        else{
          Response::send("Wrong Vars Exception Abort",'error',$params);
        }
        //return $valid?(object)$result:false;
    }

    private static function matchingFormat ($value, $rules){
        $result = true;
        if(in_array('string',$rules)){
          if(!strlen($value)){$result = false;}
          else{
            if(!self::stringLength($value, $rules)){$result = false;}
          }
        }
        else if(in_array('number',$rules)){
          if(!is_numeric($value)){$result = false;}
        }
        else if(in_array('integer',$rules)){
          if(!is_int(($value * 1))){$result = false;}
        }
        else if(in_array('boolean',$rules)){
          if(!self::isBoolean($value)){$result = false;}
        }
        else if(in_array('json',$rules)){
          if(!self::isJson($value)){$result = false;}
        }
        else{
          $result = false;
        }

        return $result;
    }

    private static function stringLength ($value, $rules){
        $rule = false;
        $result = true;
        foreach($rules as $row){
          if(strpos( $row, "max-length" ) !== false || strpos( $row, "fix-length" ) !== false){
            $rule = explode(":",$row);
            $type = $rule[0];
            $l    = $rule[1]*1;
          }
        }
        if($rule){
          if($type == "max-length"){
            if(strlen($value) <= $l){
              $result = true;
            }
            else{
              $result = false;
            }
          }
          elseif($type == "fix-length"){
            if(strlen($value) == $l){
              $result = true;
            }
            else{
              $result = false;
            }
          }
        }
        else{
          $result = true;
        }
        return $result;
    }

    public static function isBoolean($value){
      if(strtolower($value) == 'true' || strtolower($value) == 'false' ||
          strtolower($value) == '1' || $value == '0' ||
          $value === 1 || $value === 0 || $value === true || $value === false){
        return true;
      }
      else{
        return false;
      }
    }

    public static function isJson($string) {
      return ((is_string($string) &&
              (is_object(json_decode($string)) ||
              is_array(json_decode($string))))) ? json_decode($string) : false;
    }

    private static function format($value, $rules){
        if(in_array('string',$rules)){
          $result = $value;
        }
        else if(in_array('number',$rules) || in_array('integer',$rules)){
          $result = $value * 1;
        }
        else if(in_array('boolean',$rules)){
          $result = (strtolower($value) == 'true' || strtolower($value) == '1' || $value === 1 || $value === true)?true:false;
        }
        else if(in_array('json',$rules)){
          $result = self::isJson($value);
        }
        else{
          $result = false;
        }
        return $result;
    }

    private static function isMandatory ($rules){
        if(in_array('required',$rules)){
          return true;
        }
        elseif(in_array('optional',$rules)){
          return false;
        }
        else{
          return true;
        }
    }

    public static function addHeaders($parameters, $request){
        $get_first = function($x){
            return $x[0];
        };
        $headers = array_map($get_first, $request->headers->all());
        foreach($headers as $header => $value){
          $parameters['h_'.str_replace("-","_",$header)] = $value;
        }
  
        return $parameters;
    }

    public function generateCode ($type,$column=false){
      $code = $this->generateString(5);
      if($type){
          $table  = strtoupper($type);
          if(!$column){$column = strtolower($type).'Code';}
          $exist  = DB::table($table)
              ->where($column,$code)
              ->first();

          if($exist){generateCode($type);}
      }
      return $code;
    }
}