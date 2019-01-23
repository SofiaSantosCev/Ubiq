<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use App\Validator;

class UserController extends Controller
{
    const ID_ROLE = 2;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (!parent::isLoggedIn())
        {  
            return parent::error(403,"You don't have permission"); 
        }
        
        $users = User::all();
        if(empty($users))
        {
            return parent::error(400,"There are no users created");
        } 

        return response()->json([
            'users'=>$users
        ]);
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
            return parent::error(400, "The text fields cannot be empty");
        }
        
        //Comprueba que el email no esté en uso
        if (self::isEmailInUse($email)) 
        {
            return parent::error(400,"The email already exists"); 
        }
        
        //mínimo 8 caracteres en la contraseña
        if(!Validator::reachesMinLength($password, 8))
        {
            return parent::error(400,"Invalid password. It must be at least 8 characters long."); 
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
            return $this->error(301, "There is a problem with your session");
        }

        $name = $request->name;
        $email = $request->email;
        $password = $request->password;

        $user->name = $name;
        $user->email = $email;
        $user->password = $password;

        $user->update();
        return $this->success("User modified", "");
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
        return $this->success('User deleted.');
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
}