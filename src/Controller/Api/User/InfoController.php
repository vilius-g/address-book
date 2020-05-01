<?php

namespace App\Controller\Api\User;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class InfoController extends AbstractController
{
    /**
     * @param TokenStorageInterface $storage
     * @return JsonResponse
     * @Route("/api/user/info", methods={"GET"})
     */
    public function __invoke(TokenStorageInterface $storage): JsonResponse
    {
        return $this->json(['email' => $storage->getToken()->getUser()->getUsername()]);
    }

}
