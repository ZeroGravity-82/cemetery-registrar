<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Fetcher;

use Cemetery\Registrar\Domain\View\BurialPlace\ColumbariumNiche\ColumbariumFetcher;
use Cemetery\Registrar\Domain\View\BurialPlace\ColumbariumNiche\ColumbariumList;
use Cemetery\Registrar\Domain\View\BurialPlace\ColumbariumNiche\ColumbariumListItem;
use Cemetery\Registrar\Domain\View\BurialPlace\ColumbariumNiche\ColumbariumView;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class DoctrineDbalColumbariumFetcher extends DoctrineDbalFetcher implements ColumbariumFetcher
{
    protected string $tableName = 'columbarium';

    public function findViewById(string $id): ?ColumbariumView
    {
        $viewData = $this->queryViewData($id);

        return $viewData ? $this->hydrateView($viewData) : null;
    }

    public function findAll(int $page, ?string $term = null, int $pageSize = self::DEFAULT_PAGE_SIZE): ColumbariumList
    {
        $columbariumListData = $this->connection->createQueryBuilder()
            ->select(
                'c.id   AS id',
                'c.name AS name',
            )
            ->from($this->tableName, 'c')
            ->andWhere('c.removed_at IS NULL')
            ->orderBy('c.name')
            ->executeQuery()
            ->fetchAllAssociative();

        return $this->hydrateList($columbariumListData);
    }

    public function countTotal(): int
    {
        return $this->doCountTotal();
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
                'c.id                                       AS id',
                'c.name                                     AS name',
                'c.geo_position->>"$.coordinates.latitude"  AS geoPositionLatitude',
                'c.geo_position->>"$.coordinates.longitude" AS geoPositionLongitude',
                'c.geo_position->>"$.error"                 AS geoPositionError',
                'c.created_at                               AS createdAt',
                'c.updated_at                               AS updatedAt',
            )
            ->from($this->tableName, 'c')
            ->andWhere('c.id = :id')
            ->andWhere('c.removed_at IS NULL')
            ->setParameter('id', $id)
            ->executeQuery()
            ->fetchAssociative();
    }

    private function doCountTotal(): int
    {
        return $this->connection->createQueryBuilder()
            ->select('COUNT(c.id)')
            ->from($this->tableName, 'c')
            ->andWhere('c.removed_at IS NULL')
            ->executeQuery()
            ->fetchFirstColumn()[0];
    }

    private function hydrateView(array $viewData): ColumbariumView
    {
        return new ColumbariumView(
            $viewData['id'],
            $viewData['name'],
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

    private function hydrateList(
        array $listData,
    ): ColumbariumList {
        $listItems = [];
        foreach ($listData as $listItemData) {
            $listItems[] = new ColumbariumListItem(
                $listItemData['id'],
                $listItemData['name'],
            );
        }

        return new ColumbariumList($listItems);
    }
}
