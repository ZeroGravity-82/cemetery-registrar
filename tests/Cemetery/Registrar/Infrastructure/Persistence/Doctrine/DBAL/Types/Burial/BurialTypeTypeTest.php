<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types\Burial;

use Cemetery\Registrar\Domain\Burial\BurialType;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types\Burial\BurialTypeType;
use Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types\StringTypeTest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class BurialTypeTypeTest extends StringTypeTest
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
