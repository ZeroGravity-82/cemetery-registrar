<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Fetcher;

use Cemetery\Registrar\Domain\View\NaturalPerson\NaturalPersonFetcher;
use Cemetery\Registrar\Domain\View\NaturalPerson\NaturalPersonList;
use Cemetery\Registrar\Domain\View\NaturalPerson\NaturalPersonListItem;
use Doctrine\DBAL\Query\QueryBuilder;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class DoctrineDbalNaturalPersonFetcher extends DoctrineDbalFetcher implements NaturalPersonFetcher
{
    protected string $tableName = 'natural_person';

    public function findViewById(string $id): mixed
    {
        // TODO implement + fix return type
        return null;
    }

    public function findAll(int $page, ?string $term = null, int $pageSize = self::DEFAULT_PAGE_SIZE): NaturalPersonList
    {
        $sql  = $this->buildFindAllSql($page, $term, $pageSize);


        $queryBuilder = $this->connection->createQueryBuilder()
            ->select(
                'np.id                                                             AS id',
                'np.full_name                                                      AS fullName',
                'np.address                                                        AS address',
                'np.phone                                                          AS phone',
                'IF(
                    np.phone IS NOT NULL OR np.phone_additional IS NOT NULL,
                    CONCAT_WS(', ', np.phone, np.phone_additional),
                    NULL
                )                                                                  AS phone',
                'np.email                                                          AS email',
                'np.born_at                                                        AS bornAt',           // TODO fix format
                'np.place_of_birth                                                 AS placeOfBirth',
                'np.passport->>"$.series"                                          AS passport',
                'np.deceased_details->>"$.diedAt"                                  AS diedAt',
                'np.deceased_details->>"$.age"                                     AS age',
                'TIMESTAMPDIFF(YEAR, np.born_at, np.deceased_details->>"$.diedAt") AS age',
                'cd.name                                                           AS causeOfDeathName',
                'np.deceased_details->>"$.deathCertificate.series"                 AS deathCertificate',
                'np.deceased_details->>"$.cremationCertificate.number"             AS cremationCertificate',
            )
            ->from($this->tableName, 'np')
            ->andWhere('np.removed_at IS NULL')
            ->orderBy('np.full_name')
            ->addOrderBy('np.born_at')
            ->addOrderBy('np.deceased_details->>"$.diedAt"')
            ->setFirstResult(($page - 1) * $pageSize)
            ->setMaxResults($pageSize);
        $this->appendJoinsSql($sql);
        $this->appendAndWhereLikeTerm($sql, $term);
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
            ->select('COUNT(np.id)')
            ->from($this->tableName, 'np')
            ->andWhere('np.removed_at IS NULL');
        $this->appendJoins($queryBuilder);
        $this->appendAndWhereLikeTerm($queryBuilder, $term);
        $this->setTermParameter($queryBuilder, $term);

        return $queryBuilder
            ->executeQuery()
            ->fetchFirstColumn()[0];
    }






    private function buildFindAllSql(int $page, ?string $term, int $pageSize): string
    {
        $sql = <<<FIND_ALL_SQL
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
       IF(np.born_at IS NOT NULL, DATE_FORMAT(np.born_at, '%d.%m.%Y'), NULL)                  AS bornAtFormatted,
       np.place_of_birth                                                                      AS placeOfBirth,
       IF(np.passport IS NOT NULL, np.passport->>"$.series", NULL)                            AS passportSeries,
       IF(np.passport IS NOT NULL, np.passport->>"$.number", NULL)                            AS passportNumber,
       IF(
          np.passport IS NOT NULL AND np.passport->>"$.issuedAt" IS NOT NULL,
          DATE_FORMAT(np.passport->>"$.issuedAt", '%d.%m.%Y'),
          NULL
       )                                                                                      AS passportIssuedAtFormatted,
       IF(np.passport IS NOT NULL, np.passport->>"$.issuedBy", NULL)                          AS passportIssuedBy,
       IF(np.passport IS NOT NULL, np.passport->>"$.divisionCode", NULL)                      AS passportDivisionCode,
       IF(
          np.passport IS NOT NULL,
          CONCAT(
             np.passport->>"$.series",
             ' № ',
             np.passport->>"$.number",
             ' выдан ',
             np.passport->>"$.issuedBy",
             ' ',
             DATE_FORMAT(np.passport->>"$.issuedAt", '%d.%m.%Y'),
             IF(
                np.passport->>"$.divisionCode" IS NOT NULL,
                CONCAT(' (', np.passport->>"$.divisionCode", ')'),
                ''
             )
          ),
          NULL
       )                                                                                      AS passportComposed,
       IF(
          np.deceased_details IS NOT NULL AND np.deceased_details->>"$.diedAt" IS NOT NULL,
          DATE_FORMAT(np.deceased_details->>"$.diedAt", '%d.%m.%Y'),
          NULL
       )                                                                                      AS diedAtFormatted,
       IF(np.deceased_details IS NOT NULL, np.deceased_details->>"$.age", NULL)               AS age,
       IF(
          np.born_at IS NOT NULL AND np.deceased_details IS NOT NULL AND np.deceased_details->>"$.diedAt" IS NOT NULL,
          TIMESTAMPDIFF(YEAR, np.born_at, np.deceased_details->>"$.diedAt"),
          NULL
       )                                                                                      AS ageCalculated,
       cd.name                                                                                AS causeOfDeathName,
       IF(
          np.deceased_details IS NOT NULL AND np.deceased_details->>"$.deathCertificate" IS NOT NULL,
          np.deceased_details->>"$.deathCertificate.series",
          NULL
       )                                                                                      AS deathCertificateSeries,
       IF(
          np.deceased_details IS NOT NULL AND np.deceased_details->>"$.deathCertificate" IS NOT NULL,
          np.deceased_details->>"$.deathCertificate.number",
          NULL
       )                                                                                      AS deathCertificateNumber,
       IF(
          np.deceased_details IS NOT NULL AND np.deceased_details->>"$.deathCertificate" IS NOT NULL,
          DATE_FORMAT(np.deceased_details->>"$.deathCertificate.issuedAt", '%d.%m.%Y'),
          NULL
       )                                                                                      AS deathCertificateIssuedAtFormatted,
       IF(
           np.deceased_details IS NOT NULL AND np.deceased_details->>"$.deathCertificate" IS NOT NULL,
           CONCAT(
               np.deceased_details->>"$.deathCertificate.series",
               ' № ',
               np.deceased_details->>"$.deathCertificate.number",
               ' от ',
               DATE_FORMAT(np.deceased_details->>"$.deathCertificate.issuedAt", '%d.%m.%Y')
           ),
           NULL
       )                                                                                      AS deathCertificateComposed,
       IF(
          np.deceased_details IS NOT NULL AND np.deceased_details->>"$.cremationCertificate" IS NOT NULL,
          np.deceased_details->>"$.cremationCertificate.number",
          NULL
       )                                                                                      AS cremationCertificateNumber,
       IF(
          np.deceased_details IS NOT NULL AND np.deceased_details->>"$.cremationCertificate" IS NOT NULL,
          DATE_FORMAT(np.deceased_details->>"$.cremationCertificate.issuedAt", '%d.%m.%Y'),
          NULL
       )                                                                                      AS cremationCertificateIssuedAtFormatted,
       IF(
           np.deceased_details IS NOT NULL AND np.deceased_details->>"$.cremationCertificate" IS NOT NULL,
           CONCAT(
               np.deceased_details->>"$.cremationCertificate.number",
               ' от ',
               DATE_FORMAT(np.deceased_details->>"$.cremationCertificate.issuedAt", '%d.%m.%Y')
           ),
           NULL
       )                                                                                      AS cremationCertificateComposed
FROM natural_person AS np
FIND_ALL_SQL;

        $sql = $this->appendJoinsSql($sql);
        $sql = $this->appendWhereRemovedAtIsNullSql($sql);
        $sql = $this->appendAndWhereLikeTermSql($sql, $term);
        $sql = $this->appendOrderByName($sql);

        return $this->appendLimitOffset($sql, $page, $pageSize);
    }

    private function appendJoinsSql(string $sql): string
    {
        return $sql . ' LEFT JOIN cause_of_death AS cd ON np.deceased_details->>"$.causeOfDeathId" = cd.id';
    }

    private function appendWhereRemovedAtIsNullSql(string $sql): string
    {
        return $sql . ' WHERE removedAt IS NULL';
    }

    private function appendAndWhereLikeTermSql(string $sql, ?string $term): string
    {
        if ($this->isTermNotEmpty($term)) {
            $sql .= <<<LIKE_TERM_SQL
  AND (fullName                              LIKE :term
    OR address                               LIKE :term
    OR phone                                 LIKE :term
    OR phoneAdditional                       LIKE :term
    OR email                                 LIKE :term
    OR bornAtFormatted                       LIKE :term
    OR placeOfBirth                          LIKE :term
    OR passportSeries                        LIKE :term
    OR passportNumber                        LIKE :term
    OR passportIssuedAtFormatted             LIKE :term
    OR passportIssuedBy                      LIKE :term
    OR passportDivisionCode                  LIKE :term
    OR diedAtFormatted                       LIKE :term
    OR age                                   LIKE :term
    OR ageCalculated                         LIKE :term
    OR causeOfDeathName                      LIKE :term
    OR deathCertificateSeries                LIKE :term
    OR deathCertificateNumber                LIKE :term
    OR deathCertificateIssuedAtFormatted     LIKE :term
    OR cremationCertificateNumber            LIKE :term
    OR cremationCertificateIssuedAtFormatted LIKE :term)
LIKE_TERM_SQL;
        }

        return $sql;
    }


    private function appendAndWhereLikeTerm(QueryBuilder $queryBuilder, ?string $term): void
    {
        if ($this->isTermNotEmpty($term)) {
            $queryBuilder
                ->andWhere(
                    $queryBuilder->expr()->like('full_name', ':term'),
//                    $queryBuilder->expr()->like('address', ':term'),
//                    $queryBuilder->expr()->like('phone', ':term'),
//                    $queryBuilder->expr()->like('email.', ':term'),
//                    $queryBuilder->expr()->like('bornAt', ':term'),
                    $queryBuilder->expr()->like('place_of_birth', ':term'),
//                    $queryBuilder->expr()->like('passport', ':term'),
//                    $queryBuilder->expr()->like('diedAt', ':term'),
//                    $queryBuilder->expr()->like('age', ':term'),
//                    $queryBuilder->expr()->like('ageCalculated', ':term'),
//                    $queryBuilder->expr()->like('causeOfDeathName', ':term'),
//                    $queryBuilder->expr()->like('deathCertificate', ':term'),
//                    $queryBuilder->expr()->like('cremationCertificate', ':term'),
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
                    'null'  => match ($listItemData['ageCalculated']) {
                            'null'  => null,
                            default => $listItemData['ageCalculated'],
                        },
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
