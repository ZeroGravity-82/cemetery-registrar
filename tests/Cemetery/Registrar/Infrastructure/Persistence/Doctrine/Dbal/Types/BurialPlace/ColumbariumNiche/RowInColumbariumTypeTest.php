<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\BurialPlace\ColumbariumNiche;

use Cemetery\Registrar\Domain\Model\BurialPlace\ColumbariumNiche\RowInColumbarium;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\BurialPlace\ColumbariumNiche\RowInColumbariumType;
use Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\AbstractCustomIntegerTypeTest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class RowInColumbariumTypeTest extends AbstractCustomIntegerTypeTest
{
    protected string $className = RowInColumbariumType::class;
    protected string $typeName  = 'row_in_columbarium';

    public function setUp(): void
    {
        parent::setUp();

        $this->dbValue  = 10;
        $this->phpValue = new RowInColumbarium(10);
    }
}
