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
            return $this->render($response, 'pages/home.twig', ['account' => $user]);
        }else {
            return $this->redirect($response, 'login');
        }
    }

    public function admin(RequestInterface $request, ResponseInterface $response)
    {

        if($this->isAdmin()){
            $user = $this->user_infos($_SESSION['auth']['id']);
            $logs= $this->getLogs();
            $invoices = $this->latestInvoices();
            return $this->render($response, 'pages/admin/home.twig', ['account' => $user, 'logs' => $logs, 'invoices' => $invoices]);
        }else {
            $this->flash("Not Allowed !", 'error');
            return $this->redirect($response, 'root');
        }
    }

    public function adminLogs(RequestInterface $request, ResponseInterface $response, $args){
        if($this->isAdmin()){
            $user = $this->user_infos($_SESSION['auth']['id']);
            $logs = $this->getUserLogs($args['user']);
            return $this->render($response, 'pages/admin/logs.twig', ['account' => $user, 'logs' => $logs]);
        }else {
            $this->flash("Not Allowed !", 'error');
            return $this->redirect($response, 'root');
        }


    }

    public function adminLogsDelete(RequestInterface $request, ResponseInterface $response, $args){
        if($this->isAdmin()){
            $this->delLog($args['user']);
            $this->flash('Logs deleted for the user' . $args['user']);
            return $this->redirect($response,  'admin');
        }else{
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
            return $this->render($response, 'pages/payments.twig', ['account' => $user, 'invoices' => $invoices]);
        }else {
            return $this->redirect($response, 'login');
        }
    }


    public function register(RequestInterface $request, ResponseInterface $response)
    {

        if($this->isOnline()){
            return $this->redirect($response, 'root');
        }else {
            return $this->render($response, 'pages/register.twig');
        }
    }

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

    public function config(RequestInterface $request, ResponseInterface $response)
    {
        if($this->isOnline()){
            $user = $this->user_infos($_SESSION['auth']['id']);
            $plan = $this->listServers($user['plan']);
            return $this->render($response, 'pages/config.twig', ['account' => $user, 'servers' => $plan]);
        }else {
            return $this->render($response, 'pages/login.twig');
        }
    }

    public function download(RequestInterface $request, ResponseInterface $response, $args)
    {
        $user = $this->user_infos($_SESSION['auth']['id']);
        $valeurs = array('file' => $args['id'], 'plan' => $user['plan']);
        $req = $this->db->prepare("SELECT * FROM servers WHERE(id = :file AND plan = :plan)");
        $req->execute($valeurs);
        $result = $req->fetch();

        if(empty($result)){
            $this->addLog($_SESSION['auth']['username'], 'File Inexist');
            $this->flash('This file not exist', 'error');
            return $this->redirect($response, 'root');
        }else{
            $name = $result['name'];
            $file = '../app/Uploads/' . $result['file'];
            $response = $response->withHeader('Content-Description', 'File Transfer')
                ->withHeader('Content-Type', 'application/zip')
                ->withHeader('Content-Disposition', 'attachment;filename="'.$result['file'].'"')
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
        if($this->isOnline()){
            return $this->redirect($response, 'root');
        }


        $cp_merchant_id = '6a96f3f0a0f55f93ebbd12f7a6b77bbd';
        $cp_ipn_secret = 'yxhjL2gH';

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
        $amount1 = floatval($request->getParam('amount1'));
        $amount2 = floatval($request->getParam('amount2'));
        $currency1 = $request->getParam('currency1');
        $currency2 = $request->getParam('currency2');
        $status = intval($request->getParam('status'));
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
           return $this->redirect($response, 'register', 400);
       }
       return $this->redirect($response, 'login');

   }


};