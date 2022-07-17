<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite;

use Cemetery\Registrar\Domain\Model\AggregateRoot;
use Cemetery\Registrar\Domain\Model\Repository;
use Cemetery\Registrar\Domain\Model\RepositoryValidator;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class CemeteryBlockRepositoryValidator implements RepositoryValidator
{
    /**
     * @param GraveSiteRepository $graveSiteRepo
     */
    public function __construct(
        private readonly GraveSiteRepository $graveSiteRepo,
    ) {}

    /**
     * {@inheritdoc}
     */
    public function assertUnique(AggregateRoot $aggregateRoot, Repository $repository): void
    {
        /** @var CemeteryBlock           $aggregateRoot */
        /** @var CemeteryBlockRepository $repository */
        if ($repository->doesSameNameAlreadyUsed($aggregateRoot)) {
            throw new \RuntimeException(\sprintf(
                'Квартал "%s" уже существует.',
                $aggregateRoot->name()->value(),
            ));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function assertReferencesNotBroken(AggregateRoot $aggregateRoot, Repository $repository): void
    {
        // Cemetery block entity has no references
    }

    /**
     * {@inheritdoc}
     */
    public function assertRemovable(AggregateRoot $aggregateRoot, Repository $repository): void
    {
        /** @var CemeteryBlock           $aggregateRoot */
        /** @var CemeteryBlockRepository $repository */
        $relatedGraveSiteCount = $this->graveSiteRepo->countByCemeteryBlockId($aggregateRoot->id());
        if ($relatedGraveSiteCount > 0) {
            throw new \RuntimeException(\sprintf(
                'Квартал "%s" не может быть удалён, т.к. он указан для %d участков.',
                $aggregateRoot->name()->value(),
                $relatedGraveSiteCount,
            ));
        }
    }
}
