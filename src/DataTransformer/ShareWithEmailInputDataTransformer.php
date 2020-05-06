<?php

namespace App\DataTransformer;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use ApiPlatform\Core\Validator\ValidatorInterface;
use App\Dto\UserEmailInput;
use App\Entity\SharedContact;
use App\Entity\User;

class ShareWithEmailInputDataTransformer implements DataTransformerInterface
{
    /**
     * @var ValidatorInterface
     */
    private $validator;
    /**
     * @var UserEmailInputDataTransformer
     */
    private $userEmailInputDataTransformer;

    /**
     * ShareWithEmailInputDataTransformer constructor.
     * @param ValidatorInterface $validator
     * @param UserEmailInputDataTransformer $userEmailInputDataTransformer
     */
    public function __construct(
        ValidatorInterface $validator,
        UserEmailInputDataTransformer $userEmailInputDataTransformer
    ) {
        $this->validator = $validator;
        $this->userEmailInputDataTransformer = $userEmailInputDataTransformer;
    }

    /**
     * @inheritDoc
     */
    public function transform($object, string $to, array $context = [])
    {
        $this->validator->validate($object);

        $sharedContact = new SharedContact();
        $sharedContact->setContact($object->contact);
        $sharedContact->setSharedWith($this->convertEmailToUser($object->sharedWith, $context));

        return $sharedContact;
    }

    /**
     * @inheritDoc
     */
    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        if ($data instanceof SharedContact) {
            return false;
        }

        return SharedContact::class === $to && null !== ($context['input']['class'] ?? null);
    }

    /**
     * Return user instance from its email address.
     *
     * @param UserEmailInput $object
     * @param array $context
     * @return User
     */
    private function convertEmailToUser($object, array $context = []): User
    {
        return $this->userEmailInputDataTransformer->transform($object, User::class, $context);
    }
}
