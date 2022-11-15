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
    public function __construct(
        protected Connection $connection,
    ) {}

    public function getNextCode(): string
    {
        $result = $this->connection->createQueryBuilder()
            ->select(
                'MAX(CAST(b.code AS UNSIGNED)) AS codeMax'
            )
            ->from('burial', 'b')
            ->executeQuery();

        return (string) ($result->fetchAssociative()['codeMax'] + 1);
    }
}
