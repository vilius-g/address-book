<?php
declare(strict_types=1);

namespace App\Extension\UserFilters;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Provides filtering based on currently authenticated user.
 */
interface UserFilterInterface
{
    /**
     * Apply filter to existing collection.
     *
     * @param UserInterface $user
     * @param QueryBuilder $queryBuilder
     * @param QueryNameGeneratorInterface $queryNameGenerator
     * @param string $resourceClass
     * @param string|null $operationName
     */
    public function applyToCollection(
        UserInterface $user,
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        string $operationName = null
    ): void;

    /**
     * Is given resource operation supported by this filter.
     *
     * @param string $resourceClass
     * @param string|null $operationName
     * @return bool
     */
    public function supports(string $resourceClass, string $operationName = null): bool;
}
