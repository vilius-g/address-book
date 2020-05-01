<?php

namespace App\Controller\Api\User;

use App\Form\UserRegistrationType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use function array_map;
use function iterator_to_array;

class RegistrationController extends AbstractController
{
    /**
     * @Route("/api/user/register", methods={"POST"})
     */
    public function __invoke(Request $request): JsonResponse
    {
        $form = $this->createForm(UserRegistrationType::class, null, ['csrf_protection' => false]);
        $form->submit($request->request->all());

        // Validate submitted data.
        if (!$form->isSubmitted() || !$form->isValid()) {
            return $this->json(
                [
                    'errors' => array_map(
                        static function (FormError $error) {
                            return [
                                'origin' => $error->getOrigin()->getName(),
                                'message' => $error->getMessage(),
                            ];
                        },
                        iterator_to_array($form->getErrors(true))
                    ),
                ],
                Response::HTTP_BAD_REQUEST
            );
        }

        // Store new user in DB.
        $user = $form->getData();
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($user);
        $entityManager->flush();

        return $this->json(['email' => $user->getEmail()]);
    }
}
