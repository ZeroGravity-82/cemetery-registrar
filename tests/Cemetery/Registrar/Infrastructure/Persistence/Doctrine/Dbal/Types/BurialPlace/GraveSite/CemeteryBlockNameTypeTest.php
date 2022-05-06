<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\BurialPlace\GraveSite;

use Cemetery\Registrar\Domain\BurialPlace\GraveSite\CemeteryBlockName;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\BurialPlace\GraveSite\CemeteryBlockNameType;
use Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\CustomStringTypeTest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class CemeteryBlockNameTypeTest extends CustomStringTypeTest
{
    protected string $className = CemeteryBlockNameType::class;
    protected string $typeName  = 'cemetery_block_name';

    public function setUp(): void
    {
        parent::setUp();

        $this->dbValue  = 'воинский квартал';
        $this->phpValue = new CemeteryBlockName('воинский квартал');
    }
}
