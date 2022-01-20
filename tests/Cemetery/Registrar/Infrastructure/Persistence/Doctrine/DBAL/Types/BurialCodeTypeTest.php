<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types;

use Cemetery\Registrar\Domain\Burial\BurialCode;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types\BurialCodeType;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class BurialCodeTypeTest extends AbstractStringTypeTest
{
    protected string $className = BurialCodeType::class;

    protected string $typeName = 'burial_code';

    public function setUp(): void
    {
        parent::setUp();

        $this->dbValue  = '001';
        $this->phpValue = new BurialCode('001');
    }
}
