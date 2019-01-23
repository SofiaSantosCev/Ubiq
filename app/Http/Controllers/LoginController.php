<?php

namespace App\Http\Controllers;

use App\Login;
use Illuminate\Http\Request;
use \Firebase\JWT\JWT;
use App\User;
use App\Validator;
use Auth;

class LoginController extends Controller
{
    public function login()
    {
        $key = 'bHH2JilgwA3YxOqwn';

        $email = $_POST['email'];
        $password = $_POST['password'];

        if (Validator::isStringEmpty($email) or Validator::isStringEmpty($password)) {
            return $this->error(400, "All fields have to be filled");
        }
        
        $user = User::where('email', $email)->first();
        $id = $user->id;

        if ($user->email == $email and password_verify($password, $user->password))
        {
            $token = parent::generateToken($email, $password);
            
            return response()->json([
                'token' => $token,
                'user_id'=> $id
            ]);

        } else {
            
            return parent::error(400, "Wrong data"); 
        }
    } 
}