<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Fetcher;

use Cemetery\Registrar\Domain\View\BurialPlace\GraveSite\GraveSiteFetcher;
use Cemetery\Registrar\Domain\View\BurialPlace\GraveSite\GraveSiteList;
use Cemetery\Registrar\Domain\View\BurialPlace\GraveSite\GraveSiteListItem;
use Cemetery\Registrar\Domain\View\BurialPlace\GraveSite\GraveSiteView;
use Doctrine\DBAL\Query\QueryBuilder;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class DoctrineDbalGraveSiteFetcher extends DoctrineDbalFetcher implements GraveSiteFetcher
{
    public function findViewById(string $id): ?GraveSiteView
    {
        $viewData = $this->queryViewData($id);

        return $viewData ? $this->hydrateView($viewData) : null;
    }

    public function doesExistById(string $id): bool
    {
        // TODO implement
    }

    public function findAll(int $page, ?string $term = null, int $pageSize = self::DEFAULT_PAGE_SIZE): GraveSiteList
    {
        $queryBuilder = $this->connection->createQueryBuilder()
            ->select(
                'gs.id              AS id',
                'cb.name            AS cemeteryBlockName',
                'gs.row_in_block    AS rowInBlock',
                'gs.position_in_row AS positionInRow',
                'gs.size            AS size',
            )
            ->from('grave_site', 'gs')
            ->andWhere('gs.removed_at IS NULL')
            ->orderBy('cb.name')
            ->addOrderBy('gs.row_in_block')
            ->addOrderBy('gs.position_in_row')
            ->addOrderBy('gs.id')
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

    public function countTotal(): int
    {
        return $this->doCountTotal(null);
    }

    private function queryViewData(string $id): false|array
    {
        $queryBuilder = $this->connection->createQueryBuilder()
            ->select(
                'gs.id                                       AS id',
                'gs.cemetery_block_id                        AS cemeteryBlockId',
                'cb.name                                     AS cemeteryBlockName',
                'gs.row_in_block                             AS rowInBlock',
                'gs.position_in_row                          AS positionInRow',
                'gs.geo_position->>"$.coordinates.latitude"  AS geoPositionLatitude',
                'gs.geo_position->>"$.coordinates.longitude" AS geoPositionLongitude',
                'gs.geo_position->>"$.error"                 AS geoPositionError',
                'gs.size                                     AS size',
                'gs.created_at                               AS createdAt',
                'gs.updated_at                               AS updatedAt',
            )
            ->from('grave_site', 'gs')
            ->andWhere('gs.id = :id')
            ->andWhere('gs.removed_at IS NULL')
            ->setParameter('id', $id);
        $this->appendJoins($queryBuilder);

        return $queryBuilder
            ->executeQuery()
            ->fetchAssociative();
    }

    private function doCountTotal(?string $term): int
    {
        $queryBuilder = $this->connection->createQueryBuilder()
            ->select('COUNT(gs.id)')
            ->from('grave_site', 'gs')
            ->andWhere('gs.removed_at IS NULL');
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
            ->leftJoin('gs', 'cemetery_block', 'cb', 'gs.cemetery_block_id = cb.id');
    }

    private function appendAndWhereLikeTerm(QueryBuilder $queryBuilder, ?string $term): void
    {
        if ($this->isTermNotEmpty($term)) {
            $queryBuilder
                ->andWhere(
                    $queryBuilder->expr()->or(
                        $queryBuilder->expr()->like('cb.name', ':term'),
                        $queryBuilder->expr()->like('gs.row_in_block', ':term'),
                        $queryBuilder->expr()->like('gs.position_in_row', ':term'),
                        $queryBuilder->expr()->like('gs.size', ':term'),
                    )
                );
        }
    }

    private function hydrateView(array $viewData): GraveSiteView
    {
        return new GraveSiteView(
            $viewData['id'],
            $viewData['cemeteryBlockId'],
            $viewData['cemeteryBlockName'],
            $viewData['rowInBlock'],
            $viewData['positionInRow'],
            $viewData['geoPositionLatitude'],
            $viewData['geoPositionLongitude'],
            match ($viewData['geoPositionError']) {
                'null'  => null,
                default => $viewData['geoPositionError'],
            },
            $viewData['size'],
            $viewData['createdAt'],
            $viewData['updatedAt'],
        );
    }

    private function hydrateList(
        array   $listData,
        int     $page,
        int     $pageSize,
        ?string $term,
        int     $totalCount,
        int     $totalPages,
    ): GraveSiteList {
        $listItems = [];
        foreach ($listData as $listItemData) {
            $listItems[] = new GraveSiteListItem(
                $listItemData['id'],
                $listItemData['cemeteryBlockName'],
                $listItemData['rowInBlock'],
                $listItemData['positionInRow'],
                $listItemData['size'],
            );
        }

        return new GraveSiteList($listItems, $page, $pageSize, $term, $totalCount, $totalPages);
    }
}
