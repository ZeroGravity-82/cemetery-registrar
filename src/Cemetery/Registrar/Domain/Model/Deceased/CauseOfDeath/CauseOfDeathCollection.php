<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Model\Deceased\CauseOfDeath;

use Cemetery\Registrar\Domain\Model\AggregateRootCollection;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class CauseOfDeathCollection extends AggregateRootCollection
{
    /**
     * {@inheritdoc}
     */
    public function supportedClassName(): string
    {
        return CauseOfDeath::class;
    }
}
