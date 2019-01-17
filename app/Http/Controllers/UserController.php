<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    const ID_ROL = 1;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (parent::isLoggedIn())
        {   
            $users = User::all();
            $userName = [];
            $userEmail = [];
            $userPassword = [];
            $userIds = [];

            if(empty($users))
            {
                return parent::error(400,"There are no users created");
            } 

            foreach ($users as $user) {
                array_push($userName, $user->name);
            }

            return response($users);

        } else {
            return parent::error(403,"You don't have permission");
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
        $user->rol_id = self::ID_ROL;
        
        $user->save();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\rol  $rol
     * @return \Illuminate\Http\Response
     */
    public function show(rol $rol)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\rol  $rol
     * @return \Illuminate\Http\Response
     */
    public function edit(rol $rol)
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
    public function update(Request $request, rol $rol)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\rol  $rol
     * @return \Illuminate\Http\Response
     */
    public function destroy(rol $rol)
    {
        //
    }
}