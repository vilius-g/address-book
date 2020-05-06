<?php

namespace App\Controller\Api\Auth;

use LogicException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * Implements user authentication.
 */
class SecurityController extends AbstractController
{
    /**
     * Provides login function.
     *
     * @Route("/api/auth/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->json(['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * Provides logout function.
     *
     * @Route("/api/auth/logout", name="app_logout")
     */
    public function logout()
    {
        throw new LogicException(
            'This method can be blank - it will be intercepted by the logout key on your firewall.'
        );
    }
}