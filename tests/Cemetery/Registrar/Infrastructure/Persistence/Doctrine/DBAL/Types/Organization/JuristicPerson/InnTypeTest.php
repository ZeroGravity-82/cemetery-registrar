<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types\Organization\JuristicPerson;

use Cemetery\Registrar\Domain\Organization\JuristicPerson\Inn;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types\Organization\JuristicPerson\InnType;
use Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types\CustomStringTypeTest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class InnTypeTest extends CustomStringTypeTest
{
    protected string $className = InnType::class;
    protected string $typeName  = 'juristic_person_inn';

    public function setUp(): void
    {
        parent::setUp();

        $this->dbValue  = '7728168971';
        $this->phpValue = new Inn('7728168971');
    }
}
