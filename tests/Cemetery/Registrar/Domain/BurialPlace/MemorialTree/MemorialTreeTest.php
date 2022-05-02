<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\BurialPlace\MemorialTree;

use Cemetery\Registrar\Domain\BurialPlace\MemorialTree\MemorialTree;
use Cemetery\Registrar\Domain\BurialPlace\MemorialTree\MemorialTreeId;
use Cemetery\Registrar\Domain\BurialPlace\MemorialTree\MemorialTreeNumber;
use Cemetery\Registrar\Domain\GeoPosition\Coordinates;
use Cemetery\Registrar\Domain\GeoPosition\Error;
use Cemetery\Registrar\Domain\GeoPosition\GeoPosition;
use Cemetery\Tests\Registrar\Domain\AbstractAggregateRootTest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class MemorialTreeTest extends AbstractAggregateRootTest
{
    private MemorialTreeId     $id;
    private MemorialTreeNumber $treeNumber;
    private MemorialTree       $memorialTree;

    public function setUp(): void
    {
        $this->id           = new MemorialTreeId('MT001');
        $this->treeNumber   = new MemorialTreeNumber('001');
        $this->memorialTree = new MemorialTree($this->id, $this->treeNumber);
        $this->entity       = $this->memorialTree;
    }

    public function testItHasValidLabelConstant(): void
    {
        $this->assertSame('памятное дерево', MemorialTree::LABEL);
    }

    public function testItSuccessfullyCreated(): void
    {
        $this->assertInstanceOf(MemorialTreeId::class, $this->memorialTree->id());
        $this->assertTrue($this->memorialTree->id()->isEqual($this->id));
        $this->assertInstanceOf(MemorialTreeNumber::class, $this->memorialTree->treeNumber());
        $this->assertTrue($this->memorialTree->treeNumber()->isEqual($this->treeNumber));
        $this->assertNull($this->memorialTree->geoPosition());
    }

    public function testItSetsTreeNumber(): void
    {
        $treeNumber = new MemorialTreeNumber('002');
        $this->memorialTree->setTreeNumber($treeNumber);
        $this->assertInstanceOf(MemorialTreeNumber::class, $this->memorialTree->treeNumber());
        $this->assertTrue($this->memorialTree->treeNumber()->isEqual($treeNumber));
    }

    public function testItSetsGeoPosition(): void
    {
        $geoPosition = new GeoPosition(new Coordinates('54.950357', '82.7972252'), new Error('0.5'));
        $this->memorialTree->setGeoPosition($geoPosition);
        $this->assertInstanceOf(GeoPosition::class, $this->memorialTree->geoPosition());
        $this->assertTrue($this->memorialTree->geoPosition()->isEqual($geoPosition));
    }
}
