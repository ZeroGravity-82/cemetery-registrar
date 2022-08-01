<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Model\Organization\SoleProprietor;

use Cemetery\Registrar\Domain\Model\Organization\AbstractOgrn;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class Ogrnip extends AbstractOgrn
{
    private const OGRN_NAME = 'ОГРНИП';

    private const OGRN_LENGTH = 15;

    protected function ogrnName(): string
    {
        return self::OGRN_NAME;
    }

    protected function ogrnLength(): int
    {
        return self::OGRN_LENGTH;
    }
}
