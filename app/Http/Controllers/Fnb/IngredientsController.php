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
            ['ingredient_id', 'string']
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

    // public function removeIngredient (Request $request){
    //     $params = Tools::params($request,array(
    //         ['ingredient_code','string']
    //     ));
    //
    //     $ingredient = new Ingredient($params->ingredient_code);
    //     if($ingredient->exist){
    //         $ingredient->remove();
    //         return Response::send('Ingredient Type Removed', 'request');
    //     }
    //     else{
    //         return Response::send('Wrong Ingredient Code');
    //     }
    // }
}
