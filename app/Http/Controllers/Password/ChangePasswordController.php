<?php

namespace App\Http\Controllers\Password;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;
use App\CustomLibraries\PasswordChangeLog;
use Auth;

class ChangePasswordController extends Controller
{
  public function changePassword()
  { 
    return view('password.password');
  }

  public function updatePassword()
  {  
    $params = Input::all();
    $valid = \Validator::make($params,[
      'password' => ['required','regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*[@#$%^&+*!=]).(?=.*[0-9]).*$/','min:10','max:20']
    ]);
    if($valid->fails()){
      return response()->json(['status' => false]);
    }
    $userId = Auth::id();
    
    $update['password'] = bcrypt($params['password']);
    DB::table('users')
      ->where('id',$userId)
      ->update($update);
      $array_log=array('user_id'=>$userId,'password'=>$params['password'],'updated_by'=>$userId);
      PasswordChangeLog::insertChangeLog($array_log);
    return response()->json(['status' => true]);
  }
}
