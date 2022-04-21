<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types\Organization\JuristicPerson;

use Cemetery\Registrar\Domain\Organization\JuristicPerson\Okpo;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types\Organization\JuristicPerson\OkpoType;
use Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types\StringTypeTest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class OkpoTypeTest extends StringTypeTest
{
    protected string $className = OkpoType::class;

    protected string $typeName = 'juristic_person_okpo';

    public function setUp(): void
    {
        parent::setUp();

        $this->dbValue  = '23584736';
        $this->phpValue = new Okpo('23584736');
    }
}
