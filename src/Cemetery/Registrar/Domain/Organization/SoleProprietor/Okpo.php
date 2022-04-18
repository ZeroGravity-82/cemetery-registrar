<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Organization\SoleProprietor;

use Cemetery\Registrar\Domain\Organization\AbstractOkpo;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class Okpo extends AbstractOkpo
{
    private const OKPO_LENGTH = 10;

    /**
     * {@inheritdoc}
     */
    protected function okpoLength(): int
    {
        return self::OKPO_LENGTH;
    }

    /**
     * {@inheritdoc}
     */
    protected function coefficientsForTheFirstCheck(): array
    {
        return [1, 2, 3, 4, 5, 6, 7, 8, 9];
    }

    /**
     * {@inheritdoc}
     */
    protected function coefficientsForTheSecondCheck(): array
    {
        return [3, 4, 5, 6, 7, 8, 9, 10, 1];
    }
}
