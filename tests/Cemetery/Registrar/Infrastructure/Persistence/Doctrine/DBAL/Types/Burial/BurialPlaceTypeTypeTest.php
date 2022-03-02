<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types\Burial;

use Cemetery\Registrar\Domain\Burial\BurialPlaceType;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types\Burial\BurialPlaceTypeType;
use Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types\AbstractStringTypeTest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class BurialPlaceTypeTypeTest extends AbstractStringTypeTest
{
    protected string $className = BurialPlaceTypeType::class;

    protected string $typeName = 'burial_place_type';

    public function setUp(): void
    {
        parent::setUp();

        $this->dbValue  = 'колумбарная ниша';
        $this->phpValue = new BurialPlaceType('колумбарная ниша');
    }
}
