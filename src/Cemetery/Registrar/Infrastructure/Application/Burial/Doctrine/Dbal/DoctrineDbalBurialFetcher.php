<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Application\Burial\Doctrine\Dbal;

use Cemetery\Registrar\Application\Burial\BurialFetcher;
use Cemetery\Registrar\Application\Burial\BurialFormView;
use Cemetery\Registrar\Infrastructure\Application\Fetcher;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class DoctrineDbalBurialFetcher extends Fetcher implements BurialFetcher
{
    /**
     * {@inheritdoc}
     */
    public function getById(string $id): BurialFormView
    {
        $result = $this->connection->createQueryBuilder()
            ->select(
                'b.id   AS id',
                'b.code AS code',
                'b.type AS type',
                'd.id   As deceased_id',
            )
            ->from('burial', 'b')
            ->leftJoin('b', 'deceased', 'd', 'b.deceased_id = d.id')
            ->andWhere('b.id = :id')
            ->setParameter('id', $id)
            ->orderBy('b.code')
            ->executeQuery();

        $data = $result->fetchAssociative();
    }

    /**
     * {@inheritdoc}
     */
    public function findAll(int $page, ?string $term = null, int $pageSize = self::DEFAULT_PAGE_SIZE): array
    {

    }
}
