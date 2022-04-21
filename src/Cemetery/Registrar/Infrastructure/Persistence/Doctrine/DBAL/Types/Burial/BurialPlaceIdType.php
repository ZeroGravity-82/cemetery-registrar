<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types\Burial;

use Cemetery\Registrar\Domain\Burial\BurialPlaceId;
use Cemetery\Registrar\Domain\BurialPlace\ColumbariumNicheId;
use Cemetery\Registrar\Domain\BurialPlace\GraveSiteId;
use Cemetery\Registrar\Domain\BurialPlace\MemorialTreeId;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\JsonType;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class BurialPlaceIdType extends JsonType
{
    public const ID_CLASS_NAMES = [
        GraveSiteId::class        => 'GraveSiteId',
        ColumbariumNicheId::class => 'ColumbariumNicheId',
        MemorialTreeId::class     => 'MemorialTreeId',
    ];
    private const TYPE_NAME = 'burial_place_id';

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
        if ($value === null) {
            return $value;
        }

        if (!$value instanceof BurialPlaceId) {
            throw ConversionException::conversionFailedInvalidType(
                $value,
                $this->getName(),
                ['null', BurialPlaceId::class]
            );
        }

        try {
            return \json_encode($this->prepareBurialPlaceIdData($value), JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            throw ConversionException::conversionFailedSerialization($value, 'json', $e->getMessage(), $e);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function convertToPHPValue($value, AbstractPlatform $platform): ?BurialPlaceId
    {
        if ($value === null || $value instanceof BurialPlaceId) {
            return $value;
        }

        try {
            $decodedValue = \json_decode($value, true, 512, JSON_THROW_ON_ERROR);
            $this->assertValid($decodedValue, $value);

            return $this->buildBurialPlaceId($decodedValue);
        } catch (\JsonException $e) {
            throw ConversionException::conversionFailed($value, $this->getName(), $e);
        }
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

    /**
     * @param mixed $decodedValue
     * @param mixed $value
     *
     * @throws \RuntimeException when the decoded value has invalid format.
     */
    private function assertValid(mixed $decodedValue, mixed $value): void
    {
        $isInvalidValue = !isset($decodedValue['class'], $decodedValue['value']);
        if ($isInvalidValue) {
            throw new \RuntimeException(\sprintf('Неверный формат для полиморфного идентификатора: "%s".', $value));
        }
    }

    /**
     * @param BurialPlaceId $value
     *
     * @return array
     */
    private function prepareBurialPlaceIdData(BurialPlaceId $value): array
    {
        $id = $value->id();

        return [
            'class' => self::ID_CLASS_NAMES[$id::class],
            'value' => $id->value(),
        ];
    }

    /**
     * @param array $decodedValue
     *
     * @return BurialPlaceId
     */
    private function buildBurialPlaceId(array $decodedValue): BurialPlaceId
    {
        return match ($decodedValue['class']) {
            self::ID_CLASS_NAMES[GraveSiteId::class]        => new BurialPlaceId(new GraveSiteId($decodedValue['value'])),
            self::ID_CLASS_NAMES[ColumbariumNicheId::class] => new BurialPlaceId(new ColumbariumNicheId($decodedValue['value'])),
            self::ID_CLASS_NAMES[MemorialTreeId::class]     => new BurialPlaceId(new MemorialTreeId($decodedValue['value'])),
        };
    }
}
