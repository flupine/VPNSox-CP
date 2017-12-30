<?php

namespace App\Middlewares;

use Slim\Http\Request;
use Slim\Http\Response;

class OldMiddleware{

    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * OldMiddleware constructor.
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
     * @return Response
     */
    public function __invoke(Request $request, Response $response, $next)
    {
        $this->twig->addGlobal('old', isset($_SESSION['old']) ? $_SESSION['old'] : []);
        if (isset($_SESSION['old'])){
            unset($_SESSION['old']);
        }

        $response = $next($request, $response);
        if ($response->getStatusCode() === 400){
            $_SESSION['old'] = $request->getParams();
        }

        return $response;
    }
}