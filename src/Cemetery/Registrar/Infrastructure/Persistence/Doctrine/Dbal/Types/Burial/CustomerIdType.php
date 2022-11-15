<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\Burial;

use Cemetery\Registrar\Domain\Model\Exception;
use Cemetery\Registrar\Domain\Model\NaturalPerson\NaturalPerson;
use Cemetery\Registrar\Domain\Model\NaturalPerson\NaturalPersonId;
use Cemetery\Registrar\Domain\Model\Organization\JuristicPerson\JuristicPerson;
use Cemetery\Registrar\Domain\Model\Organization\JuristicPerson\JuristicPersonId;
use Cemetery\Registrar\Domain\Model\Organization\SoleProprietor\SoleProprietor;
use Cemetery\Registrar\Domain\Model\Organization\SoleProprietor\SoleProprietorId;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\AbstractCustomJsonType;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class CustomerIdType extends AbstractCustomJsonType
{
    protected string $typeName  = 'customer_id';

    private array $classNames = [
        NaturalPersonId::class,
        JuristicPersonId::class,
        SoleProprietorId::class,
    ];

    /**
     * @throws ConversionException when the value to be converted is not of the expected type
     * @throws ConversionException when the value cannot be serialized
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform): mixed
    {
        if ($value === null) {
            return $value;
        }

        $isInvalidType = true;
        foreach ($this->classNames as $className) {
            if ($value instanceof $className) {
                $isInvalidType = false;
            }
        }
        if ($isInvalidType) {
            throw ConversionException::conversionFailedInvalidType(
                $value,
                $this->getName(),
                ['null', ...$this->classNames]
            );
        }

        try {
            return \json_encode($this->preparePhpValueForJsonEncoding($value), JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            throw ConversionException::conversionFailedSerialization($value, 'json', $e->getMessage(), $e);
        }
    }

    /**
     * @throws \UnexpectedValueException when the decoded value has invalid format
     * @throws ConversionException       when a database to Doctrine type conversion fails
     */
    public function convertToPHPValue($value, AbstractPlatform $platform): mixed
    {
        if ($value === null) {
            return $value;
        }
        foreach ($this->classNames as $className) {
            if ($value instanceof $className) {
                return $value;
            }
        }

        try {
            $decodedValue = \json_decode($value, true, 512, JSON_THROW_ON_ERROR);
            $this->assertValidDecodedValue($decodedValue, $value);

            return $this->buildPhpValue($decodedValue);
        } catch (\JsonException $e) {
            throw ConversionException::conversionFailed($value, $this->getName(), $e);
        }
    }

    /**
     * @throws \UnexpectedValueException when the decoded value has invalid format
     */
    protected function assertValidDecodedValue(mixed $decodedValue, mixed $value): void
    {
        $isInvalidValue =
            !\is_array($decodedValue)                 ||
            !\array_key_exists('type', $decodedValue) ||
            !\array_key_exists('value', $decodedValue);
        if ($isInvalidValue) {
            throw new \UnexpectedValueException(\sprintf(
                'Неверный формат декодированного значения для ID: "%s".',
                $value,
            ));
        }
    }

    protected function preparePhpValueForJsonEncoding(mixed $value): array
    {
        return [
            'type' => match (true) {
                $value instanceof NaturalPersonId  => NaturalPerson::CLASS_SHORTCUT,
                $value instanceof JuristicPersonId => JuristicPerson::CLASS_SHORTCUT,
                $value instanceof SoleProprietorId => SoleProprietor::CLASS_SHORTCUT,
            },
            'value' => $value->value()
        ];
    }

    /**
     * @throws Exception when the ID is invalid
     */
    protected function buildPhpValue(array $decodedValue): NaturalPersonId|JuristicPersonId|SoleProprietorId
    {
        return match ($decodedValue['type']) {
            NaturalPerson::CLASS_SHORTCUT  => new NaturalPersonId($decodedValue['value']),
            JuristicPerson::CLASS_SHORTCUT => new JuristicPersonId($decodedValue['value']),
            SoleProprietor::CLASS_SHORTCUT => new SoleProprietorId($decodedValue['value']),
        };
    }
}
