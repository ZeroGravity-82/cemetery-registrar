<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\Organization\SoleProprietor;

use Cemetery\Registrar\Domain\Model\Organization\SoleProprietor\SoleProprietorId;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\Organization\SoleProprietor\SoleProprietorIdType;
use Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\CustomStringTypeTest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class SoleProprietorIdTypeTest extends CustomStringTypeTest
{
    protected string $className = SoleProprietorIdType::class;
    protected string $typeName  = 'sole_proprietor_id';

    public function setUp(): void
    {
        parent::setUp();

        $this->dbValue  = 'SP001';
        $this->phpValue = new SoleProprietorId('SP001');
    }
}
