<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types;

use Cemetery\Registrar\Domain\Burial\CustomerType;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types\CustomerTypeType;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class CustomerTypeTypeTest extends AbstractStringTypeTest
{
    protected string $className = CustomerTypeType::class;

    protected string $typeName = 'customer_type';

    public function setUp(): void
    {
        parent::setUp();

        $this->dbValue  = 'natural_person';
        $this->phpValue = new CustomerType('natural_person');
    }
}
