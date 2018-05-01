<?php

namespace App\Http\Middleware;

use Closure,
    App\Http\Controllers\Platform\Property,
    App\Http\Controllers\Platform\Response,
    App\Http\Controllers\Platform\Tools,
    App\Http\Controllers\Platform\User,

    Carbon\Carbon,
    Illuminate\Support\Facades\Config,
    Illuminate\Support\Facades\DB;

class VerifyHeader
{
    /**
     * Check the headers of incomming request for logged in users.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $local = '';
        if($request->has('db')){
            if($request->input('db') == 'local'){
                $local = '_local';
                (new Tools)->setDefaultDB($local);
            }
            else if($request->input('db') == 'vps'){
                $local = '_vps';
                (new Tools)->setDefaultDB($local);
            }
        }
        else{
            $local = '_local';
            (new Tools)->setDefaultDB($local);
        }

        $full_token = $request->header('TOKEN');
        $uid   = $request->header('USER_ID');
        if(($full_token == "FzDIBMlvuiFmaX7uu45RfrfjesEp7coM-4rbWz" || $full_token == 'JEb3jf8hfbs23bd8Hg37ftFG2Gdv3070-8ht3n') && $uid == "D3vU5"){
            /**************/
            /* Dev Access */
            /**************/
            if($full_token == "FzDIBMlvuiFmaX7uu45RfrfjesEp7coM-4rbWz"){
                $dbCode = '4rbWz';
            }
            else{
                $dbCode = '8ht3n';
            }
            $target = DB::table('TARGET_DB')
                ->select('databaseName')
                ->where('databaseCode', $dbCode)
                ->first();

            (new Tools)->setDB($target->databaseName);
            // Save User's Session Data
            $this->updateSessionData($request);

            return $next($request);
        }
        else{
            // Check if there are headers
            if (!empty($full_token) && !empty($uid)) {
                $result = $this->validateToken($full_token,$uid);
                if(is_array($result)){
                    $token = $result['token'];
                    $db    = $result['db'];

                    $userToken = DB::table('SESSIONS')
                        ->where('userCode', $uid)
                        ->first();

                    // Check if the user has a token
                    if(!empty($userToken->token)){

                        // Check if token is right
                        if($userToken->token == $full_token){
                            $now      = Carbon::now('Asia/Jakarta');
                            $expiry = new Carbon($userToken->expiry);
                            // Check if token is active
                            if($expiry > $now){
                                date_default_timezone_set("Asia/Jakarta");
                                $expiry = date('Y-m-d H:i:s', strtotime("+30 minutes"));
                                DB::table('SESSIONS')
                                    ->where('userCode', $uid)
                                    ->update(['expiry' => $expiry]);

                                (new Tools)->setDB($db);
                                // Save User's Session Data
                                $this->updateSessionData($request);

                                return $next($request);
                            }
                            else{
                                    return Response::send('error_token_expired');
                                }
                        }
                        else{

                            return Response::send('error_not_authorized');
                        }
                    }
                    else{

                        return Response::send('error_not_authorized');
                    }
                }
                else{
                    return $next($request);
                }
            }
            else{
                return Response::send('error_not_authorized');
            }
        }
    }

    public function validateToken ($string,$uid){
        $exploded = explode('-', $string);
        if(count($exploded) == 2 && strlen($exploded[1]) == 5){
            $result = array(
                'token' => $exploded[0],
                'db'    => ''
            );
            $code  = $exploded[1];

            $target = DB::table('ROUTING')
                ->leftJoin('TARGET_DB', 'ROUTING.targetDB', '=', 'TARGET_DB.databaseID')
                ->select('databaseName')
                ->where('databaseCode', $code)
                ->first();

            if(!empty($target->databaseName)){
                $result['db'] = $target->databaseName;
                return $result;
            }
            else{
                return false;
            }
        }
        else{
            return false;
        }
    }

    private function updateSessionData ($request){
        $user     = new User($request->header('USER_ID'));
        $property = false;
        $now      = Carbon::now('Asia/Jakarta');

        $sessionData = DB::table('SESSION_DATA')
            ->where('userID', $user->id)
            ->first();

        if($request->header('PROPERTY_ID')){
            $property = new Property($request->header('PROPERTY_ID'));
        }
        elseif(count($sessionData)){
            $property = new Property($sessionData->propertyID);
        }

        if($property){
            if($sessionData){
                DB::table('SESSION_DATA')
                    ->where('userID', $user->id)
                    ->update([
                        'propertyID' => $property->id,
                        'updated_at' => $now
                    ]);
            }
            else{
                DB::table('SESSION_DATA')->insert(
                    ['userID'    => $user->id,
                    'propertyID' => $property->id,]);
            }
        }
    }
}
