<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types;

use Cemetery\Registrar\Domain\Burial\CustomerId;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\StringType;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class CustomerIdType extends StringType
{
    private const CUSTOMER_ID_TYPE = 'customer_id';

    /**
     * Registers CustomerId type to the type map.
     */
    public static function registerType(): void
    {
        if (self::hasType(self::CUSTOMER_ID_TYPE)) {
            return;
        }
        self::addType(self::CUSTOMER_ID_TYPE, self::class);
    }

    /**
     * {@inheritdoc}
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform): mixed
    {
        return $value instanceof CustomerId ? $value->getValue() : $value;
    }

    /**
     * {@inheritdoc}
     */
    public function convertToPHPValue($value, AbstractPlatform $platform): CustomerId
    {
        return new CustomerId($value);
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return self::CUSTOMER_ID_TYPE;
    }

    /**
     * {@inheritdoc}
     */
    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        return true;
    }
}
