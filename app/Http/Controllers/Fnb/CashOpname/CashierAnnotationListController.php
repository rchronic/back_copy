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
use Carbon\Carbon;


class CashierAnnotationListController extends Controller
{
    public $cash_opname_id;
    public $cash_opname_code;
    public $date;
    public $real_cash;
    public $status;
    public $description;
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
                $this->status               = $item->status;
                $this->description          = $item->description;
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
                "status"          => $row->status,
                "deskripsi"       => $row->description,
            );
            array_push($data,$item);
        }
        return $data;
    }

    public function create()
    {
        $virCash = 0;
        $co_in = DB::table('ca_invoice')->get();
        $dt = Carbon::now();
        $dt2 = $dt->toDateString();
        foreach($co_in as $row) {
            $datetime = new Carbon($row->Buat_Tanggal);
            $dt3 = $datetime->toDateString();
            if($dt2 == $dt3) {
                $virCash += $row->Kas_Virtual;
            }
        }
        $dt4 = $dt->toDateTimeString();
        $totalDiff = abs($this->real_cash - $virCash);
        DB::unprepared(DB::raw("CALL CASH_OPNAME_INSERT('$this->cash_opname_code', '$dt4', $this->real_cash, $virCash, $totalDiff, 'Belum Disetujui', '$this->description', '$this->propertyID')"));
    }

    public function update_real_cash()
    {
        DB::unprepared(DB::raw("CALL CASH_OPNAME_UPDATE_REAL_CASH('$this->cash_opname_code',$this->real_cash)"));
    }

    public function update_description()
    {
        DB::unprepared(DB::raw("CALL CASH_OPNAME_UPDATE_DESCRIPTION('$this->cash_opname_code','$this->description')"));
    }

    // public function delete() {
    //     DB::unprepared(DB::raw("CALL RAW_MATERIAL_DELETE('$this->material_code')"));
    // }

    public function setProperty($propertyCode){
        $property = new Property ($propertyCode);
        if($property){
            $this->propertyID = $property->id;
            return true;
        }
        else{
            return false;
        }
    }

    public function getProperty (){
        return $this->propertyID;
    }
}
