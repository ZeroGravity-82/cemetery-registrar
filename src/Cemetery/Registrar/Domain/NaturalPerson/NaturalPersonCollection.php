<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\NaturalPerson;

use Cemetery\Registrar\Domain\AbstractEntityCollection;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class NaturalPersonCollection extends AbstractEntityCollection
{
    /**
     * {@inheritdoc}
     */
    public function getSupportedEntityClass(): string
    {
        return NaturalPerson::class;
    }
}
