<?php

namespace App\Http\Controllers\Platform;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Controllers\Platform\Tools;
use App\Http\Controllers\Platform\Response;
use App\Http\Controllers\Platform\Access;

class User extends Controller
{
    public $id;
    public $code;
    public $user_name;
    public $employee_id;
    public $name;
    public $email;
    public $phone;
    public $position;
    public $position_id;
    public $access_name;
    public $access_id;
    public $access_code;
    public $password;
    public $icon;
    public $disabled;
    public $exist;

    public function __construct($code = false)
    {
        if($code){
            $user = DB::table('USER')
                ->leftJoin('ACCESS_GROUP', 'USER.accessGroupID', '=', 'ACCESS_GROUP.accessGrpID')
                ->leftJoin('POSITION', 'USER.positionID', '=', 'POSITION.positionID')
                ->where('userCode', $code)
                ->first();

            if($user){
                $this->id          = $user->userID;
                $this->code        = $user->userCode;
                $this->user_name   = $user->userName;
                $this->employee_id = $user->employeeID;
                $this->name        = $user->userFullName;
                $this->email       = $user->email;
                $this->phone       = $user->userPhoneNumber;
                $this->position    = $user->positionName;
                $this->position_id = $user->positionID;
                $this->access_name = $user->accessGrpName;
                $this->access_id   = $user->accessGroupID;
                $this->access_code = $user->accessGrpCode;
                $this->password    = $user->password;
                $this->icon        = $user->userPicLink;
                $this->disabled    = $user->disableDate;
                $this->exist       = true;
            }
            else{
                $this->exist = false;
            }
        }
    }

    public function getData ($uid){ // dipanggil di AdminContent.dashboardContent

        $user = DB::table('USER')
            ->select('userID','userCode','userName','userFullName','employeeID','email',
                    'userPhoneNumber','positionID','accessGroupID','accessGrpCode','password','userPicLink',
                    'disableDate')
            ->leftJoin('ACCESS_GROUP', 'USER.accessGroupID', '=', 'ACCESS_GROUP.accessGrpID')
            ->where('userCode', $uid)
            ->orWhere('userID', $uid)
            ->first();

        if($user){
            $result = (object) [
                'id'          => $user->userID,
                'code'        => $user->userCode,
                'user_name'   => $user->userName,
                'employee_id' => $user->employeeID,
                'name'        => $user->userFullName,
                'email'       => $user->email,
                'phone'       => $user->userPhoneNumber,
                'position'    => 'Position',
                'position_id' => $user->positionID,
                'access_name' => 'Role',
                'access_id'   => $user->accessGroupID,
                'access_code' => $user->accessGrpCode,
                'password'    => $user->password,
                'icon'        => $user->userPicLink,
                'disabled'    => $user->disableDate
            ];
        }
        else{
            $result = false;
        }
        return $result;
    }

    public function getUserData (Request $request){
        $params = Tools::params($request,array(
            ['user_id','string']));

        if ($params) {
            $user = new self($request->input('user_id'));
            if($user){
                $positions  = $this->getPositions($user->position_id); // saat ini tidak begitu penting, untuk mendapatkan position
                $roles      = (new Access)->getRoles(array('id','label'), $user->access_id); // untuk medapatkan role
                $uid = $request->input('user_id');

                $data = array(
                    'user_id'       => $uid,
                    'full_name'     => $user->name,
                    'user_name'     => $user->user_name,
                    'employee_id'   => $user->employee_id,
                    'email'         => $user->email,
                    'phone'         => $user->phone,
                    'positions'     => $positions,
                    'roles'         => $roles,
                    'expiry'        => $user->disabled
                );
                return Response::send('User Data','data', $data);
            }
            else{
                return Response::send('error_user_not_exist');
            }
        }
        else{
            return Response::invalidRequest(true,$params);
        }
    }

    private function getPositions ($id=false){
        $data_positions = DB::table('POSITION')
            ->select('positionID','positionCode', 'positionName')
            ->get();
        $positions = array();

        if($data_positions){
            foreach ($data_positions as $key => $row) {
                $position = array(
                    "id"    => $row->positionCode ,
                    "label" => $row->positionName ,
                );

                // Set if Selected
                if ($id && $id == $row->positionID){
                    $position['selected'] = true;
                }
                elseif ($id) {
                    $position['selected'] = false;
                }
                array_push($positions, $position);
            }
        }
        return $positions;
    }
}
