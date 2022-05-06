<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\BurialPlace\ColumbariumNiche;

use Cemetery\Registrar\Domain\BurialPlace\ColumbariumNiche\ColumbariumId;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\BurialPlace\ColumbariumNiche\ColumbariumIdType;
use Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\CustomStringTypeTest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class ColumbariumIdTypeTest extends CustomStringTypeTest
{
    protected string $className = ColumbariumIdType::class;
    protected string $typeName  = 'columbarium_id';

    public function setUp(): void
    {
        parent::setUp();

        $this->dbValue  = 'C001';
        $this->phpValue = new ColumbariumId('C001');
    }
}
