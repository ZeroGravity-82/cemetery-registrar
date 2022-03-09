<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Organization;

use Cemetery\Registrar\Domain\Organization\OrganizationType;
use PHPUnit\Framework\TestCase;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class OrganizationTypeTest extends TestCase
{
    public function testItSuccessfullyCreated(): void
    {
        $organizationType = OrganizationType::soleProprietor();
        $this->assertSame(OrganizationType::SOLE_PROPRIETOR, $organizationType->getValue());
        $this->assertTrue($organizationType->isSoleProprietor());
        $this->assertFalse($organizationType->isJuristicPerson());

        $organizationType = OrganizationType::juristicPerson();
        $this->assertSame(OrganizationType::JURISTIC_PERSON, $organizationType->getValue());
        $this->assertFalse($organizationType->isSoleProprietor());
        $this->assertTrue($organizationType->isJuristicPerson());
    }

    public function testItFailsWithUnsupportedValue(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(\sprintf(
            'Неподдерживаемый тип организации "неподдерживаемый_тип", должен быть один из "%s", "%s".',
            OrganizationType::SOLE_PROPRIETOR,
            OrganizationType::JURISTIC_PERSON,
        ));
        new OrganizationType('неподдерживаемый_тип');
    }

    public function testItStringifyable(): void
    {
        $organizationType = OrganizationType::soleProprietor();

        $this->assertSame(OrganizationType::SOLE_PROPRIETOR, (string) $organizationType);
    }

    public function testItComparable(): void
    {
        $organizationTypeA = OrganizationType::soleProprietor();
        $organizationTypeB = OrganizationType::juristicPerson();
        $organizationTypeC = OrganizationType::soleProprietor();

        $this->assertFalse($organizationTypeA->isEqual($organizationTypeB));
        $this->assertTrue($organizationTypeA->isEqual($organizationTypeC));
        $this->assertFalse($organizationTypeB->isEqual($organizationTypeC));
    }
}
