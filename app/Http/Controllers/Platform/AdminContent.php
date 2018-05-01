<?php

namespace App\Http\Controllers\Platform;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\Platform\User;
use App\Http\Controllers\Platform\Services;
use App\Http\Controllers\Platform\Log;
use App\Http\Controllers\Platform\Response;

class AdminContent extends Controller
{
    public function dashboardContent (Request $request) {
        $user = (new User)->getData($request->header("USER_ID"));
        $services = (new Services)->getServices('menu',$user->id);

        $data = array(
            'profile'  => array(
                'id'    => $user->code,
                'name'  => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'icon'  => $user->icon,
                'status'=> 'Super Admin'
            ),
            'services' => $services,
            'user' => array(
                0 => array(
                    'label'  => 'Users & Permissions',
                    'uri'    => 'admin/users/list',
                    'icon'   => 'Ic_menu_general_platform_user_management.png',
                    'status' => true
                ),
                1 => array(
                    'label' => 'Access Group',
                    'uri'   => 'admin/users/accessGroup',
                    'icon'  => 'Ic_menu_hotel_access_group.png',
                    'status' => true
                )
            ),
            'property' => array(
                0 => array(
                    'label' => 'Property Data',
                    'uri'   => 'admin/property',
                    'icon'  => 'Ic_menu_general_platform_property_management.png',
                    'status' => true
                )
            )
        );

        Log::set($user->id,'content','Display Admin Dashboard');

        return Response::send('Dashboard Content', 'content', $data);
    }

}
?>