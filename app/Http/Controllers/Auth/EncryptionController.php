<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller,

    Illuminate\Support\Facades\DB;

class EncryptionController extends Controller
{


    /**
     * Password encryption system.
     *
     * @param  str  $password
     * @return str
     */
    public function encryptPassword ($pswd)
    {
        $key  = DB::table('COMPANY')->first();
        $salt = $key->encryptionKey?$key->encryptionKey:'defaultEncryption'; // PipeAppsEncrptKey2017ClientT3s71
        $hash = hash_pbkdf2("sha256", $pswd, $salt, 2017, 128, false);
        return $hash;
    }
}
