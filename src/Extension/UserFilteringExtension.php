<?php
declare(strict_types=1);

namespace App\Extension;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use App\Extension\UserFilters\UserFilterInterface;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Provides result filtering for API listings based on current user.
 */
class UserFilteringExtension implements QueryCollectionExtensionInterface
{
    /**
     * @var UserFilterInterface[]
     */
    private $filters;
    /**
     * @var Security
     */
    private $security;

    public function __construct(array $filters, Security $security)
    {
        $this->filters = $filters;
        $this->security = $security;

    }

    public function applyToCollection(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        string $operationName = null
    ) {
        if ($this->isUnfiltered()) {
            return;
        }

        $user = $this->getUser();

        foreach ($this->filters as $filter) {
            if ($filter->supports($resourceClass, $operationName)) {
                $filter->applyToCollection($user, $queryBuilder, $queryNameGenerator, $resourceClass, $operationName);
            }
        }
    }

    /**
     * Returns if the current user is admin and the filters do not apply.
     *
     * @return bool
     */
    private function isUnfiltered(): bool
    {
        return $this->security->isGranted('ROLE_ADMIN');
    }

    /**
     * Retrieve current user instance.
     *
     * @return UserInterface
     */
    private function getUser(): UserInterface
    {
        return $this->security->getUser();
    }
}
