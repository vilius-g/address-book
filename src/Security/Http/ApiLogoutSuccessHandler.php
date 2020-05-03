<?php

namespace App\Security\Http;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Logout\LogoutSuccessHandlerInterface;

class ApiLogoutSuccessHandler implements LogoutSuccessHandlerInterface
{
    /**
     * @inheritDoc
     */
    public function onLogoutSuccess(Request $request)
    {
        return new JsonResponse(['logout' => true]);
    }
}