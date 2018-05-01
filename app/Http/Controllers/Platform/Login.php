<?php

namespace App\Http\Controllers\Platform;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest as LoginRequest;
use App\Http\Controllers\Platform\Tools;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Http\Controllers\Auth\EncryptionController as Encryption;
use App\Http\Controllers\Platform\Property;
use App\Http\Controllers\Platform\Log;
use App\Http\Controllers\Platform\Response;
use App\Http\Requests\LogoutRequest as LogoutRequest;

/**
 * referensi: http://www.dyn-web.com/tutorials/php-js/json/decode.php
 */

class Login extends Controller {

    public function __construct() {

    }

    public function getLogin() {
        return view('login');
    }

    public function login(LoginRequest $request) {
        // dd($request->input('db'));
        $local = (new Tools)->setDefaultDB($request->input('db')); // _vps
        // dd($local);
        if($request->has('user') && $request->has('password')) {
            $user = $request->input('user'); // fnb
            $pswd = $request->input('password'); // test
            $target = DB::table("ROUTING")
                ->leftJoin('TARGET_DB','ROUTING.targetDB','=','TARGET_DB.databaseID')
                ->select('databaseName','databaseCode')
                ->where('userName',$user)
                ->orWhere('email',$user)
                ->first(); // databaseName = company_fnb_test, databaseCode = 4r1fk

            if(!empty($target->databaseName)) { // company_fnb_test
                $db_code = $target->databaseCode; // 4r1fk
                // change to company_fnb_test database
                (new Tools)->setDB($target->databaseName,$local); // company_fnb_test, _vps

                $userData = DB::table('user')
                    ->where('userName', $user) // $user = fnb
                    ->orWhere('email', $user)
                    ->first(); // object user

                if($userData) {
                    $hasSuspension = $this->checkSuspension($userData->userID); // userID=100, return: false gunanya untuk mencegah brute-force
                    $password = $userData->password; // e192419a497edc3c929e49b99a31fb8fc0d07c516a5b5d5c8b77a880f8a9a46003036b533a193dd3b81b4f09f52be4006cb21734ae8455a246044936f715eb4b
                    $hashed = (new Encryption)->encryptPassword($pswd); // $pswd='test', return: hashed

                    if(!$hasSuspension) {
                        if($password == $hashed) {
                            date_default_timezone_set("Asia/Jakarta");
                            $now = Carbon::now('Asia/Jakarta'); // waktuNow
                            $uid = $userData->userCode; // 237df
                            $uname = $userData->userFullName; // "fnb test"
                            $uicon = $userData->userPicLink; // img57286206.jpg
                            $properties = (new Property)->getProperties($uid); // uid=237df, return: object property

                            // change to general database
                            (new Tools)->setDefaultDB($local);

                            $userToken = DB::table('SESSIONS')
                                ->where('userCode',$uid) // uid=237df
                                ->first();
                            
                            if(!empty($userToken->token)) { // HdY0MpOrxfiKGByC6WLCKz2fblOnR3Gj-4r1fk
                                // user sudah log-in
                                $token = (new Tools)->generateToken($db_code); // db_code=4r1fk, return: randomString-4r1fk
                                $expiry = date('Y-m-d H:i:s', strtotime("+30 minutes")); // waktuExpiry
                                DB::table('SESSIONS')
                                    ->where('userCode',$uid) // uid=237df
                                    ->update(['token' => $token, 'expiry' => $expiry]); // token=randomString-4r1fk, expiry=waktuExpiry
                                
                                if($expiry > $now) {
                                    // User already logged in and token still active
                                    $message = "user_already_loggedin";
                                } else {
                                    // User already logged in and token expired
                                    $message = "login_success";
                                }

                                $data = array(
                                    'TOKEN' => $token, // randomString-4r1fk
                                    'USER_ID' => $uid, // 237df
                                    'user' => array(
                                        'id' => $uid, // 237df
                                        'name' => $uname, // 'fnb test'
                                        'icon' => $uicon, // img57286206.jpg
                                    ),
                                    'properties' => $properties // object property
                                );
                            } else {
                                // User belum log-in
                                $token = (new Tools)->generateToken($db_code); // db_code=4r1fk, return: randomString-4r1fk
                                $expiry = date('Y-m-d H:i:s', strtotime("+30 minutes")); // waktuExpiry
                                DB::table('SESSIONS')->insert(
                                    ['userCode' => $uid,'token' => $token,'expiry' => $expiry] // uid=237df, token=randomString-4r1fk, expiry=waktuExpiry
                                );

                                $data = array(
                                    'TOKEN' => $token, // randomString-4r1fk
                                    'USER_ID' => $uid, // 237df
                                    'user' => array(
                                        'id' => $uid, // 237df
                                        'name' => 'User',
                                        'icon' => 'none.jpg',
                                    ),
                                    'properties' => array(
                                        0 => array(
                                            'name' => 'Property 1',
                                            'property_id' => 'Pr0p1',
                                        ),
                                        1 => array(
                                            'name' => 'Property 2',
                                            'property_id' => 'Pr0p2',
                                        ),
                                    )
                                );
                                $message = "login_success";
                            }
                            (new Tools)->setDB($target->databaseName,$local); // company_fnb_test, _vps
                            $this->disableSuspension($userData->userID); // 100
                            Log::set($userData->userID,'request', 'Login', 'SESSION,USER'); // 100

                            return Response::send($message, 'data', $data); // message, data, return: result
                        } else {
                            // Password gk sama
                            $suspension = $this->failLogin($userData->userID);
                            if($suspension) {
                                $data = array(
                                    'suspend' => 1,
                                    'suspensionLimit' => $suspension,
                                );
                                Log::set($userData->$userID,'request','User Suspended', 'USER');
                                return Response::send('error_user_suspended','error',$data);
                            } else {
                                Log::set($userData->userID,'request','Wrong Password','USER');
                                return Response::send('error_login_failed');
                            }
                        }
                    } else {
                        $data = array(
                            'suspend' => 1,
                            'suspensionLimit' => $hasSuspension
                        );
                        return Response::send('error_user_not_exist');
                    }
                } else {
                    // User not existing
                    return Response::send('error_login_failed');
                }
            }
        } else {
            return Response::send('error_wrong_var');
        }
    }

