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
    protected string $tableName = 'columbarium_niche';

    public function paginate(int $page, ?string $term = null, int $pageSize = self::DEFAULT_PAGE_SIZE): ColumbariumNicheList
    {
        $queryBuilder = $this->connection->createQueryBuilder()
            ->select(
                'cn.id                 AS id',
                'c.name                AS columbariumName',
                'cn.row_in_columbarium AS rowInColumbarium',
                'cn.niche_number       AS nicheNumber',
                'cnpic.id              AS personInChargeId',
                'cnpic.full_name       AS personInChargeFullName',
            )
            ->from($this->tableName, 'cn')
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

        return $this->hydratePaginatedList($listData, $page, $pageSize, $term, $totalCount, $totalPages);
    }

    public function findViewById(string $id): ?ColumbariumNicheView
    {
        $viewData = $this->queryViewData($id);

        return $viewData ? $this->hydrateView($viewData) : null;
    }

    public function countTotal(): int
    {
        return $this->doCountTotal(null);
    }

    public function doesExistByColumbariumIdAndNicheNumber(string $columbariumId, string $nicheNumber): bool
    {
        return (bool) $this->connection->createQueryBuilder()
            ->select('COUNT(cn.id)')
            ->from($this->tableName, 'cn')
            ->andWhere('cn.columbarium_id = :columbariumId')
            ->andWhere('cn.niche_number = :nicheNumber')
            ->andWhere('cn.removed_at IS NULL')
            ->setParameter('columbariumId', $columbariumId)
            ->setParameter('nicheNumber', $nicheNumber)
            ->executeQuery()
            ->fetchFirstColumn()[0];
    }

    private function queryViewData(string $id): false|array
    {
        return $this->connection->createQueryBuilder()
            ->select(
                'cn.id                                                                        AS id',
                'cn.columbarium_id                                                            AS columbariumId',
                'cn.row_in_columbarium                                                        AS rowInColumbarium',
                'cn.niche_number                                                              AS nicheNumber',
                'cn.geo_position->>"$.coordinates.latitude"                                   AS geoPositionLatitude',
                'cn.geo_position->>"$.coordinates.longitude"                                  AS geoPositionLongitude',
                'IF(cn.geo_position->>"$.error" <> "null", cn.geo_position->>"$.error", NULL) AS geoPositionError',
                'cn.created_at                                                                AS createdAt',
                'cn.updated_at                                                                AS updatedAt',
            )
            ->from($this->tableName, 'cn')
            ->andWhere('cn.id = :id')
            ->andWhere('cn.removed_at IS NULL')
            ->setParameter('id', $id)
            ->executeQuery()
            ->fetchAssociative();
    }

    private function doCountTotal(?string $term): int
    {
        $queryBuilder = $this->connection->createQueryBuilder()
            ->select('COUNT(cn.id)')
            ->from($this->tableName, 'cn')
            ->andWhere('cn.removed_at IS NULL');
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
            ->leftJoin('cn', 'columbarium',    'c',     'cn.columbarium_id      = c.id')
            ->leftJoin('cn', 'natural_person', 'cnpic', 'cn.person_in_charge_id = cnpic.id');
    }

    private function appendAndWhereLikeTerm(QueryBuilder $queryBuilder, ?string $term): void
    {
        if ($this->isTermNotEmpty($term)) {
            $queryBuilder
                ->andWhere(
                    $queryBuilder->expr()->or(
                        $queryBuilder->expr()->like('c.name', ':term'),
                        $queryBuilder->expr()->like('cn.row_in_columbarium', ':term'),
                        $queryBuilder->expr()->like('cn.niche_number', ':term'),
                        $queryBuilder->expr()->like('cnpic.full_name', ':term'),
                    )
                );
        }
    }

    private function hydrateView(array $viewData): ColumbariumNicheView
    {
        return new ColumbariumNicheView(
            $viewData['id'],
            $viewData['columbariumId'],
            $viewData['rowInColumbarium'],
            $viewData['nicheNumber'],
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
    ): ColumbariumNicheList {
        $items = [];
        foreach ($listData as $listItemData) {
            $items[] = new ColumbariumNicheListItem(
                $listItemData['id'],
                $listItemData['columbariumName'],
                $listItemData['rowInColumbarium'],
                $listItemData['nicheNumber'],
                $listItemData['personInChargeId'],
                $listItemData['personInChargeFullName'],
            );
        }

        return new ColumbariumNicheList($items, $page, $pageSize, $term, $totalCount, $totalPages);
    }
}
