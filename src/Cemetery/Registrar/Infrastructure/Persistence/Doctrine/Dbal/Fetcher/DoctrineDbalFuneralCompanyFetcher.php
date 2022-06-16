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
    // TODO implement getViewById() method

    /**
     * {@inheritdoc}
     */
    public function findAll(int $page, ?string $term = null, int $pageSize = self::DEFAULT_PAGE_SIZE): FuneralCompanyList
    {
        $queryBuilder = $this->connection->createQueryBuilder()
            ->select(
                'fc.id                         AS id',
                'fc.organization_id->>"$.type" AS organizationType',
                'ojp.name                      AS organizationJuristicPersonName',
                'ojp.inn                       AS organizationJuristicPersonInn',
                'ojp.legal_address             AS organizationJuristicPersonLegalAddress',
                'ojp.postal_address            AS organizationJuristicPersonPostalAddress',
                'ojp.phone                     AS organizationJuristicPersonPhone',
                'osp.name                      AS organizationSoleProprietorName',
                'osp.inn                       AS organizationSoleProprietorInn',
                'osp.registration_address      AS organizationSoleProprietorRegistrationAddress',
                'osp.actual_location_address   AS organizationSoleProprietorActualLocationAddress',
                'osp.phone                     AS organizationSoleProprietorPhone',
                'fc.note                       AS note'
            )
            ->from('funeral_company', 'fc')
            ->andWhere('fc.removed_at IS NULL')
            ->orderBy('ojp.name')
            ->addOrderBy('osp.name')
            ->setFirstResult(($page - 1) * $pageSize)
            ->setMaxResults($pageSize);
        $this->appendJoins($queryBuilder);
        $this->appendAndWhereLikeTerm($queryBuilder, $term);
        $this->setTermParameter($queryBuilder, $term);

        $funeralCompanyListData = $queryBuilder
            ->executeQuery()
            ->fetchAllAssociative();
        $totalCount = $this->doCountTotal($term);
        $totalPages = (int) \ceil($totalCount / $pageSize);

        return $this->hydrateFuneralCompanyList($funeralCompanyListData, $page, $pageSize, $term, $totalCount, $totalPages);
    }

    /**
     * {@inheritdoc}
     */
    public function countTotal(): int
    {
        return $this->doCountTotal(null);
    }

    /**
     * @param string|null $term
     *
     * @return int
     */
    private function doCountTotal(?string $term): int
    {
        $queryBuilder = $this->connection->createQueryBuilder()
            ->select('COUNT(fc.id)')
            ->from('funeral_company', 'fc')
            ->andWhere('fc.removed_at IS NULL');
        $this->appendJoins($queryBuilder);
        $this->appendAndWhereLikeTerm($queryBuilder, $term);
        $this->setTermParameter($queryBuilder, $term);

        return $queryBuilder
            ->executeQuery()
            ->fetchFirstColumn()[0];
    }

    /**
     * @param QueryBuilder $queryBuilder
     */
    private function appendJoins(QueryBuilder $queryBuilder): void
    {
        $queryBuilder
            ->leftJoin('fc', 'juristic_person', 'ojp', 'fc.organization_id->>"$.value" = ojp.id')
            ->leftJoin('fc', 'sole_proprietor', 'osp', 'fc.organization_id->>"$.value" = osp.id');
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param string|null  $term
     */
    private function appendAndWhereLikeTerm(QueryBuilder $queryBuilder, ?string $term): void
    {
        if ($this->isTermNotEmpty($term)) {
            $queryBuilder
                ->andWhere(
                    $queryBuilder->expr()->or(
                        $queryBuilder->expr()->like('ojp.name', ':term'),
                        $queryBuilder->expr()->like('ojp.inn', ':term'),
                        $queryBuilder->expr()->like('ojp.legal_address', ':term'),
                        $queryBuilder->expr()->like('ojp.postal_address', ':term'),
                        $queryBuilder->expr()->like('ojp.phone', ':term'),
                        $queryBuilder->expr()->like('osp.name', ':term'),
                        $queryBuilder->expr()->like('osp.inn', ':term'),
                        $queryBuilder->expr()->like('osp.registration_address', ':term'),
                        $queryBuilder->expr()->like('osp.actual_location_address', ':term'),
                        $queryBuilder->expr()->like('osp.phone', ':term'),
                        $queryBuilder->expr()->like('fc.note', ':term'),
                    )
                );
        }
    }

    /**
     * @param array       $funeralCompanyListData
     * @param int         $page
     * @param int         $pageSize
     * @param string|null $term
     * @param int         $totalCount
     * @param int         $totalPages
     *
     * @return FuneralCompanyList
     */
    private function hydrateFuneralCompanyList(
        array   $funeralCompanyListData,
        int     $page,
        int     $pageSize,
        ?string $term,
        int     $totalCount,
        int     $totalPages,
    ): FuneralCompanyList {
        $funeralCompanyListItems = [];
        foreach ($funeralCompanyListData as $funeralCompanyListItemData) {
            $funeralCompanyListItems[] = new FuneralCompanyListItem(
                $funeralCompanyListItemData['id'],
                $funeralCompanyListItemData['organizationType'],
                $funeralCompanyListItemData['organizationJuristicPersonName'],
                $funeralCompanyListItemData['organizationJuristicPersonInn'],
                $funeralCompanyListItemData['organizationJuristicPersonLegalAddress'],
                $funeralCompanyListItemData['organizationJuristicPersonPostalAddress'],
                $funeralCompanyListItemData['organizationJuristicPersonPhone'],
                $funeralCompanyListItemData['organizationSoleProprietorName'],
                $funeralCompanyListItemData['organizationSoleProprietorInn'],
                $funeralCompanyListItemData['organizationSoleProprietorRegistrationAddress'],
                $funeralCompanyListItemData['organizationSoleProprietorActualLocationAddress'],
                $funeralCompanyListItemData['organizationSoleProprietorPhone'],
                $funeralCompanyListItemData['note'],
            );
        }

        return new FuneralCompanyList($funeralCompanyListItems, $page, $pageSize, $term, $totalCount, $totalPages);
    }
}
