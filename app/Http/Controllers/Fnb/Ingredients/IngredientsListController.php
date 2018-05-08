<?php

namespace App\Http\Controllers\Fnb\Ingredients;

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


class IngredientsListController extends Controller
{
    public $material_id;
    public $material_code;
    public $material_name;
    public $total_stock;
    public $satuan;
    private $propertyID;
    public $exist;

    public function __construct($code = false)
    {
        $this->material_id = $code;
        if($code){
            $item = DB::table('RAW_MATERIAL')
            ->where('rawMaterialCode', $code)
            ->first();
            if($item){
                $this->material_id       = $item->rawMaterialID;
                $this->material_code     = $item->rawMaterialCode;
                $this->material_name     = $item->rawMaterialName;
                $this->total_stock       = $item->totalStock;
                $this->satuan            = $item->satuan;
                $this->propertyID        = $item->propertyID;
                $this->exist             = true;
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
        $ingredients = DB::select(DB::raw("CALL RAW_MATERIAL_LIST($property->id)"));
        //dd($ingredients);
        foreach($ingredients as $row){
            $item = array(
                "id"              => $row->rawMaterialCode,
                "nama_material"   => $row->rawMaterialName,
                "total_stok"      => $row->totalStock,
                "satuan"          => $row->satuan,
            );
            array_push($data,$item);
        }
        return $data;
    }

    public function create()
    {
        DB::unprepared(DB::raw("CALL RAW_MATERIAL_INSERT('$this->material_code','$this->material_name',$this->total_stock,'$this->satuan','$this->propertyID')"));
    }

    public function update()
    {
        DB::unprepared(DB::raw("CALL RAW_MATERIAL_UPDATE('$this->material_code','$this->material_name',$this->total_stock,'$this->satuan')"));
    }

    public function delete() {
        DB::unprepared(DB::raw("CALL RAW_MATERIAL_DELETE('$this->material_code')"));
    }

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
