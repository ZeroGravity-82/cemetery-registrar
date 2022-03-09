<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Burial;

use Cemetery\Registrar\Domain\Burial\CustomerId;
use Cemetery\Registrar\Domain\Burial\CustomerType;
use PHPUnit\Framework\TestCase;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class CustomerIdTest extends TestCase
{
    public function testItSuccessfullyCreated(): void
    {
        $customerType = CustomerType::naturalPerson();
        $customerId   = new CustomerId('777', $customerType);
        $this->assertSame('777', $customerId->getValue());
        $this->assertSame($customerType, $customerId->getType());
    }

    public function testItStringifyable(): void
    {
        $customerType = CustomerType::naturalPerson();
        $customerId   = new CustomerId('777', $customerType);
        $this->assertSame(CustomerType::NATURAL_PERSON . '.' . '777', (string) $customerId);
    }
    
    public function testItComparable(): void
    {
        $customerIdA = new CustomerId('777', CustomerType::naturalPerson());
        $customerIdB = new CustomerId('777', CustomerType::soleProprietor());
        $customerIdC = new CustomerId('888', CustomerType::naturalPerson());
        $customerIdD = new CustomerId('999', CustomerType::juristicPerson());
        $customerIdE = new CustomerId('777', CustomerType::naturalPerson());
        
        $this->assertFalse($customerIdA->isEqual($customerIdB));
        $this->assertFalse($customerIdA->isEqual($customerIdC));
        $this->assertFalse($customerIdA->isEqual($customerIdD));
        $this->assertTrue($customerIdA->isEqual($customerIdE));
        $this->assertFalse($customerIdB->isEqual($customerIdC));
        $this->assertFalse($customerIdB->isEqual($customerIdD));
        $this->assertFalse($customerIdB->isEqual($customerIdE));
        $this->assertFalse($customerIdC->isEqual($customerIdD));
        $this->assertFalse($customerIdC->isEqual($customerIdE));
        $this->assertFalse($customerIdD->isEqual($customerIdE));
    }
}
