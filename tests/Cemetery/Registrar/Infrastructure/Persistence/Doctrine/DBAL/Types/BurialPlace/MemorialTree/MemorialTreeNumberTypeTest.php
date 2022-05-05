<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types\BurialPlace\MemorialTree;

use Cemetery\Registrar\Domain\BurialPlace\MemorialTree\MemorialTreeNumber;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types\BurialPlace\MemorialTree\MemorialTreeNumberType;
use Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types\CustomStringTypeTest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class MemorialTreeNumberTypeTest extends CustomStringTypeTest
{
    protected string $className = MemorialTreeNumberType::class;
    protected string $typeName  = 'memorial_tree_number';

    public function setUp(): void
    {
        parent::setUp();

        $this->dbValue  = '001';
        $this->phpValue = new MemorialTreeNumber('001');
    }
}
