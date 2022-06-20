<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Model\NaturalPerson;

use Cemetery\Registrar\Domain\Model\AggregateRootCollection;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class NaturalPersonCollection extends AggregateRootCollection
{
    /**
     * {@inheritdoc}
     */
    public function supportedClassName(): string
    {
        return NaturalPerson::class;
    }
}
