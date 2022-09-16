<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Fetcher;

use Cemetery\Registrar\Domain\View\CauseOfDeath\CauseOfDeathFetcher;
use Cemetery\Registrar\Domain\View\CauseOfDeath\CauseOfDeathPaginatedList;
use Cemetery\Registrar\Domain\View\CauseOfDeath\CauseOfDeathPaginatedListItem;
use Cemetery\Registrar\Domain\View\CauseOfDeath\CauseOfDeathSimpleList;
use Cemetery\Registrar\Domain\View\CauseOfDeath\CauseOfDeathSimpleListItem;
use Cemetery\Registrar\Domain\View\CauseOfDeath\CauseOfDeathView;
use Doctrine\DBAL\Query\QueryBuilder;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class DoctrineDbalCauseOfDeathFetcher extends DoctrineDbalFetcher implements CauseOfDeathFetcher
{
    protected string $tableName = 'cause_of_death';

    public function paginate(int $page, ?string $term = null, int $pageSize = self::DEFAULT_PAGE_SIZE): CauseOfDeathPaginatedList
    {
        $queryBuilder = $this->connection->createQueryBuilder()
            ->select(
                'cod.id   AS id',
                'cod.name AS name',
            )
            ->from($this->tableName, 'cod')
            ->andWhere('cod.removed_at IS NULL')
            ->orderBy('cod.name')
            ->setFirstResult(($page - 1) * $pageSize)
            ->setMaxResults($pageSize);
        $this->appendAndWhereLikeTerm($queryBuilder, $term);
        $this->setTermParameter($queryBuilder, $term);

        $listData = $queryBuilder
            ->executeQuery()
            ->fetchAllAssociative();
        $totalCount = $this->doCountTotal($term);
        $totalPages = (int) \ceil($totalCount / $pageSize);

        return $this->hydratePaginatedList($listData, $page, $pageSize, $term, $totalCount, $totalPages);
    }

    public function findViewById(string $id): ?CauseOfDeathView
    {
        $viewData = $this->queryViewData($id);

        return $viewData ? $this->hydrateView($viewData) : null;
    }

    public function countTotal(): int
    {
        return $this->doCountTotal(null);
    }

    public function findAll(?string $term = null): CauseOfDeathSimpleList
    {
        $queryBuilder = $this->connection->createQueryBuilder()
            ->select(
                'cod.id   AS id',
                'cod.name AS name',
            )
            ->from($this->tableName, 'cod')
            ->andWhere('cod.removed_at IS NULL')
            ->orderBy('cod.name');
        $this->appendAndWhereLikeTerm($queryBuilder, $term);
        $this->setTermParameter($queryBuilder, $term);

        $listData = $queryBuilder
            ->executeQuery()
            ->fetchAllAssociative();

        return $this->hydrateListAll($listData);
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

    private function hydratePaginatedList(
        array   $listData,
        int     $page,
        int     $pageSize,
        ?string $term,
        int     $totalCount,
        int     $totalPages,
    ): CauseOfDeathPaginatedList {
        $items = [];
        foreach ($listData as $listItemData) {
            $items[] = new CauseOfDeathPaginatedListItem(
                $listItemData['id'],
                $listItemData['name'],
            );
        }

        return new CauseOfDeathPaginatedList($items, $page, $pageSize, $term, $totalCount, $totalPages);
    }

    private function hydrateListAll(
        array $listData,
    ): CauseOfDeathSimpleList {
        $items = [];
        foreach ($listData as $listItemData) {
            $items[] = new CauseOfDeathSimpleListItem(
                $listItemData['id'],
                $listItemData['name'],
            );
        }

        return new CauseOfDeathSimpleList($items);
    }
}
