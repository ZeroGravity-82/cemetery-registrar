<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types;


/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
abstract class EntityMaskingIdType extends CustomJsonType
{
    /**
     * Returns the class shortcut for the provided fully qualified class name.
     *
     * @param string $className
     *
     * @return string
     */
    abstract public static function getClassShortcut(string $className): string;

    /**
     * {@inheritdoc}
     */
    protected function assertValidDecodedValue(mixed $decodedValue, mixed $value): void
    {
        $isInvalidValue =
            !\array_key_exists('class', $decodedValue) ||
            !\array_key_exists('value', $decodedValue);
        if ($isInvalidValue) {
            throw new \RuntimeException(\sprintf('Неверный формат идентификатора: "%s".', $value));
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function preparePhpValueForJsonEncoding(mixed $value): array
    {
        $id = $value->id();

        return [
            'class' => static::getClassShortcut($id::class),
            'value' => $id->value()
        ];
    }
}
