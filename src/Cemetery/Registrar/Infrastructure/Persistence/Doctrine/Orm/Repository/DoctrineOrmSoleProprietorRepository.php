<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Orm\Repository;

use Cemetery\Registrar\Domain\Model\Organization\SoleProprietor\SoleProprietor;
use Cemetery\Registrar\Domain\Model\Organization\SoleProprietor\SoleProprietorCollection;
use Cemetery\Registrar\Domain\Model\Organization\SoleProprietor\SoleProprietorId;
use Cemetery\Registrar\Domain\Model\Organization\SoleProprietor\SoleProprietorRepository;
use Cemetery\Registrar\Domain\Model\Organization\SoleProprietor\SoleProprietorRepositoryValidator;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class DoctrineOrmSoleProprietorRepository extends DoctrineOrmRepository implements SoleProprietorRepository
{
    /**
     * @param EntityManagerInterface            $entityManager
     * @param SoleProprietorRepositoryValidator $repositoryValidator
     */
    public function __construct(
        EntityManagerInterface            $entityManager,
        SoleProprietorRepositoryValidator $repositoryValidator,
    ) {
        parent::__construct($entityManager, $repositoryValidator);
    }


    /**
     * {@inheritdoc}
     */
    public function supportedAggregateRootClassName(): string
    {
        return SoleProprietor::class;
    }

    /**
     * {@inheritdoc}
     */
    public function supportedAggregateRootIdClassName(): string
    {
        return SoleProprietorId::class;
    }

    /**
     * {@inheritdoc}
     */
    public function supportedAggregateRootCollectionClassName(): string
    {
        return SoleProprietorCollection::class;
    }
}
