<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Domain\Burial\Doctrine\ORM;

use Cemetery\Registrar\Domain\Burial\Burial;
use Cemetery\Registrar\Domain\Burial\BurialCollection;
use Cemetery\Registrar\Domain\Burial\BurialId;
use Cemetery\Registrar\Domain\Burial\BurialRepositoryInterface;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class DoctrineORMBurialRepository implements BurialRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function save(Burial $burial): void
    {
        // TODO: Implement save() method.
    }

    /**
     * {@inheritdoc}
     */
    public function saveAll(BurialCollection $burials): void
    {
        // TODO: Implement saveAll() method.
    }

    /**
     * {@inheritdoc}
     */
    public function findById(BurialId $burialId): ?Burial
    {
        // TODO: Implement findById() method.
    }

    /**
     * {@inheritdoc}
     */
    public function remove(Burial $burial): void
    {
        // TODO: Implement remove() method.
    }

    /**
     * {@inheritdoc}
     */
    public function removeAll(BurialCollection $burials): void
    {
        // TODO: Implement removeAll() method.
    }
}
