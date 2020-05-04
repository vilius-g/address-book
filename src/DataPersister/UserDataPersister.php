<?php

namespace App\DataPersister;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use App\Entity\User;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserDataPersister implements ContextAwareDataPersisterInterface
{
    private $decorated;
    /**
     * @var UserPasswordEncoderInterface
     */
    private $userPasswordEncoder;

    public function __construct(
        ContextAwareDataPersisterInterface $decorated,
        UserPasswordEncoderInterface $userPasswordEncoder
    ) {
        $this->decorated = $decorated;
        $this->userPasswordEncoder = $userPasswordEncoder;
    }

    public function supports($data, array $context = []): bool
    {
        return $this->decorated->supports($data, $context);
    }

    /**
     * @param User $data
     */
    public function persist($data, array $context = [])
    {
        // Encode password for storage.
        if (($passwordPlain = $data->getPasswordPlain())) {
            $data->setPassword(
                $this->userPasswordEncoder->encodePassword($data, $passwordPlain)
            );
            $data->eraseCredentials();
        }

        return $this->decorated->persist($data, $context);
    }

    public function remove($data, array $context = [])
    {
        return $this->decorated->remove($data, $context);
    }
}