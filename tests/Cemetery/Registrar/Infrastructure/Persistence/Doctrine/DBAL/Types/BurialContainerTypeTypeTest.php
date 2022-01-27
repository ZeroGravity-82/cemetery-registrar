<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types;

use Cemetery\Registrar\Domain\Burial\BurialContainerType;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types\BurialContainerTypeType;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class BurialContainerTypeTypeTest extends AbstractStringTypeTest
{
    protected string $className = BurialContainerTypeType::class;

    protected string $typeName = 'burial_container_type';

    public function setUp(): void
    {
        parent::setUp();

        $this->dbValue  = 'coffin';
        $this->phpValue = new BurialContainerType('coffin');
    }
}
