<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Model\BurialPlace\ColumbariumNiche;

use Cemetery\Registrar\Domain\Model\AggregateRoot;
use Cemetery\Registrar\Domain\Model\Burial\BurialRepository;
use Cemetery\Registrar\Domain\Model\Repository;
use Cemetery\Registrar\Domain\Model\RepositoryValidator;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class ColumbariumNicheRepositoryValidator implements RepositoryValidator
{
    /**
     * @param ColumbariumRepository $columbariumRepo
     * @param BurialRepository      $burialRepo
     */
    public function __construct(
        private readonly ColumbariumRepository $columbariumRepo,
        private readonly BurialRepository      $burialRepo,
    ) {}

    /**
     * {@inheritdoc}
     */
    public function assertUnique(AggregateRoot $aggregateRoot, Repository $repository): void
    {
        /** @var ColumbariumNiche           $aggregateRoot */
        /** @var ColumbariumNicheRepository $repository */
        if ($repository->doesSameNicheNumberAlreadyUsed($aggregateRoot)) {
            throw new \RuntimeException(\sprintf(
                'Колумбарная ниша "%s" уже существует.',
                $aggregateRoot->nicheNumber()->value(),
            ));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function assertReferencesNotBroken(AggregateRoot $aggregateRoot, Repository $repository): void
    {
        /** @var ColumbariumNiche           $aggregateRoot */
        /** @var ColumbariumNicheRepository $repository */
        if (!$this->columbariumRepo->doesExistById($aggregateRoot->columbariumId())) {
            throw new \RuntimeException(\sprintf(
                'Колумбарий с ID "%s" не существует.',
                $aggregateRoot->columbariumId()->value(),
            ));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function assertRemovable(AggregateRoot $aggregateRoot, Repository $repository): void
    {
        /** @var ColumbariumNiche           $aggregateRoot */
        /** @var ColumbariumNicheRepository $repository */
        $relatedBurialCount = $this->burialRepo->countByColumbariumNicheId($aggregateRoot->id());
        if ($relatedBurialCount > 0) {
            throw new \RuntimeException(\sprintf(
                'Колумбарная ниша "%s" не может быть удалена, т.к. она указана для %d захоронений.',
                $aggregateRoot->nicheNumber()->value(),
                $relatedBurialCount,
            ));
        }
    }
}
