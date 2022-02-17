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
    protected function getOgrnName(): string
    {
        return self::OGRN_NAME;
    }

    /**
     * {@inheritdoc}
     */
    protected function getOgrnLength(): int
    {
        return self::OGRN_LENGTH;
    }
}
