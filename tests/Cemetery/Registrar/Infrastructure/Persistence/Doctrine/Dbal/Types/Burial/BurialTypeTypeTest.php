<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\Burial;

use Cemetery\Registrar\Domain\Model\Burial\BurialType;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\Burial\BurialTypeType;
use Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\AbstractCustomStringTypeTest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class BurialTypeTypeTest extends AbstractCustomStringTypeTest
{
    protected string $className = BurialTypeType::class;
    protected string $typeName  = 'burial_type';

    public function setUp(): void
    {
        parent::setUp();

        $this->dbValue  = 'ASHES_UNDER_MEMORIAL_TREE';
        $this->phpValue = BurialType::ashesUnderMemorialTree();
    }
}
