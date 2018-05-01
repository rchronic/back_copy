<?php

namespace App\Http\Controllers\Platform;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class Property extends Controller {
    public $id;
    public $code;
    public $label;
    public $icon;

    public function __construct($code = false) {
        if($code) { // false
            $position = DB::table("PROPERTY")
                ->where('propertyCode',$code)
                ->orWhere('propertyID',$code)
                ->first();

            if($position) {
                $this->id = $position->propertyID;
                $this->code = $position->propertyCode;
                $this->label = $position->propertyName;
                $this->icon = $position->propertyIcon;
            } else {
                return false;
            }
        }
    }

    public function getProperties($uid, $action='login') { // uid = 237df
        $properties = array();

        $hasCustomProperties = DB::table("PROPERTY_USER")
            ->select('propertyID')
            ->where('userID',$uid)
            ->get(); // NULL

        $hasCustomRole = array();
        if(count($hasCustomProperties)) { // 0
            $cProperties = array();
            foreach($hasCustomProperties as $key => $row) {
                array_push($cProperties, $row->propertyID);
            }
        } else {
            $hasCustomRole = DB::table('TR_MODULE_AND_AUTH')
                ->leftJoin('USER', 'USER.accessGroupID', '=', 'TR_MODULE_AND_AUTH.accessGrpID')
                ->select('propertyID')
                ->where('userID',$uid)
                ->groupBy('propertyID')
                ->get(); // NULL
            
            if(count($hasCustomRole)) { // 0
                $cProperties = array();
                foreach($hasCustomRole as $key => $row) {
                    array_push($cProperties, $row->propertyID);
                }
            } else {
                $cProperties = false;
            }
        }

        if($action == 'management') { // false
            if($cProperties) {
                $properties_query = DB::table('PROPERTY')
                    ->join('PROPERTY_TYPE','PROPERTY_TYPE.pTypeID', '=', 'PROPERTY.propertyTypeID')
                    ->join('CITY', 'CITY.cityID', '=', 'PROPERTY.cityID')
                    ->select('propertyName', 'propertyCode', 'pTypeName', 'address', 'cityName', 'propertyIcon')
                    ->whereIn('propertyID', $cProperties)
                    ->orderby('propertyName','asc')
                    ->get();
            } else {
                $properties_query = DB::table('PROPERTY')
                    ->join('PROPERTY_TYPE', 'PROPERTY_TYPE.pTypeID', '=', 'PROPERTY.propertyTypeID')
                    ->join('CITY', 'CITY.cityID', '=', 'PROPERTY.cityID')
                    ->select('propertyName', 'propertyCode', 'pTypeName', 'address', 'cityName', 'propertyIcon')
                    ->orderby('propertyName','asc')
                    ->get();
            }

            foreach ($properties_query as $key => $row) {
                $property = array(
                    "label"   => $row->propertyName,
                    "id"      => $row->propertyCode,
                    "type"    => $row->pTypeName,
                    "address" => $row->address,
                    "city"    => $row->cityName,
                    "icon"    => $row->propertyIcon
                );
                array_push($properties, $property);
            }
        } else if ($action == 'permissionDropdown') { // false
            if($cProperties) {
                $properties_query = DB::table('PROPERTY')
                    ->select('propertyName', 'propertyCode', 'propertyIcon')
                    ->whereIn('propertyID', $cProperties )
                    ->orderby('propertyName','asc')
                    ->get();
            } else {
                $properties_query = array();
            }

            $properties = $properties_query;
        } else if ($action == 'login') { // true
            $sessionData = DB::table('SESSION_DATA')
                ->where('userID', $uid) // uid=237df
                ->first(); // null
            if(!$sessionData) {
                $sessionData = (object) array(
                    "propertyID" => false
                );
            }
            $properties = array();

            if($cProperties) {
                $properties_query = DB::table('PROPERTY')
                    ->select('propertyID','propertyName', 'propertyCode', 'propertyIcon')
                    ->whereIn('propertyID', $cProperties )
                    ->orderby('propertyName','asc')
                    ->get();
            } else {
                $properties_query = DB::table('PROPERTY')
                    ->select('propertyID','propertyName', 'propertyCode', 'propertyIcon')
                    ->orderby('propertyName','asc')
                    ->get(); // query: "select propertyID, propertyName, propertyCode, propertyIcon from property;"
            }
            $i = 1;
            foreach ($properties_query as $key => $row) {
                $property = array(
                    'propertyName' => $row->propertyName,
                    'propertyCode' => $row->propertyCode,
                    'icon'         => $row->propertyIcon
                );
                if(!$sessionData->propertyID && $i == 1) {
                    $property['selected'] = true;
                } elseif($row->propertyID == $sessionData->propertyID) {
                    $property['selected'] = true;
                } else {
                    $property['selected'] = false;
                }

                array_push($properties, $property);
                $i++;
            }
        } else {
            if($cProperties) {
                $properties = DB::table('PROPERTY')
                    ->select('propertyName', 'propertyCode', 'propertyIcon')
                    ->whereIn('propertyID', $cProperties )
                    ->orderby('propertyName','asc')
                    ->get();
            } else {
                $properties = DB::table('PROPERTY')
                    ->select('propertyName', 'propertyCode', 'propertyIcon')
                    ->orderby('propertyName','asc')
                    ->get();
            }
        }

        return $properties;
    }
}