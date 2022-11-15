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
    protected string $tableName;

    public function __construct(
        protected Connection $connection,
    ) {}

    public function doesExistById(string $id): bool
    {
        return (bool) $this->connection->createQueryBuilder()
            ->select('COUNT(entity.id)')
            ->from($this->tableName, 'entity')
            ->andWhere('entity.id = :id')
            ->andWhere('entity.removed_at IS NULL')
            ->setParameter('id', $id)
            ->executeQuery()
            ->fetchFirstColumn()[0];
    }

    protected function setTermParameter(
        QueryBuilder $queryBuilder,
        ?string      $term,
        bool         $onlyStartingWithTerm = false,
        bool         $onlyEndingWithTerm   = false,
    ): void {
        if ($this->isTermNotEmpty($term)) {
            $term = \mb_strtolower($term);
            $queryBuilder
                ->setParameter(
                    'term',
                    ($onlyStartingWithTerm ? '' : '%') . $term . ($onlyEndingWithTerm ? '' : '%')
                );
        }
    }

    protected function bindTermValue(
        Statement $stmt,
        ?string   $term,
        bool      $onlyStartingWithTerm = false,
        bool      $onlyEndingWithTerm   = false,
    ): void {
        if ($term !== null && $term !== '') {
            $term = \mb_strtolower($term);
            $stmt->bindValue(
                'term',
                ($onlyStartingWithTerm ? '' : '%') . $term . ($onlyEndingWithTerm ? '' : '%')
            );
        }
    }
}
