<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Fetcher;

use Cemetery\Registrar\Domain\View\BurialPlace\ColumbariumNiche\ColumbariumFetcher;
use Cemetery\Registrar\Domain\View\BurialPlace\ColumbariumNiche\ColumbariumList;
use Cemetery\Registrar\Domain\View\BurialPlace\ColumbariumNiche\ColumbariumListItem;
use Cemetery\Registrar\Domain\View\BurialPlace\ColumbariumNiche\ColumbariumView;
use Doctrine\DBAL\Query\QueryBuilder;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class DoctrineDbalColumbariumFetcher extends DoctrineDbalFetcher implements ColumbariumFetcher
{
    protected string $tableName = 'columbarium';

    public function paginate(int $page = null, ?string $term = null, int $pageSize = self::DEFAULT_PAGE_SIZE): ColumbariumList
    {
        $queryBuilder = $this->connection->createQueryBuilder()
            ->select(
                'c.id   AS id',
                'c.name AS name',
            )
            ->from($this->tableName, 'c')
            ->andWhere('c.removed_at IS NULL')
            ->orderBy('c.name')
            ->setFirstResult(($page - 1) * $pageSize)
            ->setMaxResults($pageSize);
        $this->appendAndWhereLikeTerm($queryBuilder, $term);
        $this->setTermParameter($queryBuilder, $term);

        $paginatedListData = $queryBuilder
            ->executeQuery()
            ->fetchAllAssociative();
        $totalCount = $this->doCountTotal($term);
        $totalPages = (int) \ceil($totalCount / $pageSize);

        return $this->hydratePaginatedList($paginatedListData, $page, $pageSize, $term, $totalCount, $totalPages);
    }

    public function findViewById(string $id): ?ColumbariumView
    {
        $viewData = $this->queryViewData($id);

        return $viewData ? $this->hydrateView($viewData) : null;
    }

    public function countTotal(): int
    {
        return $this->doCountTotal(null);
    }

    public function doesExistByName(string $name): bool
    {
        return (bool) $this->connection->createQueryBuilder()
            ->select('COUNT(c.id)')
            ->from($this->tableName, 'c')
            ->andWhere('c.name = :name')
            ->andWhere('c.removed_at IS NULL')
            ->setParameter('name', $name)
            ->executeQuery()
            ->fetchFirstColumn()[0];
    }

    private function queryViewData(string $id): false|array
    {
        return $this->connection->createQueryBuilder()
            ->select(
                'c.id                                                                       AS id',
                'c.name                                                                     AS name',
                'c.geo_position->>"$.coordinates.latitude"                                  AS geoPositionLatitude',
                'c.geo_position->>"$.coordinates.longitude"                                 AS geoPositionLongitude',
                'IF(c.geo_position->>"$.error" <> "null", c.geo_position->>"$.error", NULL) AS geoPositionError',
                'c.created_at                                                               AS createdAt',
                'c.updated_at                                                               AS updatedAt',
            )
            ->from($this->tableName, 'c')
            ->andWhere('c.id = :id')
            ->andWhere('c.removed_at IS NULL')
            ->setParameter('id', $id)
            ->executeQuery()
            ->fetchAssociative();
    }

    private function doCountTotal(?string $term): int
    {
        $queryBuilder = $this->connection->createQueryBuilder()
            ->select('COUNT(c.id)')
            ->from($this->tableName, 'c')
            ->andWhere('c.removed_at IS NULL');
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
                    $queryBuilder->expr()->like('c.name', ':term'),
                );
        }
    }

    private function hydrateView(array $viewData): ColumbariumView
    {
        return new ColumbariumView(
            $viewData['id'],
            $viewData['name'],
            $viewData['geoPositionLatitude'],
            $viewData['geoPositionLongitude'],
            $viewData['geoPositionError'],
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
    ): ColumbariumList {
        $items = [];
        foreach ($paginatedListData as $paginatedListItemData) {
            $items[] = new ColumbariumListItem(
                $paginatedListItemData['id'],
                $paginatedListItemData['name'],
            );
        }

        return new ColumbariumList($items, $page, $pageSize, $term, $totalCount, $totalPages);
    }
}
