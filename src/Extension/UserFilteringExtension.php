<?php

namespace App\Extension;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use App\Entity\Contact;
use App\Entity\SharedContact;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Security\Core\Security;

class UserFilteringExtension implements QueryCollectionExtensionInterface
{
    /**
     * @var Security
     */
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function applyToCollection(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        string $operationName = null
    )
    {
        if ($this->security->isGranted('ROLE_ADMIN')) // allow all for admin.
        {
            return;
        }

        if (Contact::class === $resourceClass) {
            $queryBuilder->where('o.owner = :user')
                ->setParameter(':user', $this->security->getUser());
        } elseif (SharedContact::class === $resourceClass) {
            $queryBuilder->innerJoin(Contact::class, 'c')
                ->where('o.sharedWith = :user or c.owner = :user')
                ->setParameter(':user', $this->security->getUser());
        }
    }
}