<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types\Deceased;

use Cemetery\Registrar\Domain\Deceased\DeathCertificateId;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\StringType;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class DeathCertificateIdType extends StringType
{
    private const DEATH_CERTIFICATE_ID_TYPE = 'death_certificate_id';

    /**
     * Registers type to the type map.
     */
    public static function registerType(): void
    {
        if (self::hasType(self::DEATH_CERTIFICATE_ID_TYPE)) {
            return;
        }
        self::addType(self::DEATH_CERTIFICATE_ID_TYPE, self::class);
    }

    /**
     * {@inheritdoc}
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform): mixed
    {
        return $value instanceof DeathCertificateId ? $value->getValue() : $value;
    }

    /**
     * {@inheritdoc}
     */
    public function convertToPHPValue($value, AbstractPlatform $platform): ?DeathCertificateId
    {
        return !empty($value) ? new DeathCertificateId($value) : null;
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return self::DEATH_CERTIFICATE_ID_TYPE;
    }

    /**
     * {@inheritdoc}
     */
    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        return true;
    }
}
