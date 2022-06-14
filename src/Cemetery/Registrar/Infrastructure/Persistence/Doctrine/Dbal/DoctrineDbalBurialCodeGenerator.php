<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal;

use Cemetery\Registrar\Domain\Model\Burial\BurialCodeGenerator;
use Doctrine\DBAL\Connection;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class DoctrineDbalBurialCodeGenerator implements BurialCodeGenerator
{
    /**
     * @param Connection $connection
     */
    public function __construct(
        protected readonly Connection $connection,
    ) {}

    /**
     * {@inheritdoc}
     */
    public function getNextCode(): string
    {
        $result = $this->connection->createQueryBuilder()
            ->select(
                'MAX(CAST(b.code AS UNSIGNED)) AS code_max'
            )
            ->from('burial', 'b')
            ->executeQuery();

        return (string) ($result->fetchAssociative()['code_max'] + 1);
    }
}
