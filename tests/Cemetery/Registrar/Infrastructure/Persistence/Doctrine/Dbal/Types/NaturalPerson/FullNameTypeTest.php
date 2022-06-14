<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\NaturalPerson;

use Cemetery\Registrar\Domain\Model\NaturalPerson\FullName;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\NaturalPerson\FullNameType;
use Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\CustomStringTypeTest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class FullNameTypeTest extends CustomStringTypeTest
{
    protected string $className = FullNameType::class;
    protected string $typeName  = 'full_name';

    public function setUp(): void
    {
        parent::setUp();

        $this->dbValue  = 'Иванов Иван Иванович';
        $this->phpValue = new FullName('Иванов Иван Иванович');
    }
}
