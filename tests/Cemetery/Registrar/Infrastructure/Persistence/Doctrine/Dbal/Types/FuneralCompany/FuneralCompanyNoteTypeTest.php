<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\FuneralCompany;

use Cemetery\Registrar\Domain\Model\FuneralCompany\FuneralCompanyNote;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\FuneralCompany\FuneralCompanyNoteType;
use Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\CustomStringTypeTest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class FuneralCompanyNoteTypeTest extends CustomStringTypeTest
{
    protected string $className = FuneralCompanyNoteType::class;
    protected string $typeName  = 'funeral_company_note';

    public function setUp(): void
    {
        parent::setUp();

        $this->dbValue  = 'Примечание 1';
        $this->phpValue = new FuneralCompanyNote('Примечание 1');
    }
}
