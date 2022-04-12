<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types\NaturalPerson;

use Cemetery\Registrar\Domain\NaturalPerson\NaturalPersonId;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types\NaturalPerson\NaturalPersonIdType;
use Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types\AbstractStringTypeTest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class NaturalPersonIdTypeTest extends AbstractStringTypeTest
{
    protected string $className = NaturalPersonIdType::class;

    protected string $typeName = 'natural_person_id';

    public function setUp(): void
    {
        parent::setUp();

        $this->dbValue  = 'NP001';
        $this->phpValue = new NaturalPersonId('NP001');
    }
}
