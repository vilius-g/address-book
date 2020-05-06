<?php

namespace App\Extension\UserFilters;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use App\Entity\Contact;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Security\Core\User\UserInterface;

class UserContactFilter implements UserFilterInterface
{
    /**
     * @inheritDoc
     */
    public function applyToCollection(
        UserInterface $user,
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        string $operationName = null
    ): void {
        $queryBuilder->where('o.owner = :user')
            ->setParameter(':user', $user);
    }

    /**
     * @inheritDoc
     */
    public function supports(string $resourceClass, string $operationName = null): bool
    {
        return Contact::class === $resourceClass;
    }
}
