<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types\Burial;

use Cemetery\Registrar\Domain\Burial\CustomerId;
use Cemetery\Registrar\Domain\NaturalPerson\NaturalPersonId;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types\Burial\CustomerIdType;
use Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types\AbstractJsonTypeTest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class CustomerIdTypeTest extends AbstractJsonTypeTest
{
    protected string $className = CustomerIdType::class;
    protected string $typeName  = 'customer_id';

    public function setUp(): void
    {
        parent::setUp();

        $this->dbValue  = '{"type":"NaturalPersonId","value":"NP001"}';
        $this->phpValue = new CustomerId(new NaturalPersonId('NP001'));
    }
}
