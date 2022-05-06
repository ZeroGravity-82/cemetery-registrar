<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\BurialPlace\ColumbariumNiche;

use Cemetery\Registrar\Domain\BurialPlace\ColumbariumNiche\ColumbariumNicheNumber;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\BurialPlace\ColumbariumNiche\ColumbariumNicheNumberType;
use Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\CustomStringTypeTest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class ColumbariumNicheNumberTypeTest extends CustomStringTypeTest
{
    protected string $className = ColumbariumNicheNumberType::class;
    protected string $typeName  = 'columbarium_niche_number';

    public function setUp(): void
    {
        parent::setUp();

        $this->dbValue  = '001';
        $this->phpValue = new ColumbariumNicheNumber('001');
    }
}
