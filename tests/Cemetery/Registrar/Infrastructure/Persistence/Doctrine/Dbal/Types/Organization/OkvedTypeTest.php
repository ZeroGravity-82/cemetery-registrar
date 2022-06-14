<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\Organization;

use Cemetery\Registrar\Domain\Model\Organization\Okved;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\Organization\OkvedType;
use Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\CustomStringTypeTest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class OkvedTypeTest extends CustomStringTypeTest
{
    protected string $className = OkvedType::class;
    protected string $typeName  = 'okved';

    public function setUp(): void
    {
        parent::setUp();

        $this->dbValue  = '74.82';
        $this->phpValue = new Okved('74.82');
    }
}
