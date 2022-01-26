<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types;

use Cemetery\Registrar\Domain\Burial\BurialPlaceType;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\StringType;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class BurialPlaceTypeType extends StringType
{
    private const BURIAL_PLACE_TYPE_TYPE = 'burial_place_type';

    /**
     * Registers BurialPlaceType type to the type map.
     */
    public static function registerType(): void
    {
        if (self::hasType(self::BURIAL_PLACE_TYPE_TYPE)) {
            return;
        }
        self::addType(self::BURIAL_PLACE_TYPE_TYPE, self::class);
    }

    /**
     * {@inheritdoc}
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform): mixed
    {
        return $value instanceof BurialPlaceType ? $value->getValue() : $value;
    }

    /**
     * {@inheritdoc}
     */
    public function convertToPHPValue($value, AbstractPlatform $platform): ?BurialPlaceType
    {
        return !empty($value) ? new BurialPlaceType($value) : null;
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return self::BURIAL_PLACE_TYPE_TYPE;
    }

    /**
     * {@inheritdoc}
     */
    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        return true;
    }
}
