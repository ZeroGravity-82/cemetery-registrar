<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types\Burial;

use Cemetery\Registrar\Domain\Burial\BurialPlaceId;
use Cemetery\Registrar\Domain\BurialPlace\GraveSiteId;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types\Burial\BurialPlaceIdType;
use Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types\AbstractPolymorphicIdTypeTest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class BurialPlaceIdTypeTest extends AbstractPolymorphicIdTypeTest
{
    protected string $className = BurialPlaceIdType::class;
    protected string $typeName  = 'burial_place_id';

    public function setUp(): void
    {
        parent::setUp();

        $this->dbValue  = '{"class":"GraveSiteId","value":"GS001"}';
        $this->phpValue = new BurialPlaceId(new GraveSiteId('GS001'));
    }
}
