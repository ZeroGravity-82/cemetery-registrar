<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
abstract class CustomJsonType extends CustomType
{
    /**
     * @throws ConversionException when the value to be converted is not of the expected type
     * @throws ConversionException when the value cannot be serialized
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform): mixed
    {
        if ($value === null) {
            return $value;
        }

        if (!$value instanceof $this->className) {
            throw ConversionException::conversionFailedInvalidType(
                $value,
                $this->getName(),
                ['null', $this->className]
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
        if ($value === null || $value instanceof $this->className) {
            return $value;
        }

        try {
            $decodedValue = \json_decode($value, true, 512, JSON_THROW_ON_ERROR);
            $this->assertValidDecodedValue($decodedValue, $value);

            return $this->buildPhpValue($decodedValue);
        } catch (\JsonException $e) {
            throw ConversionException::conversionFailed($value, $this->getName(), $e);
        }
    }

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $platform->getJsonTypeDeclarationSQL($column);
    }

    /**
     * Checks that the decoded value has valid format that is compatible with PHP value building process.
     *
     * @see buildPhpValue
     *
     * @throws \UnexpectedValueException when the decoded value has invalid format
     */
    abstract protected function assertValidDecodedValue(mixed $decodedValue, mixed $value): void;

    /**
     * Prepares the PHP value for JSON encoding as part of the conversion to a database value.
     */
    abstract protected function preparePhpValueForJsonEncoding(mixed $value): array;

    /**
     * Builds a PHP value from the decoded database value.
     */
    abstract protected function buildPhpValue(array $decodedValue): mixed;
}
