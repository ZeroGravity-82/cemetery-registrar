<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types\Organization\SoleProprietor;

use Cemetery\Registrar\Domain\Organization\SoleProprietor\SoleProprietorId;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types\Organization\SoleProprietor\SoleProprietorIdType;
use Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types\AbstractStringTypeTest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class SoleProprietorIdTypeTest extends AbstractStringTypeTest
{
    protected string $className = SoleProprietorIdType::class;

    protected string $typeName = 'sole_proprietor_id';

    public function setUp(): void
    {
        parent::setUp();

        $this->dbValue  = '8fcd705a-682d-479f-9740-c8e4fac82aff';
        $this->phpValue = new SoleProprietorId('8fcd705a-682d-479f-9740-c8e4fac82aff');
    }
}
