<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Fetcher;

use Cemetery\Registrar\Domain\View\BurialPlace\MemorialTree\MemorialTreeFetcherInterface;
use Cemetery\Registrar\Domain\View\BurialPlace\MemorialTree\MemorialTreeList;
use Cemetery\Registrar\Domain\View\BurialPlace\MemorialTree\MemorialTreeListItem;
use Cemetery\Registrar\Domain\View\BurialPlace\MemorialTree\MemorialTreeView;
use Doctrine\DBAL\Query\QueryBuilder;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class DoctrineDbalMemorialTreeFetcher extends AbstractDoctrineDbalFetcher implements MemorialTreeFetcherInterface
{
    protected string $tableName = 'memorial_tree';

    public function paginate(int $page, ?string $term = null, int $pageSize = self::DEFAULT_PAGE_SIZE): MemorialTreeList
    {
        $queryBuilder = $this->connection->createQueryBuilder()
            ->select(
                'mt.id           AS id',
                'mt.tree_number  AS treeNumber',
                'mtpic.id        AS personInChargeId',
                'mtpic.full_name AS personInChargeFullName',
            )
            ->from($this->tableName, 'mt')
            ->andWhere('mt.removed_at IS NULL')
            ->orderBy('mt.tree_number')
            ->setFirstResult(($page - 1) * $pageSize)
            ->setMaxResults($pageSize);
        $this->appendJoins($queryBuilder);
        $this->appendAndWhereLikeTerm($queryBuilder, $term);
        $this->setTermParameter($queryBuilder, $term);

        $listData = $queryBuilder
            ->executeQuery()
            ->fetchAllAssociative();
        $totalCount = $this->doCountTotal($term);
        $totalPages = (int) \ceil($totalCount / $pageSize);

        return $this->hydratePaginatedList($listData, $page, $pageSize, $term, $totalCount, $totalPages);
    }

    public function findViewById(string $id): ?MemorialTreeView
    {
        $viewData = $this->queryViewData($id);

        return $viewData ? $this->hydrateView($viewData) : null;
    }

    public function countTotal(): int
    {
        return $this->doCountTotal(null);
    }

    public function doesExistByTreeNumber(string $treeNumber): bool
    {
        return (bool) $this->connection->createQueryBuilder()
            ->select('COUNT(mt.id)')
            ->from($this->tableName, 'mt')
            ->andWhere('mt.tree_number = :treeNumber')
            ->andWhere('mt.removed_at IS NULL')
            ->setParameter('treeNumber', $treeNumber)
            ->executeQuery()
            ->fetchFirstColumn()[0];
    }

    private function queryViewData(string $id): false|array
    {
        return $this->connection->createQueryBuilder()
            ->select(
                'mt.id                                                                        AS id',
                'mt.tree_number                                                               AS treeNumber',
                'mt.geo_position->>"$.coordinates.latitude"                                   AS geoPositionLatitude',
                'mt.geo_position->>"$.coordinates.longitude"                                  AS geoPositionLongitude',
                'IF(mt.geo_position->>"$.error" <> "null", mt.geo_position->>"$.error", NULL) AS geoPositionError',
                'mt.created_at                                                                AS createdAt',
                'mt.updated_at                                                                AS updatedAt',
            )
            ->from($this->tableName, 'mt')
            ->andWhere('mt.id = :id')
            ->andWhere('mt.removed_at IS NULL')
            ->setParameter('id', $id)
            ->executeQuery()
            ->fetchAssociative();
    }

    private function doCountTotal(?string $term): int
    {
        $queryBuilder = $this->connection->createQueryBuilder()
            ->select('COUNT(mt.id)')
            ->from($this->tableName, 'mt')
            ->andWhere('mt.removed_at IS NULL');
        $this->appendJoins($queryBuilder);
        $this->appendAndWhereLikeTerm($queryBuilder, $term);
        $this->setTermParameter($queryBuilder, $term);

        return $queryBuilder
            ->executeQuery()
            ->fetchFirstColumn()[0];
    }

    private function appendJoins(QueryBuilder $queryBuilder): void
    {
        $queryBuilder
            ->leftJoin('mt', 'natural_person', 'mtpic', 'mt.person_in_charge_id = mtpic.id');
    }

    private function appendAndWhereLikeTerm(QueryBuilder $queryBuilder, ?string $term): void
    {
        if ($this->isTermNotEmpty($term)) {
            $queryBuilder
                ->andWhere(
                    $queryBuilder->expr()->or(
                        $queryBuilder->expr()->like('mt.tree_number', ':term'),
                        $queryBuilder->expr()->like('mtpic.full_name', ':term'),
                    )
                );
        }
    }

    private function hydrateView(array $viewData): MemorialTreeView
    {
        return new MemorialTreeView(
            $viewData['id'],
            $viewData['treeNumber'],
            $viewData['geoPositionLatitude'],
            $viewData['geoPositionLongitude'],
            $viewData['geoPositionError'],
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
    ): MemorialTreeList {
        $items = [];
        foreach ($listData as $listItemData) {
            $items[] = new MemorialTreeListItem(
                $listItemData['id'],
                $listItemData['treeNumber'],
                $listItemData['personInChargeId'],
                $listItemData['personInChargeFullName'],
            );
        }

        return new MemorialTreeList($items, $page, $pageSize, $term, $totalCount, $totalPages);
    }
}
