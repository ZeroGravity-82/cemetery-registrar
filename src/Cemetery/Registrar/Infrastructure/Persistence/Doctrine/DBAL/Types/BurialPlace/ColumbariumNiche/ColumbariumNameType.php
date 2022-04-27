<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types\BurialPlace\ColumbariumNiche;

use Cemetery\Registrar\Domain\BurialPlace\ColumbariumNiche\ColumbariumName;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\StringType;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class ColumbariumNameType extends StringType
{
    private const TYPE_NAME = 'columbarium_name';

    /**
     * Registers type to the type map.
     */
    public static function registerType(): void
    {
        if (self::hasType(self::TYPE_NAME)) {
            return;
        }
        self::addType(self::TYPE_NAME, self::class);
    }

    /**
     * {@inheritdoc}
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform): mixed
    {
        return $value instanceof ColumbariumName ? $value->value() : $value;
    }

    /**
     * {@inheritdoc}
     */
    public function convertToPHPValue($value, AbstractPlatform $platform): ?ColumbariumName
    {
        return !empty($value) ? new ColumbariumName($value) : null;
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return self::TYPE_NAME;
    }

    /**
     * {@inheritdoc}
     */
    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        return true;
    }
}
