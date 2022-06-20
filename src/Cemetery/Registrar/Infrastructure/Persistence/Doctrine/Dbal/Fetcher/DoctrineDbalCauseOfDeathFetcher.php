<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Fetcher;

use Cemetery\Registrar\Domain\View\CauseOfDeath\CauseOfDeathFetcher;
use Cemetery\Registrar\Domain\View\CauseOfDeath\CauseOfDeathList;
use Cemetery\Registrar\Domain\View\CauseOfDeath\CauseOfDeathListItem;
use Cemetery\Registrar\Domain\View\CauseOfDeath\CauseOfDeathView;
use Doctrine\DBAL\Query\QueryBuilder;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class DoctrineDbalCauseOfDeathFetcher extends DoctrineDbalFetcher implements CauseOfDeathFetcher
{
    /**
     * {@inheritdoc}
     */
    public function getViewById(string $id): CauseOfDeathView
    {
        $causeOfDeathViewData = $this->queryCauseOfDeathViewData($id);
        if ($causeOfDeathViewData === false) {
            throw new \RuntimeException(\sprintf('Причина смерти с ID "%s" не найдена.', $id));
        }

        return $this->hydrateCauseOfDeathView($causeOfDeathViewData);
    }

    /**
     * {@inheritdoc}
     */
    public function findAll(int $page, ?string $term = null, int $pageSize = self::DEFAULT_PAGE_SIZE): CauseOfDeathList
    {
        $queryBuilder = $this->connection->createQueryBuilder()
            ->select(
                'cod.id   AS id',
                'cod.name AS name',
            )
            ->from('cause_of_death', 'cod')
            ->andWhere('cod.removed_at IS NULL')
            ->orderBy('cod.name')
            ->setFirstResult(($page - 1) * $pageSize)
            ->setMaxResults($pageSize);
        $this->appendAndWhereLikeTerm($queryBuilder, $term);
        $this->setTermParameter($queryBuilder, $term);

        $causeOfDeathListData = $queryBuilder
            ->executeQuery()
            ->fetchAllAssociative();
        $totalCount = $this->doCountTotal($term);
        $totalPages = (int) \ceil($totalCount / $pageSize);

        return $this->hydrateCauseOfDeathList($causeOfDeathListData, $page, $pageSize, $term, $totalCount, $totalPages);
    }

    /**
     * {@inheritdoc}
     */
    public function countTotal(): int
    {
        return $this->doCountTotal(null);
    }

    /**
     * @param string $id
     *
     * @return false|array
     */
    private function queryCauseOfDeathViewData(string $id): false|array
    {
        return $this->connection->createQueryBuilder()
            ->select(
                'cod.id         AS id',
                'cod.name       AS name',
                'cod.created_at AS createdAt',
                'cod.updated_at AS updatedAt',
            )
            ->from('cause_of_death', 'cod')
            ->andWhere('cod.id = :id')
            ->andWhere('cod.removed_at IS NULL')
            ->setParameter('id', $id)
            ->executeQuery()
            ->fetchAssociative();
    }

    /**
     * @param string|null $term
     *
     * @return int
     */
    private function doCountTotal(?string $term): int
    {
        $queryBuilder = $this->connection->createQueryBuilder()
            ->select('COUNT(cod.id)')
            ->from('cause_of_death', 'cod')
            ->andWhere('cod.removed_at IS NULL');
        $this->appendAndWhereLikeTerm($queryBuilder, $term);
        $this->setTermParameter($queryBuilder, $term);

        return $queryBuilder
            ->executeQuery()
            ->fetchFirstColumn()[0];
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
                    $queryBuilder->expr()->like('cod.name', ':term'),
                );
        }
    }

    /**
     * @param array $causeOfDeathViewData
     *
     * @return CauseOfDeathView
     */
    private function hydrateCauseOfDeathView(array $causeOfDeathViewData): CauseOfDeathView
    {
        return new CauseOfDeathView(
            $causeOfDeathViewData['id'],
            $causeOfDeathViewData['name'],
            $causeOfDeathViewData['createdAt'],
            $causeOfDeathViewData['updatedAt'],
        );
    }

    /**
     * @param array       $causeOfDeath
     * @param int         $page
     * @param int         $pageSize
     * @param string|null $term
     * @param int         $totalCount
     * @param int         $totalPages
     *
     * @return CauseOfDeathList
     */
    private function hydrateCauseOfDeathList(
        array   $causeOfDeath,
        int     $page,
        int     $pageSize,
        ?string $term,
        int     $totalCount,
        int     $totalPages,
    ): CauseOfDeathList {
        $listItems = [];
        foreach ($causeOfDeath as $listItemData) {
            $listItems[] = new CauseOfDeathListItem(
                $listItemData['id'],
                $listItemData['name'],
            );
        }

        return new CauseOfDeathList($listItems, $page, $pageSize, $term, $totalCount, $totalPages);
    }
}
