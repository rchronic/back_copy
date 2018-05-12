<?php

namespace App\Http\Controllers\Fnb;

/**
 * @resource Fnb
 *
 * @author Radhitya Rahman <radhityachronicle@gmail.com>
 * @copyright 2018 PipeApps
 */

use App\Http\Controllers\Controller;
use App\Http\Controllers\Fnb\CashOpname\CashOpnameListController as CashOpname;
use App\Http\Controllers\Platform\Response;
use App\Http\Controllers\Platform\Tools;
use Illuminate\Http\Request;

class CashOpnameController extends Controller
{
    public function getListCashOpname(Request $request) {
        //bila parameter tidak dibutuhkan
        $params = Tools::params($request,array());

        if(1) {
            $data = (new CashOpname)->getList($params->h_property_id);
            return Response::send('Cash Opname List', 'content', $data);
        }
        else {
            return Response::send('Wrong Parameters');
        }
    }

    public function getCashOpnameDescription(Request $request)
    {
        $params = Tools::params($request, array(
            ['cash_opname_id', 'string'],
        ));

        $item = new CashOpname($params->cash_opname_id);
        if($item->exist) {
            $data = array(
                "tanggal"      => $item->date,
                "deskripsi"    => $item->description,
            );
            return Response::send('Cash Opname Description', 'content', $data);
        } else {
            return Response::send('Wrong Ingredient ID');
        }
    }
    
    public function getStatusDetail(Request $request)
    {
        $params = Tools::params($request, array(
            ['cash_opname_id', 'string'],
        ));

        $item = new CashOpname($params->cash_opname_id);
        if($item->exist) {
            $data = array(
                "id"         => $item->cash_opname_code,
                "status"     => $item->status,
            );
            return Response::send('Status Detail', 'content', $data);
        } else {
            return Response::send('Wrong Cashier Annotation ID');
        }
    }

    public function updateStatus(Request $request)
    {
        $params = Tools::params($request, array(
            ['cash_opname_id', 'string'],
            ['status', 'string'],
        ));

        $item = new CashOpname($params->cash_opname_id);
        $item->cash_opname_code    = $params->cash_opname_id;
        $item->status              = $params->status;
        $item->update_status();
        return Response::send('Status Update', 'content');
    }
}
