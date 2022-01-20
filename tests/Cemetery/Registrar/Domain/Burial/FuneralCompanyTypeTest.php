<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Burial;

use Cemetery\Registrar\Domain\Burial\FuneralCompanyType;
use PHPUnit\Framework\TestCase;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class FuneralCompanyTypeTest extends TestCase
{
    public function testItSuccessfullyCreated(): void
    {
        $funeralCompanyType = FuneralCompanyType::soleProprietor();
        $this->assertSame(FuneralCompanyType::SOLE_PROPRIETOR, $funeralCompanyType->getValue());
        $this->assertTrue($funeralCompanyType->isSoleProprietor());
        $this->assertFalse($funeralCompanyType->isJuristicPerson());

        $funeralCompanyType = new FuneralCompanyType(FuneralCompanyType::JURISTIC_PERSON);
        $this->assertSame(FuneralCompanyType::JURISTIC_PERSON, $funeralCompanyType->getValue());
        $this->assertFalse($funeralCompanyType->isSoleProprietor());
        $this->assertTrue($funeralCompanyType->isJuristicPerson());
    }

    public function testItFailsWithUnsupportedValue(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(\sprintf(
            'Unsupported funeral company type "wrong_type", expected to be one of "%s", "%s".',
            FuneralCompanyType::SOLE_PROPRIETOR,
            FuneralCompanyType::JURISTIC_PERSON,
        ));
        new FuneralCompanyType('wrong_type');
    }

    public function testItStringifyable(): void
    {
        $funeralCompanyType = FuneralCompanyType::soleProprietor();

        $this->assertSame(FuneralCompanyType::SOLE_PROPRIETOR, (string) $funeralCompanyType);
    }

    public function testItComparable(): void
    {
        $funeralCompanyTypeSoleProprietorA = FuneralCompanyType::soleProprietor();
        $funeralCompanyTypeJuristicPerson  = FuneralCompanyType::juristicPerson();
        $funeralCompanyTypeSoleProprietorB = FuneralCompanyType::soleProprietor();

        $this->assertFalse($funeralCompanyTypeSoleProprietorA->isEqual($funeralCompanyTypeJuristicPerson));
        $this->assertTrue($funeralCompanyTypeSoleProprietorA->isEqual($funeralCompanyTypeSoleProprietorB));
        $this->assertFalse($funeralCompanyTypeJuristicPerson->isEqual($funeralCompanyTypeSoleProprietorB));
    }
}
