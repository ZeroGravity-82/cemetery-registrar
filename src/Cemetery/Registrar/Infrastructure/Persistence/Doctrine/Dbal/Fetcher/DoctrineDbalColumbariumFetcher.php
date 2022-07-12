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
    /**
     * {@inheritdoc}
     */
    public function findViewById(string $id): ?ColumbariumView
    {
        $viewData = $this->queryViewData($id);

        return $viewData ? $this->hydrateView($viewData) : null;
    }

    /**
     * {@inheritdoc}
     */
    public function findAll(int $page, ?string $term = null, int $pageSize = self::DEFAULT_PAGE_SIZE): ColumbariumList
    {
        $columbariumListData = $this->connection->createQueryBuilder()
            ->select(
                'c.id   AS id',
                'c.name AS name',
            )
            ->from('columbarium', 'c')
            ->andWhere('c.removed_at IS NULL')
            ->orderBy('c.name')
            ->executeQuery()
            ->fetchAllAssociative();

        return $this->hydrateList($columbariumListData);
    }

    /**
     * {@inheritdoc}
     */
    public function countTotal(): int
    {
        return $this->doCountTotal();
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
                'c.id                                       AS id',
                'c.name                                     AS name',
                'c.geo_position->>"$.coordinates.latitude"  AS geoPositionLatitude',
                'c.geo_position->>"$.coordinates.longitude" AS geoPositionLongitude',
                'c.geo_position->>"$.error"                 AS geoPositionError',
                'c.created_at                               AS createdAt',
                'c.updated_at                               AS updatedAt',
            )
            ->from('columbarium', 'c')
            ->andWhere('c.id = :id')
            ->andWhere('c.removed_at IS NULL')
            ->setParameter('id', $id)
            ->executeQuery()
            ->fetchAssociative();
    }

    /**
     * @return int
     */
    private function doCountTotal(): int
    {
        return $this->connection->createQueryBuilder()
            ->select('COUNT(c.id)')
            ->from('columbarium', 'c')
            ->andWhere('c.removed_at IS NULL')
            ->executeQuery()
            ->fetchFirstColumn()[0];
    }

    /**
     * @param array $viewData
     *
     * @return ColumbariumView
     */
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

    /**
     * @param array $listData
     *
     * @return ColumbariumList
     */
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
