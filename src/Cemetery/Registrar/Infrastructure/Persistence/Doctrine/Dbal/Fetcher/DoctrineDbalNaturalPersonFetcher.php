<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Fetcher;

use Cemetery\Registrar\Domain\View\NaturalPerson\NaturalPersonFetcher;
use Cemetery\Registrar\Domain\View\NaturalPerson\NaturalPersonPaginatedList;
use Cemetery\Registrar\Domain\View\NaturalPerson\NaturalPersonPaginatedListItem;
use Cemetery\Registrar\Domain\View\NaturalPerson\NaturalPersonSimpleList;
use Cemetery\Registrar\Domain\View\NaturalPerson\NaturalPersonSimpleListItem;
use Cemetery\Registrar\Domain\View\NaturalPerson\NaturalPersonView;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class DoctrineDbalNaturalPersonFetcher extends DoctrineDbalFetcher implements NaturalPersonFetcher
{
    protected string $tableName = 'natural_person';

    public function paginate(int $page, ?string $term = null, int $pageSize = self::DEFAULT_PAGE_SIZE): NaturalPersonPaginatedList
    {
        $sql = $this->buildSelectSql();
        $sql = $this->appendJoinsSql($sql);
        $sql = $this->appendWhereRemovedAtIsNullSql($sql);
        $sql = $this->appendAndWhereLikeTermSql($sql, $term);
        $sql = $this->appendOrderByFullNameThenByBornAtThenByDiedAtSql($sql);
        $sql = $this->appendLimitOffset($sql, $page, $pageSize);

        $stmt = $this->connection->prepare($sql);
        $this->bindTermValue($stmt, $term);
        $result = $stmt->executeQuery();

        $paginatedListData = $result->fetchAllAssociative();
        $totalCount        = $this->doCountTotal($term);
        $totalPages        = (int) \ceil($totalCount / $pageSize);

        return $this->hydratePaginatedList($paginatedListData, $page, $pageSize, $term, $totalCount, $totalPages);
    }

    public function findViewById(string $id): ?NaturalPersonView
    {
        $sql = $this->buildSelectSql();
        $sql = $this->appendJoinsSql($sql);
        $sql = $this->appendWhereRemovedAtIsNullSql($sql);
        $sql = $this->appendWhereIdIsEqualSql($sql);

        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue('id', $id);
        $result   = $stmt->executeQuery();
        $viewData = $result->fetchAllAssociative()[0] ?? false;

        return $viewData ? $this->hydrateView($viewData) : null;
    }

    public function countTotal(): int
    {
        return $this->doCountTotal(null);
    }

    public function findAll(?string $term = null): NaturalPersonSimpleList
    {
        $sql = $this->buildSelectSimpleSql();
        $sql = $this->appendWhereRemovedAtIsNullSql($sql);
        $sql = $this->appendAndWhereFullNameStartsWithTermSql($sql, $term);
        $sql = $this->appendOrderByFullNameThenByBornAtThenByDiedAtSql($sql);

        $stmt = $this->connection->prepare($sql);
        $this->bindTermValue($stmt, $term, true);
        $result      = $stmt->executeQuery();
        $listAllData = $result->fetchAllAssociative();

        return $this->hydrateListAll($listAllData);
    }

    public function findAlive(?string $term = null): NaturalPersonSimpleList
    {
        $sql = $this->buildSelectSimpleSql();
        $sql = $this->appendWhereRemovedAtIsNullSql($sql);
        $sql = $this->appendAndWhereDiedAtIsNullSql($sql);
        $sql = $this->appendAndWhereFullNameStartsWithTermSql($sql, $term);
        $sql = $this->appendOrderByFullNameThenByBornAtThenByDiedAtSql($sql);

        $stmt = $this->connection->prepare($sql);
        $this->bindTermValue($stmt, $term, true);
        $result      = $stmt->executeQuery();
        $listAllData = $result->fetchAllAssociative();

        return $this->hydrateListAll($listAllData);
    }

    private function doCountTotal(?string $term): int
    {
        $sql = $this->buildCountSql();
        $sql = $this->appendJoinsSql($sql);
        $sql = $this->appendWhereRemovedAtIsNullSql($sql);
        $sql = $this->appendAndWhereLikeTermSql($sql, $term);

        $stmt = $this->connection->prepare($sql);
        $this->bindTermValue($stmt, $term);
        $result = $stmt->executeQuery();

        return $result->fetchFirstColumn()[0];
    }

    private function buildSelectSql(): string
    {
        return <<<SELECT_SQL
SELECT np.id                                                                                  AS id,
       np.full_name                                                                           AS fullName,
       np.address                                                                             AS address,
       np.phone                                                                               AS phone,
       np.phone_additional                                                                    AS phoneAdditional,
       np.email                                                                               AS email,
       DATE_FORMAT(np.born_at, '%d.%m.%Y')                                                    AS bornAtFormatted,
       np.place_of_birth                                                                      AS placeOfBirth,
       np.passport->>"$.series"                                                               AS passportSeries,
       np.passport->>"$.number"                                                               AS passportNumber,
       DATE_FORMAT(np.passport->>"$.issuedAt", '%d.%m.%Y')                                    AS passportIssuedAt,
       np.passport->>"$.issuedBy"                                                             AS passportIssuedBy,
       IF(
           np.passport->>"$.divisionCode" <> 'null',
           np.passport->>"$.divisionCode",
           NULL
       )                                                                                      AS passportDivisionCode,
       DATE_FORMAT(np.deceased_details->>"$.diedAt", '%d.%m.%Y')                              AS diedAtFormatted,
       IF(
          np.deceased_details->>"$.age" <> 'null',
          np.deceased_details->>"$.age",
          NULL
       )                                                                                      AS age,
       TIMESTAMPDIFF(YEAR, np.born_at, np.deceased_details->>"$.diedAt")                      AS ageCalculated,
       cd.name                                                                                AS causeOfDeathName,
       np.deceased_details->>"$.deathCertificate.series"                                      AS deathCertificateSeries,
       np.deceased_details->>"$.deathCertificate.number"                                      AS deathCertificateNumber,
       DATE_FORMAT(np.deceased_details->>"$.deathCertificate.issuedAt", '%d.%m.%Y')           AS deathCertificateIssuedAt,
       np.deceased_details->>"$.cremationCertificate.number"                                  AS cremationCertificateNumber,
       DATE_FORMAT(np.deceased_details->>"$.cremationCertificate.issuedAt", '%d.%m.%Y')       AS cremationCertificateIssuedAt,
       np.created_at                                                                          AS createdAt,
       np.updated_at                                                                          AS updatedAt
FROM $this->tableName AS np
SELECT_SQL;
    }

    private function buildSelectSimpleSql(): string
    {
        return <<<SELECT_SIMPLE_SQL
SELECT np.id                                                     AS id,
       np.full_name                                              AS fullName,
       DATE_FORMAT(np.born_at, '%d.%m.%Y')                       AS bornAtFormatted,
       DATE_FORMAT(np.deceased_details->>"$.diedAt", '%d.%m.%Y') AS diedAtFormatted
FROM $this->tableName AS np
SELECT_SIMPLE_SQL;
    }

    private function buildCountSql(): string
    {
        return sprintf('SELECT COUNT(np.id) FROM %s AS np', $this->tableName);
    }

    private function appendJoinsSql(string $sql): string
    {
        return $sql . ' LEFT JOIN cause_of_death AS cd ON np.deceased_details->>"$.causeOfDeathId" = cd.id';
    }

    private function appendWhereRemovedAtIsNullSql(string $sql): string
    {
        return $sql . ' WHERE np.removed_at IS NULL';
    }

    private function appendAndWhereDiedAtIsNullSql(string $sql): string
    {
        return $sql . ' AND np.deceased_details->>"$.diedAt" IS NULL';
    }


    private function appendWhereIdIsEqualSql(string $sql): string
    {
        return $sql . ' AND np.id = :id';
    }

    private function appendAndWhereLikeTermSql(string $sql, ?string $term): string
    {
        if ($this->isTermNotEmpty($term)) {
            $sql .= <<<LIKE_TERM_SQL
  AND (np.full_name                                                                     LIKE :term
    OR np.address                                                                       LIKE :term
    OR np.phone                                                                         LIKE :term
    OR np.phone_additional                                                              LIKE :term
    OR np.email                                                                         LIKE :term
    OR DATE_FORMAT(np.born_at, '%d.%m.%Y')                                              LIKE :term
    OR np.place_of_birth                                                                LIKE :term
    OR LOWER(np.passport->>"$.series")                                                  LIKE :term
    OR LOWER(np.passport->>"$.number")                                                  LIKE :term
    OR DATE_FORMAT(np.passport->>"$.issuedAt", '%d.%m.%Y')                              LIKE :term
    OR LOWER(np.passport->>"$.issuedBy")                                                LIKE :term
    OR IF(
          np.passport->>"$.divisionCode" <> 'null',
          np.passport->>"$.divisionCode",
          NULL
       )                                                                                LIKE :term
    OR DATE_FORMAT(np.deceased_details->>"$.diedAt", '%d.%m.%Y')                        LIKE :term
    OR IF(
          np.deceased_details->>"$.age" <> 'null',
          np.deceased_details->>"$.age",
          NULL
       )                                                                                LIKE :term
    OR TIMESTAMPDIFF(YEAR, np.born_at, np.deceased_details->>"$.diedAt")                LIKE :term
    OR cd.name                                                                          LIKE :term
    OR LOWER(np.deceased_details->>"$.deathCertificate.series")                         LIKE :term
    OR LOWER(np.deceased_details->>"$.deathCertificate.number")                         LIKE :term
    OR DATE_FORMAT(np.deceased_details->>"$.deathCertificate.issuedAt", '%d.%m.%Y')     LIKE :term
    OR LOWER(np.deceased_details->>"$.cremationCertificate.number")                     LIKE :term
    OR DATE_FORMAT(np.deceased_details->>"$.cremationCertificate.issuedAt", '%d.%m.%Y') LIKE :term)
LIKE_TERM_SQL;
        }

        return $sql;
    }

    private function appendAndWhereFullNameStartsWithTermSql(string $sql, ?string $term): string
    {
        if ($this->isTermNotEmpty($term)) {
            $sql .= ' AND np.full_name LIKE :term';
        }

        return $sql;
    }

    private function appendOrderByFullNameThenByBornAtThenByDiedAtSql(string $sql): string
    {
        return $sql . ' ORDER BY np.full_name, np.born_at, np.deceased_details->>"$.diedAt"';
    }

    private function appendLimitOffset(string $sql, int $page, int $pageSize): string
    {
        return $sql . \sprintf(' LIMIT %d OFFSET %d', $pageSize, ($page - 1) * $pageSize);
    }

    private function hydrateView(array $viewData): NaturalPersonView
    {
        return new NaturalPersonView(
            $viewData['id'],
            $viewData['fullName'],
            $viewData['address'],
            $viewData['phone'],
            $viewData['phoneAdditional'],
            $viewData['email'],
            $viewData['bornAtFormatted'],
            $viewData['placeOfBirth'],
            $viewData['passportSeries'],
            $viewData['passportNumber'],
            $viewData['passportIssuedAt'],
            $viewData['passportIssuedBy'],
            $viewData['passportDivisionCode'],
            $viewData['diedAtFormatted'],
            match ($viewData['age']) {
                null    => $viewData['ageCalculated'],
                default => (int) $viewData['age'],
            },
            $viewData['causeOfDeathName'],
            $viewData['deathCertificateSeries'],
            $viewData['deathCertificateNumber'],
            $viewData['deathCertificateIssuedAt'],
            $viewData['cremationCertificateNumber'],
            $viewData['cremationCertificateIssuedAt'],
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
    ): NaturalPersonPaginatedList {
        $items = [];
        foreach ($paginatedListData as $paginatedListItemData) {
            $items[] = new NaturalPersonPaginatedListItem(
                $paginatedListItemData['id'],
                $paginatedListItemData['fullName'],
                $paginatedListItemData['address'],
                $paginatedListItemData['phone'],
                $paginatedListItemData['phoneAdditional'],
                $paginatedListItemData['email'],
                $paginatedListItemData['bornAtFormatted'],
                $paginatedListItemData['placeOfBirth'],
                $paginatedListItemData['passportSeries'],
                $paginatedListItemData['passportNumber'],
                $paginatedListItemData['passportIssuedAt'],
                $paginatedListItemData['passportIssuedBy'],
                $paginatedListItemData['passportDivisionCode'],
                $paginatedListItemData['diedAtFormatted'],
                match ($paginatedListItemData['age']) {
                    null    => $paginatedListItemData['ageCalculated'],
                    default => (int) $paginatedListItemData['age'],
                },
                $paginatedListItemData['causeOfDeathName'],
                $paginatedListItemData['deathCertificateSeries'],
                $paginatedListItemData['deathCertificateNumber'],
                $paginatedListItemData['deathCertificateIssuedAt'],
                $paginatedListItemData['cremationCertificateNumber'],
                $paginatedListItemData['cremationCertificateIssuedAt'],
            );
        }

        return new NaturalPersonPaginatedList($items, $page, $pageSize, $term, $totalCount, $totalPages);
    }

    private function hydrateListAll(
        array $listAllData,
    ): NaturalPersonSimpleList {
        $items = [];
        foreach ($listAllData as $simpleListItemData) {
            $items[] = new NaturalPersonSimpleListItem(
                $simpleListItemData['id'],
                $simpleListItemData['fullName'],
                $simpleListItemData['bornAtFormatted'],
                $simpleListItemData['diedAtFormatted'],
            );
        }

        return new NaturalPersonSimpleList($items);
    }
}
