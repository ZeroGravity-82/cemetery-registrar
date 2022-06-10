<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Application\FuneralCompany\Doctrine\Dbal;

use Cemetery\Registrar\Application\ListFuneralCompanies\FuneralCompanyFetcher;
use Cemetery\Registrar\Application\ListFuneralCompanies\FuneralCompanyViewList;
use Cemetery\Registrar\Application\ListFuneralCompanies\FuneralCompanyViewListItem;
use Cemetery\Registrar\Infrastructure\Application\Fetcher;
use Doctrine\DBAL\Query\QueryBuilder;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class DoctrineDbalFuneralCompanyFetcher extends Fetcher implements FuneralCompanyFetcher
{
    // TODO implement getFormViewById() method

    /**
     * {@inheritdoc}
     */
    public function findAll(int $page, ?string $term = null, int $pageSize = self::DEFAULT_PAGE_SIZE): FuneralCompanyViewList
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
        $this->addJoinsToQueryBuilder($queryBuilder);
        $this->addWheresToQueryBuilder($queryBuilder, $term);

        $funeralCompanyViewListData = $queryBuilder
            ->executeQuery()
            ->fetchAllAssociative();
        $totalCount = $this->doGetTotalCount($term);
        $totalPages = (int) \ceil($totalCount / $pageSize);

        return $this->hydrateFuneralCompanyViewList($funeralCompanyViewListData, $page, $pageSize, $term, $totalCount, $totalPages);
    }

    /**
     * {@inheritdoc}
     */
    public function getTotalCount(): int
    {
        return $this->doGetTotalCount(null);
    }

    /**
     * @param string|null $term
     *
     * @return int
     */
    private function doGetTotalCount(?string $term): int
    {
        $queryBuilder = $this->connection->createQueryBuilder()
            ->select('COUNT(fc.id)')
            ->from('funeral_company', 'fc')
            ->andWhere('fc.removed_at IS NULL');
        $this->addJoinsToQueryBuilder($queryBuilder);
        $this->addWheresToQueryBuilder($queryBuilder, $term);

        return $queryBuilder
            ->executeQuery()
            ->fetchFirstColumn()[0];
    }

    /**
     * @param QueryBuilder $queryBuilder
     */
    private function addJoinsToQueryBuilder(QueryBuilder $queryBuilder): void
    {
        $queryBuilder
            ->leftJoin('fc', 'juristic_person', 'ojp', 'fc.organization_id->>"$.value" = ojp.id')
            ->leftJoin('fc', 'sole_proprietor', 'osp', 'fc.organization_id->>"$.value" = osp.id');
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param string|null  $term
     */
    private function addWheresToQueryBuilder(QueryBuilder $queryBuilder, ?string $term): void
    {
        if ($term === null || $term === '') {
            return;
        }
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
            )
            ->setParameter('term', "%$term%");
    }

    /**
     * @param array       $funeralCompanyViewListData
     * @param int         $page
     * @param int         $pageSize
     * @param string|null $term
     * @param int         $totalCount
     * @param int         $totalPages
     *
     * @return FuneralCompanyViewList
     */
    private function hydrateFuneralCompanyViewList(
        array   $funeralCompanyViewListData,
        int     $page,
        int     $pageSize,
        ?string $term,
        int     $totalCount,
        int     $totalPages,
    ): FuneralCompanyViewList {
        $funeralCompanyViewListItems = [];
        foreach ($funeralCompanyViewListData as $funeralCompanyViewListItemData) {
            $funeralCompanyViewListItems[] = new FuneralCompanyViewListItem(
                $funeralCompanyViewListItemData['id'],
                $funeralCompanyViewListItemData['organizationType'],
                $funeralCompanyViewListItemData['organizationJuristicPersonName'],
                $funeralCompanyViewListItemData['organizationJuristicPersonInn'],
                $funeralCompanyViewListItemData['organizationJuristicPersonLegalAddress'],
                $funeralCompanyViewListItemData['organizationJuristicPersonPostalAddress'],
                $funeralCompanyViewListItemData['organizationJuristicPersonPhone'],
                $funeralCompanyViewListItemData['organizationSoleProprietorName'],
                $funeralCompanyViewListItemData['organizationSoleProprietorInn'],
                $funeralCompanyViewListItemData['organizationSoleProprietorRegistrationAddress'],
                $funeralCompanyViewListItemData['organizationSoleProprietorActualLocationAddress'],
                $funeralCompanyViewListItemData['organizationSoleProprietorPhone'],
                $funeralCompanyViewListItemData['note'],
            );
        }

        return new FuneralCompanyViewList($funeralCompanyViewListItems, $page, $pageSize, $term, $totalCount, $totalPages);
    }
}
