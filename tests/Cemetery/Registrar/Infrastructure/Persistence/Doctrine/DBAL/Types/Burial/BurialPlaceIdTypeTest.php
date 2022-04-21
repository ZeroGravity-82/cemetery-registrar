<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types\Burial;

use Cemetery\Registrar\Domain\Burial\BurialPlaceId;
use Cemetery\Registrar\Domain\BurialPlace\ColumbariumNicheId;
use Cemetery\Registrar\Domain\BurialPlace\GraveSiteId;
use Cemetery\Registrar\Domain\BurialPlace\MemorialTreeId;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types\Burial\BurialPlaceIdType;
use Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types\MaskingIdTypeTest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class BurialPlaceIdTypeTest extends MaskingIdTypeTest
{
    protected string $className          = BurialPlaceIdType::class;
    protected string $typeName           = 'burial_place_id';
    protected string $phpValueClassName  = BurialPlaceId::class;

    protected function getConversionTests(): array
    {
        return [
            // database value, PHP value
            ['{"class":"GraveSiteId","value":"GS001"}',        new BurialPlaceId(new GraveSiteId('GS001'))],
            ['{"class":"ColumbariumNicheId","value":"CN001"}', new BurialPlaceId(new ColumbariumNicheId('CN001'))],
            ['{"class":"MemorialTreeId","value":"MT001"}',     new BurialPlaceId(new MemorialTreeId('MT001'))],
        ];
    }
}
