<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types\Burial;

use Cemetery\Registrar\Domain\Burial\BurialContainerType;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types\Burial\BurialContainerTypeType;
use Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types\AbstractStringTypeTest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class BurialContainerTypeTypeTest extends AbstractStringTypeTest
{
    protected string $className = BurialContainerTypeType::class;
    protected string $typeName  = 'burial_container_type';

    public function setUp(): void
    {
        parent::setUp();

        $this->dbValue  = 'гроб';
        $this->phpValue = new BurialContainerType('гроб');
    }
}
