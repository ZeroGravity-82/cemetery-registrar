<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types\BurialPlace\MemorialTree;

use Cemetery\Registrar\Domain\BurialPlace\MemorialTree\MemorialTreeId;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types\BurialPlace\MemorialTree\MemorialTreeIdType;
use Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types\CustomStringTypeTest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class MemorialTreeIdTypeTest extends CustomStringTypeTest
{
    protected string $className = MemorialTreeIdType::class;
    protected string $typeName  = 'memorial_tree_id';

    public function setUp(): void
    {
        parent::setUp();

        $this->dbValue  = 'MT001';
        $this->phpValue = new MemorialTreeId('MT001');
    }
}
