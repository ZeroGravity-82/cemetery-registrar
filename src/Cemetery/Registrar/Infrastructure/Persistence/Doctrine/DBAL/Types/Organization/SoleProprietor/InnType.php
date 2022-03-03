<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types\Organization\SoleProprietor;

use Cemetery\Registrar\Domain\Organization\SoleProprietor\Inn;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\StringType;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class InnType extends StringType
{
    private const INN_TYPE = 'sole_proprietor_inn';

    /**
     * Registers type to the type map.
     */
    public static function registerType(): void
    {
        if (self::hasType(self::INN_TYPE)) {
            return;
        }
        self::addType(self::INN_TYPE, self::class);
    }

    /**
     * {@inheritdoc}
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform): mixed
    {
        return $value instanceof Inn ? $value->getValue() : $value;
    }

    /**
     * {@inheritdoc}
     */
    public function convertToPHPValue($value, AbstractPlatform $platform): ?Inn
    {
        return !empty($value) ? new Inn($value) : null;
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return self::INN_TYPE;
    }

    /**
     * {@inheritdoc}
     */
    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        return true;
    }
}
