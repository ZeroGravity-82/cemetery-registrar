<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\BurialPlace;

use Cemetery\Registrar\Domain\Model\BurialPlace\AbstractBurialPlaceId;
use Cemetery\Registrar\Domain\Model\BurialPlace\ColumbariumNiche\ColumbariumNiche;
use Cemetery\Registrar\Domain\Model\BurialPlace\ColumbariumNiche\ColumbariumNicheId;
use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\GraveSite;
use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\GraveSiteId;
use Cemetery\Registrar\Domain\Model\BurialPlace\MemorialTree\MemorialTree;
use Cemetery\Registrar\Domain\Model\BurialPlace\MemorialTree\MemorialTreeId;
use Cemetery\Registrar\Domain\Model\Exception;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\AbstractCustomJsonType;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class BurialPlaceIdType extends AbstractCustomJsonType
{
    protected string $className = AbstractBurialPlaceId::class;
    protected string $typeName  = 'burial_place_id';

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
                $value instanceof GraveSiteId        => GraveSite::CLASS_SHORTCUT,
                $value instanceof ColumbariumNicheId => ColumbariumNiche::CLASS_SHORTCUT,
                $value instanceof MemorialTreeId     => MemorialTree::CLASS_SHORTCUT,
            },
            'value' => $value->value()
        ];
    }

    /**
     * @throws Exception when the ID is invalid
     */
    protected function buildPhpValue(array $decodedValue): AbstractBurialPlaceId
    {
        return match ($decodedValue['type']) {
            GraveSite::CLASS_SHORTCUT        => new GraveSiteId($decodedValue['value']),
            ColumbariumNiche::CLASS_SHORTCUT => new ColumbariumNicheId($decodedValue['value']),
            MemorialTree::CLASS_SHORTCUT     => new MemorialTreeId($decodedValue['value']),
        };
    }
}
