<?php
namespace App\Controllers;


use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Respect\Validation\Validator;
use Slim\Handlers\Strategies\RequestResponseArgs;

class LoginController extends Controller {
  

    public function logout(RequestInterface $request, ResponseInterface $response)
    {
        $this->addLog($_SESSION['auth']['username'], 'Logout');
        unset($_SESSION['auth']);
        $this->flash('You\'re now disconnected');
        return $this->redirect($response, 'login');

    }

    public function login(RequestInterface $request, ResponseInterface $response)
    {
        if($this->isOnline()){
            return $this->redirect($response, 'root');
        }else {
            return $this->render($response, 'pages/login.twig');
        }
    }
  
  public function postLogin(RequestInterface $request, ResponseInterface $response)
    {
        $errors = [];
        Validator::notEmpty()->validate($request->getParam('username') || $errors['name'] = 'Please enter a username');
        Validator::notEmpty()->validate($request->getParam('password') || $errors['password'] = 'Please enter a password');

        if(empty($errors)){
            $req = $this->db->prepare('SELECT * FROM users WHERE (username = :username OR email = :username) AND confirmed_at IS NOT NULL');
            $req->execute(['username' => $request->getParam('username')]);
            $user = $req->fetch();

            if($user == null){
                $this->flash('Username or Password is invalid', 'error');
                return $this->redirect($response, 'login');

            }elseif(password_verify($request->getParam('password'), $user['password'])){
                $this->addLog($request->getParam('username'), 'Login Success');
                $this->flash('You\' re now Connected');
                $_SESSION['auth'] = $user;
                return $this->redirect($response, 'root');
            }else{
                $this->addLog($request->getParam('username'), 'Login Error');
                $this->flash('Username or Password is invalid', 'error');
                return $this->redirect($response, 'login');

            }


        }else{
            $this->flash('Oups, check your form:', 'error');
            $this->flash($errors, 'errors');
            return $this->redirect($response, 'login');
        }

        return $this->redirect($response, 'login');


    }

    public function confirm(RequestInterface $request, ResponseInterface $response, $args)
    {
        $errors = [];
        Validator::notEmpty()->validate($args['id'] || $errors['id'] = 'ID is empty');
        Validator::notEmpty()->validate($args['token'] || $errors['token'] = 'token is empty');

        if(empty($errors)) {

            $user_id = $args['id'];
            $token = $args['token'];
            $req = $this->db->prepare('SELECT * FROM users WHERE id = ?');
            $req->execute([$user_id]);
            $user = $req->fetch();

            if($user && $user['confirmation_token'] == $token ){
                $this->db->prepare('UPDATE users SET confirmation_token = NULL, confirmed_at = NOW() WHERE id = ?')->execute([$user_id]);
                $this->addLog($user['username'], 'Account Verified');
                $this->flash('Account Verified');
                $_SESSION['auth'] = $user;
                return $this->redirect($response, 'root');
            }else{
                $this->flash("Token Expired", 'error');
                var_dump($user);
                return $this->redirect($response, 'login');
            }

        }
        return $this->redirect($response, 'login');

    }
}