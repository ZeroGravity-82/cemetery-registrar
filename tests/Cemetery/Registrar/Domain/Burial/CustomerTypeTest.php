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
            'Неподдерживаемый тип заказчика захоронения "неподдерживаемый_тип", должен быть один из "%s", "%s", "%s".',
            CustomerType::NATURAL_PERSON,
            CustomerType::SOLE_PROPRIETOR,
            CustomerType::JURISTIC_PERSON,
        ));
        new CustomerType('неподдерживаемый_тип');
    }

    public function testItStringifyable(): void
    {
        $customerType = CustomerType::naturalPerson();

        $this->assertSame(CustomerType::NATURAL_PERSON, (string) $customerType);
    }

    public function testItComparable(): void
    {
        $customerTypeA = CustomerType::naturalPerson();
        $customerTypeB = CustomerType::soleProprietor();
        $customerTypeC = CustomerType::juristicPerson();
        $customerTypeD = CustomerType::naturalPerson();

        $this->assertFalse($customerTypeA->isEqual($customerTypeB));
        $this->assertFalse($customerTypeA->isEqual($customerTypeC));
        $this->assertTrue($customerTypeA->isEqual($customerTypeD));
        $this->assertFalse($customerTypeB->isEqual($customerTypeC));
        $this->assertFalse($customerTypeB->isEqual($customerTypeD));
        $this->assertFalse($customerTypeC->isEqual($customerTypeD));
    }
}
