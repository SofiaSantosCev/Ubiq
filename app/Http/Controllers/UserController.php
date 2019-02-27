<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use App\Validator;
use \Firebase\JWT\JWT;
use Auth;
use Mail;
use Hash;

class UserController extends Controller
{
    const ROLE_ID = 2;
    const TOKEN_KEY = 'bHH2JilgwA3YxOqwn';
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    //Devuelve una lista de usuarios. method GET
    public function index()
    {
        if (!parent::checkLogin())
        {  
            return parent::response("You don't have permission", 403); 
        }
        
        $users = User::all();

        if(empty($users))
        {
            return parent::response("There are no users created", 400);
        } 

        return response()->json([
            'users'=>$users
        ]);
    }

    //Logea al usuario
    public function login()
    {
        $email = $_POST['email'];
        $password = $_POST['password'];

        if (Validator::isStringEmpty($email) or Validator::isStringEmpty($password)) {
            return parent::response("All fields have to be filled",400);
        }
        
        $user = User::where('email', $email)->first();
        $id = $user->id;
        $role_id = $user->rol_id;

        if ($user->email == $email and password_verify($password, $user->password))
        {
            $token = self::generateToken($email, $password);
            
            return response()->json([
                'token' => $token,
                'user_id'=> $id, 
                'role_id' => $role_id
            ]);

        } else {

            return parent::response("You don't have access",400); 
        }
    }

    //Recuperacion de contraseña
    public function recoveryPassword(Request $request){
        if (Validator::isEmailInUse($request->email)){
            try {
                $user = parent::findUser($request->email);
                $newPassword = parent::randomString(8);
                $to_name = $user->name;
                $to_email = $user->email;
                $user->update([
                    'password' => Hash::make($newPassword),
                ]);

                $data = array('name'=>$user->name, "password" => $newPassword );

                Mail::send('emails.forgot', $data, function($message) use ($to_name, $to_email) {
                    $message->to($to_email, $to_name)->subject('Ubiq | Forgot password');
                    $message->from('sofia_santos_apps1ma1718@cev.com','Ubiq');
                });
                return parent::response('New password sent', 200);

            } catch (Exception $e) {
                return parent::response('Error in the request', 400);
            }      
        
        } else {
            return parent::response('This email is not registered', 400);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    //Crea un usuario en la BBDD desde un formulario de la app
    public function store(Request $request)
    {
        $name = $_POST['name'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $rol_id = 2;
        
        //Comprueba que no haya campos vacíos
        if(Validator::isStringEmpty($email) or Validator::isStringEmpty($name) or Validator::isStringEmpty($password))
        {
            return parent::response("The text fields cannot be empty",400);
        }
        
        //Comprueba que el email no esté en uso
        if (self::isEmailInUse($email)) 
        {
            return parent::response("The email already exists",400); 
        }
        
        //mínimo 8 caracteres en la contraseña
        if(!Validator::reachesMinLength($password, 8))
        {
            return parent::response("Invalid password. It must be at least 8 characters long.",400); 
        }

        $user = new User;
        $user->name = $name;
        $user->email = $email;
        $encondedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        if (isset($rol_id)){
            $user->rol_id = $rol_id;
        } else {
            $user->rol_id = self::ROLE_ID;
        }
        $user->password = $encondedPassword;
        $user->save();

        return parent::response("User created", 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\rol  $rol
     * @return \Illuminate\Http\Response
     */
    public function show(User $User)
    {
        return response()->json([
            'user'=>$User
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\rol  $rol
     * @return \Illuminate\Http\Response
     */
    public function edit(User $User)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\rol  $rol
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {

        $name = $request['name'];
        $email = $request['email'];

        if(!parent::checkLogin()){
            return parent::response("There is a problem with your session",301);
        }

        if(!Validator::isValidEmail($request['email']) && !is_null($request['email'])){
            return parent::response('Use a valid email.', 400);
        }
        
        //Comprueba que el email no esté en uso
        if (self::isEmailInUse($email)) 
        {
            return parent::response("The email already exists",400); 
        }

        if($user->name != $name && !is_null($name)){
            $user->name = $name;
        }

        if($user->email != $email && !is_null($email) && !self::isEmailInUse($email)){
            $user->email = $email;
        }

        $user->update();
        return parent::response("User modified",200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\rol  $rol
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        $user->delete();
        return parent::response('User deleted.',200);
    }

    //Comprueba si el email ya está utilizado
    private function isEmailInUse($email)
    {  
        if (User::where('email', $email)->first()){
            return true;
        } else {
            return false; 
        }
    }

//Genera el token con los datos introducidos
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

public function banned($id){
    
    if(!parent::checkLogin()){
        return parent::response("There is a problem with your session",301);
    }

    $user = User::find($id);
    $user->banned = !$user->banned;

    $user->update();

    return response()->json([
        "message" => "user banned",
        "code" => 200,
        "status" => $user->banned,
    ]);

}
}