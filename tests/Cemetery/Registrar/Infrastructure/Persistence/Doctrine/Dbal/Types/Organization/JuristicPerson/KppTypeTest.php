<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\Organization\JuristicPerson;

use Cemetery\Registrar\Domain\Model\Organization\JuristicPerson\Kpp;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\Organization\JuristicPerson\KppType;
use Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\AbstractCustomStringTypeTest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class KppTypeTest extends AbstractCustomStringTypeTest
{
    protected string $className = KppType::class;
    protected string $typeName  = 'kpp';

    public function setUp(): void
    {
        parent::setUp();

        $this->dbValue  = '1234AB789';
        $this->phpValue = new Kpp('1234AB789');
    }
}
