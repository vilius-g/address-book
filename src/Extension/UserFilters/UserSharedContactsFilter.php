<?php

namespace App\Extension\UserFilters;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use App\Entity\Contact;
use App\Entity\SharedContact;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Security\Core\User\UserInterface;

class UserSharedContactsFilter implements UserFilterInterface
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
        $queryBuilder->innerJoin(Contact::class, 'c')
            ->where('o.sharedWith = :user or c.owner = :user')
            ->setParameter(':user', $user);
    }

    /**
     * @inheritDoc
     */
    public function supports(string $resourceClass, string $operationName = null): bool
    {
        return SharedContact::class === $resourceClass;
    }
}
