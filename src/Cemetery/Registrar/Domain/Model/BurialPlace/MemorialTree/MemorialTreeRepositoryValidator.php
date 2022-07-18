<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Model\BurialPlace\MemorialTree;

use Cemetery\Registrar\Domain\Model\AggregateRoot;
use Cemetery\Registrar\Domain\Model\Burial\BurialRepository;
use Cemetery\Registrar\Domain\Model\Repository;
use Cemetery\Registrar\Domain\Model\RepositoryValidator;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class MemorialTreeRepositoryValidator implements RepositoryValidator
{
    /**
     * @param BurialRepository $burialRepo
     */
    public function __construct(
        private readonly BurialRepository $burialRepo,
    ) {}

    /**
     * {@inheritdoc}
     */
    public function assertUnique(AggregateRoot $aggregateRoot, Repository $repository): void
    {
        /** @var MemorialTree           $aggregateRoot */
        /** @var MemorialTreeRepository $repository */
        if ($repository->doesSameTreeNumberAlreadyUsed($aggregateRoot)) {
            throw new \RuntimeException(\sprintf(
                'Памятное дерево "%s" уже существует.',
                $aggregateRoot->treeNumber()->value(),
            ));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function assertReferencesNotBroken(AggregateRoot $aggregateRoot, Repository $repository): void
    {
        // Memorial tree entity has no references
    }

    /**
     * {@inheritdoc}
     */
    public function assertRemovable(AggregateRoot $aggregateRoot, Repository $repository): void
    {
        /** @var MemorialTree           $aggregateRoot */
        /** @var MemorialTreeRepository $repository */
        $relatedBurialCount = $this->burialRepo->countByMemorialTreeId($aggregateRoot->id());
        if ($relatedBurialCount > 0) {
            throw new \RuntimeException(\sprintf(
                'Памятное дерево "%s" не может быть удалено, т.к. оно указано для %d захоронений.',
                $aggregateRoot->treeNumber()->value(),
                $relatedBurialCount,
            ));
        }
    }
}
