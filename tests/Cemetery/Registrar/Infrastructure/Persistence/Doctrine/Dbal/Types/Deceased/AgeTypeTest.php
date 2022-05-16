<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\Deceased;

use Cemetery\Registrar\Domain\Deceased\Age;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\Deceased\AgeType;
use Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\CustomIntegerTypeTest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class AgeTypeTest extends CustomIntegerTypeTest
{
    protected string $className = AgeType::class;
    protected string $typeName  = 'age';

    public function setUp(): void
    {
        parent::setUp();

        $this->dbValue  = 82;
        $this->phpValue = new Age(82);
    }
}
