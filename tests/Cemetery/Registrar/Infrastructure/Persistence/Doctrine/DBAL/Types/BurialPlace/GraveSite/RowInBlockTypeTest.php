<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types\BurialPlace\GraveSite;

use Cemetery\Registrar\Domain\BurialPlace\GraveSite\RowInBlock;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types\BurialPlace\GraveSite\RowInBlockType;
use Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types\CustomIntegerTypeTest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class RowInBlockTypeTest extends CustomIntegerTypeTest
{
    protected string $className = RowInBlockType::class;
    protected string $typeName  = 'row_in_block';

    public function setUp(): void
    {
        parent::setUp();

        $this->dbValue  = 10;
        $this->phpValue = new RowInBlock(10);
    }
}
