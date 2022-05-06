<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\Burial;

use Cemetery\Registrar\Domain\Burial\FuneralCompanyId;
use Cemetery\Registrar\Domain\Organization\JuristicPerson\JuristicPersonId;
use Cemetery\Registrar\Domain\Organization\SoleProprietor\SoleProprietorId;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\Burial\FuneralCompanyIdType;
use Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\EntityMaskingIdTypeTest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class FuneralCompanyIdTypeTest extends EntityMaskingIdTypeTest
{
    protected string $className         = FuneralCompanyIdType::class;
    protected string $typeName          = 'funeral_company_id';
    protected string $phpValueClassName = FuneralCompanyId::class;

    protected function getConversionTests(): array
    {
        return [
            // database value, PHP value
            ['{"class":"JuristicPersonId","value":"JP001"}', new FuneralCompanyId(new JuristicPersonId('JP001'))],
            ['{"class":"SoleProprietorId","value":"SP001"}', new FuneralCompanyId(new SoleProprietorId('SP001'))],
        ];
    }
}
