<?php

namespace App\Controller\Api\User;

use App\Form\UserRegistrationType;
use App\Security\LoginApiAuthenticator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use function array_map;
use function iterator_to_array;

class RegistrationController extends AbstractController
{
    /**
     * @Route("/api/user/register", methods={"POST"})
     */
    public function __invoke(
        Request $request,
        GuardAuthenticatorHandler $guardHandler,
        LoginApiAuthenticator $authenticator,
        UserPasswordEncoderInterface $passwordEncoder
    ): Response {
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

        $user = $form->getData();
        // Encode password.
        $user->setPassword($passwordEncoder->encodePassword($user, $user->getPassword()));

        // Store new user in DB.
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($user);
        $entityManager->flush();

        // Log-in user.
        return $guardHandler->authenticateUserAndHandleSuccess(
            $user,
            $request,
            $authenticator,
            'main' // firewall name in security.yaml
        );
    }
}
