<?php

namespace App\Http\Controllers\Fnb\CashOpname;

/**
 * @resource Fnb Content Ingredients
 *
 *
 * @author Radhitya Rahman <radhityachronicle@gmail.com>
 * @copyright 2018 PipeApps
 */

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB as DB;
use App\Http\Controllers\Platform\Property;


class CashOpnameListController extends Controller
{
    public $cash_opname_id;
    public $cash_opname_code;
    public $date;
    public $real_cash;
    public $virtual_cash;
    public $total_diff;
    public $status;
    public $deskripsi;
    private $propertyID;
    public $exist;

    public function __construct($code = false)
    {
        $this->material_id = $code;
        if($code){
            $item = DB::table('CASH_OPNAME')
            ->where('cashOpnameCode', $code)
            ->first();
            if($item){
                $this->cash_opname_id       = $item->cashOpnameID;
                $this->cash_opname_code     = $item->cashOpnameCode;
                $this->date                 = $item->date;
                $this->real_cash            = $item->realCash;
                $this->virtual_cash         = $item->virtualCash;
                $this->total_diff           = $item->totalDiff;
                $this->status               = $item->status;
                $this->deskripsi            = $item->deskripsi;
                $this->propertyID           = $item->propertyID;
                $this->exist                = true;
            }
            else {
                $this->exist    = false;
            }
        }
        else{

        }
    }

    public function getList ($propertyCode){
        $property = new Property ($propertyCode);
        $data = [];
        $cashOpname = DB::select(DB::raw("CALL CASH_OPNAME_LIST($property->id)"));
        foreach($cashOpname as $row){
            $item = array(
                "id"              => $row->cashOpnameCode,
                "tanggal"         => $row->date,
                "kas_asli"        => $row->realCash,
                "kas_virtual"     => $row->virtualCash,
                "selisih_kas"     => $row->totalDiff,
                "status"          => $row->status,
                "deskripsi"       => $row->deskripsi,
            );
            array_push($data,$item);
        }
        return $data;
    }

    // public function create()
    // {
    //     DB::unprepared(DB::raw("CALL RAW_MATERIAL_INSERT('$this->material_code','$this->material_name',$this->total_stock,'$this->satuan','$this->propertyID')"));
    // }

    // public function update()
    // {
    //     DB::unprepared(DB::raw("CALL RAW_MATERIAL_UPDATE('$this->material_code','$this->material_name',$this->total_stock,'$this->satuan')"));
    // }

    // public function delete() {
    //     DB::unprepared(DB::raw("CALL RAW_MATERIAL_DELETE('$this->material_code')"));
    // }

    // public function setProperty($propertyCode){
    //     $property = new Property ($propertyCode);
    //     if($property){
    //         $this->propertyID = $property->id;
    //         return true;
    //     }
    //     else{
    //         return false;
    //     }
    // }

    // public function getProperty (){
    //     return $this->propertyID;
    // }
}
