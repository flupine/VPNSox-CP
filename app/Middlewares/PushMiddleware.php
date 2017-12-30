<?php

namespace App\Middlewares;

use Slim\Http\Request;
use Slim\Http\Response;

class PushMiddleware{

    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * PushMiddleware constructor.
     * @param \Twig_Environment $twig
     */
    public function __construct(\Twig_Environment $twig)
    {

        $this->twig = $twig;
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param $next
     * @return mixed
     */
    public function __invoke(Request $request, Response $response, $next)
    {
        $this->twig->addGlobal('push', isset($_SESSION['push']) ? $_SESSION['push'] : []);
       if (isset($_SESSION['push'])){
           unset($_SESSION['push']);
       }

       return $next($request, $response);
    }
}