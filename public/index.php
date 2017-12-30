<?php
use App\Controllers\PagesController;
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
$app->get('/', PagesController::class . ':home')->setName('root');
$app->get('/admin', PagesController::class . ':admin')->setName('admin');
$app->get('/admin/logs/{user}', PagesController::class . ':adminLogs')->setName('adminLogs');
$app->get('/admin/logs/{user}/delete', PagesController::class . ':adminLogsDelete');
$app->get('/tos', PagesController::class . ':tos')->setName('tos');
$app->get('/cancel', PagesController::class . ':cancel')->setName('cancel');
$app->get('/success', PagesController::class . ':cancel')->setName('success');
$app->get('/order/{plan}', PagesController::class . ':plan')->setName('order');;
$app->get('/register', PagesController::class . ':register')->setName('register');
$app->get('/login/{id}/{token}', PagesController::class . ':confirm');
$app->get('/login', PagesController::class . ':login')->setName('login');
$app->get('/logout', PagesController::class . ':logout')->setName('logout');
$app->get('/payments', PagesController::class . ':invoices')->setName('payments');
$app->get('/logs', PagesController::class . ':logs')->setName('logs');
$app->get('/config', PagesController::class . ':config')->setName('config');
$app->get('/download/{id}', PagesController::class . ':download')->setName('download');

//$app->get('/referral/{id}', PagesController::class . ':referral');



$app->post('/register', PagesController::class . ':postRegister');
$app->post('/login', PagesController::class . ':postLogin');
$app->post('/ipn', PagesController::class . ':ipn');

//EXECUTION
$app->run();
