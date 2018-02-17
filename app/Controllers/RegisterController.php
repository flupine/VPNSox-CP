<?php
namespace App\Controllers;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Respect\Validation\Validator;
use Slim\Handlers\Strategies\RequestResponseArgs;

class RegisterController extends Controller {
  
    public function register(RequestInterface $request, ResponseInterface $response)
    {

        if($this->isOnline()){
            return $this->redirect($response, 'root');
        }else {
            return $this->render($response, 'pages/register.twig');
        }
    }
  
   public function postRegister(RequestInterface $request, ResponseInterface $response)
   {
       $errors = [];
       Validator::email()->validate($request->getParam('email') || $errors['email'] = 'Please enter a valid email');
       Validator::notEmpty()->validate($request->getParam('username') || $errors['name'] = 'Please enter a username');
       Validator::notEmpty()->validate($request->getParam('password') || $errors['password'] = 'Please enter a password');
       Validator::notEmpty()->validate($request->getParam('password_confirm') || $errors['password'] = 'Please confirm your password');
       if($request->getParam('password') != $request->getParam('password_confirm')){
           $errors['password_confirm'] = 'Not the same password';
       }
       if(strlen($request->getParam('username')) < 3 || strlen($request->getParam('username')) > 15 ||  !ctype_alnum($request->getParam('username'))){

           $errors['username'] = 'Username error';
       }
       if($this->user_exists($request->getParam('username')) || $this->email_exists($request->getParam('email'))){
           $errors['user_exist'] = 'User already exist';
       }

       if(empty($errors)){

           $req = $this->db->prepare("INSERT INTO users SET username = ?, password = ?, email = ?, confirmation_token = ?, vpn_pass = ?");
           $password = password_hash($request->getParam('password'), PASSWORD_BCRYPT);
           $token = $this->str_random(60);
           $vpn_pass = $this->str_random(8);
           $req->execute([$request->getParam('username'), $password, $request->getParam('email'), $token, $vpn_pass]);
           $user_id = $this->db->lastInsertId();
           $message = \Swift_Message::newInstance('Confirmation de votre compte')
               ->setFrom(["noreply@vpnsox.org" => "VPNSox"])
               ->setTo($request->getParam('email'))
               ->setBody("Afin de valider votre compte merci de cliquer sur ce lien https://panel.vpnsox.org/login/{$user_id}/{$token}"
               );
           $this->mailer->send($message);
           $this->addLog($request->getParam('username'), 'Account Created');
           $this->flash('Un email de confirmation vous a été envoyé pour valider votre compte');
           return $this->redirect($response, 'login');

       }else{
           $this->flash('Please check forms', 'error');
           $this->flash($errors, 'errors');
           return $this->redirect($response, 'register');
       }
       return $this->redirect($response, 'login');
   }



}