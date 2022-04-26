<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\BurialPlace\GraveSite;

use Cemetery\Registrar\Domain\BurialPlace\GraveSite\CemeteryBlock;
use Cemetery\Registrar\Domain\BurialPlace\GraveSite\CemeteryBlockId;
use Cemetery\Registrar\Domain\BurialPlace\GraveSite\CemeteryBlockName;
use Cemetery\Registrar\Domain\GeoPosition\Accuracy;
use Cemetery\Registrar\Domain\GeoPosition\Coordinates;
use Cemetery\Registrar\Domain\GeoPosition\GeoPosition;
use Cemetery\Tests\Registrar\Domain\AbstractAggregateRootTest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class CemeteryBlockTest extends AbstractAggregateRootTest
{
    private CemeteryBlockId   $id;
    private CemeteryBlockName $name;
    private CemeteryBlock     $cemeteryBlock;

    public function setUp(): void
    {
        $this->id            = new CemeteryBlockId('CB001');
        $this->name          = new CemeteryBlockName('воинский квартал');
        $this->cemeteryBlock = new CemeteryBlock($this->id, $this->name);
        $this->entity        = $this->cemeteryBlock;
    }

    public function testItSuccessfullyCreated(): void
    {
        $this->assertInstanceOf(CemeteryBlockId::class, $this->cemeteryBlock->id());
        $this->assertTrue($this->cemeteryBlock->id()->isEqual($this->id));
        $this->assertInstanceOf(CemeteryBlockName::class, $this->cemeteryBlock->name());
        $this->assertTrue($this->cemeteryBlock->name()->isEqual($this->name));
    }

    public function testItSetsName(): void
    {
        $name = new CemeteryBlockName('мусульманский квартал');
        $this->cemeteryBlock->setName($name);
        $this->assertInstanceOf(CemeteryBlockName::class, $this->cemeteryBlock->name());
        $this->assertTrue($this->cemeteryBlock->name()->isEqual($name));
    }
}
