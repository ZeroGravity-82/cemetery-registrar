<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types\Deceased;

use Cemetery\Registrar\Domain\Deceased\DeceasedId;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types\Deceased\DeceasedIdType;
use Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types\CustomStringTypeTest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class DeceasedIdTypeTest extends CustomStringTypeTest
{
    protected string $className = DeceasedIdType::class;
    protected string $typeName  = 'deceased_id';

    public function setUp(): void
    {
        parent::setUp();

        $this->dbValue  = 'D001';
        $this->phpValue = new DeceasedId('D001');
    }
}
