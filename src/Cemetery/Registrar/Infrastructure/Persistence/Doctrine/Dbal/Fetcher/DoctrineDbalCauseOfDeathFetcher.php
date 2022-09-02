<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Fetcher;

use Cemetery\Registrar\Domain\View\CauseOfDeath\CauseOfDeathFetcher;
use Cemetery\Registrar\Domain\View\CauseOfDeath\CauseOfDeathList;
use Cemetery\Registrar\Domain\View\CauseOfDeath\CauseOfDeathListItem;
use Cemetery\Registrar\Domain\View\CauseOfDeath\CauseOfDeathView;
use Doctrine\DBAL\Query\QueryBuilder;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class DoctrineDbalCauseOfDeathFetcher extends DoctrineDbalFetcher implements CauseOfDeathFetcher
{
    protected string $tableName = 'cause_of_death';

    public function findViewById(string $id): ?CauseOfDeathView
    {
        $viewData = $this->queryViewData($id);

        return $viewData ? $this->hydrateView($viewData) : null;
    }

    public function findAll(?int $page = null, ?string $term = null, int $pageSize = self::DEFAULT_PAGE_SIZE): CauseOfDeathList
    {
        $queryBuilder = $this->connection->createQueryBuilder()
            ->select(
                'cod.id   AS id',
                'cod.name AS name',
            )
            ->from($this->tableName, 'cod')
            ->andWhere('cod.removed_at IS NULL')
            ->orderBy('cod.name');
        if ($page !== null) {
            $queryBuilder
                ->setFirstResult(($page - 1) * $pageSize)
                ->setMaxResults($pageSize);
        }
        $this->appendAndWhereLikeTerm($queryBuilder, $term);
        $this->setTermParameter($queryBuilder, $term);

        $listData = $queryBuilder
            ->executeQuery()
            ->fetchAllAssociative();
        $totalCount = $page !== null ? $this->doCountTotal($term) : \count($listData);
        $totalPages = $page !== null ? (int) \ceil($totalCount / $pageSize) : null;

        return $this->hydrateList($listData, $page, $pageSize, $term, $totalCount, $totalPages);
    }

    public function countTotal(): int
    {
        return $this->doCountTotal(null);
    }

    public function doesExistByName(string $name): bool
    {
        return (bool) $this->connection->createQueryBuilder()
            ->select('COUNT(cod.id)')
            ->from($this->tableName, 'cod')
            ->andWhere('cod.name = :name')
            ->andWhere('cod.removed_at IS NULL')
            ->setParameter('name', $name)
            ->executeQuery()
            ->fetchFirstColumn()[0];
    }

    private function queryViewData(string $id): false|array
    {
        return $this->connection->createQueryBuilder()
            ->select(
                'cod.id         AS id',
                'cod.name       AS name',
                'cod.created_at AS createdAt',
                'cod.updated_at AS updatedAt',
            )
            ->from($this->tableName, 'cod')
            ->andWhere('cod.id = :id')
            ->andWhere('cod.removed_at IS NULL')
            ->setParameter('id', $id)
            ->executeQuery()
            ->fetchAssociative();
    }

    private function doCountTotal(?string $term): int
    {
        $queryBuilder = $this->connection->createQueryBuilder()
            ->select('COUNT(cod.id)')
            ->from($this->tableName, 'cod')
            ->andWhere('cod.removed_at IS NULL');
        $this->appendAndWhereLikeTerm($queryBuilder, $term);
        $this->setTermParameter($queryBuilder, $term);

        return $queryBuilder
            ->executeQuery()
            ->fetchFirstColumn()[0];
    }

    private function appendAndWhereLikeTerm(QueryBuilder $queryBuilder, ?string $term): void
    {
        if ($this->isTermNotEmpty($term)) {
            $queryBuilder
                ->andWhere(
                    $queryBuilder->expr()->like('cod.name', ':term'),
                );
        }
    }

    private function hydrateView(array $viewData): CauseOfDeathView
    {
        return new CauseOfDeathView(
            $viewData['id'],
            $viewData['name'],
            $viewData['createdAt'],
            $viewData['updatedAt'],
        );
    }

    private function hydrateList(
        array   $listData,
        ?int    $page,
        int     $pageSize,
        ?string $term,
        int     $totalCount,
        ?int    $totalPages,
    ): CauseOfDeathList {
        $listItems = [];
        foreach ($listData as $listItemData) {
            $listItems[] = new CauseOfDeathListItem(
                $listItemData['id'],
                $listItemData['name'],
            );
        }

        return new CauseOfDeathList($listItems, $page, $pageSize, $term, $totalCount, $totalPages);
    }
}
