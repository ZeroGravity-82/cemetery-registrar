<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Fetcher;

use Cemetery\Registrar\Domain\View\BurialPlace\GraveSite\CemeteryBlockFetcher;
use Cemetery\Registrar\Domain\View\BurialPlace\GraveSite\CemeteryBlockList;
use Cemetery\Registrar\Domain\View\BurialPlace\GraveSite\CemeteryBlockListItem;
use Cemetery\Registrar\Domain\View\BurialPlace\GraveSite\CemeteryBlockView;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class DoctrineDbalCemeteryBlockFetcher extends DoctrineDbalFetcher implements CemeteryBlockFetcher
{
    /**
     * {@inheritdoc}
     */
    public function findViewById(string $id): ?CemeteryBlockView
    {
        $viewData = $this->queryViewData($id);

        return $viewData ? $this->hydrateView($viewData) : null;
    }

    /**
     * {@inheritdoc}
     */
    public function findAll(int $page, ?string $term = null, int $pageSize = self::DEFAULT_PAGE_SIZE): CemeteryBlockList
    {
        $cemeteryBlockListData = $this->connection->createQueryBuilder()
            ->select(
                'cb.id   AS id',
                'cb.name AS name',
            )
            ->from('cemetery_block', 'cb')
            ->andWhere('cb.removed_at IS NULL')
            ->orderBy('cb.name')
            ->executeQuery()
            ->fetchAllAssociative();

        return $this->hydrateList($cemeteryBlockListData);
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
                'cb.id         AS id',
                'cb.name       AS name',
                'cb.created_at AS createdAt',
                'cb.updated_at AS updatedAt',
            )
            ->from('cemetery_block', 'cb')
            ->andWhere('cb.id = :id')
            ->andWhere('cb.removed_at IS NULL')
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
            ->select('COUNT(cb.id)')
            ->from('cemetery_block', 'cb')
            ->andWhere('cb.removed_at IS NULL')
            ->executeQuery()
            ->fetchFirstColumn()[0];
    }

    /**
     * @param array $viewData
     *
     * @return CemeteryBlockView
     */
    private function hydrateView(array $viewData): CemeteryBlockView
    {
        return new CemeteryBlockView(
            $viewData['id'],
            $viewData['name'],
            $viewData['createdAt'],
            $viewData['updatedAt'],
        );
    }

    /**
     * @param array $listData
     *
     * @return CemeteryBlockList
     */
    private function hydrateList(
        array $listData,
    ): CemeteryBlockList {
        $listItems = [];
        foreach ($listData as $listItemData) {
            $listItems[] = new CemeteryBlockListItem(
                $listItemData['id'],
                $listItemData['name'],
            );
        }

        return new CemeteryBlockList($listItems);
    }
}
