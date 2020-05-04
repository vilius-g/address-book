<?php

namespace App\Controller\Api\User;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use function assert;

class InfoController extends AbstractController
{
    /**
     * @param Security $security
     * @return JsonResponse
     * @Route("/api/whoami", methods={"GET"})
     */
    public function __invoke(Security $security): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $user = $security->getUser();
        assert($user instanceof User);

        return $this->json(['id' => $user->getId(), 'email' => $user->getUsername()]);
    }

}
