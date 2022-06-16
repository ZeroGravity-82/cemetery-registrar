<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Fetcher;

use Cemetery\Registrar\Domain\Model\Organization\JuristicPerson\JuristicPerson;
use Cemetery\Registrar\Domain\Model\Organization\SoleProprietor\SoleProprietor;
use Cemetery\Registrar\Domain\View\Organization\OrganizationFetcher;
use Cemetery\Registrar\Domain\View\Organization\OrganizationList;
use Cemetery\Registrar\Domain\View\Organization\OrganizationListItem;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class DoctrineDbalOrganizationFetcher extends DoctrineDbalFetcher implements OrganizationFetcher
{
    // TODO implement getViewById() method

    /**
     * {@inheritdoc}
     */
    public function findAll(int $page, ?string $term = null, int $pageSize = self::DEFAULT_PAGE_SIZE): OrganizationList
    {




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
        $sql  = $this->buildCountTotalSql($term);
        $stmt = $this->connection->prepare($sql);
        $this->bindTermValue($stmt, $term);
        $result = $stmt->executeQuery();

        return $result->fetchFirstColumn()[0];
    }

    /**
     * @param string|null $term
     *
     * @return string
     */
    private function buildCountTotalSql(?string $term): string
    {
        $sql = \sprintf('SELECT COUNT(*) FROM (%s) AS union_table WHERE removed_at IS NULL', $this->buildUnionSql());

        return $this->appendAndWhereLikeTermSql($sql, $term);
    }

    /**
     * @return string
     */
    private function buildUnionSql(): string
    {
        return \sprintf(<<<UNION_SQL
SELECT id                                                 AS id,
       '%s'                                               AS type_shortcut,
       '%s'                                               AS type_label,
       name                                               AS juristic_person_name,
       inn                                                AS juristic_person_inn,
       kpp                                                AS juristic_person_kpp,
       ogrn                                               AS juristic_person_ogrn,
       okpo                                               AS juristic_person_okpo,
       okved                                              AS juristic_person_okved,
       legal_address                                      AS juristic_person_legal_address,
       postal_address                                     AS juristic_person_postal_address,
       JSON_VALUE(bank_details, '$.bankName')             AS juristic_person_bank_details_bank_name,
       JSON_VALUE(bank_details, '$.bik')                  AS juristic_person_bank_details_bik,
       JSON_VALUE(bank_details, '$.correspondentAccount') AS juristic_person_bank_details_correspondent_account,
       JSON_VALUE(bank_details, '$.currentAccount')       AS juristic_person_bank_details_current_account,
       phone                                              AS juristic_person_phone,
       phone_additional                                   AS juristic_person_phone_additional,
       fax                                                AS juristic_person_fax,
       general_director                                   AS juristic_person_general_director,
       email                                              AS juristic_person_email,
       website                                            AS juristic_person_website,
       NULL                                               AS sole_proprietor_name,
       NULL                                               AS sole_proprietor_inn,
       NULL                                               AS sole_proprietor_ogrnip,
       NULL                                               AS sole_proprietor_okpo,
       NULL                                               AS sole_proprietor_okved,
       NULL                                               AS sole_proprietor_registration_address,
       NULL                                               AS sole_proprietor_actual_location_address,
       NULL                                               AS sole_proprietor_bank_details_bank_name,
       NULL                                               AS sole_proprietor_bank_details_bik,
       NULL                                               AS sole_proprietor_bank_details_correspondent_account,
       NULL                                               AS sole_proprietor_bank_details_current_account,
       NULL                                               AS sole_proprietor_phone,
       NULL                                               AS sole_proprietor_phone_additional,
       NULL                                               AS sole_proprietor_fax,
       NULL                                               AS sole_proprietor_email,
       NULL                                               AS sole_proprietor_website,
       removed_at                                         AS removed_at
FROM juristic_person
UNION
SELECT id                                                 AS id,
       '%s'                                               AS type_shortcut,
       '%s'                                               AS type_label,
       NULL                                               AS juristic_person_name,
       NULL                                               AS juristic_person_inn,
       NULL                                               AS juristic_person_kpp,
       NULL                                               AS juristic_person_ogrn,
       NULL                                               AS juristic_person_okpo,
       NULL                                               AS juristic_person_okved,
       NULL                                               AS juristic_person_legal_address,
       NULL                                               AS juristic_person_postal_address,
       NULL                                               AS juristic_person_bank_details_bank_name,
       NULL                                               AS juristic_person_bank_details_bik,
       NULL                                               AS juristic_person_bank_details_correspondent_account,
       NULL                                               AS juristic_person_bank_details_current_account,
       NULL                                               AS juristic_person_phone,
       NULL                                               AS juristic_person_phone_additional,
       NULL                                               AS juristic_person_fax,
       NULL                                               AS juristic_person_general_director,
       NULL                                               AS juristic_person_email,
       NULL                                               AS juristic_person_website,
       name                                               AS sole_proprietor_name,
       inn                                                AS sole_proprietor_inn,
       ogrnip                                             AS sole_proprietor_ogrnip,
       okpo                                               AS sole_proprietor_okpo,
       okved                                              AS sole_proprietor_okved,
       registration_address                               AS sole_proprietor_registration_address,
       actual_location_address                            AS sole_proprietor_actual_location_address,
       JSON_VALUE(bank_details, '$.bankName')             AS sole_proprietor_bank_details_bank_name,
       JSON_VALUE(bank_details, '$.bik')                  AS sole_proprietor_bank_details_bik,
       JSON_VALUE(bank_details, '$.correspondentAccount') AS sole_proprietor_bank_details_correspondent_account,
       JSON_VALUE(bank_details, '$.currentAccount')       AS sole_proprietor_bank_details_current_account,
       phone                                              AS sole_proprietor_phone,
       phone_additional                                   AS sole_proprietor_phone_additional,
       fax                                                AS sole_proprietor_fax,
       email                                              AS sole_proprietor_email,
       website                                            AS sole_proprietor_website,
       removed_at                                         AS removed_at
FROM sole_proprietor
UNION_SQL
            ,
            JuristicPerson::CLASS_SHORTCUT,
            JuristicPerson::CLASS_LABEL,
            SoleProprietor::CLASS_SHORTCUT,
            SoleProprietor::CLASS_LABEL,
        );
    }

    /**
     * @param string      $sql
     * @param string|null $term
     *
     * @return string
     */
    private function appendAndWhereLikeTermSql(string $sql, ?string $term): string
    {
        if ($this->isTermNotEmpty($term)) {
            $sql .= <<<LIKE_TERM_SQL
  AND (type_label                                         LIKE :term
    OR juristic_person_name                               LIKE :term
    OR juristic_person_inn                                LIKE :term
    OR juristic_person_kpp                                LIKE :term
    OR juristic_person_ogrn                               LIKE :term
    OR juristic_person_okpo                               LIKE :term
    OR juristic_person_okved                              LIKE :term
    OR juristic_person_legal_address                      LIKE :term
    OR juristic_person_postal_address                     LIKE :term
    OR juristic_person_bank_details_bank_name             LIKE :term
    OR juristic_person_bank_details_bik                   LIKE :term
    OR juristic_person_bank_details_correspondent_account LIKE :term
    OR juristic_person_bank_details_current_account       LIKE :term
    OR juristic_person_phone                              LIKE :term
    OR juristic_person_phone_additional                   LIKE :term
    OR juristic_person_fax                                LIKE :term
    OR juristic_person_general_director                   LIKE :term
    OR juristic_person_email                              LIKE :term
    OR juristic_person_website                            LIKE :term
    OR sole_proprietor_name                               LIKE :term
    OR sole_proprietor_inn                                LIKE :term
    OR sole_proprietor_ogrnip                             LIKE :term
    OR sole_proprietor_okpo                               LIKE :term
    OR sole_proprietor_okved                              LIKE :term
    OR sole_proprietor_registration_address               LIKE :term
    OR sole_proprietor_actual_location_address            LIKE :term
    OR sole_proprietor_bank_details_bank_name             LIKE :term
    OR sole_proprietor_bank_details_bik                   LIKE :term
    OR sole_proprietor_bank_details_correspondent_account LIKE :term
    OR sole_proprietor_bank_details_current_account       LIKE :term
    OR sole_proprietor_phone                              LIKE :term
    OR sole_proprietor_phone_additional                   LIKE :term
    OR sole_proprietor_fax                                LIKE :term
    OR sole_proprietor_email                              LIKE :term
    OR sole_proprietor_website                            LIKE :term)
LIKE_TERM_SQL;
        }

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
