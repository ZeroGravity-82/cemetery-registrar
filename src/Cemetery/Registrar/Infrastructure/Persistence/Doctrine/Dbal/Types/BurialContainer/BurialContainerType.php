<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\BurialContainer;

use Cemetery\Registrar\Domain\BurialContainer\BurialContainer;
use Cemetery\Registrar\Domain\BurialContainer\Coffin;
use Cemetery\Registrar\Domain\BurialContainer\CoffinShape;
use Cemetery\Registrar\Domain\BurialContainer\CoffinSize;
use Cemetery\Registrar\Domain\BurialContainer\Urn;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\CustomJsonType;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class BurialContainerType extends CustomJsonType
{
    /**
     * {@inheritdoc}
     */
    protected string $className = BurialContainer::class;

    /**
     * {@inheritdoc}
     */
    protected string $typeName = 'burial_container';

    /**
     * {@inheritdoc}
     */
    protected function assertValidDecodedValue(mixed $decodedValue, mixed $value): void
    {
        $isInvalidValue = false;
        if (!\array_key_exists('type', $decodedValue)) {
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
            throw new \RuntimeException(\sprintf('Неверный формат для контейнера захоронения: "%s".', $value));
        }
    }

    /**
     * {@inheritdoc}
     */
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
     * {@inheritdoc}
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
