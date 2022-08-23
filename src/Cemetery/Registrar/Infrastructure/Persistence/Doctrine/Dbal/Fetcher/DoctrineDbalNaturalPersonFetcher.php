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
        $queryBuilder = $this->connection->createQueryBuilder()
            ->select(
                'np.id                                                             AS id',
                'np.full_name                                                      AS fullName',
                'np.address                                                        AS address',
                'np.phone                                                          AS phone',
//                'IF(
//                    np.phone IS NOT NULL OR np.phone_additional IS NOT NULL,
//                    CONCAT_WS(', ', np.phone, np.phone_additional),
//                    NULL
//                )                                                                  AS phone',
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
        $this->appendJoins($queryBuilder);
        $this->appendAndWhereLikeTerm($queryBuilder, $term);
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

    private function appendJoins(QueryBuilder $queryBuilder): void
    {
        $queryBuilder
            ->leftJoin('np', 'cause_of_death', 'cd', 'np.deceased_details->>"$.causeOfDeathId" = cd.id');
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
                $listItemData['phone'],
                $listItemData['email'],
                $listItemData['bornAt'],
                $listItemData['placeOfBirth'],
                $listItemData['passport'],
                $listItemData['diedAt'],
                match ($listItemData['age']) {
                    'null'  => match ($listItemData['ageCalculated']) {
                            'null'  => null,
                            default => $listItemData['ageCalculated'],
                        },
                    default => (int) $listItemData['age'],
                },
                $listItemData['causeOfDeathName'],
                $listItemData['deathCertificate'],
                $listItemData['cremationCertificate'],
            );
        }

        return new NaturalPersonList($listItems, $page, $pageSize, $term, $totalCount, $totalPages);
    }
}
