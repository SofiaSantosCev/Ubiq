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

    protected function findUser($email)
    {
        $user = User::where('email',$email)->first();       
        return $user; 
    }
    protected function returnToken($dataToken)
    {
        $key = 'bHH2JilgwA3YxOqwn';

        $token = JWT::encode($dataToken, $key);         

        return $token;
    }

    protected function getUserfromToken()
    {
        $tokenDecoded = self::decodeToken();
        $user = self::findUser($tokenDecoded->email);
        return $user;
    }

    protected function error($code, $message)
    {
        $json = ['message'=> $message];
        $json = json_encode($json);
        return response($json, $code)->header('Access-Control-Allow-Origin', '*');
    }

    protected function success($message, $data = [])
    {
        $json = ['message'=> $message, 'data' => $data];
        $json = json_encode($json);
        return response($json, 200)->withHeaders([
            'Content-Type' => 'application/json',
            'Access-Control-Allow-Origin' => '*',
        ]);
    }

    protected function generateToken($email, $password)
    {
        $dataToken = [
            'email' => $email,
            'password' => $password,
            'random' => time()
        ];

        $token = JWT::encode($dataToken, self::TOKEN_KEY);         

        return $token;
    }

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

    protected function IsLoggedIn()
    {
        $headers = getallheaders();
        if (!isset($headers['Authorization'])) 
        {
            return false;
        } else {
            $user = self::getUserfromToken();
            $tokenDecoded = self::decodeToken();
            if ($tokenDecoded->password == $user->password and $tokenDecoded->email == $user->email) 
            {
                return true;
            } else {
                return self::error(301, 'no tienes permisos');
            }
        }
    }
}
