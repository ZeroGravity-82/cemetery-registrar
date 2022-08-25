<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Fetcher;

use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\CemeteryBlockId;
use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\CemeteryBlockRepository;
use Cemetery\Registrar\Domain\View\BurialPlace\GraveSite\CemeteryBlockList;
use Cemetery\Registrar\Domain\View\BurialPlace\GraveSite\CemeteryBlockListItem;
use Cemetery\Registrar\Domain\View\BurialPlace\GraveSite\CemeteryBlockView;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Fetcher\DoctrineDbalCemeteryBlockFetcher;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Orm\Repository\DoctrineOrmCemeteryBlockRepository;
use DataFixtures\BurialPlace\GraveSite\CemeteryBlockFixtures;

/**
 * @group database
 *
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class DoctrineDbalCemeteryBlockFetcherIntegrationTest extends DoctrineDbalFetcherIntegrationTest
{
    private CemeteryBlockRepository $repo;

    public function setUp(): void
    {
        parent::setUp();

        $this->repo    = new DoctrineOrmCemeteryBlockRepository($this->entityManager);
        $this->fetcher = new DoctrineDbalCemeteryBlockFetcher($this->connection);
        $this->loadFixtures();
    }

    public function testItReturnsCemeteryBlockViewById(): void
    {
        $this->testItReturnsCemeteryBlockViewForCB001();
    }

    public function testItReturnsNullForRemovedCemeteryBlock(): void
    {
        // Prepare database table for testing
        $cemeteryBlockToRemove = $this->repo->findById(new CemeteryBlockId('CB002'));
        $this->repo->remove($cemeteryBlockToRemove);
        $removedCemeteryBlockId = $cemeteryBlockToRemove->id()->value();

        // Testing itself
        $view = $this->fetcher->findViewById($removedCemeteryBlockId);
        $this->assertNull($view);
    }

    public function testItChecksExistenceById(): void
    {
        // Prepare database table for testing
        $cemeteryBlockToRemove = $this->repo->findById(new CemeteryBlockId('CB002'));
        $this->repo->remove($cemeteryBlockToRemove);
        $removedCemeteryBlockId = $cemeteryBlockToRemove->id()->value();

        $this->assertTrue($this->fetcher->doesExistById('CB001'));
        $this->assertFalse($this->fetcher->doesExistById('unknown_id'));
        $this->assertFalse($this->fetcher->doesExistById($removedCemeteryBlockId));
    }

    public function testItChecksExistenceByName(): void
    {
        // Prepare database table for testing
        $cemeteryBlockToRemove = $this->repo->findById(new CemeteryBlockId('CB002'));
        $this->repo->remove($cemeteryBlockToRemove);
        $removedCemeteryBlockName = $cemeteryBlockToRemove->name()->value();

        $this->assertTrue($this->fetcher->doesExistByName('общий Б'));
        $this->assertFalse($this->fetcher->doesExistByName('unknown_name'));
        $this->assertFalse($this->fetcher->doesExistByName($removedCemeteryBlockName));
    }

    public function testItReturnsCemeteryBlockList(): void
    {
        // All at once
        $listForAll = $this->fetcher->findAll(1);
        $this->assertInstanceOf(CemeteryBlockList::class, $listForAll);
        $this->assertIsArray($listForAll->items);
        $this->assertContainsOnlyInstancesOf(CemeteryBlockListItem::class, $listForAll->items);
        $this->assertCount(4, $listForAll->items);
        $this->assertListItemEqualsCB001($listForAll->items[0]);  // Items are ordered by name
        $this->assertListItemEqualsCB004($listForAll->items[1]);
        $this->assertListItemEqualsCB002($listForAll->items[2]);
        $this->assertListItemEqualsCB003($listForAll->items[3]);
    }

    public function testItReturnsCemeteryBlockTotalCount(): void
    {
        $this->assertSame(4, $this->fetcher->countTotal());
    }

    public function testItDoesNotCountRemovedCemeteryBlockWhenCalculatingTotalCount(): void
    {
        // Prepare database table for testing
        $cemeteryBlockToRemove = $this->repo->findById(new CemeteryBlockId('CB002'));
        $this->repo->remove($cemeteryBlockToRemove);

        // Testing itself
        $this->assertSame(3, $this->fetcher->countTotal());
    }

    protected function loadFixtures(): void
    {
        $this->databaseTool->loadFixtures([
            CemeteryBlockFixtures::class,
        ]);
    }

    private function assertListItemEqualsCB001(CemeteryBlockListItem $listItem): void
    {
        $this->assertSame('CB001',    $listItem->id);
        $this->assertSame('воинский', $listItem->name);
    }

    private function assertListItemEqualsCB002(CemeteryBlockListItem $listItem): void
    {
        $this->assertSame('CB002',   $listItem->id);
        $this->assertSame('общий А', $listItem->name);
    }

    private function assertListItemEqualsCB003(CemeteryBlockListItem $listItem): void
    {
        $this->assertSame('CB003',   $listItem->id);
        $this->assertSame('общий Б', $listItem->name);
    }

    private function assertListItemEqualsCB004(CemeteryBlockListItem $listItem): void
    {
        $this->assertSame('CB004',         $listItem->id);
        $this->assertSame('мусульманский', $listItem->name);
    }

    private function testItReturnsCemeteryBlockViewForCB001(): void
    {
        $view = $this->fetcher->findViewById('CB001');
        $this->assertInstanceOf(CemeteryBlockView::class, $view);
        $this->assertSame('CB001',    $view->id);
        $this->assertSame('воинский', $view->name);
        $this->assertValidDateTimeValue($view->createdAt);
        $this->assertValidDateTimeValue($view->updatedAt);
    }
}
