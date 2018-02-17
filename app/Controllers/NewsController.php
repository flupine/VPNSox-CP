<?php
namespace App\Controllers;


use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Respect\Validation\Validator;
use Slim\Handlers\Strategies\RequestResponseArgs;

class NewsController extends Controller {
  

   public function news(RequestInterface $request, ResponseInterface $response)
   {
       if($this->isAdmin()){
           $user = $this->user_infos($_SESSION['auth']['id']);
           return $this->render($response, 'pages/admin/news.twig', ['account' => $user]);
       }else {
           $this->flash("Not Allowed !", 'error');
           return $this->redirect($response, 'root');
       }
    }
  
  public function addNews(RequestInterface $request, ResponseInterface $response)
  {
       if (!$this->isOnline())
       {
           $this->flash("Not Allowed !", 'error');
           return $this->redirect($response, 'login');
       }
    
       $user = $this->user_infos($_SESSION['auth']['id']);
       if($this->isAdmin()){
            $errors = [];
             Validator::notEmpty()->validate($request->getParam('title') || $errors['title'] = 'Please enter a valid title');
             Validator::notEmpty()->validate($request->getParam('content') || $errors['content'] = 'Please enter a valid content');
             if(empty($errors)){
               $req = $this->db->prepare("INSERT INTO news SET title = ?, content = ?, author = ?, date = NOW()");
               $req->execute([htmlspecialchars($request->getParam('title'), ENT_NOQUOTES), htmlspecialchars($request->getParam('content'), ENT_NOQUOTES), $user['username']]);
               $this->addLog($user['username'], 'Created new article');
               $this->flash('Article submited', 'success');
               return $this->redirect($response, 'news');
             }else{
                 $this->addLog($user['username'], 'Tried to create article');
                 $this->flash('Please check forms', 'error');
                 $this->flash($errors, 'errors');
                 return $this->redirect($response, 'news');
             }
         return $this->redirect($response, 'news');
        }else {
            $this->addLog($user['username'], 'Tried to access admin articles area');
            $this->flash("Not Allowed !", 'error');
            return $this->redirect($response, 'root');
        }
  }

}