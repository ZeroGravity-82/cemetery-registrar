<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\NaturalPerson;

use Cemetery\Registrar\Domain\EntityCollection;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class NaturalPersonCollection extends EntityCollection
{
    /**
     * {@inheritdoc}
     */
    public function supportedEntityClassName(): string
    {
        return NaturalPerson::class;
    }
}
