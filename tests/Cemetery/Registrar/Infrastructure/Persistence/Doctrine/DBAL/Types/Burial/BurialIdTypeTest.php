<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types\Burial;

use Cemetery\Registrar\Domain\Burial\BurialId;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types\Burial\BurialIdType;
use Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types\AbstractStringTypeTest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class BurialIdTypeTest extends AbstractStringTypeTest
{
    protected string $className = BurialIdType::class;
    protected string $typeName  = 'burial_id';

    public function setUp(): void
    {
        parent::setUp();

        $this->dbValue  = 'B001';
        $this->phpValue = new BurialId('B001');
    }
}
