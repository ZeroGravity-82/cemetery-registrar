<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types\Organization\SoleProprietor;

use Cemetery\Registrar\Domain\Organization\SoleProprietor\Okpo;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types\Organization\SoleProprietor\OkpoType;
use Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types\CustomStringTypeTest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class OkpoTypeTest extends CustomStringTypeTest
{
    protected string $className = OkpoType::class;
    protected string $typeName  = 'sole_proprietor_okpo';

    public function setUp(): void
    {
        parent::setUp();

        $this->dbValue  = '0148543122';
        $this->phpValue = new Okpo('0148543122');
    }
}
