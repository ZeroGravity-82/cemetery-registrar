<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types;

use Cemetery\Registrar\Domain\Burial\BurialId;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\StringType;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class BurialIdType extends StringType
{
    private const BURIAL_ID_TYPE = 'burial_id';

    /**
     * Registers BurialId type to the type map.
     */
    public static function registerType(): void
    {
        if (self::hasType(self::BURIAL_ID_TYPE)) {
            return;
        }
        self::addType(self::BURIAL_ID_TYPE, self::class);
    }

    /**
     * {@inheritdoc}
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform): mixed
    {
        return $value instanceof BurialId ? $value->getValue() : $value;
    }

    /**
     * {@inheritdoc}
     */
    public function convertToPHPValue($value, AbstractPlatform $platform): BurialId
    {
        return new BurialId($value);
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return self::BURIAL_ID_TYPE;
    }

    /**
     * {@inheritdoc}
     */
    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        return true;
    }
}
