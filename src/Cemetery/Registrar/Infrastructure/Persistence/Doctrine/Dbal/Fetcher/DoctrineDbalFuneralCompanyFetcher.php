<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Fetcher;

use Cemetery\Registrar\Domain\View\FuneralCompany\FuneralCompanyFetcher;
use Cemetery\Registrar\Domain\View\FuneralCompany\FuneralCompanyList;
use Cemetery\Registrar\Domain\View\FuneralCompany\FuneralCompanyListItem;
use Cemetery\Registrar\Domain\View\FuneralCompany\FuneralCompanyView;
use Doctrine\DBAL\Query\QueryBuilder;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class DoctrineDbalFuneralCompanyFetcher extends DoctrineDbalFetcher implements FuneralCompanyFetcher
{
    protected string $tableName = 'funeral_company';

    public function paginate(int $page = null, ?string $term = null, int $pageSize = self::DEFAULT_PAGE_SIZE): FuneralCompanyList
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

        $paginatedListData = $queryBuilder
            ->executeQuery()
            ->fetchAllAssociative();
        $totalCount = $this->doCountTotal($term);
        $totalPages = (int) \ceil($totalCount / $pageSize);

        return $this->hydratePaginatedList($paginatedListData, $page, $pageSize, $term, $totalCount, $totalPages);
    }

    public function findViewById(string $id): ?FuneralCompanyView
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
            ->select('COUNT(fc.id)')
            ->from($this->tableName, 'fc')
            ->andWhere('fc.name = :name')
            ->andWhere('fc.removed_at IS NULL')
            ->setParameter('name', $name)
            ->executeQuery()
            ->fetchFirstColumn()[0];
    }

    private function queryViewData(string $id): false|array
    {
        return $this->connection->createQueryBuilder()
            ->select(
                'fc.id         AS id',
                'fc.name       AS name',
                'fc.note       AS note',
                'fc.created_at AS createdAt',
                'fc.updated_at AS updatedAt',
            )
            ->from($this->tableName, 'fc')
            ->andWhere('fc.id = :id')
            ->andWhere('fc.removed_at IS NULL')
            ->setParameter('id', $id)
            ->executeQuery()
            ->fetchAssociative();
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

    private function hydrateView(array $viewData): FuneralCompanyView
    {
        return new FuneralCompanyView(
            $viewData['id'],
            $viewData['name'],
            $viewData['note'],
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
    ): FuneralCompanyList {
        $items = [];
        foreach ($paginatedListData as $paginatedListItemData) {
            $items[] = new FuneralCompanyListItem(
                $paginatedListItemData['id'],
                $paginatedListItemData['name'],
                $paginatedListItemData['note'],
            );
        }

        return new FuneralCompanyList($items, $page, $pageSize, $term, $totalCount, $totalPages);
    }
}
