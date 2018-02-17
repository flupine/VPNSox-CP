<?php
namespace App\Controllers;


use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Respect\Validation\Validator;
use Slim\Handlers\Strategies\RequestResponseArgs;

class PagesController extends Controller {


    /**
     * @param RequestInterface $request
     * @param ResponseInterface $response
     */
    public function home(RequestInterface $request, ResponseInterface $response)
    {
        if($this->isOnline()){
            $user = $this->user_infos($_SESSION['auth']['id']);
            $news = $this->getNews();
            return $this->render($response, 'pages/home.twig', ['account' => $user, 'ip' => $_SERVER['SERVER_NAME'], 'news' => $news]);
        }else {
            return $this->redirect($response, 'login');
        }
    }

    public function admin(RequestInterface $request, ResponseInterface $response)
    {
        if (!$this->isOnline())
        {
            $this->flash("Not Allowed !", 'error');
            return $this->redirect($response, 'login');
        }
        
      
        $user = $this->user_infos($_SESSION['auth']['id']);
        if($this->isAdmin()){
            $user = $this->user_infos($_SESSION['auth']['id']);
            $logs= $this->getLogs();
            $invoices = $this->latestInvoices();
            return $this->render($response, 'pages/admin/home.twig', ['account' => $user, 'logs' => $logs, 'invoices' => $invoices]);
        }else {
            $this->addLog($user['username'], 'Tried to access admin articles area');
            $this->flash("Not Allowed !", 'error');
            return $this->redirect($response, 'root');
        }
    }

    public function adminLogs(RequestInterface $request, ResponseInterface $response, $args){
        if (!$this->isOnline())
        {
            $this->flash("Not Allowed !", 'error');
            return $this->redirect($response, 'login');
        }
        $user = $this->user_infos($_SESSION['auth']['id']);
      
        if($this->isAdmin()){
            $client = $this->user_infos_username($args['user']);
            if($user == 84 || $client == 84)
            {
              $this->flash("No user with this id or name !", 'error');
              return $this->redirect($response, 'admin');
            }
            $logs = $this->getUserLogs($args['user']);
            $pages = intval($args['page']) . 0;
            return $this->render($response, 'pages/admin/logs.twig', ['account' => $user, 'logs' => $logs, 'user' => $client, 'page' => $pages, 'current' => intval($args['page'])]);
        }else {
            $this->addLog($user['username'], 'Tried to access admin logs area');
            $this->flash("Not Allowed !", 'error');
            return $this->redirect($response, 'root');
        }


    }
  
     public function adminLogsPurge(RequestInterface $request, ResponseInterface $response, $args){
        if (!$this->isOnline())
        {
            $this->flash("Not Allowed !", 'error');
            return $this->redirect($response, 'login');
        }
        $user = $this->user_infos($_SESSION['auth']['id']);
      
        if($this->isAdmin()){
            $client = $this->user_infos_username($args['user']);
            if($user == 84 || $client == 84)
            {
              $this->addLog($user['username'], ' Failed to find' . $client['username'] . 'logs');
              $this->flash("No user with this id or name !", 'error');
              return $this->redirect($response, 'admin');
            }
            $req = $this->db->prepare("DELETE FROM `logs` WHERE `log_user` = ?");
            $req->execute([$client['username']]); 
            $this->addLog($user['username'], 'Remove : ' . $client['username'] . ' logs');
            $this->addLog($client['username'], 'Logs removed by : ' . $user['username']);
            $this->flash("All logs removed !", 'success');
            return $this->redirect($response, 'admin');
        }else {
            $this->addLog($user['username'], 'Tried to access admin logs area');
            $this->flash("Not Allowed !", 'error');
            return $this->redirect($response, 'root');
        }


    }
  
