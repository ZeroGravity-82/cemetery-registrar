<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types\Burial;

use Cemetery\Registrar\Domain\Burial\FuneralCompanyId;
use Cemetery\Registrar\Domain\Organization\JuristicPerson\JuristicPersonId;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types\Burial\FuneralCompanyIdType;
use Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types\AbstractPolymorphicIdTypeTest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class FuneralCompanyIdTypeTest extends AbstractPolymorphicIdTypeTest
{
    protected string $className = FuneralCompanyIdType::class;
    protected string $typeName  = 'funeral_company_id';

    public function setUp(): void
    {
        parent::setUp();

        $this->dbValue  = '{"type":"JuristicPersonId","value":"JP001"}';
        $this->phpValue = new FuneralCompanyId(new JuristicPersonId('JP001'));
    }
}
