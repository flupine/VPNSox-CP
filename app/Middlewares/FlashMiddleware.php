<?php

namespace App\Middlewares;

use Slim\Http\Request;
use Slim\Http\Response;

class FlashMiddleware{

    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * FlashMiddleware constructor.
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
        $this->twig->addGlobal('flash', isset($_SESSION['flash']) ? $_SESSION['flash'] : []);
       if (isset($_SESSION['flash'])){
           unset($_SESSION['flash']);
       }

       return $next($request, $response);
    }
}