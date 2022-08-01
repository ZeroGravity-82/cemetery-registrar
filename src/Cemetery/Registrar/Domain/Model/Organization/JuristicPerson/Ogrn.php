<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Model\Organization\JuristicPerson;

use Cemetery\Registrar\Domain\Model\Organization\AbstractOgrn;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class Ogrn extends AbstractOgrn
{
    private const OGRN_NAME = 'ОГРН';

    private const OGRN_LENGTH = 13;

    protected function ogrnName(): string
    {
        return self::OGRN_NAME;
    }

    protected function ogrnLength(): int
    {
        return self::OGRN_LENGTH;
    }
}
