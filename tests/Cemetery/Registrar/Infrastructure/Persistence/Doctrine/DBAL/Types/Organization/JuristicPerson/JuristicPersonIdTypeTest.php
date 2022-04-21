<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types\Organization\JuristicPerson;

use Cemetery\Registrar\Domain\Organization\JuristicPerson\JuristicPersonId;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types\Organization\JuristicPerson\JuristicPersonIdType;
use Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types\StringTypeTest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class JuristicPersonIdTypeTest extends StringTypeTest
{
    protected string $className = JuristicPersonIdType::class;

    protected string $typeName = 'juristic_person_id';

    public function setUp(): void
    {
        parent::setUp();

        $this->dbValue  = 'JP001';
        $this->phpValue = new JuristicPersonId('JP001');
    }
}
