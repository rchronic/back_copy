<?php

namespace App\Http\Controllers\Fnb;

/**
 * @resource Fnb
 *
 * @author Radhitya Rahman <radhityachronicle@gmail.com>
 * @copyright 2018 PipeApps
 */

use App\Http\Controllers\Controller;
use App\Http\Controllers\Fnb\CashOpname\CashierAnnotationListController as CashierAnnotation;
use App\Http\Controllers\Platform\Response;
use App\Http\Controllers\Platform\Tools;
use Illuminate\Http\Request;

class CashierAnnotationController extends Controller
{
    public function getListCashierAnnotation(Request $request) {
        //bila parameter tidak dibutuhkan
        $params = Tools::params($request,array());

        if(1) {
            $data = (new CashierAnnotation)->getList($params->h_property_id);
            return Response::send('Cashier Annotation List', 'content', $data);
        }
        else {
            return Response::send('Wrong Parameters');
        }
    }

    public function createCashierAnnotation(Request $request)
    {
        $params = Tools::params($request, array(
            ['kas_asli', 'string'],
            ['deskripsi', 'string'],
        ));

        $item = new CashierAnnotation();
        $item->setProperty($params->h_property_id);
        $item->cash_opname_code    = (new Tools)->generateCode('cash_opname','cashOpnameCode');
        $item->real_cash           = $params->kas_asli;
        $item->description         = $params->deskripsi;
        $item->create();
        return Response::send('Cashier Annotation Created', 'content');
    }

    public function getRealCashDetail(Request $request)
    {
        $params = Tools::params($request, array(
            ['cashier_annotation_id', 'string'],
        ));

        $item = new CashierAnnotation($params->cashier_annotation_id);
        if($item->exist) {
            $data = array(
                "id"         => $item->cash_opname_code,
                "kas_asli"   => $item->real_cash,
            );
            return Response::send('Real Cash Detail', 'content', $data);
        } else {
            return Response::send('Wrong Cashier Annotation ID');
        }
    }

    public function updateRealCash(Request $request)
    {
        $params = Tools::params($request, array(
            ['cashier_annotation_id', 'string'],
            ['kas_asli', 'string'],
        ));

        $item = new CashierAnnotation($params->cashier_annotation_id);
        $item->cash_opname_code    = $params->cashier_annotation_id;
        $item->real_cash           = $params->kas_asli;
        $item->update_real_cash();
        return Response::send('Real Cash Update', 'content');
    }

    public function getDescriptionDetail(Request $request)
    {
        $params = Tools::params($request, array(
            ['cashier_annotation_id', 'string'],
        ));

        $item = new CashierAnnotation($params->cashier_annotation_id);
        if($item->exist) {
            $data = array(
                "id"         => $item->cash_opname_code,
                "deskripsi"  => $item->description,
            );
            return Response::send('Description Detail', 'content', $data);
        } else {
            return Response::send('Wrong Cashier Annotation ID');
        }
    }

    public function updateDescription(Request $request)
    {
        $params = Tools::params($request, array(
            ['cashier_annotation_id', 'string'],
            ['deskripsi', 'string'],
        ));

        $item = new CashierAnnotation($params->cashier_annotation_id);
        $item->cash_opname_code    = $params->cashier_annotation_id;
        $item->description         = $params->deskripsi;
        $item->update_description();
        return Response::send('Description Update', 'content');
    }

    // public function deleteIngredient(Request $request)
    // {
    //     $params = Tools::params($request,array(
    //         ['ingredient_id','string'],
    //     ));
    
    //     $item = new Ingredients($params->ingredient_id);
    //     if($item->exist){
    //         $item->delete();
    //         return Response::send('Ingredient Deleted', 'request');
    //     }
    //     else{
    //         return Response::send('Wrong Ingredient ID');
    //     }
    // }
}
