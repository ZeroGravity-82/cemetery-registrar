<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\Organization\JuristicPerson;

use Cemetery\Registrar\Domain\Model\Organization\JuristicPerson\Ogrn;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\Organization\JuristicPerson\OgrnType;
use Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\AbstractCustomStringTypeTest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class OgrnTypeTest extends AbstractCustomStringTypeTest
{
    protected string $className = OgrnType::class;
    protected string $typeName  = 'ogrn';

    public function setUp(): void
    {
        parent::setUp();

        $this->dbValue  = '1027700132195';
        $this->phpValue = new Ogrn('1027700132195');
    }
}
