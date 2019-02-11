<?php
namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use \Firebase\JWT\JWT;
use App\User;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected const TOKEN_KEY = 'bHH2JilgwA3YxOqwn';

    //Busca en la BBDD el usuario con el email introducido.
    protected function findUser($email)
    {
        $user = User::where('email',$email)->first();       
        return $user; 
    }
    
    //Comprueba si el token es valido.
    protected function checkLogin()
    {   
        $headers = getallheaders();
        
        if(!isset($headers['Authorization']))
        { 
            return false;
        }

        $tokenDecoded = self::decodeToken();
        $user = self::getUserFromToken();
        
        if ($tokenDecoded->password == $user->password and $tokenDecoded->email == $user->email) 
        {
            return true;
        } else {
            return self::response('You dont have permission',301);
        }

    }

    //Obtiene los datos del usuario del token decodificado.
    protected function getUserfromToken()
    {
        $tokenDecoded = self::decodeToken();
        $user = self::findUser($tokenDecoded->email);
        return $user;
    }

    //Respuesta personalizable para success o error
    protected function response($text, $code){
        return response()->json([
            'message' => $text
        ],$code);
    }

    //Decodificador de token.
    protected function decodeToken() 
    {
        $headers = getallheaders();
        if(isset($headers['Authorization']))
        {
            $token = $headers['Authorization'];
            $tokenDecoded = JWT::decode($token, self::TOKEN_KEY, array('HS256'));
            return $tokenDecoded;
        }
    }
    
    protected function randomString($size){
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $count = mb_strlen($chars);
        for ($i = 0, $result = ''; $i < $size; $i++) {
            $index = rand(0, $count - 1);
            $result .= mb_substr($chars, $index, 1);
        }
        return $result;
    }

}
