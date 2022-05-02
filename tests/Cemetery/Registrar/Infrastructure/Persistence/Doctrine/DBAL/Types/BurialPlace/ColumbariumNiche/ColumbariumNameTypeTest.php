<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types\BurialPlace\ColumbariumNiche;

use Cemetery\Registrar\Domain\BurialPlace\ColumbariumNiche\ColumbariumName;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types\BurialPlace\ColumbariumNiche\ColumbariumNameType;
use Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types\StringTypeTest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class ColumbariumNameTypeTest extends StringTypeTest
{
    protected string $className = ColumbariumNameType::class;
    protected string $typeName  = 'columbarium_name';

    public function setUp(): void
    {
        parent::setUp();

        $this->dbValue  = 'западный колумбарий';
        $this->phpValue = new ColumbariumName('западный колумбарий');
    }
}
