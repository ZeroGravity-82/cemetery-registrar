<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\Organization;

use Cemetery\Registrar\Domain\Model\Organization\Name;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\Organization\NameType;
use Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\AbstractCustomStringTypeTest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class NameTypeTest extends AbstractCustomStringTypeTest
{
    protected string $className = NameType::class;
    protected string $typeName  = 'organization_name';

    public function setUp(): void
    {
        parent::setUp();

        $this->dbValue  = 'ИП Иванов Иван Иванович';
        $this->phpValue = new Name('ИП Иванов Иван Иванович');
    }
}
