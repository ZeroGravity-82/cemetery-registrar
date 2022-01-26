<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types;

use Cemetery\Registrar\Domain\Burial\BurialPlaceType;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types\BurialPlaceTypeType;

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

        $this->dbValue  = 'columbarium_niche';
        $this->phpValue = new BurialPlaceType('columbarium_niche');
    }
}
