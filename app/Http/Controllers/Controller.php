<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected const TOKEN_KEY = "fo23k4f2o34fij324f023j4f2034";

    protected function findUser($email)
    {
        $user = User::where('email',$email)->first();       
        return $user;
        
    }

    protected function getUserFromToken()
    {
        $decodedToken = self::decodeToken();
        $user = self::findUser($decodedToken->email);
        return $user;
    }

    //Comprueba si el token es valido.
    protected function checkLogin()
    {   
        $headers = getallheaders();
        if(!isset($headers['Authorization']) ) { return false;}    
        $tokenDecoded = self::decodeToken();
        $user = self::getUserFromToken();
        if ($tokenDecoded->password == $user->password and $tokenDecoded->email == $user->email) 
        {
            return true;
        }        
        else             
        {
            return response ('no tienes permisos', 301);
        }

    }

    private static function decodeToken()
    {  
        $headers = getallheaders();
        if(isset($headers['Authorization'])) 
        {
            $token = $headers['Authorization'];
            $tokenDecoded = JWT::decode($token, self::TOKEN_KEY, array('HS256'));
            return $tokenDecoded;
        }
    }

    protected function response($text, $code = 400){
    	return response()->json ([
            'text' => $text
        ],$code);
    }

    private function hasOnlyOneWord($name)
    {     
        if(ctype_graph($name)) 
        {
            return true;
        }
        else 
        {
            return false; 
        }
    }
}
