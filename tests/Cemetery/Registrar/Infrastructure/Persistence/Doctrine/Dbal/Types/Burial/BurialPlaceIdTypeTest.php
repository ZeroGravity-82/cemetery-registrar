<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\Burial;

use Cemetery\Registrar\Domain\Model\BurialPlace\BurialPlaceId;
use Cemetery\Registrar\Domain\Model\BurialPlace\ColumbariumNiche\ColumbariumNicheId;
use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\GraveSiteId;
use Cemetery\Registrar\Domain\Model\BurialPlace\MemorialTree\MemorialTreeId;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\Burial\BurialPlaceIdType;
use Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\EntityMaskingIdTypeTest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class BurialPlaceIdTypeTest extends EntityMaskingIdTypeTest
{
    protected string $className         = BurialPlaceIdType::class;
    protected string $typeName          = 'burial_place_id';
    protected string $phpValueClassName = BurialPlaceId::class;

    protected function getConversionData(): iterable
    {
        // database value, PHP value
        yield ['{"type":"GRAVE_SITE","value":"GS001"}',        new BurialPlaceId(new GraveSiteId('GS001'))];
        yield ['{"type":"COLUMBARIUM_NICHE","value":"CN001"}', new BurialPlaceId(new ColumbariumNicheId('CN001'))];
        yield ['{"type":"MEMORIAL_TREE","value":"MT001"}',     new BurialPlaceId(new MemorialTreeId('MT001'))];
    }
}
