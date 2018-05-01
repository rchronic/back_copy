<?php

namespace App\Http\Controllers\Platform;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class Services extends Controller
{
    public $id;
    public $code;
    public $name;
    public $description;
    public $uri;
    public $icon;
    public $position;
    public $status;
    public $created_at;
    public $updated_at;
    public $exist;

    public function __construct($code=false)
    {
        if($code){
            $item = DB::table('SERVICE')
                ->where('serviceCode', $code)
                ->orWhere('serviceID', $code)
                ->first();
            if($item){
              $this->id         = $item->serviceID;
              $this->code       = $item->serviceCode;
              $this->name       = $item->serviceName;
              $this->description = $item->serviceDesc;
              $this->uri        = $item->uri;
              $this->icon       = $item->icon;
              $this->position   = $item->position;
              $this->status     = $item->status;
              $this->created_at = $item->created_at;
              $this->updated_at = $item->updated_at;
              $this->exist = true;
            }
            else{
              $this->exist = false;
            }
        }
        else{
          $this->exist = false;
        }
    }

    /**
     * [getServices description]
     * @return arr service listing
     */

    public function getServices ($src = "menu"){
        $services = DB::table('SERVICE')
            ->select('serviceID','serviceCode', 'serviceName', 'serviceDesc', 'uri', 'icon', 'status')
            ->orderBy('position', 'asc')
            ->get();

        $result = array();

        if($services){
            foreach ($services as $key => $row) {
                if($row->status != 'hidden' && $src == 'menu'){
                    $service = array(
                        "id"    => $row->serviceCode,
                        "label" => $row->serviceName,
                        "desc"  => $row->serviceDesc,
                        "uri"   => $row->uri,
                        "icon"  => $row->icon,
                        "status" => ($row->status == "enabled")?true:false
                    );
                    array_push($result, $service);
                }
                else if($src != 'menu'){
                    $service = array(
                        "id"    => $row->serviceID,
                        "code"  => $row->serviceCode,
                        "label" => $row->serviceName,
                        "desc"  => $row->serviceDesc,
                        "uri"   => $row->uri,
                        "icon"  => $row->icon,
                        "status" => ($row->status == "enabled")?true:false
                    );
                    array_push($result, $service);
                }
            }
        }

        return $result;
    }

 }
