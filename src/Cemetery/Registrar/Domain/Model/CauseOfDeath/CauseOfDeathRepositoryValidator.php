<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Model\CauseOfDeath;

use Cemetery\Registrar\Domain\Model\AggregateRoot;
use Cemetery\Registrar\Domain\Model\NaturalPerson\NaturalPersonRepository;
use Cemetery\Registrar\Domain\Model\Repository;
use Cemetery\Registrar\Domain\Model\RepositoryValidator;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class CauseOfDeathRepositoryValidator implements RepositoryValidator
{
    /**
     * @param NaturalPersonRepository $naturalPersonRepo
     */
    public function __construct(
        private readonly NaturalPersonRepository $naturalPersonRepo,
    ) {}

    /**
     * {@inheritdoc}
     */
    public function assertUnique(AggregateRoot $aggregateRoot, Repository $repository): void
    {
        /** @var CauseOfDeath           $aggregateRoot */
        /** @var CauseOfDeathRepository $repository */
        if ($repository->doesSameNameAlreadyUsed($aggregateRoot)) {
            throw new \RuntimeException(\sprintf(
                'Причина смерти "%s" уже существует.',
                $aggregateRoot->name()->value(),
            ));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function assertReferencesNotBroken(AggregateRoot $aggregateRoot, Repository $repository): void
    {
        // Cause of death entity has no references
    }

    /**
     * {@inheritdoc}
     */
    public function assertRemovable(AggregateRoot $aggregateRoot, Repository $repository): void
    {
        /** @var CauseOfDeath           $aggregateRoot */
        /** @var CauseOfDeathRepository $repository */
        $relatedNaturalPersonCount = $this->naturalPersonRepo->countByCauseOfDeathId($aggregateRoot->id());
        if ($relatedNaturalPersonCount > 0) {
            throw new \RuntimeException(\sprintf(
                'Причина смерти "%s" не может быть удалена, т.к. она указана для %d умерших.',
                $aggregateRoot->name()->value(),
                $relatedNaturalPersonCount,
            ));
        }
    }
}
