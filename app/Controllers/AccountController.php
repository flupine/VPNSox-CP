<?php
namespace App\Controllers;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Respect\Validation\Validator;
use Slim\Handlers\Strategies\RequestResponseArgs;

class AccountController extends Controller {
  
    public function account(RequestInterface $request, ResponseInterface $response)
    {
        if($this->isOnline()){
            $user = $this->user_infos($_SESSION['auth']['id']);
            return $this->render($response, 'pages/account/home.twig', ['account' => $user]);
        }else {
            $this->flash('Please login to your account', 'error');
            return $this->redirect($response, 'login');
        }
    }
  
   public function newApi(RequestInterface $request, ResponseInterface $response)
    {
        if($this->isOnline()){
            $user = $this->user_infos($_SESSION['auth']['id']);
            $api_key = $this->str_random(42);
            $api_secret = $this->str_random(42);
            $req = $this->db->prepare("UPDATE users SET api_key = ?, api_secret = ? WHERE id = ?");
            $req->execute([$api_key, $api_secret, $user['id']]);
            $this->addLog($user['username'], 'Regenerate API Keys');
            $this->flash('New API Keys generated with success', 'success');
            return $this->redirect($response, 'account');
        }else {
            $this->flash('Please login to your account', 'error');
            return $this->redirect($response, 'login');
        }
    }
 
  public function changePassword(RequestInterface $request, ResponseInterface $response)
  {
        if($this->isOnline())
        {
          $user = $this->user_infos($_SESSION['auth']['id']);
          $errors = [];
          if($request->getParam('password') != $request->getParam('password_confirm'))
          {
              $errors['password_confirm'] = 'Not the same password';
          }
          if(password_verify($request->getParam('current_password'), $user['password']) && empty($errors))
          {
              $req = $this->db->prepare("UPDATE users SET password = ? WHERE id = ?");
              $password = password_hash($request->getParam('password'), PASSWORD_BCRYPT);
              $req->execute([$password, $user['id']]);
              $this->addLog($user['username'], 'Modified Password');
              $this->flash('Password modified with success', 'success');
              return $this->redirect($response, 'account');
          }
          $errors['current_password'] = 'Actual password invalid';
          $this->addLog($user['username'], 'Failed to modify Password');
          $this->flash('Please check forms', 'error');
          $this->flash($errors, 'errors');
          return $this->redirect($response, 'account');
        }
        else
        {
          $this->flash('Please login to your account', 'error');
          return $this->redirect($response, 'login');
        }
  }
  
   public function changeVpnPassword(RequestInterface $request, ResponseInterface $response)
  {
     if($this->isOnline()){
            $user = $this->user_infos($_SESSION['auth']['id']);
            $vpn_pwd = $this->str_random(12);
            $req = $this->db->prepare("UPDATE users SET vpn_pass = ? WHERE id = ?");
            $req->execute([$vpn_pwd, $user['id']]);
            $req = $this->db2->prepare("UPDATE user SET user_pass = ? WHERE user_id = ?");
            $req->execute([$vpn_pwd, $user['username']]);
            $this->addLog($user['username'], 'Regenerate VPN password');
            $this->flash('New VPN password generated with success', 'success');
            return $this->redirect($response, 'account');
        }else {
            $this->flash('Please login to your account', 'error');
            return $this->redirect($response, 'login');
        }
     
  }
  
  
  
}