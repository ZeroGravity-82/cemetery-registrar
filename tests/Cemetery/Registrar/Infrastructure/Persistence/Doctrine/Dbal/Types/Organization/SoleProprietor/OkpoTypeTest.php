<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\Organization\SoleProprietor;

use Cemetery\Registrar\Domain\Model\Organization\SoleProprietor\Okpo;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\Organization\SoleProprietor\OkpoType;
use Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\AbstractCustomStringTypeTest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class OkpoTypeTest extends AbstractCustomStringTypeTest
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
