<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\BurialPlace\MemorialTree;

use Cemetery\Registrar\Domain\Model\BurialPlace\MemorialTree\MemorialTreeNumber;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\BurialPlace\MemorialTree\MemorialTreeNumberType;
use Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\AbstractCustomStringTypeTest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class MemorialTreeNumberTypeTest extends AbstractCustomStringTypeTest
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
