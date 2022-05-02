<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types\Organization\SoleProprietor;

use Cemetery\Registrar\Domain\Organization\SoleProprietor\SoleProprietorId;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types\Organization\SoleProprietor\SoleProprietorIdType;
use Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types\StringTypeTest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class SoleProprietorIdTypeTest extends StringTypeTest
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
