<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Persistence\Pdo\MySql\Fetcher;

use Cemetery\Registrar\Domain\Model\Organization\JuristicPerson\JuristicPerson;
use Cemetery\Registrar\Domain\Model\Organization\SoleProprietor\SoleProprietor;
use Cemetery\Registrar\Domain\View\Organization\OrganizationFetcher;
use Cemetery\Registrar\Domain\View\Organization\OrganizationList;
use Cemetery\Registrar\Domain\View\Organization\OrganizationListItem;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Fetcher\DoctrineDbalFetcher;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class PdoMySqlOrganizationFetcher implements OrganizationFetcher
{
    /**
     * @param Connection $connection
     */
    public function __construct(
        private readonly Connection $connection,
    ) {}

    // TODO implement getViewById() method

    /**
     * {@inheritdoc}
     */
    public function findAll(int $page, ?string $term = null, int $pageSize = self::DEFAULT_PAGE_SIZE): OrganizationList
    {
        return new OrganizationList([], 1, 10, null, 1, 1);
//        $queryBuilder = $this->connection->createQueryBuilder()
//            ->select(
//                'fc.id                         AS id',
//                'fc.organization_id->>"$.type" AS organizationType',
//                'ojp.name                      AS organizationJuristicPersonName',
//                'ojp.inn                       AS organizationJuristicPersonInn',
//                'ojp.legal_address             AS organizationJuristicPersonLegalAddress',
//                'ojp.postal_address            AS organizationJuristicPersonPostalAddress',
//                'ojp.phone                     AS organizationJuristicPersonPhone',
//                'osp.name                      AS organizationSoleProprietorName',
//                'osp.inn                       AS organizationSoleProprietorInn',
//                'osp.registration_address      AS organizationSoleProprietorRegistrationAddress',
//                'osp.actual_location_address   AS organizationSoleProprietorActualLocationAddress',
//                'osp.phone                     AS organizationSoleProprietorPhone',
//                'fc.note                       AS note'
//            )
//            ->from('funeral_company', 'fc')
//            ->andWhere('fc.removed_at IS NULL')
//            ->orderBy('ojp.name')
//            ->addOrderBy('osp.name')
//            ->setFirstResult(($page - 1) * $pageSize)
//            ->setMaxResults($pageSize);
//        $this->addJoinsToQueryBuilder($queryBuilder);
//        $this->addWheresToQueryBuilder($queryBuilder, $term);
//
//        $organizationListData = $queryBuilder
//            ->executeQuery()
//            ->fetchAllAssociative();
//        $totalCount = $this->doGetTotalCount($term);
//        $totalPages = (int) \ceil($totalCount / $pageSize);
//
//        return $this->hydrateOrganizationList($organizationListData, $page, $pageSize, $term, $totalCount, $totalPages);
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
        $sql = \sprintf(<<<'SQL_START'
SELECT COUNT(*)
FROM (SELECT id,
             '%s' AS type,
             name,
             inn,
             phone,
             removed_at
      FROM juristic_person
      UNION
      SELECT id,
             '%s' AS type,
             name,
             inn,
             phone,
             removed_at
      FROM sole_proprietor) AS union_table
WHERE removed_at IS NULL
SQL_START
            ,
            JuristicPerson::CLASS_SHORTCUT,
            SoleProprietor::CLASS_SHORTCUT
        );

        $term = 'SOLE';

        $hasTerm = $term !== null && $term !== '';
        if ($hasTerm) {
            $sql = $this->addWheresToSql($sql, $term);
        }
        $stmt = $this->connection->prepare($sql);
        if ($hasTerm) {
            $stmt->bindValue('term', "%$term%");
        }
        $result = $stmt->executeQuery();

        return $result->fetchFirstColumn()[0];
    }

    /**
     * @param string       $sql
     * @param string|null  $term
     *
     * @return string
     */
    private function addWheresToSql(string $sql, ?string $term): string
    {


        $sql .= <<<'SQL_WHERE'
  AND (name                LIKE :term
    OR inn                 LIKE :term
    OR phone               LIKE :term)
SQL_WHERE;


//    OR juristic_person.legal_address           LIKE %:term%
//    OR juristic_person.postal_address          LIKE %:term%
//    OR juristic_person.phone                   LIKE %:term%
//    OR sole_proprietor.name                    LIKE %:term%
//    OR sole_proprietor.inn                     LIKE %:term%
//    OR sole_proprietor.registration_address    LIKE %:term%
//    OR sole_proprietor.actual_location_address LIKE %:term%

//
//            $queryBuilder
//            ->andWhere(
//                $queryBuilder->expr()->or(
//                    $queryBuilder->expr()->like('ojp.name', ':term'),
//                    $queryBuilder->expr()->like('ojp.inn', ':term'),
//                    $queryBuilder->expr()->like('ojp.legal_address', ':term'),
//                    $queryBuilder->expr()->like('ojp.postal_address', ':term'),
//                    $queryBuilder->expr()->like('ojp.phone', ':term'),
//                    $queryBuilder->expr()->like('osp.name', ':term'),
//                    $queryBuilder->expr()->like('osp.inn', ':term'),
//                    $queryBuilder->expr()->like('osp.registration_address', ':term'),
//                    $queryBuilder->expr()->like('osp.actual_location_address', ':term'),
//                    $queryBuilder->expr()->like('osp.phone', ':term'),
//                )
//            )
//            ->setParameter('term', "%$term%");

        return $sql;
    }

    /**
     * @param array       $organizationListData
     * @param int         $page
     * @param int         $pageSize
     * @param string|null $term
     * @param int         $totalCount
     * @param int         $totalPages
     *
     * @return OrganizationList
     */
    private function hydrateOrganizationList(
        array   $organizationListData,
        int     $page,
        int     $pageSize,
        ?string $term,
        int     $totalCount,
        int     $totalPages,
    ): OrganizationList {
        $organizationListItems = [];
        foreach ($organizationListData as $organizationListItemData) {
            $organizationListItems[] = new OrganizationListItem(
                $organizationListItemData['id'],
                $organizationListItemData['organizationType'],
                $organizationListItemData['organizationJuristicPersonName'],
                $organizationListItemData['organizationJuristicPersonInn'],
                $organizationListItemData['organizationJuristicPersonLegalAddress'],
                $organizationListItemData['organizationJuristicPersonPostalAddress'],
                $organizationListItemData['organizationJuristicPersonPhone'],
                $organizationListItemData['organizationSoleProprietorName'],
                $organizationListItemData['organizationSoleProprietorInn'],
                $organizationListItemData['organizationSoleProprietorRegistrationAddress'],
                $organizationListItemData['organizationSoleProprietorActualLocationAddress'],
                $organizationListItemData['organizationSoleProprietorPhone'],
                $organizationListItemData['note'],
            );
        }

        return new OrganizationList($organizationListItems, $page, $pageSize, $term, $totalCount, $totalPages);
    }
}