    public function adminSearch(RequestInterface $request, ResponseInterface $response, $args){
        if (!$this->isOnline())
        {
            $this->flash("Not Allowed !", 'error');
            return $this->redirect($response, 'login');
        }
        $user = $this->user_infos($_SESSION['auth']['id']);
      
        if($this->isAdmin()){
            $client = $this->user_infos_username($request->getParam('user'));
            if($user == 84 || $client == 84)
            {
              $this->flash("No user with this id or name !", 'error');
              $this->addLog($user['username'], 'User Search error');
              return $this->redirect($response, 'admin');
            }
            $logs = $this->getUserLogs($request->getParam('user'));
            return $this->render($response, 'pages/admin/logs.twig', ['account' => $user, 'logs' => $logs, 'user' => $client, 'page' => $pages, 'current' => 1]);
        }else {
            $this->addLog($user['username'], 'Tried to access admin logs area');
            $this->flash("Not Allowed !", 'error');
            return $this->redirect($response, 'root');
        }


    }

    public function tos(RequestInterface $request, ResponseInterface $response)
    {

        if($this->isOnline()){
            $user = $this->user_infos($_SESSION['auth']['id']);
            return $this->render($response, 'pages/tos.twig', ['account' => $user]);
        }else {
            return $this->redirect($response, 'login');
        }
    }

    public function cancel(RequestInterface $request, ResponseInterface $response)
    {

        if($this->isOnline()){
            $this->addLog($_SESSION['auth']['username'], 'Payment Cancel');
            $this->flash('Payment Cancel', 'error');
            return $this->redirect($response, 'root');
        }else {
            return $this->redirect($response, 'login');
        }
    }

    public function success(RequestInterface $request, ResponseInterface $response)
    {

        if($this->isOnline()){
            $this->flash('Payment Success');
            return $this->redirect($response, 'root');
        }else {
            return $this->redirect($response, 'login');
        }
    }



    public function logs(RequestInterface $request, ResponseInterface $response)
    {

        if($this->isOnline()){
            $user = $this->user_infos($_SESSION['auth']['id']);
            $logs= $this->vpnLogs($_SESSION['auth']['username']);
            return $this->render($response, 'pages/logs.twig', ['account' => $user, 'logs' => $logs]);
        }else {
            return $this->redirect($response, 'login');
        }
    }

    public function invoices(RequestInterface $request, ResponseInterface $response)
    {

        if($this->isOnline()){
            $user = $this->user_infos($_SESSION['auth']['id']);
            $invoices = $this->getInvoices($_SESSION['auth']['username']);
            return $this->render($response, 'pages/payments.twig', ['account' => $user, 'invoices' => $invoices, 'ip' => $_SERVER['SERVER_NAME']]);
        }else {
            return $this->redirect($response, 'login');
        }
    }



    public function config(RequestInterface $request, ResponseInterface $response)
    {
        if($this->isOnline()){
            $user = $this->user_infos($_SESSION['auth']['id']);
            $plan = $this->listServers($user['plan']);
            return $this->render($response, 'pages/config.twig', ['account' => $user, 'servers' => $plan, 'ip' => $_SERVER['SERVER_NAME']]);
        }else {
            return $this->render($response, 'pages/login.twig');
        }
    }

    public function download(RequestInterface $request, ResponseInterface $response, $args)
    {
        $user = $this->user_infos($_SESSION['auth']['id']);
        $valeurs = array('file' => $args['id'], 'plan' => $user['plan']);
        $req = $this->db->prepare("SELECT * FROM servers WHERE(id = :file AND plan <= :plan)");
        $req->execute($valeurs);
        $result = $req->fetch();

        if(empty($result)){
            $this->addLog($_SESSION['auth']['username'], 'File Inexist');
            $this->flash('This file not exist', 'error');
            return $this->redirect($response, 'root');
        }else{
            $name = $result['name'];
            $file = '../app/Uploads/' . $name . '.zip';
            $response = $response->withHeader('Content-Description', 'File Transfer')
                ->withHeader('Content-Type', 'application/zip')
                ->withHeader('Content-Disposition', 'attachment;filename="'.$name.'.zip"')
                ->withHeader('Expires', '0')
                ->withHeader('Cache-Control', 'must-revalidate')
                ->withHeader('Pragma', 'public')
                ->withHeader('Content-Length', filesize($file));
            readfile($file);
            $this->addLog($_SESSION['auth']['username'], 'Server ' . $result['name'] . ' Downloaded');
            return $response;
            return $this->redirect($response, 'config');

        }
    }

