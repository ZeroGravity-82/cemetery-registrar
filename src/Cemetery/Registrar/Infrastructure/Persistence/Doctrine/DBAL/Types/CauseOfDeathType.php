<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types;

use Cemetery\Registrar\Domain\Deceased\CauseOfDeath;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\StringType;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class CauseOfDeathType extends StringType
{
    private const CAUSE_OF_DEATH_TYPE = 'cause_of_death';

    /**
     * Registers CauseOfDeath type to the type map.
     */
    public static function registerType(): void
    {
        if (self::hasType(self::CAUSE_OF_DEATH_TYPE)) {
            return;
        }
        self::addType(self::CAUSE_OF_DEATH_TYPE, self::class);
    }

    /**
     * {@inheritdoc}
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform): mixed
    {
        return $value instanceof CauseOfDeath ? $value->getValue() : $value;
    }

    /**
     * {@inheritdoc}
     */
    public function convertToPHPValue($value, AbstractPlatform $platform): ?CauseOfDeath
    {
        return !empty($value) ? new CauseOfDeath($value) : null;
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return self::CAUSE_OF_DEATH_TYPE;
    }

    /**
     * {@inheritdoc}
     */
    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        return true;
    }
}