    public function checksuspension($uid) { // 100
        $result = false;

        $user = DB::table('USER')
            ->select('suspended','suspensionLimit')
            ->where('userID',$uid) // 100
            ->first(); // object user
        
        if($user->suspended) { // 0
            $now = Carbon::now();
            $suspension = Carbon::parse($user->suspensionLimit);
            if($now < $suspension) {
                $result = $suspension->toDateTimeString();
            }
        }

        return $result; // false
    }

    public function disableSuspension($uid) { // 100
        $minimum = Carbon::parse('2000-01-01 00:00:01');

        DB::table('USER')
            ->where('userID', $uid)
            ->update(['failedLogin' => 0,
                'suspended' => 0,
                'suspensionLimit' => $minimum,
            ]);
    }

    public function logout (LogoutRequest $request)
    {
        $params = Tools::params($request,array(
            ['user_id','string'],
            ['token','string']));

        if ($params) {
            $local = (new Tools)->setDefaultDB($request->input('db'));

            $uid   = $request->input('user_id');
            $token = $request->input('token');

            $target = DB::table('SESSIONS')
                ->select('id')
                ->where([['userCode', $uid],['token', $token]])
                ->first();

            if(!empty($target->id)){
                DB::table('SESSIONS') // inti dari proses logout
                    ->where('id', $target->id)
                    ->delete();

                $exploded = explode('-', $token);
                $db = $exploded[1];
                $targetDB = DB::table('TARGET_DB') // buat ganti database ke yang ada di general.target_db
                    ->select('databaseName')
                    ->where('databaseCode', $db)
                    ->first();

                (new Tools)->setDB($targetDB->databaseName);
                $user = DB::table('USER')
                    ->select('userID')
                    ->where('userCode', $uid)
                    ->first();
                Log::set($user->userID,'request','Logout','SESSIONS');

                return Response::send('User Logged Out', 'data');
            }
            else{
                return Response::send('Wrong Session');
            }
        }
        else{
            return Response::send('error_wrong_var');
        }
    }
}