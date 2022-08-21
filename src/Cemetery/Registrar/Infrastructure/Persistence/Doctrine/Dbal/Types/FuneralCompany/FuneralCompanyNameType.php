<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\FuneralCompany;

use Cemetery\Registrar\Domain\Model\FuneralCompany\FuneralCompanyName;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\CustomStringType;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class FuneralCompanyNameType extends CustomStringType
{
    protected string $className = FuneralCompanyName::class;
    protected string $typeName  = 'funeral_company_name';
}
