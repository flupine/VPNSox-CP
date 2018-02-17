<?php
use App\Controllers\PagesController;
use App\Controllers\LoginController;
use App\Controllers\RegisterController;
use App\Controllers\AccountController;
use App\Controllers\NewsController;

require '../vendor/autoload.php';
session_start();

//DECLARATION
$app = new \Slim\App([
    'settings' => [
        'displayErrorDetails' => true
    ],
]);
require('../app/container.php');
$container = $app->getContainer();

//MIDDLEWARE
$app->add(new \App\Middlewares\PushMiddleware($container->view->getEnvironment()));
$app->add(new \App\Middlewares\FlashMiddleware($container->view->getEnvironment()));
$app->add(new \App\Middlewares\OldMiddleware($container->view->getEnvironment()));
$app->add(new \App\Middlewares\TwigCsrfMiddleware($container->view->getEnvironment(), $container->csrf));
$app->add($container->csrf);

//ROUTES
  //LOGIN 
  $app->get('/login', LoginController::class . ':login')->setName('login');
  $app->get('/login/{id}/{token}', LoginController::class . ':confirm');
  $app->get('/logout', LoginController::class . ':logout')->setName('logout');
  $app->post('/login', LoginController::class . ':postLogin');

  //REGISTER
  $app->get('/register', RegisterController::class . ':register')->setName('register');
  $app->post('/register', RegisterController::class . ':postRegister');

  //ACCOUNT
  $app->get('/account', AccountController::class . ':account')->setName('account');
  $app->post('/account/newapi', AccountController::class . ':newApi')->setName('newApi');
  $app->post('/account/changepwd', AccountController::class . ':changePassword')->setName('changePwd');
  $app->post('/account/changevpnpwd', AccountController::class . ':changeVpnPassword')->setName('changeVpnPwd');


  //PAYMENT
  $app->get('/payments', PagesController::class . ':invoices')->setName('payments');
  $app->get('/cancel', PagesController::class . ':cancel')->setName('cancel');
  $app->get('/success', PagesController::class . ':cancel')->setName('success');
  $app->get('/order/{plan}', PagesController::class . ':plan')->setName('order');
  $app->post('/ipn', PagesController::class . ':ipn');

  //NEWS
  $app->get('/admin/news', NewsController::class . ':news')->setName('news');
  $app->post('/admin/news', NewsController::class . ':addNews');
  
  //ADMIN
  $app->get('/admin', PagesController::class . ':admin')->setName('admin');
  $app->get('/admin/logs/{user}/{page}', PagesController::class . ':adminLogs')->setName('adminLogs');
  $app->get('/admin/purge/{user}', PagesController::class . ':adminLogsPurge')->setName('adminLogsPurge');
  $app->post('/admin/search', PagesController::class . ':adminSearch')->setName('adminSearch');

  //API
  //$app->get('/api/{api_key}/{api_secret}/infos', ApiController::class . 'infos');

  //OTHERS
  $app->get('/', PagesController::class . ':home')->setName('root');


  $app->get('/tos', PagesController::class . ':tos')->setName('tos');


  $app->get('/logs', PagesController::class . ':logs')->setName('logs');
  $app->get('/config', PagesController::class . ':config')->setName('config');
  $app->get('/download/{id}', PagesController::class . ':download')->setName('download');

  //$app->get('/referral/{id}', PagesController::class . ':referral');




//EXECUTION
$app->run();
