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
    public function paginate(int $page, ?string $term = null, int $pageSize = self::DEFAULT_PAGE_SIZE): OrganizationList
    {
        $sql  = $this->buildPaginateSql($page, $term, $pageSize);
        $stmt = $this->connection->prepare($sql);
        $this->bindTermValue($stmt, $term);
        $result = $stmt->executeQuery();

        $paginatedListData = $result->fetchAllAssociative();
        $totalCount        = $this->doCountTotal($term) ;
        $totalPages        = (int) \ceil($totalCount / $pageSize);

        return $this->hydratePaginatedList($paginatedListData, $page, $pageSize, $term, $totalCount, $totalPages);
    }

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

    private function buildPaginateSql(int $page, ?string $term, int $pageSize): string
    {
        $sql = \sprintf(<<<PAGINATE_SQL
SELECT id,
       typeShortcut,
       typeLabel,
       name,
       inn,
       kpp,
       ogrn,
       okpo,
       okved,
       address1,
       address2,
       bankDetailsBankName,
       bankDetailsBik,
       bankDetailsCorrespondentAccount,
       bankDetailsCurrentAccount,
       phone,
       phoneAdditional,
       fax,
       generalDirector,
       email,
       website
FROM (%s) AS unionTable
WHERE removedAt IS NULL
PAGINATE_SQL
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
       ogrn                                               AS ogrn,
       okpo                                               AS okpo,
       okved                                              AS okved,
       legal_address                                      AS address1,
       postal_address                                     AS address2,
       bank_details->>"$.bankName"                        AS bankDetailsBankName,
       bank_details->>"$.bik"                             AS bankDetailsBik,
       IF(
          bank_details->>"$.correspondentAccount" <> 'null',
          bank_details->>"$.correspondentAccount",
          NULL
       )                                                  AS bankDetailsCorrespondentAccount,
       bank_details->>"$.currentAccount"                  AS bankDetailsCurrentAccount,
       phone                                              AS phone,
       phone_additional                                   AS phoneAdditional,
       fax                                                AS fax,
       general_director                                   AS generalDirector,
       email                                              AS email,
       website                                            AS website,
       removed_at                                         AS removedAt
FROM juristic_person
UNION
SELECT id                                                 AS id,
       '%s'                                               AS typeShortcut,
       '%s'                                               AS typeLabel,
       name                                               AS name,
       inn                                                AS inn,
       NULL                                               AS kpp,
       ogrnip                                             AS ogrn,
       okpo                                               AS okpo,
       okved                                              AS okved,
       registration_address                               AS address1,
       actual_location_address                            AS address2,
       bank_details->>"$.bankName"                        AS bankDetailsBankName,
       bank_details->>"$.bik"                             AS bankDetailsBik,
       IF(
          bank_details->>"$.correspondentAccount" <> 'null',
          bank_details->>"$.correspondentAccount",
          NULL
       )                                                  AS bankDetailsCorrespondentAccount,
       bank_details->>"$.currentAccount"                  AS bankDetailsCurrentAccount,
       phone                                              AS phone,
       phone_additional                                   AS phoneAdditional,
       fax                                                AS fax,
       NULL                                               AS generalDirector,
       email                                              AS email,
       website                                            AS website,
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
    OR LOWER(bankDetailsBankName)              LIKE :term
    OR bankDetailsBik                          LIKE :term
    OR bankDetailsCorrespondentAccount         LIKE :term
    OR bankDetailsCurrentAccount               LIKE :term
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
        return $sql . \sprintf(' LIMIT %d OFFSET %d', $pageSize, ($page - 1) * $pageSize);
    }

    private function hydratePaginatedList(
        array   $paginatedListData,
        int     $page,
        int     $pageSize,
        ?string $term,
        int     $totalCount,
        int     $totalPages,
    ): OrganizationList {
        $items = [];
        foreach ($paginatedListData as $paginatedListItemData) {
            $items[] = new OrganizationListItem(
                $paginatedListItemData['id'],
                $paginatedListItemData['typeShortcut'],
                $paginatedListItemData['typeLabel'],
                $paginatedListItemData['name'],
                $paginatedListItemData['inn'],
                $paginatedListItemData['kpp'],
                $paginatedListItemData['ogrn'],
                $paginatedListItemData['okpo'],
                $paginatedListItemData['okved'],
                $paginatedListItemData['address1'],
                $paginatedListItemData['address2'],
                $paginatedListItemData['bankDetailsBankName'],
                $paginatedListItemData['bankDetailsBik'],
                $paginatedListItemData['bankDetailsCorrespondentAccount'],
                $paginatedListItemData['bankDetailsCurrentAccount'],
                $paginatedListItemData['phone'],
                $paginatedListItemData['phoneAdditional'],
                $paginatedListItemData['fax'],
                $paginatedListItemData['generalDirector'],
                $paginatedListItemData['email'],
                $paginatedListItemData['website'],
            );
        }

        return new OrganizationList($items, $page, $pageSize, $term, $totalCount, $totalPages);
    }
}
