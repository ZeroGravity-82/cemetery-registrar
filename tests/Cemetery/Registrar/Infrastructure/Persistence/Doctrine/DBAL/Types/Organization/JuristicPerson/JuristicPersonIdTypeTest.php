<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types\Organization\JuristicPerson;

use Cemetery\Registrar\Domain\Organization\JuristicPerson\JuristicPersonId;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types\Organization\JuristicPerson\JuristicPersonIdType;
use Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types\AbstractStringTypeTest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class JuristicPersonIdTypeTest extends AbstractStringTypeTest
{
    protected string $className = JuristicPersonIdType::class;

    protected string $typeName = 'juristic_person_id';

    public function setUp(): void
    {
        parent::setUp();

        $this->dbValue  = '5847d50c-9710-4b80-b1f8-de4632dacf64';
        $this->phpValue = new JuristicPersonId('5847d50c-9710-4b80-b1f8-de4632dacf64');
    }
}
