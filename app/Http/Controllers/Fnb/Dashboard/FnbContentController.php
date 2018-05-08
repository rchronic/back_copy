<?php

namespace App\Http\Controllers\Fnb\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Platform\Response;

use Illuminate\Http\Request;

class FnbContentController extends Controller
{

    /**
     * Menu Content
     *
     * This method provides all the content for the Menu
     *
     * ### Required Headers
     *
     * Key | Value
     * --- | -----
     * TOKEN | Session_Token
     * USER_ID | User_ID
     * PROPERTY_ID | Property_ID
     */

    public function FnbMenuContent (){
        $data = array(
            array(
                'label'   => 'Dashboard',
                'uri'     => 'fnb/dashboard',
                'icon'    => 'ic_hotel_dashboard_idle.png',
                'submenu' => array(),
                'enabled' => true
            ),
            array(
                'label'   => 'Cash Opname',
                'uri'     => false,
                'icon'    => 'cash-opname',
                'submenu' => array(
                   array(
                       'label' => 'Cash Opname List',
                       'uri'   => 'fnb/cash-opname-list',
                       'icon'    => false,
                       'enabled' => true
                   ),
                   array(
                       'label' => 'Cashier Annotation',
                       'uri'   => 'fnb/cashier-annotation',
                       'icon'    => false,
                       'enabled' => true
                   )
                ),
                'enabled' => true
            ),
            array(
                'label'   => 'Ingredients',
                'uri'     => 'fnb/ingredients-list',
                'icon'    => 'ingredients',
                'submenu' => array(),
                'enabled' => true
            )
        );
        return $data;
        // return Response::send('Menu Content', 'content', $data);
    }
}