    public function ipn(RequestInterface $request, ResponseInterface $response){
        if(!$this->isOnline()){
            return $this->redirect($response, 'root');
        }


        $cp_merchant_id = '04f4b9c69ad87a3d78ae53497a65beca';
        $cp_ipn_secret = 'Sj2P37wVwwg6T53B4H93KbUwc';

        //These would normally be loaded from your database, the most common way is to pass the Order ID through the 'custom' POST field.
        $order_currency = 'EUR';
        $order_total = 3.00;


        if (is_null($request->getParam('ipn_mode')) || $request->getParam('ipn_mode') != 'hmac') {
            $this->errorAndDie('IPN Mode is not HMAC');
        }

        if (is_null($_SERVER['HTTP_HMAC']) || empty($_SERVER['HTTP_HMAC'])) {
            $this->errorAndDie('No HMAC signature sent.');
        }

        $file = file_get_contents('php://input');
        if ($file === FALSE || empty($file)) {
            $this->errorAndDie('Error reading POST data');
        }

        if (is_null($request->getParam('merchant')) || $request->getParam('merchant') != trim($cp_merchant_id)) {
            $this->errorAndDie('No or incorrect Merchant ID passed');
        }

        $hmac = hash_hmac("sha512", $file, trim($cp_ipn_secret));
        if ($hmac != $_SERVER['HTTP_HMAC']) {
            $this->errorAndDie('HMAC signature does not match');
        }

        // HMAC Signature verified at this point, load some variables.

        $txn_id = $request->getParam('txn_id');
        $item_name = $request->getParam('item_name');
        $item_number = $request->getParam('item_number');
        $amount1 = $request->getParam('amount1');
        $amount2 = $request->getParam('amount2');
        $currency1 = $request->getParam('currency1');
        $currency2 = $request->getParam('currency2');
        $status = $request->getParam('status');
        $status_text = $request->getParam('status_text');

        //depending on the API of your system, you may want to check and see if the transaction ID $txn_id has already been handled before at this point

        // Check the original currency to make sure the buyer didn't change it.
        if ($currency1 != $order_currency) {
            $this->errorAndDie('Original currency mismatch!');
        }

        // Check amount against order total
        if ($amount1 < $order_total) {
            $this->errorAndDie('Amount is less than order total!');
        }

        if ($status >= 100 || $status == 2) {
            $this->addCredits(3, $_SESSION['auth']['id']);
            $this->addLog($_SESSION['auth']['username'], 'Payment Success: ' . $order_total . '€');
            $this->flash('Account Credited !');
            return $this->redirect($response, 'root');

        } else if ($status < 0) {
            $this->addLog($_SESSION['auth']['username'], 'Payment Error');
            $this->flash('Payment Error, contact support', 'error');
            return $this->redirect($response, 'root');
        } else {
            $this->addLog($_SESSION['auth']['username'], 'Payment Pending');
            $this->flash('Payment is pending, contact support', 'error');
            return $this->redirect($response, 'root');
        }
    }



    public function plan(RequestInterface $request, ResponseInterface $response, $args)
    {
        if($this->isOnline() != true){
            return $this->redirect($response, 'root');
        }
        $id = $args['plan'];
        if(!empty($this->plan_exist($id))){
            if($this->change_user_plan($id, $_SESSION['auth']['id']) == true) {
                $this->addLog($_SESSION['auth']['username'], 'User plan :' . $id . ' Activated');
                $this->flash('New user plan activated');
                return $this->redirect($response, 'root');
            }else{
                $this->addLog($_SESSION['auth']['username'], 'User plan: ' . $id . ' Purchase Error');
                $this->flash('You need more credits to purchase this plan or you already have this offer', 'error');
                return $this->redirect($response, 'root');
            }

        }else {
            $this->flash('This plan doesn\'t exist', 'error');
            return $this->redirect($response, 'root');
        }
    }



    

};
