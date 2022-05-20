<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types;

use Cemetery\Registrar\Domain\EntityMaskingId;

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
            !\array_key_exists('type', $decodedValue) ||
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
        /** @var EntityMaskingId $value */
        $id = $value->id();

        return [
            'type'  => $value->idType(),
            'value' => $id->value()
        ];
    }
}
