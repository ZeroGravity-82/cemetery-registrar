<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types;

use Cemetery\Registrar\Domain\Model\EntityMaskingId;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
abstract class EntityMaskingIdType extends CustomJsonType
{
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
                'Неверный формат декодированного значения для идентификатора: "%s".',
                $value,
            ));
        }
    }

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
