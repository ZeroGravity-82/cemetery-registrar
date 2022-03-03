<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Organization\JuristicPerson;

use Cemetery\Registrar\Domain\AbstractEntityCollection;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class JuristicPersonCollection extends AbstractEntityCollection
{
    /**
     * {@inheritdoc}
     */
    public function getSupportedEntityClass(): string
    {
        return JuristicPerson::class;
    }
}
