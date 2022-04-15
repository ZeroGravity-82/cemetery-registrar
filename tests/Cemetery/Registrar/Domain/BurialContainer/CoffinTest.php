<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\BurialContainer;

use Cemetery\Registrar\Domain\BurialContainer\Coffin;
use Cemetery\Registrar\Domain\BurialContainer\CoffinShape;
use Cemetery\Registrar\Domain\BurialContainer\CoffinSize;
use PHPUnit\Framework\TestCase;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class CoffinTest extends TestCase
{
    private Coffin $coffinA;
    private Coffin $coffinB;

    public function setUp(): void
    {
        $this->coffinA = new Coffin(new CoffinSize(180), CoffinShape::trapezoid(), true);
        $this->coffinB = new Coffin(new CoffinSize(165), CoffinShape::greekWithHandles(), false);
    }

    public function testItSuccessfullyCreated(): void
    {
        $this->assertInstanceOf(CoffinSize::class, $this->coffinA->size());
        $this->assertSame(180, $this->coffinA->size()->value());
        $this->assertInstanceOf(CoffinShape::class, $this->coffinA->shape());
        $this->assertTrue($this->coffinA->shape()->isTrapezoid());
        $this->assertTrue($this->coffinA->isNonStandard());

        $this->assertInstanceOf(CoffinSize::class, $this->coffinB->size());
        $this->assertSame(165, $this->coffinB->size()->value());
        $this->assertInstanceOf(CoffinShape::class, $this->coffinB->shape());
        $this->assertTrue($this->coffinB->shape()->isGreekWithHandles());
        $this->assertFalse($this->coffinB->isNonStandard());
    }

    public function testItStringifyable(): void
    {
        $this->assertSame('гроб: размер 180 см, форма "трапеция", нестандартный', (string) $this->coffinA);
        $this->assertSame('гроб: размер 165 см, форма "грек (с ручками)", стандартный', (string) $this->coffinB);
    }

    public function testItComparable(): void
    {
        $coffinA = $this->coffinA;
        $coffinB = $this->coffinB;
        $coffinC = new Coffin(new CoffinSize(165), CoffinShape::trapezoid(), true);
        $coffinD = new Coffin(new CoffinSize(180), CoffinShape::greekWithHandles(), true);
        $coffinE = new Coffin(new CoffinSize(180), CoffinShape::trapezoid(), false);
        $coffinF = new Coffin(new CoffinSize(180), CoffinShape::trapezoid(), true);
        $this->assertFalse($coffinA->isEqual($coffinB));
        $this->assertFalse($coffinA->isEqual($coffinC));
        $this->assertFalse($coffinA->isEqual($coffinD));
        $this->assertFalse($coffinA->isEqual($coffinE));
        $this->assertTrue($coffinA->isEqual($coffinF));
        $this->assertFalse($coffinB->isEqual($coffinC));
        $this->assertFalse($coffinB->isEqual($coffinD));
        $this->assertFalse($coffinB->isEqual($coffinE));
        $this->assertFalse($coffinB->isEqual($coffinF));
        $this->assertFalse($coffinC->isEqual($coffinD));
        $this->assertFalse($coffinC->isEqual($coffinE));
        $this->assertFalse($coffinC->isEqual($coffinF));
        $this->assertFalse($coffinD->isEqual($coffinE));
        $this->assertFalse($coffinD->isEqual($coffinF));
        $this->assertFalse($coffinE->isEqual($coffinF));
    }
}
