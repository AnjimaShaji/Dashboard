<?php

namespace App\CustomLibraries;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;
use Log;
/**
 * Description of PasswordChangeLog
 *
 * @author waybeo
 */
class PasswordChangeLog {

    public static function insertChangeLog($data)
    {
        DB::table('password_change_log')->insert($data);
    }
}