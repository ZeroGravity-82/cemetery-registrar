<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Fetcher;

use Cemetery\Registrar\Domain\View\BurialPlace\ColumbariumNiche\ColumbariumNicheFetcher;
use Cemetery\Registrar\Domain\View\BurialPlace\ColumbariumNiche\ColumbariumNicheList;
use Cemetery\Registrar\Domain\View\BurialPlace\ColumbariumNiche\ColumbariumNicheListItem;
use Cemetery\Registrar\Domain\View\BurialPlace\ColumbariumNiche\ColumbariumNicheView;
use Doctrine\DBAL\Query\QueryBuilder;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class DoctrineDbalColumbariumNicheFetcher extends DoctrineDbalFetcher implements ColumbariumNicheFetcher
{
    /**
     * {@inheritdoc}
     */
    public function getViewById(string $id): ColumbariumNicheView
    {
        $viewData = $this->queryViewData($id);
        if ($viewData === false) {
            throw new \RuntimeException(\sprintf('Колумбарная ниша с ID "%s" не найдена.', $id));
        }

        return $this->hydrateView($viewData);
    }

    /**
     * {@inheritdoc}
     */
    public function findAll(int $page, ?string $term = null, int $pageSize = self::DEFAULT_PAGE_SIZE): ColumbariumNicheList
    {
        $queryBuilder = $this->connection->createQueryBuilder()
            ->select(
                'cn.id                 AS id',
                'c.name                AS columbariumName',
                'cn.row_in_columbarium AS rowInColumbarium',
                'cn.niche_number       AS nicheNumber',
            )
            ->from('columbarium_niche', 'cn')
            ->andWhere('cn.removed_at IS NULL')
            ->orderBy('c.name')
            ->addOrderBy('cn.niche_number')
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
                'cn.id                                       AS id',
                'cn.columbarium_id                           AS columbariumId',
                'cn.row_in_columbarium                       AS rowInColumbarium',
                'cn.niche_number                             AS nicheNumber',
                'cn.geo_position->>"$.coordinates.latitude"  AS geoPositionLatitude',
                'cn.geo_position->>"$.coordinates.longitude" AS geoPositionLongitude',
                'cn.geo_position->>"$.error"                 AS geoPositionError',
                'cn.created_at                               AS createdAt',
                'cn.updated_at                               AS updatedAt',
            )
            ->from('columbarium_niche', 'cn')
            ->andWhere('cn.id = :id')
            ->andWhere('cn.removed_at IS NULL')
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
            ->select('COUNT(cn.id)')
            ->from('columbarium_niche', 'cn')
            ->andWhere('cn.removed_at IS NULL');
        $this->appendJoins($queryBuilder);
        $this->appendAndWhereLikeTerm($queryBuilder, $term);
        $this->setTermParameter($queryBuilder, $term);

        return $queryBuilder
            ->executeQuery()
            ->fetchFirstColumn()[0];
    }

    /**
     * @param QueryBuilder $queryBuilder
     */
    private function appendJoins(QueryBuilder $queryBuilder): void
    {
        $queryBuilder
            ->leftJoin('cn', 'columbarium', 'c', 'cn.columbarium_id = c.id');
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
                        $queryBuilder->expr()->like('c.name', ':term'),
                        $queryBuilder->expr()->like('cn.row_in_columbarium', ':term'),
                        $queryBuilder->expr()->like('cn.niche_number', ':term'),
                    )
                );
        }
    }

    /**
     * @param array $viewData
     *
     * @return ColumbariumNicheView
     */
    private function hydrateView(array $viewData): ColumbariumNicheView
    {
        return new ColumbariumNicheView(
            $viewData['id'],
            $viewData['columbariumId'],
            $viewData['rowInColumbarium'],
            $viewData['nicheNumber'],
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
     * @return ColumbariumNicheList
     */
    private function hydrateList(
        array   $listData,
        int     $page,
        int     $pageSize,
        ?string $term,
        int     $totalCount,
        int     $totalPages,
    ): ColumbariumNicheList {
        $listItems = [];
        foreach ($listData as $listItemData) {
            $listItems[] = new ColumbariumNicheListItem(
                $listItemData['id'],
                $listItemData['columbariumName'],
                $listItemData['rowInColumbarium'],
                $listItemData['nicheNumber'],
            );
        }

        return new ColumbariumNicheList($listItems, $page, $pageSize, $term, $totalCount, $totalPages);
    }
}