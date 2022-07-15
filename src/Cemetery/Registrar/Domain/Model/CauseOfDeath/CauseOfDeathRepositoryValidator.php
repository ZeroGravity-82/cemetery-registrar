<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Model\CauseOfDeath;

use Cemetery\Registrar\Domain\Model\AggregateRoot;
use Cemetery\Registrar\Domain\Model\Repository;
use Cemetery\Registrar\Domain\Model\RepositoryValidator;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class CauseOfDeathRepositoryValidator implements RepositoryValidator
{
    /**
     * {@inheritdoc}
     */
    public function validateUniqueness(AggregateRoot $aggregateRoot, Repository $repository): void
    {

    }

    /**
     * {@inheritdoc}
     */
    public function validateReferences(AggregateRoot $aggregateRoot, Repository $repository): void
    {

    }

    /**
     * {@inheritdoc}
     */
    public function validateInverseReferences(AggregateRoot $aggregateRoot, Repository $repository): void
    {

    }
}
