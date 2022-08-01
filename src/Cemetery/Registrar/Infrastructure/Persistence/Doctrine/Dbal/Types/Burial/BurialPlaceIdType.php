<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\Burial;

use Cemetery\Registrar\Domain\Model\Burial\BurialPlaceId;
use Cemetery\Registrar\Domain\Model\BurialPlace\ColumbariumNiche\ColumbariumNiche;
use Cemetery\Registrar\Domain\Model\BurialPlace\ColumbariumNiche\ColumbariumNicheId;
use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\GraveSite;
use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\GraveSiteId;
use Cemetery\Registrar\Domain\Model\BurialPlace\MemorialTree\MemorialTree;
use Cemetery\Registrar\Domain\Model\BurialPlace\MemorialTree\MemorialTreeId;
use Cemetery\Registrar\Domain\Model\Exception;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\EntityMaskingIdType;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class BurialPlaceIdType extends EntityMaskingIdType
{
    protected string $className = BurialPlaceId::class;
    protected string $typeName  = 'burial_place_id';

    /**
     * @throws Exception when the ID is invalid
     */
    protected function buildPhpValue(array $decodedValue): BurialPlaceId
    {
        return match ($decodedValue['type']) {
            GraveSite::CLASS_SHORTCUT        => new BurialPlaceId(new GraveSiteId($decodedValue['value'])),
            ColumbariumNiche::CLASS_SHORTCUT => new BurialPlaceId(new ColumbariumNicheId($decodedValue['value'])),
            MemorialTree::CLASS_SHORTCUT     => new BurialPlaceId(new MemorialTreeId($decodedValue['value'])),
        };
    }
}
