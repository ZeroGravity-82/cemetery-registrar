<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types\Burial;

use Cemetery\Registrar\Domain\Burial\BurialCode;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types\Burial\BurialCodeType;
use Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types\AbstractStringTypeTest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class BurialCodeTypeTest extends AbstractStringTypeTest
{
    protected string $className = BurialCodeType::class;
    protected string $typeName  = 'burial_code';

    public function setUp(): void
    {
        parent::setUp();

        $this->dbValue  = '000001';
        $this->phpValue = new BurialCode('000001');
    }
}
