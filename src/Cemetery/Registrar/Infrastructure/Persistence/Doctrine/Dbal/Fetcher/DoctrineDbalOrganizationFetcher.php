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
    public function findViewById(string $id): mixed
    {
        // Always returns null because there is no such entity as an organization.
        return null;
    }

    public function doesExistById(string $id): bool
    {
        // Always returns false because there is no such entity as an organization.
        return false;
    }

    public function findAll(?int $page = null, ?string $term = null, int $pageSize = self::DEFAULT_PAGE_SIZE): OrganizationList
    {
        $sql  = $this->buildFindAllSql($page, $term, $pageSize);
        $stmt = $this->connection->prepare($sql);
        $this->bindTermValue($stmt, $term);
        $result = $stmt->executeQuery();

        $listData   = $result->fetchAllAssociative();
        $totalCount = $page !== null ? $this->doCountTotal($term) : \count($listData);
        $totalPages = $page !== null ? (int) \ceil($totalCount / $pageSize) : null;

        return $this->hydrateList($listData, $page, $pageSize, $term, $totalCount, $totalPages);
    }

    public function countTotal(): int
    {
        return $this->doCountTotal(null);
    }

    private function doCountTotal(?string $term): int
    {
        $sql  = $this->buildCountTotalSql($term);
        $stmt = $this->connection->prepare($sql);
        $this->bindTermValue($stmt, $term);
        $result = $stmt->executeQuery();

        return $result->fetchFirstColumn()[0];
    }

    private function buildCountTotalSql(?string $term): string
    {
        $sql = \sprintf('SELECT COUNT(*) FROM (%s) AS unionTable WHERE removedAt IS NULL', $this->buildUnionSql());

        return $this->appendAndWhereLikeTermSql($sql, $term);
    }

    private function buildFindAllSql(?int $page, ?string $term, int $pageSize): string
    {
        $sql = \sprintf(<<<FIND_ALL_SQL
SELECT id,
       typeShortcut,
       typeLabel,
       name,
       inn,
       kpp,
       innKppComposed,
       ogrn,
       okpo,
       okved,
       address1,
       address2,
       addressComposed,
       bankDetails,
       bankDetailsComposed,
       phone,
       phoneAdditional,
       fax,
       phoneFaxComposed,
       generalDirector,
       email,
       website,
       emailWebsiteComposed
FROM (%s) AS unionTable
WHERE removedAt IS NULL
FIND_ALL_SQL
            ,
            $this->buildUnionSql()
        );

        $sql = $this->appendAndWhereLikeTermSql($sql, $term);
        $sql = $this->appendOrderByName($sql);

        return $this->appendLimitOffset($sql, $page, $pageSize);
    }

    private function buildUnionSql(): string
    {
        return \sprintf(<<<UNION_SQL
SELECT id                                                 AS id,
       '%s'                                               AS typeShortcut,
       '%s'                                               AS typeLabel,
       name                                               AS name,
       inn                                                AS inn,
       kpp                                                AS kpp,
       IF(
           inn IS NOT NULL OR kpp IS NOT NULL,
           CONCAT_WS('/', IFNULL(inn, '-'), IFNULL(kpp, '-')),
           NULL
       )                                                  AS innKppComposed,
       ogrn                                               AS ogrn,
       okpo                                               AS okpo,
       okved                                              AS okved,
       legal_address                                      AS address1,
       postal_address                                     AS address2,
       IF(
           legal_address IS NOT NULL OR postal_address IS NOT NULL,
           CONCAT_WS(', ', legal_address, postal_address),
           NULL
       )                                                  AS addressComposed,
       bank_details                                       AS bankDetails,
       IF(
           bank_details IS NOT NULL,
           CONCAT(
               JSON_VALUE(bank_details, '$.bankName'),
               ', р/счёт ',
               JSON_VALUE(bank_details, '$.currentAccount'),
               IF(
                   JSON_VALUE(bank_details, '$.correspondentAccount') IS NOT NULL,
                   CONCAT(', к/счёт ', JSON_VALUE(bank_details, '$.correspondentAccount')),
                   ''
               ),
               ', БИК ',
               JSON_VALUE(bank_details, '$.bik')
           ),
           NULL
       )                                                  AS bankDetailsComposed,
       phone                                              AS phone,
       phone_additional                                   AS phoneAdditional,
       fax                                                AS fax,
       IF(
           phone IS NOT NULL OR phone_additional IS NOT NULL OR fax IS NOT NULL,
           CONCAT_WS(
               ', ',
               phone,
               phone_additional,
               IF(fax IS NOT NULL, CONCAT(fax, ' (факс)'), NULL)
           ),
           NULL
       )                                                  AS phoneFaxComposed,
       general_director                                   AS generalDirector,
       email                                              AS email,
       website                                            AS website,
       IF(
           email IS NOT NULL OR website IS NOT NULL,
           CONCAT_WS(', ', email, website),
           NULL
       )                                                  AS emailWebsiteComposed,
       removed_at                                         AS removedAt
FROM juristic_person
UNION
SELECT id                                                 AS id,
       '%s'                                               AS typeShortcut,
       '%s'                                               AS typeLabel,
       name                                               AS name,
       inn                                                AS inn,
       NULL                                               AS kpp,
       IF(inn IS NOT NULL, CONCAT(inn, '/-'), NULL)       AS innKppComposed,
       ogrnip                                             AS ogrn,
       okpo                                               AS okpo,
       okved                                              AS okved,
       registration_address                               AS address1,
       actual_location_address                            AS address2,
       IF(
           registration_address IS NOT NULL OR actual_location_address IS NOT NULL,
           CONCAT_WS(
               ', ',
               registration_address,
               actual_location_address
           ),
           NULL
       )                                                  AS addressComposed,
       bank_details                                       AS bankDetails,
       IF(
           bank_details IS NOT NULL,
           CONCAT(
               JSON_VALUE(bank_details, '$.bankName'),
               ', р/счёт ',
               JSON_VALUE(bank_details, '$.currentAccount'),
               IF(
                   JSON_VALUE(bank_details, '$.correspondentAccount') IS NOT NULL,
                   CONCAT(', к/счёт ', JSON_VALUE(bank_details, '$.correspondentAccount')),
                   ''
               ),
               ', БИК ',
               JSON_VALUE(bank_details, '$.bik')
           ),
           NULL
       )                                                  AS bankDetailsComposed,
       phone                                              AS phone,
       phone_additional                                   AS phoneAdditional,
       fax                                                AS fax,
       IF(
           phone IS NOT NULL OR phone_additional IS NOT NULL OR fax IS NOT NULL,
           CONCAT_WS(
               ', ',
               phone,
               phone_additional,
               IF(fax IS NOT NULL, CONCAT(fax, ' (факс)'), NULL)
           ),
           NULL
       )                                                  AS phoneFaxComposed,
       NULL                                               AS generalDirector,
       email                                              AS email,
       website                                            AS website,
       IF(
           email IS NOT NULL OR website IS NOT NULL,
           CONCAT_WS(', ', email, website),
           NULL
       )                                                  AS emailWebsiteComposed,
       removed_at                                         AS removedAt
FROM sole_proprietor
UNION_SQL
            ,
            JuristicPerson::CLASS_SHORTCUT,
            JuristicPerson::CLASS_LABEL,
            SoleProprietor::CLASS_SHORTCUT,
            SoleProprietor::CLASS_LABEL,
        );
    }

    private function appendAndWhereLikeTermSql(string $sql, ?string $term): string
    {
        if ($this->isTermNotEmpty($term)) {
            $sql .= <<<LIKE_TERM_SQL
  AND (typeLabel                               LIKE :term
    OR name                                    LIKE :term
    OR inn                                     LIKE :term
    OR kpp                                     LIKE :term
    OR ogrn                                    LIKE :term
    OR okpo                                    LIKE :term
    OR okved                                   LIKE :term
    OR address1                                LIKE :term
    OR address2                                LIKE :term
    OR LOWER(bankDetails->>"$.bankName")       LIKE :term
    OR bankDetails->>"$.bik"                   LIKE :term
    OR bankDetails->>"$.correspondentAccount"  LIKE :term
    OR bankDetails->>"$.currentAccount"        LIKE :term
    OR phone                                   LIKE :term
    OR phoneAdditional                         LIKE :term
    OR fax                                     LIKE :term
    OR generalDirector                         LIKE :term
    OR email                                   LIKE :term
    OR website                                 LIKE :term)
LIKE_TERM_SQL;
        }

        return $sql;
    }

    private function appendOrderByName(string $sql): string
    {
        return \sprintf('%s ORDER BY name', $sql);
    }

    private function appendLimitOffset(string $sql, ?int $page, int $pageSize): string
    {
        if ($page !== null) {
            $sql .= \sprintf(' LIMIT %d OFFSET %d', $pageSize, ($page - 1) * $pageSize);
        }

        return $sql;
    }

    private function hydrateList(
        array   $listData,
        ?int    $page,
        int     $pageSize,
        ?string $term,
        int     $totalCount,
        ?int    $totalPages,
    ): OrganizationList {
        $listItems = [];
        foreach ($listData as $listItemData) {
            $listItems[] = new OrganizationListItem(
                $listItemData['id'],
                $listItemData['typeShortcut'],
                $listItemData['typeLabel'],
                $listItemData['name'],
                $listItemData['innKppComposed'],
                $listItemData['ogrn'],
                $listItemData['okpo'],
                $listItemData['okved'],
                $listItemData['addressComposed'],
                $listItemData['bankDetailsComposed'],
                $listItemData['phoneFaxComposed'],
                $listItemData['generalDirector'],
                $listItemData['emailWebsiteComposed'],
            );
        }

        return new OrganizationList($listItems, $page, $pageSize, $term, $totalCount, $totalPages);
    }
}
