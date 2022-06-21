<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Fetcher;

use Cemetery\Registrar\Infrastructure\Persistence\Fetcher;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\DBAL\Statement;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
abstract class DoctrineDbalFetcher extends Fetcher
{
    /**
     * @param Connection $connection
     */
    public function __construct(
        protected readonly Connection $connection,
    ) {}

    /**
     * @param QueryBuilder $queryBuilder
     * @param string|null  $term
     */
    protected function setTermParameter(QueryBuilder $queryBuilder, ?string $term): void
    {
        if ($this->isTermNotEmpty($term)) {
            $queryBuilder
                ->setParameter('term', "%$term%");
        }
    }

    /**
     * @param Statement   $stmt
     * @param string|null $term
     */
    protected function bindTermValue(Statement $stmt, ?string $term): void
    {
        if ($term !== null && $term !== '') {
            $stmt->bindValue('term', "%$term%");
        }
    }
}
