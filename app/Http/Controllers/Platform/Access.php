<?php

namespace App\Http\Controllers\Platform;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class Access extends Controller
{
    public $id;
    public $code;
    public $label;
    public $description;
    public $icon;
    public $status;
    public $isCustom;
    public $editable;
    public $removable;
    public $createdAt;
    public $updatedAt;

    public function __construct($code = false)
    {
        if($code){
            $accessGroup = DB::table('ACCESS_GROUP')
                ->where('accessGrpCode', $code)
                ->first();

            if($accessGroup){
                $this->id           = $accessGroup->accessGrpID;
                $this->code         = $accessGroup->accessGrpCode;
                $this->label        = $accessGroup->accessGrpName;
                $this->description  = $accessGroup->accessGrpDesc;
                $this->icon         = $accessGroup->icon;
                $this->status       = $accessGroup->status;
                $this->isCustom     = $accessGroup->isCustom;
                $this->editable     = $accessGroup->update;
                $this->removable    = $accessGroup->delete;
                $this->createdAt    = $accessGroup->created_at;
                $this->updatedAt    = $accessGroup->updated_at;
            }
            else{
                return false;
            }
        }
    }

    /**
     * DEPRECATED
     */

    public function getRoles ($params=false, $userRole=false){
        $access_groups = DB::table('ACCESS_GROUP')
            ->select('accessGrpID','accessGrpCode', 'accessGrpName','accessGrpDesc','status','isCustom','update','delete')
            ->where('isCustom',false)
            ->get();
        $roles = array();

        if(!$params){
            $params = array('label','description','id','status','editable','removable');
        }
        if($access_groups){
            foreach ($access_groups as $key => $row) {
                $role = array();
                // Set Parameters
                if(in_array('label', $params) ){
                    $role["label"] = $row->accessGrpName;
                }
                if(in_array('description', $params) ){
                    $role["description"] = $row->accessGrpDesc;
                }
                if(in_array('id', $params) ){
                    $role["id"] = $row->accessGrpCode;
                }
                if(in_array('status', $params) ){
                    $role["status"] = $row->status;
                }
                if(in_array('editable', $params) ){
                    $role["editable"] = ($row->update)?true:false;
                }
                if(in_array('removable', $params) ){
                    $role["removable"] = ($row->delete)?true:false;
                }

                // Set if Selected
                if($userRole && $userRole == $row->accessGrpID){
                    $role['selected'] = true;
                }
                elseif ($userRole) {
                    $role['selected'] = false;
                }
                array_push($roles, $role);
            }
        }
        else{
            $roles = array();
        }
        return $roles;
    }
}
