<?php

namespace App\Extension;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use App\Entity\Contact;
use App\Entity\SharedContact;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserFilteringExtension implements QueryCollectionExtensionInterface
{
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * UserFilteringExtension constructor.
     * @param TokenStorageInterface $tokenStorage
     */
    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    public function applyToCollection(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        string $operationName = null
    ) {
        if (Contact::class === $resourceClass) {
            $queryBuilder->where('o.owner = :user')
                ->setParameter(':user', $this->getUser());
        } elseif (SharedContact::class === $resourceClass) {
            $queryBuilder->innerJoin(Contact::class, 'c')
                ->where('o.sharedWith = :user or c.owner = :user')
                ->setParameter(':user', $this->getUser());
        }
    }

    private function getUser(): ?UserInterface
    {
        return $this->tokenStorage->getToken()->getUser();
    }
}
