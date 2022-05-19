<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
abstract class EntityMaskingIdType extends CustomJsonType
{
    /**
     * {@inheritdoc}
     */
    protected function assertValidDecodedValue(mixed $decodedValue, mixed $value): void
    {
        $isInvalidValue =
            !\array_key_exists('classShortcut', $decodedValue) ||
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
            'classShortcut' => $id::CLASS_SHORTCUT,
            'value'         => $id->value()
        ];
    }
}
