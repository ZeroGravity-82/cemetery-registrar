<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types;

use Cemetery\Registrar\Domain\NaturalPerson\FullName;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types\FullNameType;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class FullNameTypeTest extends AbstractStringTypeTest
{
    protected string $className = FullNameType::class;

    protected string $typeName = 'full_name';

    public function setUp(): void
    {
        parent::setUp();

        $this->dbValue  = 'Иванов Иван Иванович';
        $this->phpValue = new FullName('Иванов Иван Иванович');
    }
}
