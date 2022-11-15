<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Orm\Repository;

use Cemetery\Registrar\Domain\Model\FuneralCompany\FuneralCompany;
use Cemetery\Registrar\Domain\Model\FuneralCompany\FuneralCompanyCollection;
use Cemetery\Registrar\Domain\Model\FuneralCompany\FuneralCompanyId;
use Cemetery\Registrar\Domain\Model\FuneralCompany\FuneralCompanyRepositoryInterface;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class DoctrineOrmFuneralCompanyRepository extends DoctrineOrmRepository implements FuneralCompanyRepositoryInterface
{
    protected function supportedAggregateRootClassName(): string
    {
        return FuneralCompany::class;
    }

    protected function supportedAggregateRootIdClassName(): string
    {
        return FuneralCompanyId::class;
    }

    protected function supportedAggregateRootCollectionClassName(): string
    {
        return FuneralCompanyCollection::class;
    }
}
