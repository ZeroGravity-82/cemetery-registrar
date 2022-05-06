<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\Burial;

use Cemetery\Registrar\Domain\Burial\BurialPlaceId;
use Cemetery\Registrar\Domain\BurialPlace\ColumbariumNiche\ColumbariumNicheId;
use Cemetery\Registrar\Domain\BurialPlace\GraveSite\GraveSiteId;
use Cemetery\Registrar\Domain\BurialPlace\MemorialTree\MemorialTreeId;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\EntityMaskingIdType;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class BurialPlaceIdType extends EntityMaskingIdType
{
    /**
     * {@inheritdoc}
     */
    protected string $className = BurialPlaceId::class;

    /**
     * {@inheritdoc}
     */
    protected string $typeName = 'burial_place_id';

    /**
     * {@inheritdoc}
     */
    public static function getClassShortcut(string $className): string
    {
        return match ($className) {
            GraveSiteId::class        => 'GraveSiteId',
            ColumbariumNicheId::class => 'ColumbariumNicheId',
            MemorialTreeId::class     => 'MemorialTreeId',
        };
    }

    /**
     * {@inheritdoc}
     */
    protected function buildPhpValue(array $decodedValue): BurialPlaceId
    {
        return match ($decodedValue['class']) {
            self::getClassShortcut(GraveSiteId::class)        => new BurialPlaceId(new GraveSiteId($decodedValue['value'])),
            self::getClassShortcut(ColumbariumNicheId::class) => new BurialPlaceId(new ColumbariumNicheId($decodedValue['value'])),
            self::getClassShortcut(MemorialTreeId::class)     => new BurialPlaceId(new MemorialTreeId($decodedValue['value'])),
        };
    }
}
