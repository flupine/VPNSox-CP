<?php
namespace App\Controllers;

use Psr\Http\Message\ResponseInterface;

class Controller{

    private $container;

    /**
     * Controller constructor.
     * @param $container
     */
    public function __construct($container)
    {

        $this->container = $container;

    }


    /**
     * @param ResponseInterface $response
     * @param $file
     * @param array $params
     */
    public function render(ResponseInterface $response, $file, $params= []) {
        $this->container->view->render($response, $file, $params);
    }


    /**
     * @param $response
     * @param $name
     * @param int $status
     * @return mixed
     */
    public function redirect($response, $name, $status = 302){
        return $response->withStatus($status)->withHeader('Location', $this->router->pathFor($name));
    }


    /**
     * @param $message
     * @param string $type
     * @return mixed
     */
    public function flash($message, $type = 'success') {
        if(!isset($_SESSION['flash'])) {
            $_SESSION['flash'] = [];
        }
        return $_SESSION['flash'][$type] = $message;
    }




    /**
     * @param $message
     * @return mixed
     */
    public function push($message) {
        if(!isset($_SESSION['push'])) {
            $_SESSION['push'] = [];
        }
        return $_SESSION['push'] = $message;
    }


    /**
     * @param $url
     * @return mixed
     */
    public function tinyUrl($url)
    {
        $curl = curl_init();
        curl_setopt($curl,CURLOPT_RETURNTRANSFER,1);
        curl_setopt($curl,CURLOPT_URL,'http://tinyurl.com/api-create.php?url='.$url);
        curl_setopt($curl,CURLOPT_CONNECTTIMEOUT,7);
        $ret = curl_exec($curl);
        curl_close($curl);
        return $ret;
    }


    /**
     * @param $name
     * @return mixed
     */
    public function __get($name){
        return $this->container->get($name);
    }

    function str_random($length){
        $alphabet = "0123456789azertyuiopqsdfghjklmwxcvbnAZERTYUIOPQSDFGHJKLMWXCVBN";
        return substr(str_shuffle(str_repeat($alphabet, $length)), 0, $length);
    }

    public function user_exists($user_id)
    {
        $q = array('user' => $user_id);
        $req = $this->db->prepare("SELECT username FROM users WHERE username = :user");
        $req->execute($q);
        $count = count($req->fetchAll());
        if($count > 0){
            return true;
        }
    }

    public function email_exists($user_id)
    {
        $q = array('user' => $user_id);
        $req = $this->db->prepare("SELECT username FROM users WHERE email = :user");
        $req->execute($q);
        $count = count($req->fetchAll());
        if($count > 0){
            return true;
        }
    }

    public function plan_exist($plan_id){
        $q = array('plan' => $plan_id);
        $req = $this->db->prepare("SELECT id FROM plans WHERE id = :plan");
        $req->execute($q);
        $result = $req->fetch();
        if(!empty($result)){
            return $result;
        }else{
            return false;
        }
    }

    public function isOnline()
    {
        if(!empty($_SESSION['auth'])){
            return true;
        }else {
            return false;
        }
    }

    public function isAdmin()
    {
        if($_SESSION['auth']['admin'] == 1){
            return true;
        }else {
            return false;
        }
    }


    public function user_infos($user_id){
        $q = array('userid' => $user_id);
        $req = $this->db->prepare("SELECT * FROM users WHERE id = :userid");
        $req->execute($q);
        $result = $req->fetch();
        if (empty($result)){return 84;}
        return $result;
    }
  
   public function user_infos_username($username){
        $q = array('username' => $username);
        $req = $this->db->prepare("SELECT * FROM users WHERE username = :username");
        $req->execute($q);
        $result = $req->fetch();
        if (empty($result)){return 84;}
        return $result;
    }

    public function plan_info($plan_id){
        $q = array('plan' => $plan_id);
        $req = $this->db->prepare("SELECT * FROM plans WHERE id = :plan");
        $req->execute($q);
        $result = $req->fetch();
        return $result;

    }

    public function check_user_balance($user, $amount){
        $user = $this->user_infos($user);
        if(($user['credits'] - $amount) >= 0){
            return true;
        }else{
            return false;
        }
    }

