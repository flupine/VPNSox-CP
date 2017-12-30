<?php

namespace App\Middlewares;

use Slim\Http\Request;
use Slim\Http\Response;

class TwigCsrfMiddleware{

    /**
     * @var \Twig_Environment
     */
    private $twig;
    private $csrf;

    /**
     * TwigCsrfMiddleware constructor.
     * @param \Twig_Environment $twig
     * @param $csrf
     */
    public function __construct(\Twig_Environment $twig, $csrf)
    {

        $this->twig = $twig;
        $this->csrf = $csrf;
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param $next
     * @return mixed
     */
    public function __invoke(Request $request, Response $response, $next)
    {
        $csrf = $this->csrf;
        $this->twig->addFunction(new \Twig_SimpleFunction('csrf', function () use ($csrf, $request){
            $nameKey = $csrf->getTokenNameKey();
            $valueKey = $csrf->getTokenValueKey();
            $name = $request->getAttribute($nameKey);
            $value = $request->getAttribute($valueKey);
            return "<input type=\"hidden\" name=\"$nameKey\" value=\"$name\"> <input type=\"hidden\" name=\"$valueKey\" value=\"$value\">";
        }, ['is_safe' => ['html']]));

        return $next($request, $response);
    }
}