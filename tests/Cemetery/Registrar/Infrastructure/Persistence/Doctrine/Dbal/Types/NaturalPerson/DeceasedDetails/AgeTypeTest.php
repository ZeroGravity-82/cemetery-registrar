<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\NaturalPerson\DeceasedDetails;

use Cemetery\Registrar\Domain\Model\NaturalPerson\DeceasedDetails\Age;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\NaturalPerson\DeceasedDetails\AgeType;
use Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\CustomIntegerTypeTest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class AgeTypeTest extends CustomIntegerTypeTest
{
    protected string $className = AgeType::class;
    protected string $typeName  = 'age';

    public function setUp(): void
    {
        parent::setUp();

        $this->dbValue  = 82;
        $this->phpValue = new Age(82);
    }
}
