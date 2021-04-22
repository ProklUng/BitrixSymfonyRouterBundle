<?php

namespace Prokl\BitrixSymfonyRouterBundle\Services\Controllers;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Class RedirectingController
 * Контроллер редиректов.
 * @package Prokl\BitrixSymfonyRouterBundle\Services\Controllers
 */
class RedirectingController extends AbstractController
{
    /**
     * Редирект URL без слеша на конце на URL с слешом на конце.
     *
     * @param Request $request
     *
     * @return RedirectResponse | null
     */
    public function removeTrailingSlash(Request $request) : ?RedirectResponse
    {
        $pathInfo = $request->getPathInfo();
        $requestUri = $request->getRequestUri();

        if (strpos($requestUri, '?') === false
            &&
            substr($pathInfo, -1) !== '/'
        ) {
            $url = str_replace($pathInfo, $pathInfo.'/', $requestUri);

            // 308 (Постоянное перенаправление) схоже с 301 (Перманентно перемещено), только
            // он не позволяет изменения метода запроса (например, с POST на GET)
            return $this->redirect($url, 308);
        }

        return null;
    }
}
