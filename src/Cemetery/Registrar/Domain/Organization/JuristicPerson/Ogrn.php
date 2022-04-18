<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Organization\JuristicPerson;

use Cemetery\Registrar\Domain\Organization\AbstractOgrn;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class Ogrn extends AbstractOgrn
{
    private const OGRN_NAME = 'ОГРН';

    private const OGRN_LENGTH = 13;

    /**
     * {@inheritdoc}
     */
    protected function ogrnName(): string
    {
        return self::OGRN_NAME;
    }

    /**
     * {@inheritdoc}
     */
    protected function ogrnLength(): int
    {
        return self::OGRN_LENGTH;
    }
}
