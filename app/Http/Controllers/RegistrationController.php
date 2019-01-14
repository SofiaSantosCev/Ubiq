<?php
namespace App\Http\Controllers;
 
use Illuminate\Http\Request;
 
class RegistrationController extends Controller
{
	public function store()
    {
        $name = $_POST['user'];
        $email = $_POST['email'];
        $password = $_POST['password'];

        //Si el campo no está vacío
        if (!ctype_graph($name)) {
            return parent::error(400,"The user name must be only one word"); 
        }
  
        $user = User::where('email', $email)->first();

        if($user != null){
            if ($email == $user->email) {
                return parent::error(400,"The email already exists"); 
            }
        }

        //minimo de caracteres en la contraseña
        if (strlen($password) < 8) {
            return parent::error(400,"Invalid password. It must be at least 8 characters long."); 
        }

        $rol_id = self::ID_ROL;

        $user = new User;

        $user->name = $name;
        $user->email = $email;
        $encondedPassword = password_hash($password, PASSWORD_DEFAULT);
        $user->password = $encondedPassword;
        $user->rol_id = $rol_id;

        $dataToken =[
            'email' => $user->email, 
            'password' => $user->password,
            'random' => time()
        ];
        
        $user->save();
        return parent::success("User created", parent::returnToken($dataToken));
    }

    public function destroy($id)
    {
        $user = User::find($id);
        $user->delete();
        var_dump("Your account has been deleted");
    }
}