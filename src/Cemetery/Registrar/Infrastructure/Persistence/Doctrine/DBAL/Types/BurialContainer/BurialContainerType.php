<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types\BurialContainer;

use Cemetery\Registrar\Domain\BurialContainer\BurialContainer;
use Cemetery\Registrar\Domain\BurialContainer\Coffin;
use Cemetery\Registrar\Domain\BurialContainer\CoffinShape;
use Cemetery\Registrar\Domain\BurialContainer\CoffinSize;
use Cemetery\Registrar\Domain\BurialContainer\Urn;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\JsonType;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class BurialContainerType extends JsonType
{
    private const TYPE_NAME = 'burial_container';

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

        if (!$value instanceof BurialContainer) {
            throw ConversionException::conversionFailedInvalidType(
                $value,
                $this->getName(),
                ['null', BurialContainer::class]
            );
        }

        try {
            return \json_encode($this->prepareBurialContainerData($value), JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            throw ConversionException::conversionFailedSerialization($value, 'json', $e->getMessage(), $e);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function convertToPHPValue($value, AbstractPlatform $platform): ?BurialContainer
    {
        if ($value === null || $value instanceof BurialContainer) {
            return $value;
        }

        try {
            $decodedValue = \json_decode($value, true, 512, JSON_THROW_ON_ERROR);
            $this->assertValid($decodedValue, $value);

            return $this->buildBurialContainer($decodedValue);
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
        $isInvalidValue = false;
        if (!isset($decodedValue['type'])) {
            $isInvalidValue = true;
        }
        $isInvalidValue = $isInvalidValue || match ($decodedValue['type']) {
            'Coffin' =>  !isset(
                $decodedValue['value']['size'],
                $decodedValue['value']['shape'],
                $decodedValue['value']['isNonStandard']
            ),
            'Urn' => $decodedValue['value'] !== null,
        };
        if ($isInvalidValue) {
            throw new \RuntimeException(\sprintf('Неверный формат для контейнера захоронения: "%s".', $value));
        }
    }

    /**
     * @param BurialContainer $value
     *
     * @return array
     */
    private function prepareBurialContainerData(BurialContainer $value): array
    {
        $container = $value->container();
        return [
            'type'  => $container->className(),
            'value' => match (true) {
                $container instanceof Coffin => [
                    'size'          => $container->size()->value(),
                    'shape'         => $container->shape()->value(),
                    'isNonStandard' => $container->isNonStandard(),
                ],
                $container instanceof Urn => null,
            },
        ];
    }

    /**
     * @param array $decodedValue
     *
     * @return BurialContainer
     */
    private function buildBurialContainer(array $decodedValue): BurialContainer
    {
        $container = match ($decodedValue['type']) {
            'Coffin' => new Coffin(
                new CoffinSize($decodedValue['value']['size']),
                new CoffinShape($decodedValue['value']['shape']),
                $decodedValue['value']['isNonStandard'],
            ),
            'Urn' => new Urn(),
        };

        return new BurialContainer($container);
    }
}
