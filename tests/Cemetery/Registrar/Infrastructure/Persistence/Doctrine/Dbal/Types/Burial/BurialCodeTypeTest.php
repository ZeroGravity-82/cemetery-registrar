<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\Burial;

use Cemetery\Registrar\Domain\Model\Burial\BurialCode;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\Burial\BurialCodeType;
use Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\CustomStringTypeTest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class BurialCodeTypeTest extends CustomStringTypeTest
{
    protected string $className = BurialCodeType::class;
    protected string $typeName  = 'burial_code';

    public function setUp(): void
    {
        parent::setUp();

        $this->dbValue  = '10001';
        $this->phpValue = new BurialCode('10001');
    }
}
