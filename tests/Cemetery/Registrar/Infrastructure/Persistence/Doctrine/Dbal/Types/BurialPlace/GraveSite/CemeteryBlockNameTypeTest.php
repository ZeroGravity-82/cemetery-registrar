<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\BurialPlace\GraveSite;

use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\CemeteryBlockName;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\BurialPlace\GraveSite\CemeteryBlockNameType;
use Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\AbstractCustomStringTypeTest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class CemeteryBlockNameTypeTest extends AbstractCustomStringTypeTest
{
    protected string $className = CemeteryBlockNameType::class;
    protected string $typeName  = 'cemetery_block_name';

    public function setUp(): void
    {
        parent::setUp();

        $this->dbValue  = 'воинский';
        $this->phpValue = new CemeteryBlockName('воинский');
    }
}
