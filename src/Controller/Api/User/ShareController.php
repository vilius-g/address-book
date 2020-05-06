<?php

namespace App\Controller\Api\User;

use ApiPlatform\Core\Bridge\Symfony\Validator\Exception\ValidationException;
use App\Entity\Contact;
use App\Entity\SharedContact;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use function json_decode;
use const JSON_THROW_ON_ERROR;

class ShareController extends AbstractController
{
    /**
     * Implements contact sharing with other user email.
     *
     * @Route(
     *     path="/api/contacts/{id}/share-with-email",
     *     methods={"POST"}
     * )
     */
    public function __invoke(
        string $id,
        Request $request,
        NormalizerInterface $normalizer,
        EntityManagerInterface $em,
        ValidatorInterface $validator
    ): Response {
        $contact = $em->getRepository(Contact::class)->find($id);

        if (!$contact) {
            throw $this->createNotFoundException();
        }

        if (!($this->isGranted('ROLE_ADMIN') || $this->getUser() === $contact->getOwner())) {
            throw $this->createAccessDeniedException();
        }

        $decoded = json_decode($request->getContent(), true, 3, JSON_THROW_ON_ERROR);

        $email = $decoded['sharedWith']['email'];
        $otherUser = $em->getRepository(User::class)->findOneBy(['email' => $email]);

        if (null === $otherUser) {
            throw new BadRequestHttpException("User {$email} not found.");
        }

        // Create shared contact.
        $shared = new SharedContact();
        $shared->setContact($contact);
        $shared->setSharedWith($otherUser);

        // Validate new object.
        $violations = $validator->validate($shared);
        if ($violations->count() > 0) {
            throw new ValidationException($violations);
        }

        // Persist in DB.
        $em->persist($shared);
        $em->flush();

        return $this->json($normalizer->normalize($shared));
    }
}
