<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Orm\Repository;

use Doctrine\ORM\EntityManagerInterface;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
abstract class DoctrineOrmRepository
{
    /**
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        protected readonly EntityManagerInterface $entityManager,
    ) {}
}
