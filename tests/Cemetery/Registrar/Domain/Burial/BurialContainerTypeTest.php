<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Burial;

use Cemetery\Registrar\Domain\Burial\BurialContainerType;
use PHPUnit\Framework\TestCase;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class BurialContainerTypeTest extends TestCase
{
    public function testItSuccessfullyCreated(): void
    {
        $burialContainerType = new BurialContainerType(BurialContainerType::COFFIN);
        $this->assertSame(BurialContainerType::COFFIN, $burialContainerType->getValue());
        $this->assertTrue($burialContainerType->isCoffin());
        $this->assertFalse($burialContainerType->isUrn());

        $burialContainerType = BurialContainerType::urn();
        $this->assertSame(BurialContainerType::URN, $burialContainerType->getValue());
        $this->assertFalse($burialContainerType->isCoffin());
        $this->assertTrue($burialContainerType->isUrn());
    }

    public function testItFailsWithUnsupportedValue(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(\sprintf(
            'Неподдерживаемый тип контейнера захоронения "неверный_тип", должен быть один из "%s", "%s".',
            BurialContainerType::COFFIN,
            BurialContainerType::URN,
        ));
        new BurialContainerType('неверный_тип');
    }

    public function testItStringifyable(): void
    {
        $burialContainerType = BurialContainerType::coffin();

        $this->assertSame(BurialContainerType::COFFIN, (string) $burialContainerType);
    }

    public function testItComparable(): void
    {
        $burialContainerTypeA = BurialContainerType::coffin();
        $burialContainerTypeB = BurialContainerType::urn();
        $burialContainerTypeC = BurialContainerType::coffin();

        $this->assertFalse($burialContainerTypeA->isEqual($burialContainerTypeB));
        $this->assertTrue($burialContainerTypeA->isEqual($burialContainerTypeC));
        $this->assertFalse($burialContainerTypeB->isEqual($burialContainerTypeC));
    }
}
