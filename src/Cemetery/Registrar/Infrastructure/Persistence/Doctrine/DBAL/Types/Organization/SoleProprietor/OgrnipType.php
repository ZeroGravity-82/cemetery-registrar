<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types\Organization\SoleProprietor;

use Cemetery\Registrar\Domain\Organization\SoleProprietor\Ogrnip;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\StringType;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class OgrnipType extends StringType
{
    private const OGRNIP_TYPE = 'ogrnip';

    /**
     * Registers type to the type map.
     */
    public static function registerType(): void
    {
        if (self::hasType(self::OGRNIP_TYPE)) {
            return;
        }
        self::addType(self::OGRNIP_TYPE, self::class);
    }

    /**
     * {@inheritdoc}
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform): mixed
    {
        return $value instanceof Ogrnip ? $value->getValue() : $value;
    }

    /**
     * {@inheritdoc}
     */
    public function convertToPHPValue($value, AbstractPlatform $platform): ?Ogrnip
    {
        return !empty($value) ? new Ogrnip($value) : null;
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return self::OGRNIP_TYPE;
    }

    /**
     * {@inheritdoc}
     */
    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        return true;
    }
}
