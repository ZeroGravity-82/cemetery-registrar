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
    protected string $tableName = 'grave_site';

    public function paginate(int $page = null, ?string $term = null, int $pageSize = self::DEFAULT_PAGE_SIZE): GraveSiteList
    {
        $queryBuilder = $this->connection->createQueryBuilder()
            ->select(
                'gs.id              AS id',
                'cb.name            AS cemeteryBlockName',
                'gs.row_in_block    AS rowInBlock',
                'gs.position_in_row AS positionInRow',
                'gs.size            AS size',
                'gspic.full_name    AS personInChargeFullName',
            )
            ->from($this->tableName, 'gs')
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

        $paginatedListData = $queryBuilder
            ->executeQuery()
            ->fetchAllAssociative();
        $totalCount = $this->doCountTotal($term);
        $totalPages = (int) \ceil($totalCount / $pageSize);

        return $this->hydratePaginatedList($paginatedListData, $page, $pageSize, $term, $totalCount, $totalPages);
    }

    public function findViewById(string $id): ?GraveSiteView
    {
        $viewData = $this->queryViewData($id);

        return $viewData ? $this->hydrateView($viewData) : null;
    }

    public function countTotal(): int
    {
        return $this->doCountTotal(null);
    }

    public function doesAlreadyUsedCemeteryBlockIdAndRowInBlockAndPositionInRow(
        ?string $id,
        string  $cemeteryBlockId,
        int     $rowInBlock,
        ?int    $positionInRow,
    ): bool {
        if ($positionInRow === null) {  // It is impossible to say with certainty whether a grave site exists,
            return false;               // because position in row data not provided.
        }

        $queryBuilder = $this->connection->createQueryBuilder()
            ->select('COUNT(gs.id)')
            ->from($this->tableName, 'gs')
            ->andWhere('gs.cemetery_block_id = :cemeteryBlockId')
            ->andWhere('gs.row_in_block = :rowInBlock')
            ->andWhere('gs.position_in_row = :positionInRow')
            ->andWhere('gs.removed_at IS NULL')
            ->setParameter('cemeteryBlockId', $cemeteryBlockId)
            ->setParameter('rowInBlock', $rowInBlock)
            ->setParameter('positionInRow', $positionInRow);
        if ($id !== null) {
            $queryBuilder
                ->andWhere('gs.id <> :id')
                ->setParameter('id', $id);
        }

        return (bool) $queryBuilder
            ->executeQuery()
            ->fetchFirstColumn()[0];
    }

    private function queryViewData(string $id): false|array
    {
        $queryBuilder = $this->connection->createQueryBuilder()
            ->select(
                'gs.id                                                                        AS id',
                'gs.cemetery_block_id                                                         AS cemeteryBlockId',
                'cb.name                                                                      AS cemeteryBlockName',
                'gs.row_in_block                                                              AS rowInBlock',
                'gs.position_in_row                                                           AS positionInRow',
                'gs.geo_position->>"$.coordinates.latitude"                                   AS geoPositionLatitude',
                'gs.geo_position->>"$.coordinates.longitude"                                  AS geoPositionLongitude',
                'IF(gs.geo_position->>"$.error" <> "null", gs.geo_position->>"$.error", NULL) AS geoPositionError',
                'gs.size                                                                      AS size',
                'gspic.full_name                                                              AS personInChargeFullName',
                'gs.created_at                                                                AS createdAt',
                'gs.updated_at                                                                AS updatedAt',
            )
            ->from($this->tableName, 'gs')
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
            ->from($this->tableName, 'gs')
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
            ->leftJoin('gs', 'cemetery_block', 'cb',    'gs.cemetery_block_id   = cb.id')
            ->leftJoin('gs', 'natural_person', 'gspic', 'gs.person_in_charge_id = gspic.id');
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
                        $queryBuilder->expr()->like('gspic.full_name', ':term'),
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
            $viewData['geoPositionError'],
            $viewData['size'],
            $viewData['personInChargeFullName'],
            $viewData['createdAt'],
            $viewData['updatedAt'],
        );
    }

    private function hydratePaginatedList(
        array   $paginatedListData,
        int     $page,
        int     $pageSize,
        ?string $term,
        int     $totalCount,
        int     $totalPages,
    ): GraveSiteList {
        $items = [];
        foreach ($paginatedListData as $paginatedListItemData) {
            $items[] = new GraveSiteListItem(
                $paginatedListItemData['id'],
                $paginatedListItemData['cemeteryBlockName'],
                $paginatedListItemData['rowInBlock'],
                $paginatedListItemData['positionInRow'],
                $paginatedListItemData['size'],
                $paginatedListItemData['personInChargeFullName'],
            );
        }

        return new GraveSiteList($items, $page, $pageSize, $term, $totalCount, $totalPages);
    }
}
