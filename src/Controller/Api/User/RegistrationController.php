<?php

namespace App\Controller\Api\User;

use App\Form\UserRegistrationType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RegistrationController extends AbstractController
{
    /**
     * @Route("/api/user/register", methods={"POST"})
     */
    public function __invoke(Request $request): JsonResponse
    {
        $form = $this->get('form.factory')->createNamed(
            '',
            UserRegistrationType::class,
            null,
            ['csrf_protection' => false]
        );
        $form->handleRequest($request);

        if (!$form->isSubmitted() || !$form->isValid()) {
            return $this->json(['errors' => $form->getErrors(true)], Response::HTTP_BAD_REQUEST);
        }

        $user = $form->getData();
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($user);
        $entityManager->flush();

        return $this->json(['email' => $user->getEmail()]);
    }
}
