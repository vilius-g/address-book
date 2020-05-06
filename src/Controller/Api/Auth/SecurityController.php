<?php
declare(strict_types=1);

namespace App\Controller\Api\Auth;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Implements user authentication.
 */
class SecurityController extends AbstractController
{
    /**
     * Provides successful login response.
     *
     * @Route("/api/auth/login", name="app_login")
     * @return JsonResponse
     */
    public function login(): JsonResponse
    {
        $user = $this->getUser();
        assert($user instanceof User);

        return $this->json(
            [
                'id' => $user->getId(),
                'email' => $user->getUsername(),
                'roles' => $user->getRoles(),
            ]
        );
    }

    /**
     * Provides logout function.
     *
     * @Route("/api/auth/logout", name="app_logout")
     */
    public function logout(): JsonResponse
    {
        return $this->json([]);
    }
}