    public function getVpnInfos($user){
        $q = array('user' => $user);
        $req = $this->db2->prepare("SELECT * FROM user WHERE user_id = :user");
        $req->execute($q);
        $result = $req->fetch();
        return $result['user_id'];
    }

    public function addVpnUser($user_id){
        $user = $this->user_infos($user_id);
        if($user['plan'] != 2){ $premium = 0; }else{ $premium = 1;}
        if(empty($this->getVpnInfos($user['username']))) {
            $this->db2->prepare('INSERT INTO user  SET  user_id = ?, user_pass = ?, user_mail = ?, user_enable = 1, user_start_date = NOW(), user_end_date = NOW() + INTERVAL 1 MONTH, premium = ?')->execute([$user['username'], $user['vpn_pass'], $user['email'], $premium]);
            $this->addLog($user['username'], 'New VPN account');
        }else{
            $this->db2->prepare('UPDATE user SET user_start_date = NOW(), user_end_date = NOW() + INTERVAL 1 MONTH, premium = ? WHERE user_id = ?')->execute([$premium, $user['username']]);
            $this->addLog($user['username'], 'VPN Renewed');
        }
    }

    public function change_user_plan($plan_id, $user_id){
        $plan = $this->plan_info($plan_id);
        $user = $this->user_infos($user_id);
        if($this->check_user_balance($user_id, $plan['price']) == true && $user['plan'] < $plan['id']) {
            $this->db->prepare('UPDATE users SET credits = credits - ?, plan = ?, plan_date = NOW() + INTERVAL 1 MONTH WHERE id = ?')->execute([$plan['price'], $plan['id'], $user_id]);
            $this->db->prepare('INSERT INTO invoices SET name = ?, price = ?, date = NOW(), plan_expiration = NOW() + INTERVAL 1 MONTH')->execute([$user['username'], $plan['price']]);
            $this->addVpnUser($user['id']);

            return true;
        }else{

            return false;
        }
    }


    public function getInvoices($name){
        $q = array('username' => $name);
        $req = $this->db->prepare("SELECT * FROM invoices WHERE name = :username ORDER BY id DESC");
        $req->execute($q);
        $result = $req->fetchAll();
        return $result;
    }

    function errorAndDie($error_msg){
        die('IPN Error: '.$error_msg);
    }


   function addCredits($amount, $user_id){
       $user = $this->user_infos($user_id);
       $this->db->prepare('UPDATE users SET credits = credits + ? WHERE id = ?')->execute([$amount, $user_id]);

   }

   function addLog($user, $message){
       $this->db->prepare('INSERT INTO logs SET log_date = NOW(), log_user = ?, log_action = ?')->execute([$user, $message]);
   }

    public function listServers($plan){
        $q = array('plan' => $plan);
        $req = $this->db->prepare("SELECT * FROM servers WHERE plan <= :plan ORDER BY id DESC");
        $req->execute($q);
        $result = $req->fetchAll();
        return $result;
    }



    public function vpnLogs($user){
        $q = array('user' => $user);
        $req = $this->db2->prepare("SELECT * FROM log WHERE user_id = :user ORDER BY log_id ");
        $req->execute($q);
        $result = $req->fetchAll();
        return $result;
    }

    public function getLogs(){
        $req = $this->db->query("SELECT * FROM logs ORDER BY id DESC LIMIT 5");
        $result = $req->fetchAll();
        return $result;
    }
  
    public function getNews(){
        $req = $this->db->query("SELECT * FROM news ORDER BY id DESC LIMIT 5");
        $result = $req->fetchAll();
        return $result;
    }

    public function latestInvoices(){
        $req = $this->db->query("SELECT * FROM invoices ORDER BY id DESC LIMIT 5");
        $result = $req->fetchAll();
        return $result;
    }

    public function getUserLogs($user){
        $q = array('user' => $user);
        $req = $this->db->prepare("SELECT * FROM logs WHERE log_user = :user ORDER BY id DESC");
        $req->execute($q);
        $result = $req->fetchAll();
        return $result;
    }


}