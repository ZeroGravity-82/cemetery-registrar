<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\BurialPlace\MemorialTree;

use Cemetery\Registrar\Domain\Model\BurialPlace\MemorialTree\MemorialTreeId;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\BurialPlace\MemorialTree\MemorialTreeIdType;
use Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\AbstractCustomStringTypeTest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class MemorialTreeIdTypeTest extends AbstractCustomStringTypeTest
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
