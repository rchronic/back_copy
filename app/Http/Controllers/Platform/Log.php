<?php

namespace App\Http\Controllers\Platform;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Platform\Language;
use App\Http\Controllers\Platform\Response;
use App\Http\Controllers\Platform\Tools;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class Log extends Controller
{

    public static function set($uid, $type, $action, $tables=null, $request=''){ // 100,'request','Login','session,user'

        //Confirm User ID
        if(strlen($uid) == 5){ // 100
          $user = DB::table('USER')
            ->where('userCode', $uid)
            ->first();
          if($user){
            $uid = $user->userID;
          }
          else{
            $uid = false;
          }
        }
        else{
          $user = DB::table('USER')
            ->where('userID', $uid) // 100
            ->first();
          if(!$user){
            $uid = false;
          }
        }

        if($uid){ // 100
            if($type == 'request'){$type = 'REQUEST';} // type='request'
            else{$type = 'CONTENT';}
            $action = '['.$type.'] ' . $action; // [request]Login

            DB::table('LOG')->insert(
                ['userID'       => $uid, // 100
                'action'        => $action, // Login
                'tableInCharge' => strtoupper($tables), // session,user
                'request'       => json_encode($request) // ''
            ]);
        }
    }

    /**
     * Get Logs
     *
     * This method provides the list of actiity logs from the users.
     *
     * ### Required Headers
     *
     * Key | Value
     * --- | -----
     * TOKEN | Session_Token
     * USER_ID | User_ID
     */

    public function getLogs(Request $request){
        //Check Request Validity
        $params = Tools::params($request,array(
            ['start','string']));

        $offset = $request->start*1;
        if(!is_int($offset) || $offset < 0){$offset = 0;}
        $limit  = 15;
        $logs   = $this->getFormattedLogs($offset,$limit);

        return Response::send('Activity Logs','data',$logs);
    }

    private function getFormattedLogs ($offset, $limit){
        $logs = DB::table('LOG')
            ->join('USER', 'LOG.userID', '=', 'USER.userID')
            ->select('userFullName','userPicLink','action','LOG.created_at')
            ->where('action', 'LIKE', '[REQUEST]%')
            ->orderBy('LOG.created_at', 'desc')
            ->offset($offset)
            ->limit($limit)
            ->get();
        $result = array();

        foreach ($logs as $key => $row) {
            $action = (new Language)->getLogText($row->action);
            if($action){
                $action = str_replace('%n', '<b>'.$row->userFullName.'</b>', $action);
            }
            else{
                $action = "--UNDEFINED_VALUE--";
            }
            $var = array(
                "text" => $action,
                "icon" => $row->userPicLink,
                "date" => $row->created_at
            );
            array_push($result, $var);
        }

        return $result;
    }
}
