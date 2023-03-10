<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\BurialContainer;

use Cemetery\Registrar\Domain\Model\Burial\BurialContainer\BurialContainer;
use Cemetery\Registrar\Domain\Model\Burial\BurialContainer\Coffin;
use Cemetery\Registrar\Domain\Model\Burial\BurialContainer\CoffinShape;
use Cemetery\Registrar\Domain\Model\Burial\BurialContainer\CoffinSize;
use Cemetery\Registrar\Domain\Model\Burial\BurialContainer\Urn;
use Cemetery\Registrar\Domain\Model\Exception;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\AbstractCustomJsonType;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class BurialContainerType extends AbstractCustomJsonType
{
    protected string $className = BurialContainer::class;
    protected string $typeName  = 'burial_container';

    /**
     * @throws \UnexpectedValueException when the decoded value has invalid format
     */
    protected function assertValidDecodedValue(mixed $decodedValue, mixed $value): void
    {
        $isInvalidValue = false;
        if (!\is_array($decodedValue) || !\array_key_exists('type', $decodedValue)) {
            $isInvalidValue = true;
        }
        $isInvalidValue = $isInvalidValue || match ($decodedValue['type']) {
            Coffin::CLASS_SHORTCUT =>
                !\array_key_exists('value',         $decodedValue)          ||
                !\array_key_exists('size',          $decodedValue['value']) ||
                !\array_key_exists('shape',         $decodedValue['value']) ||
                !\array_key_exists('isNonStandard', $decodedValue['value']),
            Urn::CLASS_SHORTCUT =>
                !\array_key_exists('value', $decodedValue) ||
                $decodedValue['value'] !== null,
        };
        if ($isInvalidValue) {
            throw new \UnexpectedValueException(\sprintf(
                'Неверный формат декодированного значения для контейнера захоронения: "%s".',
                $value,
            ));
        }
    }

    protected function preparePhpValueForJsonEncoding(mixed $value): array
    {
        /** @var BurialContainer $value */
        $container = $value->container();

        return match (true) {
            $container instanceof Coffin => [
                'type'  => Coffin::CLASS_SHORTCUT,
                'value' => [
                    'size'          => $container->size()->value(),
                    'shape'         => $container->shape()->value(),
                    'isNonStandard' => $container->isNonStandard(),
                ],
            ],
            $container instanceof Urn => [
                'type'  => Urn::CLASS_SHORTCUT,
                'value' => null,
            ],
        };
    }

    /**
     * @throws Exception       when the coffin size is invalid
     * @throws \LogicException when the coffin shape is not supported
     */
    protected function buildPhpValue(array $decodedValue): BurialContainer
    {
        $container = match ($decodedValue['type']) {
            Coffin::CLASS_SHORTCUT => new Coffin(
                new CoffinSize($decodedValue['value']['size']),
                new CoffinShape($decodedValue['value']['shape']),
                $decodedValue['value']['isNonStandard'],
            ),
            Urn::CLASS_SHORTCUT => new Urn(),
        };

        return new BurialContainer($container);
    }
}
