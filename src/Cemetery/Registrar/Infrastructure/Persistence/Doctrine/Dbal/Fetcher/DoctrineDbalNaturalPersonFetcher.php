<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Fetcher;

use Cemetery\Registrar\Domain\View\NaturalPerson\NaturalPersonFetcher;
use Cemetery\Registrar\Domain\View\NaturalPerson\NaturalPersonList;
use Cemetery\Registrar\Domain\View\NaturalPerson\NaturalPersonListItem;
use Cemetery\Registrar\Domain\View\NaturalPerson\NaturalPersonView;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class DoctrineDbalNaturalPersonFetcher extends DoctrineDbalFetcher implements NaturalPersonFetcher
{
    protected string $tableName = 'natural_person';

    public function findViewById(string $id): ?NaturalPersonView
    {
        $viewData = $this->queryViewData($id);

        return $viewData ? $this->hydrateView($viewData) : null;
    }

    public function findAll(?int $page = null, ?string $term = null, int $pageSize = self::DEFAULT_PAGE_SIZE): NaturalPersonList
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

    private function queryViewData(string $id): false|array
    {
        $sql  = $this->findViewByIdSql();
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue('id', $id);
        $result = $stmt->executeQuery();

        return $result->fetchAllAssociative()[0] ?? false;
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
        $sql = 'SELECT COUNT(np.id) FROM natural_person AS np';
        $sql = $this->appendJoinsSql($sql);
        $sql = $this->appendWhereRemovedAtIsNullSql($sql);

        return $this->appendAndWhereLikeTermSql($sql, $term);
    }

    private function findViewByIdSql(): string
    {
        $sql = $this->buildSelectSql();
        $sql = $this->appendJoinsSql($sql);
        $sql = $this->appendWhereRemovedAtIsNullSql($sql);

        return $this->appendWhereIdIsEqualSql($sql);
    }

    private function buildFindAllSql(?int $page, ?string $term, int $pageSize): string
    {
        $sql = $this->buildSelectSql();
        $sql = $this->appendJoinsSql($sql);
        $sql = $this->appendWhereRemovedAtIsNullSql($sql);
        $sql = $this->appendAndWhereLikeTermSql($sql, $term);
        $sql = $this->appendOrderByFullNameThenByBornAtThenByDiedAtSql($sql);

        return $this->appendLimitOffset($sql, $page, $pageSize);
    }

    private function buildSelectSql(): string
    {
        return <<<SELECT_SQL
SELECT np.id                                                                                  AS id,
       np.full_name                                                                           AS fullName,
       np.address                                                                             AS address,
       np.phone                                                                               AS phone,
       np.phone_additional                                                                    AS phoneAdditional,
       IF(
          np.phone IS NOT NULL OR np.phone_additional IS NOT NULL,
          CONCAT_WS(', ', np.phone, np.phone_additional),
          NULL
       )                                                                                      AS phoneComposed,
       np.email                                                                               AS email,
       DATE_FORMAT(np.born_at, '%d.%m.%Y')                                                    AS bornAtFormatted,
       np.place_of_birth                                                                      AS placeOfBirth,
       np.passport->>"$.series"                                                               AS passportSeries,
       np.passport->>"$.number"                                                               AS passportNumber,
       DATE_FORMAT(np.passport->>"$.issuedAt", '%d.%m.%Y')                                    AS passportIssuedAtFormatted,
       np.passport->>"$.issuedBy"                                                             AS passportIssuedBy,
       IF(
          np.passport->>"$.divisionCode" <> 'null',
          np.passport->>"$.divisionCode",
          NULL
       )                                                                                      AS passportDivisionCode,
       IF(
          np.passport IS NOT NULL,
          CONCAT(
             np.passport->>"$.series",
             ' № ',
             np.passport->>"$.number",
             ', выдан ',
             np.passport->>"$.issuedBy",
             ' ',
             DATE_FORMAT(np.passport->>"$.issuedAt", '%d.%m.%Y'),
             IF(
                np.passport->>"$.divisionCode" <> 'null',
                CONCAT(' (', np.passport->>"$.divisionCode", ')'),
                ''
             )
          ),
          NULL
       )                                                                                      AS passportComposed,
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
       DATE_FORMAT(np.deceased_details->>"$.deathCertificate.issuedAt", '%d.%m.%Y')           AS deathCertificateIssuedAtFormatted,
       IF(
           np.deceased_details->>"$.deathCertificate" IS NOT NULL,
           CONCAT(
               np.deceased_details->>"$.deathCertificate.series",
               ' № ',
               np.deceased_details->>"$.deathCertificate.number",
               ' от ',
               DATE_FORMAT(np.deceased_details->>"$.deathCertificate.issuedAt", '%d.%m.%Y')
           ),
           NULL
       )                                                                                      AS deathCertificateComposed,
       np.deceased_details->>"$.cremationCertificate.number"                                  AS cremationCertificateNumber,
       DATE_FORMAT(np.deceased_details->>"$.cremationCertificate.issuedAt", '%d.%m.%Y')       AS cremationCertificateIssuedAtFormatted,
       IF(
           np.deceased_details->>"$.cremationCertificate" IS NOT NULL,
           CONCAT(
               '№ ',
               np.deceased_details->>"$.cremationCertificate.number",
               ' от ',
               DATE_FORMAT(np.deceased_details->>"$.cremationCertificate.issuedAt", '%d.%m.%Y')
           ),
           NULL
       )                                                                                      AS cremationCertificateComposed,
       np.created_at                                                                          AS createdAt,
       np.updated_at                                                                          AS updatedAt
FROM natural_person AS np
SELECT_SQL;
    }

    private function appendJoinsSql(string $sql): string
    {
        return $sql . ' LEFT JOIN cause_of_death AS cd ON np.deceased_details->>"$.causeOfDeathId" = cd.id';
    }

    private function appendWhereRemovedAtIsNullSql(string $sql): string
    {
        return $sql . ' WHERE np.removed_at IS NULL';
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
    OR np.passport->>"$.series"                                                         LIKE :term
    OR np.passport->>"$.number"                                                         LIKE :term
    OR DATE_FORMAT(np.passport->>"$.issuedAt", '%d.%m.%Y')                              LIKE :term
    OR np.passport->>"$.issuedBy"                                                       LIKE :term
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
    OR np.deceased_details->>"$.deathCertificate.series"                                LIKE :term
    OR np.deceased_details->>"$.deathCertificate.number"                                LIKE :term
    OR DATE_FORMAT(np.deceased_details->>"$.deathCertificate.issuedAt", '%d.%m.%Y')     LIKE :term
    OR np.deceased_details->>"$.cremationCertificate.number"                            LIKE :term
    OR DATE_FORMAT(np.deceased_details->>"$.cremationCertificate.issuedAt", '%d.%m.%Y') LIKE :term)
LIKE_TERM_SQL;
        }

        return $sql;
    }

    private function appendOrderByFullNameThenByBornAtThenByDiedAtSql(string $sql): string
    {
        return $sql . ' ORDER BY np.full_name, np.born_at, np.deceased_details->>"$.diedAt"';
    }

    private function appendLimitOffset(string $sql, ?int $page, int $pageSize): string
    {
        if ($page !== null) {
            $sql .= \sprintf(' LIMIT %d OFFSET %d', $pageSize, ($page - 1) * $pageSize);
        }
        
        return $sql;
    }

    private function hydrateView(array $viewData): NaturalPersonView
    {
        return new NaturalPersonView(
            $viewData['id'],
            $viewData['fullName'],
            $viewData['address'],
            $viewData['phoneComposed'],
            $viewData['email'],
            $viewData['bornAtFormatted'],
            $viewData['placeOfBirth'],
            $viewData['passportComposed'],
            $viewData['diedAtFormatted'],
            match ($viewData['age']) {
                null    => $viewData['ageCalculated'],
                default => (int) $viewData['age'],
            },
            $viewData['causeOfDeathName'],
            $viewData['deathCertificateComposed'],
            $viewData['cremationCertificateComposed'],
            $viewData['createdAt'],
            $viewData['updatedAt'],
        );
    }

    private function hydrateList(
        array   $listData,
        ?int    $page,
        int     $pageSize,
        ?string $term,
        int     $totalCount,
        ?int    $totalPages,
    ): NaturalPersonList {
        $listItems = [];
        foreach ($listData as $listItemData) {
            $listItems[] = new NaturalPersonListItem(
                $listItemData['id'],
                $listItemData['fullName'],
                $listItemData['address'],
                $listItemData['phoneComposed'],
                $listItemData['email'],
                $listItemData['bornAtFormatted'],
                $listItemData['placeOfBirth'],
                $listItemData['passportComposed'],
                $listItemData['diedAtFormatted'],
                match ($listItemData['age']) {
                    null    => $listItemData['ageCalculated'],
                    default => (int) $listItemData['age'],
                },
                $listItemData['causeOfDeathName'],
                $listItemData['deathCertificateComposed'],
                $listItemData['cremationCertificateComposed'],
            );
        }

        return new NaturalPersonList($listItems, $page, $pageSize, $term, $totalCount, $totalPages);
    }
}
