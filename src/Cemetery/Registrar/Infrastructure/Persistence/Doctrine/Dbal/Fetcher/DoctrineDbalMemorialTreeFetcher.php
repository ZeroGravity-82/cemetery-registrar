<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Fetcher;

use Cemetery\Registrar\Domain\View\BurialPlace\MemorialTree\MemorialTreeFetcher;
use Cemetery\Registrar\Domain\View\BurialPlace\MemorialTree\MemorialTreeList;
use Cemetery\Registrar\Domain\View\BurialPlace\MemorialTree\MemorialTreeListItem;
use Cemetery\Registrar\Domain\View\BurialPlace\MemorialTree\MemorialTreeView;
use Doctrine\DBAL\Query\QueryBuilder;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class DoctrineDbalMemorialTreeFetcher extends DoctrineDbalFetcher implements MemorialTreeFetcher
{
    /**
     * {@inheritdoc}
     */
    public function findViewById(string $id): ?MemorialTreeView
    {
        $viewData = $this->queryViewData($id);

        return $viewData ? $this->hydrateView($viewData) : null;
    }

    /**
     * {@inheritdoc}
     */
    public function findAll(int $page, ?string $term = null, int $pageSize = self::DEFAULT_PAGE_SIZE): MemorialTreeList
    {
        $queryBuilder = $this->connection->createQueryBuilder()
            ->select(
                'mt.id          AS id',
                'mt.tree_number AS treeNumber',
            )
            ->from('memorial_tree', 'mt')
            ->andWhere('mt.removed_at IS NULL')
            ->orderBy('mt.tree_number')
            ->setFirstResult(($page - 1) * $pageSize)
            ->setMaxResults($pageSize);
        $this->appendAndWhereLikeTerm($queryBuilder, $term);
        $this->setTermParameter($queryBuilder, $term);

        $listData = $queryBuilder
            ->executeQuery()
            ->fetchAllAssociative();
        $totalCount = $this->doCountTotal($term);
        $totalPages = (int) \ceil($totalCount / $pageSize);

        return $this->hydrateList($listData, $page, $pageSize, $term, $totalCount, $totalPages);
    }

    /**
     * {@inheritdoc}
     */
    public function countTotal(): int
    {
        return $this->doCountTotal(null);
    }

    /**
     * @param string $id
     *
     * @return false|array
     */
    private function queryViewData(string $id): false|array
    {
        return $this->connection->createQueryBuilder()
            ->select(
                'mt.id                                       AS id',
                'mt.tree_number                              AS treeNumber',
                'mt.geo_position->>"$.coordinates.latitude"  AS geoPositionLatitude',
                'mt.geo_position->>"$.coordinates.longitude" AS geoPositionLongitude',
                'mt.geo_position->>"$.error"                 AS geoPositionError',
                'mt.created_at                               AS createdAt',
                'mt.updated_at                               AS updatedAt',
            )
            ->from('memorial_tree', 'mt')
            ->andWhere('mt.id = :id')
            ->andWhere('mt.removed_at IS NULL')
            ->setParameter('id', $id)
            ->executeQuery()
            ->fetchAssociative();
    }

    /**
     * @param string|null $term
     *
     * @return int
     */
    private function doCountTotal(?string $term): int
    {
        $queryBuilder = $this->connection->createQueryBuilder()
            ->select('COUNT(mt.id)')
            ->from('memorial_tree', 'mt')
            ->andWhere('mt.removed_at IS NULL');
        $this->appendAndWhereLikeTerm($queryBuilder, $term);
        $this->setTermParameter($queryBuilder, $term);

        return $queryBuilder
            ->executeQuery()
            ->fetchFirstColumn()[0];
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param string|null  $term
     */
    private function appendAndWhereLikeTerm(QueryBuilder $queryBuilder, ?string $term): void
    {
        if ($this->isTermNotEmpty($term)) {
            $queryBuilder
                ->andWhere(
                    $queryBuilder->expr()->or(
                        $queryBuilder->expr()->like('mt.tree_number', ':term'),
                    )
                );
        }
    }

    /**
     * @param array $viewData
     *
     * @return MemorialTreeView
     */
    private function hydrateView(array $viewData): MemorialTreeView
    {
        return new MemorialTreeView(
            $viewData['id'],
            $viewData['treeNumber'],
            $viewData['geoPositionLatitude'],
            $viewData['geoPositionLongitude'],
            match ($viewData['geoPositionError']) {
                'null'  => null,
                default => $viewData['geoPositionError'],
            },
            $viewData['createdAt'],
            $viewData['updatedAt'],
        );
    }

    /**
     * @param array       $listData
     * @param int         $page
     * @param int         $pageSize
     * @param string|null $term
     * @param int         $totalCount
     * @param int         $totalPages
     *
     * @return MemorialTreeList
     */
    private function hydrateList(
        array   $listData,
        int     $page,
        int     $pageSize,
        ?string $term,
        int     $totalCount,
        int     $totalPages,
    ): MemorialTreeList {
        $listItems = [];
        foreach ($listData as $listItemData) {
            $listItems[] = new MemorialTreeListItem(
                $listItemData['id'],
                $listItemData['treeNumber'],
            );
        }

        return new MemorialTreeList($listItems, $page, $pageSize, $term, $totalCount, $totalPages);
    }
}
