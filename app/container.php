<?php
$container = $app->getContainer();

/**
 * @return bool
 */
$container['debug'] = function () {
    return true;
};

/**
 * @return \Slim\Csrf\Guard
 */
$container['csrf'] = function () {
    return new \Slim\Csrf\Guard;
};


/**
 * @param $container
 * @return \Slim\Views\Twig
 */
$container['view'] = function ($container) {
    $dir = dirname(__DIR__);
    $view = new \Slim\Views\Twig($dir . '/app/views', [
        'cache' => $container->debug ? false : $dir . '/tmp/cache',
        'debug' => $container->debug
    ]);
    if ($container->debug) {
        $view->addExtension(new Twig_Extension_Debug());
    }
    // Instantiate and add Slim specific extension
    $basePath = rtrim(str_ireplace('index.php', '', $container['request']->getUri()->getBasePath()), '/');
    $view->addExtension(new Slim\Views\TwigExtension($container['router'], $basePath));

    return $view;
};

/**
 * @param $container
 * @return Swift_Mailer
 */
$container['mailer'] = function ($container) {
    if ($container->debug){
        $transport = Swift_SmtpTransport::newInstance('smtp.sendgrid.net', 587);
    } else {
        $transport = Swift_SmtpTransport::newInstance('smtp.sendgrid.net', 587);
    }
    $transport->setUsername('root');
    $transport->setPassword('root');
    $mailer = Swift_Mailer::newInstance($transport);
    return $mailer;

};

/**
 * @param $c
 * @return PDO
 */
$container['db'] = function ($c) {
    $pdo = new PDO("mysql:host=localhost;dbname=vpnsox", 'root', 'root');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    return $pdo;
};

$container['db2'] = function ($c) {
    $pdo = new PDO("mysql:host=localhost;dbname=openvpn", 'root', 'root');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    return $pdo;
};
