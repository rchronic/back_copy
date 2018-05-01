<?php

namespace App\Http\Middleware;

use App\Http\Controllers\Platform\Controllers\Platform\Access,
    App\Http\Controllers\Platform\Response,
    App\Http\Controllers\Platform\Tools,
    App\Http\Controllers\Platform\Models\UserModel as User,

    Closure,
    Carbon\Carbon,
    Illuminate\Support\Facades\Config,
    Illuminate\Support\Facades\DB;

class VerifyAccess
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
        $result   = true;

        $target   = self::getRequestTarget($request->route()->action['controller']);
        $property = $request->header('PROPERTY_ID');
        //$user     = new User($request->header('USER_ID'));


        if($result){
            return $next($request);
        }
        else{

            return Response::send("error_not_authorized",'error',$controller.' @ '.$method);
        }
    }

    private static function getRequestTarget ($path){
        $exploded_path = explode('\\',$path);

        // Get Service / Module / Method called

        $service = explode("@",$exploded_path[count($exploded_path)-2])[0];
        $service = ($service == "Controllers") ? "Platform" : $service;

        $module  = explode("@",$exploded_path[count($exploded_path)-1])[0];
        $module  = substr($module, 0, strlen($module) - strlen("Controller"));

        $method  = explode("@",$exploded_path[count($exploded_path)-1])[1];

        return (object)array(
            "service" => $service,
            "module"  => $module,
            "method"  => $method
        );
    }

}
