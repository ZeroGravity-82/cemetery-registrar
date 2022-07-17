<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Model\BurialPlace\ColumbariumNiche;

use Cemetery\Registrar\Domain\Model\AggregateRoot;
use Cemetery\Registrar\Domain\Model\Repository;
use Cemetery\Registrar\Domain\Model\RepositoryValidator;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class ColumbariumRepositoryValidator implements RepositoryValidator
{
    /**
     * @param ColumbariumNicheRepository $columbariumNicheRepo
     */
    public function __construct(
        private readonly ColumbariumNicheRepository $columbariumNicheRepo,
    ) {}

    /**
     * {@inheritdoc}
     */
    public function assertUnique(AggregateRoot $aggregateRoot, Repository $repository): void
    {
        /** @var Columbarium           $aggregateRoot */
        /** @var ColumbariumRepository $repository */
        if ($repository->doesSameNameAlreadyUsed($aggregateRoot)) {
            throw new \RuntimeException(\sprintf(
                'Колумбарий "%s" уже существует.',
                $aggregateRoot->name()->value(),
            ));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function assertReferencesNotBroken(AggregateRoot $aggregateRoot, Repository $repository): void
    {
        // Columbarium entity has no references
    }

    /**
     * {@inheritdoc}
     */
    public function assertRemovable(AggregateRoot $aggregateRoot, Repository $repository): void
    {
        /** @var Columbarium           $aggregateRoot */
        /** @var ColumbariumRepository $repository */
        $relatedColumbariumNicheCount = $this->columbariumNicheRepo->countByColumbariumId($aggregateRoot->id());
        if ($relatedColumbariumNicheCount > 0) {
            throw new \RuntimeException(\sprintf(
                'Колумбарий "%s" не может быть удалён, т.к. он указан для %d колумбарных ниш.',
                $aggregateRoot->name()->value(),
                $relatedColumbariumNicheCount,
            ));
        }
    }
}
