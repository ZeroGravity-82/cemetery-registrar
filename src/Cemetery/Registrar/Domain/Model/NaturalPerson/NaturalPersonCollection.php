<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Model\NaturalPerson;

use Cemetery\Registrar\Domain\Model\EntityCollection;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class NaturalPersonCollection extends EntityCollection
{
    /**
     * {@inheritdoc}
     */
    public function supportedEntityClassName(): string
    {
        return NaturalPerson::class;
    }
}
