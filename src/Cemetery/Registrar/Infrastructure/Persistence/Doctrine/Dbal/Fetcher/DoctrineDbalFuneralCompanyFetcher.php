<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Fetcher;

use Cemetery\Registrar\Domain\View\FuneralCompany\FuneralCompanyFetcher;
use Cemetery\Registrar\Domain\View\FuneralCompany\FuneralCompanyList;
use Cemetery\Registrar\Domain\View\FuneralCompany\FuneralCompanyListItem;
use Doctrine\DBAL\Query\QueryBuilder;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class DoctrineDbalFuneralCompanyFetcher extends DoctrineDbalFetcher implements FuneralCompanyFetcher
{
    protected string $tableName = 'funeral_company';

    public function findViewById(string $id): mixed
    {
        // TODO: implement + fix return type
        return null;
    }

    public function findAll(int $page, ?string $term = null, int $pageSize = self::DEFAULT_PAGE_SIZE): FuneralCompanyList
    {
        $queryBuilder = $this->connection->createQueryBuilder()
            ->select(
                'fc.id   AS id',
                'fc.name AS name',
                'fc.note AS note'
            )
            ->from($this->tableName, 'fc')
            ->andWhere('fc.removed_at IS NULL')
            ->orderBy('fc.name')
            ->setFirstResult(($page - 1) * $pageSize)
            ->setMaxResults($pageSize);
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

    private function doCountTotal(?string $term): int
    {
        $queryBuilder = $this->connection->createQueryBuilder()
            ->select('COUNT(fc.id)')
            ->from($this->tableName, 'fc')
            ->andWhere('fc.removed_at IS NULL');
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
                    $queryBuilder->expr()->or(
                        $queryBuilder->expr()->like('fc.name', ':term'),
                        $queryBuilder->expr()->like('fc.note', ':term'),
                    )
                );
        }
    }

    private function hydrateList(
        array   $listData,
        int     $page,
        int     $pageSize,
        ?string $term,
        int     $totalCount,
        int     $totalPages,
    ): FuneralCompanyList {
        $listItems = [];
        foreach ($listData as $listItemData) {
            $listItems[] = new FuneralCompanyListItem(
                $listItemData['id'],
                $listItemData['name'],
                $listItemData['note'],
            );
        }

        return new FuneralCompanyList($listItems, $page, $pageSize, $term, $totalCount, $totalPages);
    }
}
