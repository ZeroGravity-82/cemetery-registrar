<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types;

use Cemetery\Registrar\Domain\Burial\BurialId;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types\BurialIdType;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class BurialIdTypeTest extends AbstractStringTypeTest
{
    protected string $className = BurialIdType::class;

    protected string $typeName = 'burial_id';

    public function setUp(): void
    {
        parent::setUp();

        $this->dbValue  = '28485684-6cf6-4bca-adfc-37a67c3ec4ec';
        $this->phpValue = new BurialId('28485684-6cf6-4bca-adfc-37a67c3ec4ec');
    }
}
