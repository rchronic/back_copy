<?php

namespace App\Http\Controllers\Fnb;

/**
 * @resource Fnb
 *
 * @author Radhitya Rahman <radhityachronicle@gmail.com>
 * @copyright 2018 PipeApps
 */

use App\Http\Controllers\Controller;
use App\Http\Controllers\Fnb\Ingredients\IngredientsListController as Ingredients;
use App\Http\Controllers\Platform\Response;
use App\Http\Controllers\Platform\Tools;
use Illuminate\Http\Request;

class IngredientsController extends Controller
{
    public function getListIngredients(Request $request) {
        //bila parameter tidak dibutuhkan
        $params = Tools::params($request,array());

        if(1) {
            $data = (new Ingredients)->getList($params->h_property_id);
            return Response::send('Ingredients List', 'content', $data);
        }
        else {
            return Response::send('Wrong Parameters');
        }
    }

    public function getIngredientDetail(Request $request)
    {
        $params = Tools::params($request, array(
            ['ingredient_id', 'string'],
        ));

        $item = new Ingredients($params->ingredient_id);
        if($item->exist) {
            $data = array(
                "id"              => $item->material_code,
                "nama_material"   => $item->material_name,
                "total_stok"      => $item->total_stock,
                "satuan"          => $item->satuan,
            );
            return Response::send('Ingredient Detail', 'content', $data);
        } else {
            return Response::send('Wrong Ingredient ID');
        }
    }
    
    public function createIngredient(Request $request)
    {
        $params = Tools::params($request, array(
            ['nama_material', 'string'],
            ['total_stok', 'string'],
            ['satuan', 'string'],
        ));

        $item = new Ingredients();
        $item->setProperty($params->h_property_id);
        $item->material_code    = (new Tools)->generateCode('raw_material','rawMaterialCode');
        $item->material_name    = $params->nama_material;
        $item->total_stock      = $params->total_stok;
        $item->satuan           = $params->satuan;
        $item->create();
        return Response::send('Ingredient Created', 'content');
    }

    public function updateIngredient(Request $request)
    {
        $params = Tools::params($request, array(
            ['ingredient_id', 'string'],
            ['nama_material', 'string'],
            ['total_stok', 'string'],
            ['satuan', 'string'],
        ));

        $item = new Ingredients($params->ingredient_id);
        $item->material_code    = $params->ingredient_id;
        $item->material_name    = $params->nama_material;
        $item->total_stock      = $params->total_stok;
        $item->satuan           = $params->satuan;
        $item->update();
        return Response::send('Ingredient Update', 'content');
    }

    public function deleteIngredient(Request $request)
    {
        $params = Tools::params($request,array(
            ['ingredient_id','string'],
        ));
    
        $item = new Ingredients($params->ingredient_id);
        if($item->exist){
            $item->delete();
            return Response::send('Ingredient Deleted', 'request');
        }
        else{
            return Response::send('Wrong Ingredient ID');
        }
    }
}
