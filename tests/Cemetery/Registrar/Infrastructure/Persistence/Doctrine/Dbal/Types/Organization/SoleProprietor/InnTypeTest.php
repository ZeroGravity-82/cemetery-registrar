<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\Organization\SoleProprietor;

use Cemetery\Registrar\Domain\Organization\SoleProprietor\Inn;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\Organization\SoleProprietor\InnType;
use Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\CustomStringTypeTest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class InnTypeTest extends CustomStringTypeTest
{
    protected string $className = InnType::class;
    protected string $typeName  = 'sole_proprietor_inn';

    public function setUp(): void
    {
        parent::setUp();

        $this->dbValue  = '772208786091';
        $this->phpValue = new Inn('772208786091');
    }
}
