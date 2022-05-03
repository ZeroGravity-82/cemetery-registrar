<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types\BurialPlace\GraveSite;

use Cemetery\Registrar\Domain\BurialPlace\GraveSite\CemeteryBlockId;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types\BurialPlace\GraveSite\CemeteryBlockIdType;
use Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types\CustomStringTypeTest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class CemeteryBlockIdTypeTest extends CustomStringTypeTest
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
