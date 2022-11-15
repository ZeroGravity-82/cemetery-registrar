<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Fetcher;

use Cemetery\Registrar\Domain\View\BurialPlace\GraveSite\CemeteryBlockFetcherInterface;
use Cemetery\Registrar\Domain\View\BurialPlace\GraveSite\CemeteryBlockList;
use Cemetery\Registrar\Domain\View\BurialPlace\GraveSite\CemeteryBlockListItem;
use Cemetery\Registrar\Domain\View\BurialPlace\GraveSite\CemeteryBlockView;
use Doctrine\DBAL\Query\QueryBuilder;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class DoctrineDbalCemeteryBlockFetcher extends DoctrineDbalFetcher implements CemeteryBlockFetcherInterface
{
    protected string $tableName = 'cemetery_block';

    public function paginate(int $page, ?string $term = null, int $pageSize = self::DEFAULT_PAGE_SIZE): CemeteryBlockList
    {
        $queryBuilder = $this->connection->createQueryBuilder()
            ->select(
                'cb.id   AS id',
                'cb.name AS name',
            )
            ->from($this->tableName, 'cb')
            ->andWhere('cb.removed_at IS NULL')
            ->orderBy('cb.name')
            ->setFirstResult(($page - 1) * $pageSize)
            ->setMaxResults($pageSize);
        $this->appendAndWhereLikeTerm($queryBuilder, $term);
        $this->setTermParameter($queryBuilder, $term);

        $listData = $queryBuilder
            ->executeQuery()
            ->fetchAllAssociative();
        $totalCount = $this->doCountTotal($term);
        $totalPages = (int) \ceil($totalCount / $pageSize);

        return $this->hydratePaginatedList($listData, $page, $pageSize, $term, $totalCount, $totalPages);
    }

    public function findViewById(string $id): ?CemeteryBlockView
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
            ->select('COUNT(cb.id)')
            ->from($this->tableName, 'cb')
            ->andWhere('cb.name = :name')
            ->andWhere('cb.removed_at IS NULL')
            ->setParameter('name', $name)
            ->executeQuery()
            ->fetchFirstColumn()[0];
    }

    private function queryViewData(string $id): false|array
    {
        return $this->connection->createQueryBuilder()
            ->select(
                'cb.id         AS id',
                'cb.name       AS name',
                'cb.created_at AS createdAt',
                'cb.updated_at AS updatedAt',
            )
            ->from($this->tableName, 'cb')
            ->andWhere('cb.id = :id')
            ->andWhere('cb.removed_at IS NULL')
            ->setParameter('id', $id)
            ->executeQuery()
            ->fetchAssociative();
    }

    private function doCountTotal(?string $term): int
    {
        $queryBuilder = $this->connection->createQueryBuilder()
            ->select('COUNT(cb.id)')
            ->from($this->tableName, 'cb')
            ->andWhere('cb.removed_at IS NULL');
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
                    $queryBuilder->expr()->like('cb.name', ':term'),
                );
        }
    }

    private function hydrateView(array $viewData): CemeteryBlockView
    {
        return new CemeteryBlockView(
            $viewData['id'],
            $viewData['name'],
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
    ): CemeteryBlockList {
        $items = [];
        foreach ($listData as $listItemData) {
            $items[] = new CemeteryBlockListItem(
                $listItemData['id'],
                $listItemData['name'],
            );
        }

        return new CemeteryBlockList($items, $page, $pageSize, $term, $totalCount, $totalPages);
    }
}
