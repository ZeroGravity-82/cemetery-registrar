<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types\Burial;

use Cemetery\Registrar\Domain\Burial\CustomerId;
use Cemetery\Registrar\Domain\NaturalPerson\NaturalPersonId;
use Cemetery\Registrar\Domain\Organization\JuristicPerson\JuristicPersonId;
use Cemetery\Registrar\Domain\Organization\SoleProprietor\SoleProprietorId;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\JsonType;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class CustomerIdType extends JsonType
{
    public const ID_CLASS_NAMES = [
        NaturalPersonId::class  => 'NaturalPersonId',
        JuristicPersonId::class => 'JuristicPersonId',
        SoleProprietorId::class => 'SoleProprietorId',
    ];
    private const TYPE_NAME = 'customer_id';

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

        if (!$value instanceof CustomerId) {
            throw ConversionException::conversionFailedInvalidType(
                $value,
                $this->getName(),
                ['null', CustomerId::class]
            );
        }

        try {
            return \json_encode($this->prepareCustomerIdData($value), JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            throw ConversionException::conversionFailedSerialization($value, 'json', $e->getMessage(), $e);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function convertToPHPValue($value, AbstractPlatform $platform): ?CustomerId
    {
        if ($value === null || $value instanceof CustomerId) {
            return $value;
        }

        try {
            $decodedValue = \json_decode($value, true, 512, JSON_THROW_ON_ERROR);
            $this->assertValid($decodedValue, $value);

            return $this->buildCustomerId($decodedValue);
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
            throw new \RuntimeException(\sprintf('Неверный формат идентификатора: "%s".', $value));
        }
    }

    /**
     * @param CustomerId $value
     *
     * @return array
     */
    private function prepareCustomerIdData(CustomerId $value): array
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
     * @return CustomerId
     */
    private function buildCustomerId(array $decodedValue): CustomerId
    {
        return match ($decodedValue['class']) {
            self::ID_CLASS_NAMES[NaturalPersonId::class]  => new CustomerId(new NaturalPersonId($decodedValue['value'])),
            self::ID_CLASS_NAMES[JuristicPersonId::class] => new CustomerId(new JuristicPersonId($decodedValue['value'])),
            self::ID_CLASS_NAMES[SoleProprietorId::class] => new CustomerId(new SoleProprietorId($decodedValue['value'])),
        };
    }
}
