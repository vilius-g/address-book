<?php

declare(strict_types=1);

namespace App\DataTransformer;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use ApiPlatform\Core\Validator\ValidatorInterface;
use App\Entity\User;
use App\Repository\UserRepository;

class UserEmailInputDataTransformer implements DataTransformerInterface
{
    /**
     * @var ValidatorInterface
     */
    private $validator;
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * ShareWithEmailInputDataTransformer constructor.
     */
    public function __construct(ValidatorInterface $validator, UserRepository $userRepository)
    {
        $this->validator = $validator;
        $this->userRepository = $userRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function transform($object, string $to, array $context = [])
    {
        $this->validator->validate($object);

        return $this->userRepository->findOneByEmail($object->email);
    }

    /**
     * {@inheritdoc}
     */
    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        if ($data instanceof User) {
            return false;
        }

        return User::class === $to && null !== ($context['input']['class'] ?? null);
    }
}
