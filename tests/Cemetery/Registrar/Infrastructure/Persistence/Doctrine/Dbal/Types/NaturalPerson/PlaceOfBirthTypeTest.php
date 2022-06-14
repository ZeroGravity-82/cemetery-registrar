<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\NaturalPerson;

use Cemetery\Registrar\Domain\Model\NaturalPerson\PlaceOfBirth;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\NaturalPerson\PlaceOfBirthType;
use Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\CustomStringTypeTest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class PlaceOfBirthTypeTest extends CustomStringTypeTest
{
    protected string $className = PlaceOfBirthType::class;
    protected string $typeName  = 'place_of_birth';

    public function setUp(): void
    {
        parent::setUp();

        $this->dbValue  = 'город Новосибирск';
        $this->phpValue = new PlaceOfBirth('город Новосибирск');
    }
}
