<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types\Burial;

use Cemetery\Registrar\Domain\Burial\BurialContainerType;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\StringType;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class BurialContainerTypeType extends StringType
{
    private const BURIAL_CONTAINER_TYPE_TYPE = 'burial_container_type';

    /**
     * Registers type to the type map.
     */
    public static function registerType(): void
    {
        if (self::hasType(self::BURIAL_CONTAINER_TYPE_TYPE)) {
            return;
        }
        self::addType(self::BURIAL_CONTAINER_TYPE_TYPE, self::class);
    }

    /**
     * {@inheritdoc}
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform): mixed
    {
        return $value instanceof BurialContainerType ? $value->getValue() : $value;
    }

    /**
     * {@inheritdoc}
     */
    public function convertToPHPValue($value, AbstractPlatform $platform): ?BurialContainerType
    {
        return !empty($value) ? new BurialContainerType($value) : null;
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return self::BURIAL_CONTAINER_TYPE_TYPE;
    }

    /**
     * {@inheritdoc}
     */
    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        return true;
    }
}
