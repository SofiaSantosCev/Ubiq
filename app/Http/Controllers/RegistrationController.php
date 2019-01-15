<?php
namespace App\Http\Controllers;
 
use Illuminate\Http\Request;
use App\User;
use App\Validator;
 
class RegistrationController extends Controller
{
    const ID_ROL = 2;
    
    //Crear nuevo usuario
    public function store()
    {
        $name = $_POST['user'];
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

        $token = parent::generateToken($email, $password);
        
        $user->save();
        return response()->json([
            'token' => $token
        ]);
    }

    //Eliminar usuario
    public function destroy($id)
    {
        if(parent::isLoggedIn())
        {
            $user = parent::getUserfromToken();
            $user->delete();
            return parent::success('Your account has been deleted', "");  
        } else {
            return parent::error('Error in login', 301);
        }
        
    }

    private function isEmailInUse($email)
    {
      $users = User:where('email', $email)->get();
      foreach ($users as $user) 
      {
            if($user->email == $email)
            {
                return true;
            }
        }  
    }
}