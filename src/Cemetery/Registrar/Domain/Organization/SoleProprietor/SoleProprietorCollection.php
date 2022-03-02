<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Organization\SoleProprietor;

use Cemetery\Registrar\Domain\AbstractEntityCollection;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class SoleProprietorCollection extends AbstractEntityCollection
{
    /**
     * {@inheritdoc}
     */
    public function getSupportedEntityClass(): string
    {
        return SoleProprietor::class;
    }
}
