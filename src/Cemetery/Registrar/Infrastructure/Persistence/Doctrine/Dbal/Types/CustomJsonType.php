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
     * {@inheritdoc}
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
     * {@inheritdoc}
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

    /**
     * {@inheritdoc}
     */
    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $platform->getJsonTypeDeclarationSQL($column);
    }

    /**
     * Prepares the PHP value for JSON encoding as part of the conversion to a database value.
     *
     * @param mixed $value
     *
     * @return array
     */
    abstract protected function preparePhpValueForJsonEncoding(mixed $value): array;

    /**
     * Checks that the decoded value has valid format that is compatible with PHP value building process.
     *
     * @see buildPhpValue
     *
     * @param mixed $decodedValue
     * @param mixed $value
     *
     * @throws \RuntimeException when the decoded value has invalid format.
     */
    abstract protected function assertValidDecodedValue(mixed $decodedValue, mixed $value): void;

    /**
     * Builds a PHP value from the decoded database value.
     *
     * @param array $decodedValue
     *
     * @return mixed
     */
    abstract protected function buildPhpValue(array $decodedValue): mixed;
}
