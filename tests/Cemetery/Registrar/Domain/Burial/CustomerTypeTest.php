<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Burial;

use Cemetery\Registrar\Domain\Burial\CustomerType;
use PHPUnit\Framework\TestCase;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class CustomerTypeTest extends TestCase
{
    public function testItSuccessfullyCreated(): void
    {
        $customerType = new CustomerType(CustomerType::NATURAL_PERSON);
        $this->assertSame(CustomerType::NATURAL_PERSON, $customerType->getValue());
        $this->assertTrue($customerType->isNaturalPerson());
        $this->assertFalse($customerType->isSoleProprietor());
        $this->assertFalse($customerType->isJuristicPerson());

        $customerType = CustomerType::juristicPerson();
        $this->assertSame(CustomerType::JURISTIC_PERSON, $customerType->getValue());
        $this->assertFalse($customerType->isNaturalPerson());
        $this->assertFalse($customerType->isSoleProprietor());
        $this->assertTrue($customerType->isJuristicPerson());
    }

    public function testItFailsWithUnsupportedValue(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(\sprintf(
            'Unsupported customer type "wrong_type", expected to be one of "%s", "%s", "%s".',
            CustomerType::NATURAL_PERSON,
            CustomerType::SOLE_PROPRIETOR,
            CustomerType::JURISTIC_PERSON,
        ));
        new CustomerType('wrong_type');
    }

    public function testItStringifyable(): void
    {
        $customerType = CustomerType::naturalPerson();

        $this->assertSame(CustomerType::NATURAL_PERSON, (string) $customerType);
    }

    public function testItComparable(): void
    {
        $customerTypeNaturalPersonA = CustomerType::naturalPerson();
        $customerTypeSoleProprietor = CustomerType::soleProprietor();
        $customerTypeJuristicPerson = CustomerType::juristicPerson();
        $customerTypeNaturalPersonB = CustomerType::naturalPerson();

        $this->assertFalse($customerTypeNaturalPersonA->isEqual($customerTypeSoleProprietor));
        $this->assertFalse($customerTypeNaturalPersonA->isEqual($customerTypeJuristicPerson));
        $this->assertTrue($customerTypeNaturalPersonA->isEqual($customerTypeNaturalPersonB));
        $this->assertFalse($customerTypeSoleProprietor->isEqual($customerTypeJuristicPerson));
        $this->assertFalse($customerTypeSoleProprietor->isEqual($customerTypeNaturalPersonB));
        $this->assertFalse($customerTypeJuristicPerson->isEqual($customerTypeNaturalPersonB));
    }
}
