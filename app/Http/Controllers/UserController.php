<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use App\Validator;
use \Firebase\JWT\JWT;
use Auth;

class UserController extends Controller
{
    const ID_ROLE = 2;
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
        $ids = $users->id;

        if(empty($users))
        {
            return parent::response("There are no users created", 400);
        } 

        return response()->json([
            'users'=>$users,
            'ids' => $ids
        ]);
    }

    public function register(Request $request){
        $name = $_POST['name'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        
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
        $user->password = $encondedPassword;
        $user->rol_id = self::ID_ROLE;
        $user->save();

        //Si queremos loguearnos directamente sin pasar por el login
        /*
        $token = self::generateToken($email, $password);
            return response()->json ([
                'token' => $token,
                'role_id' => $user->role_id
            ]);
        */
    }

    public function login()
    {
        $key = 'bHH2JilgwA3YxOqwn';

        $email = $_POST['email'];
        $password = $_POST['password'];

        if (Validator::isStringEmpty($email) or Validator::isStringEmpty($password)) {
            return parent::response("All fields have to be filled",400);
        }
        
        $user = User::where('email', $email)->first();
        $id = $user->id;
        $rol_id = $user->rol_id;

        if ($user->email == $email and password_verify($password, $user->password) and self::ID_ROLE == 2)
        {
            $token = self::generateToken($email, $password);
            
            return response()->json([
                'token' => $token,
                'user_id'=> $id, 
                'rol_id' => $rol_id
            ]);

        } else {
            
            return parent::response("Wrong data",400); 
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
    public function store(Request $request)
    {
        $name = $_POST['name'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        
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
        $user->password = $encondedPassword;
        $user->rol_id = self::ID_ROLE;
        
        $user->save();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\rol  $rol
     * @return \Illuminate\Http\Response
     */
    public function show(User $User)
    {
        //
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
        if(parent::checkLogin() == false){
            return parent::response("There is a problem with your session",301);
        }

        if(!Validator::isValidEmail($request['email'] && !is_null($request['email']))){
            return parent::response('Use a valid email.', 400);
        }

        if(isEmailInUse($request['email']) && !is_null($request['email'] &&))

        $name = $request['name'];
        $email = $request['email'];
        $password = $request['password'];

        $user->name = $name;
        $user->email = $email;
        $user->password = $password;

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

    private function isEmailInUse($email)
    {
      $users = User::where('email', $email)->get();
      foreach ($users as $user) 
      {
            if($user->email == $email)
            {
                return true;
            }
        }  
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
}