<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\Organization\JuristicPerson;

use Cemetery\Registrar\Domain\Model\Organization\JuristicPerson\JuristicPersonId;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\Organization\JuristicPerson\JuristicPersonIdType;
use Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\AbstractCustomStringTypeTest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class JuristicPersonIdTypeTest extends AbstractCustomStringTypeTest
{
    protected string $className = JuristicPersonIdType::class;
    protected string $typeName  = 'juristic_person_id';

    public function setUp(): void
    {
        parent::setUp();

        $this->dbValue  = 'JP001';
        $this->phpValue = new JuristicPersonId('JP001');
    }
}
