<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Organization\JuristicPerson;

use Cemetery\Registrar\Domain\Organization\AbstractOkpo;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class Okpo extends AbstractOkpo
{
    private const OKPO_LENGTH = 8;

    /**
     * {@inheritdoc}
     */
    protected function getOkpoLength(): int
    {
        return self::OKPO_LENGTH;
    }

    /**
     * {@inheritdoc}
     */
    protected function getCoefficientsForTheFirstCheck(): array
    {
        return [1, 2, 3, 4, 5, 6, 7];
    }

    /**
     * {@inheritdoc}
     */
    protected function getCoefficientsForTheSecondCheck(): array
    {
        return [3, 4, 5, 6, 7, 8, 9];
    }
}
