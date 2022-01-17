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
    public function testItSuccessfullyCreatedForNaturalPerson(): void
    {
        $customerType = new CustomerType(CustomerType::NATURAL_PERSON);

        $this->assertSame(CustomerType::NATURAL_PERSON, $customerType->getValue());
        $this->assertTrue($customerType->isNaturalPerson());
        $this->assertFalse($customerType->isSoleProprietor());
        $this->assertFalse($customerType->isJuristicPerson());
    }

    public function testItFailsWithUnsupportedValue(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Unsupported customer type "wrong_type", expected to be one of "natural_person", "sole_proprietor", "juristic_person".'
        );
        new CustomerType('wrong_type');
    }

    public function testItStringifyable(): void
    {
        $customerType = new CustomerType(CustomerType::NATURAL_PERSON);

        $this->assertSame(CustomerType::NATURAL_PERSON, (string) $customerType);
    }

    public function testItComparable(): void
    {
        $customerTypeNaturalPersonA = new CustomerType(CustomerType::NATURAL_PERSON);
        $customerTypeSoleProprietor = new CustomerType(CustomerType::SOLE_PROPRIETOR);
        $customerTypeJuristicPerson = new CustomerType(CustomerType::JURISTIC_PERSON);
        $customerTypeNaturalPersonB = new CustomerType(CustomerType::NATURAL_PERSON);

        $this->assertFalse($customerTypeNaturalPersonA->isEqual($customerTypeSoleProprietor));
        $this->assertFalse($customerTypeNaturalPersonA->isEqual($customerTypeJuristicPerson));
        $this->assertTrue($customerTypeNaturalPersonA->isEqual($customerTypeNaturalPersonB));
        $this->assertFalse($customerTypeSoleProprietor->isEqual($customerTypeJuristicPerson));
        $this->assertFalse($customerTypeSoleProprietor->isEqual($customerTypeNaturalPersonB));
        $this->assertFalse($customerTypeJuristicPerson->isEqual($customerTypeNaturalPersonB));
    }
}
