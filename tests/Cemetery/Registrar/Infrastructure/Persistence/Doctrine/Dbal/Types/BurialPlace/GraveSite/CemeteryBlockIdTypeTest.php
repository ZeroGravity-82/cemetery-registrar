<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\BurialPlace\GraveSite;

use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\CemeteryBlockId;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\BurialPlace\GraveSite\CemeteryBlockIdType;
use Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\AbstractCustomStringTypeTest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class CemeteryBlockIdTypeTest extends AbstractCustomStringTypeTest
{
    protected string $className = CemeteryBlockIdType::class;
    protected string $typeName  = 'cemetery_block_id';

    public function setUp(): void
    {
        parent::setUp();

        $this->dbValue  = 'CB001';
        $this->phpValue = new CemeteryBlockId('CB001');
    }
}
