<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Organization\SoleProprietor;

use Cemetery\Registrar\Domain\Organization\AbstractOgrn;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class Ogrnip extends AbstractOgrn
{
    private const OGRN_NAME = 'ОГРНИП';

    private const OGRN_LENGTH = 15;

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
