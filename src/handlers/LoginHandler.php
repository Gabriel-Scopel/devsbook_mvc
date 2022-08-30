<?php
namespace src\handlers;
use \src\models\User;
//classe específica para fazer verificações de login
class LoginHandler {
    public static function checkLogin(){
        if(!empty($_SESSION['token'])){ //verficando se há algum token na sessão
            $token = $_SESSION['token'];
            $data = User::select()->where('token, $token')->one();//verificando de qual usuário é aquele token
            if(count($data)>0){
                $loggedUser = new User();
                $loggedUser->Id = ($data['id']);
                $loggedUser->email = ($data['email']);
                $loggedUser->name = ($data['name']);
                return $loggedUser;
            }
        }
        return false;
    }
    public static function verifyLogin($email, $password){
        $user = User::select()->where('email', $email)->one();
        if($user){
            if(password_verify($password, $user['password'])){
                $token = md5(time().rand(0,9999).time());
                User::update()->set('token, $token')->where('email', $email)->execute();
                return $token;
            }
        }
        return false;
    }
    public function emailExists($email){
        $user = User::select()->where('email', $email)->one();
        return $user ? true : false;
    }
    public function addUser($name, $email, $password, $birthdate){
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $token = md5(time().rand(0, 9999).time());

        User::insert([
            'email' => $email,
            'password' => $hash,
            'birthdate' => $birthdate,
            'token' => $token
        ])->execute();
        return $token;
    }
}