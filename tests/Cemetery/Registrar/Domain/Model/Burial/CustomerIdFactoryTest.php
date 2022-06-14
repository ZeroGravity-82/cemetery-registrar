<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Model\Burial;

use Cemetery\Registrar\Domain\Model\Burial\CustomerId;
use Cemetery\Registrar\Domain\Model\Burial\CustomerIdFactory;
use Cemetery\Registrar\Domain\Model\NaturalPerson\NaturalPersonId;
use Cemetery\Registrar\Domain\Model\Organization\JuristicPerson\JuristicPersonId;
use Cemetery\Registrar\Domain\Model\Organization\SoleProprietor\SoleProprietorId;
use PHPUnit\Framework\TestCase;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class CustomerIdFactoryTest extends TestCase
{
    private CustomerIdFactory $customerIdFactory;

    public function setUp(): void
    {
        $this->customerIdFactory = new CustomerIdFactory();
    }

    public function testItCreatesCustomerId(): void
    {
        $customerId = $this->customerIdFactory->create(new NaturalPersonId('ID001'));
        $this->assertInstanceOf(CustomerId::class, $customerId);
        $this->assertInstanceOf(NaturalPersonId::class, $customerId->id());
        $this->assertSame('ID001', $customerId->id()->value());

        $customerId = $this->customerIdFactory->create(new SoleProprietorId('ID002'));
        $this->assertInstanceOf(CustomerId::class, $customerId);
        $this->assertInstanceOf(SoleProprietorId::class, $customerId->id());
        $this->assertSame('ID002', $customerId->id()->value());

        $customerId = $this->customerIdFactory->create(new JuristicPersonId('ID003'));
        $this->assertInstanceOf(CustomerId::class, $customerId);
        $this->assertInstanceOf(JuristicPersonId::class, $customerId->id());
        $this->assertSame('ID003', $customerId->id()->value());
    }

    public function testItCreatesCustomerIdForNaturalPerson(): void
    {
        $customerId = $this->customerIdFactory->createForNaturalPerson('ID004');
        $this->assertInstanceOf(CustomerId::class, $customerId);
        $this->assertInstanceOf(NaturalPersonId::class, $customerId->id());
        $this->assertSame('ID004', $customerId->id()->value());
    }

    public function testItCreatesCustomerIdForSoleProprietor(): void
    {
        $customerId = $this->customerIdFactory->createForSoleProprietor('ID005');
        $this->assertInstanceOf(CustomerId::class, $customerId);
        $this->assertInstanceOf(SoleProprietorId::class, $customerId->id());
        $this->assertSame('ID005', $customerId->id()->value());
    }

    public function testItCreatesCustomerIdForJuristicPerson(): void
    {
        $customerId = $this->customerIdFactory->createForJuristicPerson('ID006');
        $this->assertInstanceOf(CustomerId::class, $customerId);
        $this->assertInstanceOf(JuristicPersonId::class, $customerId->id());
        $this->assertSame('ID006', $customerId->id()->value());
    }
}
