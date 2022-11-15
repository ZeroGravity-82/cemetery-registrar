<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\BurialPlace\ColumbariumNiche;

use Cemetery\Registrar\Domain\Model\BurialPlace\ColumbariumNiche\ColumbariumNicheId;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\BurialPlace\ColumbariumNiche\ColumbariumNicheIdType;
use Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\AbstractCustomStringTypeTest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class ColumbariumNicheIdTypeTest extends AbstractCustomStringTypeTest
{
    protected string $className = ColumbariumNicheIdType::class;
    protected string $typeName  = 'columbarium_niche_id';

    public function setUp(): void
    {
        parent::setUp();

        $this->dbValue  = 'CN001';
        $this->phpValue = new ColumbariumNicheId('CN001');
    }
}
