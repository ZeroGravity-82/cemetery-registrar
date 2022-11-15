<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\Burial;

use Cemetery\Registrar\Domain\Model\Burial\BurialId;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\Burial\BurialIdType;
use Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\AbstractCustomStringTypeTest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class BurialIdTypeTest extends AbstractCustomStringTypeTest
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
