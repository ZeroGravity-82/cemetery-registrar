<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\FuneralCompany;

use Cemetery\Registrar\Domain\FuneralCompany\FuneralCompanyId;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\CustomStringType;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class FuneralCompanyIdType extends CustomStringType
{
    /**
     * {@inheritdoc}
     */
    protected string $className = FuneralCompanyId::class;

    /**
     * {@inheritdoc}
     */
    protected string $typeName  = 'funeral_company_id';
}
