<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Burial;

use Cemetery\Registrar\Domain\Burial\CustomerId;
use Cemetery\Registrar\Domain\NaturalPerson\NaturalPersonId;
use Cemetery\Registrar\Domain\Organization\JuristicPerson\JuristicPersonId;
use Cemetery\Registrar\Domain\Organization\SoleProprietor\SoleProprietorId;
use PHPUnit\Framework\TestCase;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class CustomerIdTest extends TestCase
{
    public function testItSuccessfullyCreated(): void
    {
        $customerId = new CustomerId(new NaturalPersonId('NP001'));
        $this->assertInstanceOf(NaturalPersonId::class, $customerId->getId());
        $this->assertSame('NP001', $customerId->getId()->getValue());

        $customerId = new CustomerId(new JuristicPersonId('JP001'));
        $this->assertInstanceOf(JuristicPersonId::class, $customerId->getId());
        $this->assertSame('JP001', $customerId->getId()->getValue());

        $customerId = new CustomerId(new SoleProprietorId('SP001'));
        $this->assertInstanceOf(SoleProprietorId::class, $customerId->getId());
        $this->assertSame('SP001', $customerId->getId()->getValue());
    }

    public function testItStringifyable(): void
    {
        $customerId = new CustomerId(new NaturalPersonId('NP001'));
        $this->assertSame(\json_encode(['value' => 'NP001', 'type' => 'NaturalPersonId']), (string) $customerId);

        $customerId = new CustomerId(new JuristicPersonId('JP001'));
        $this->assertSame(\json_encode(['value' => 'JP001', 'type' => 'JuristicPersonId']), (string) $customerId);

        $customerId = new CustomerId(new SoleProprietorId('SP001'));
        $this->assertSame(\json_encode(['value' => 'SP001', 'type' => 'SoleProprietorId']), (string) $customerId);
    }
    
    public function testItComparable(): void
    {
        $customerIdA = new CustomerId(new NaturalPersonId('ID001'));
        $customerIdB = new CustomerId(new SoleProprietorId('ID001'));
        $customerIdC = new CustomerId(new NaturalPersonId('ID002'));
        $customerIdD = new CustomerId(new JuristicPersonId('ID003'));
        $customerIdE = new CustomerId(new NaturalPersonId('ID001'));
        
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
