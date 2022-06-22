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
    public function getViewById(string $id): CemeteryBlockView
    {
        $cemeteryBlockViewData = $this->queryCemeteryBlockViewData($id);
        if ($cemeteryBlockViewData === false) {
            throw new \RuntimeException(\sprintf('Квартал с ID "%s" не найден.', $id));
        }

        return $this->hydrateView($cemeteryBlockViewData);
    }

    /**
     * {@inheritdoc}
     */
    public function findAll(): CemeteryBlockList
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
        return $this->connection->createQueryBuilder()
            ->select('COUNT(cb.id)')
            ->from('cemetery_block', 'cb')
            ->andWhere('cb.removed_at IS NULL')
            ->executeQuery()
            ->fetchFirstColumn()[0];
    }

    /**
     * @param string $id
     *
     * @return false|array
     */
    private function queryCemeteryBlockViewData(string $id): false|array
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
