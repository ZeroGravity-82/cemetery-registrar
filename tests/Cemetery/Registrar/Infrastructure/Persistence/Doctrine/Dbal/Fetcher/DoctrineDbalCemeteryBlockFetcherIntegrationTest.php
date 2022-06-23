<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Fetcher;

use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\CemeteryBlockId;
use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\CemeteryBlockRepository;
use Cemetery\Registrar\Domain\View\BurialPlace\GraveSite\CemeteryBlockFetcher;
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
class DoctrineDbalCemeteryBlockFetcherIntegrationTest extends FetcherIntegrationTest
{
    private CemeteryBlockRepository $cemeteryBlockRepo;
    private CemeteryBlockFetcher    $cemeteryBlockFetcher;

    public function setUp(): void
    {
        parent::setUp();

        $this->cemeteryBlockRepo    = new DoctrineOrmCemeteryBlockRepository($this->entityManager);
        $this->cemeteryBlockFetcher = new DoctrineDbalCemeteryBlockFetcher($this->connection);
        $this->loadFixtures();
    }

    public function testItReturnsCemeteryBlockViewById(): void
    {
        $this->testItReturnsCemeteryBlockViewForCB001();
    }

    public function testItFailsToReturnCemeteryBlockViewByUnknownId(): void
    {
        $this->expectExceptionForNotFoundCemeteryBlockById('unknown_id');
        $this->cemeteryBlockFetcher->getViewById('unknown_id');
    }

    public function testItFailsToReturnCemeteryBlockViewForRemovedCemeteryBlock(): void
    {
        // Prepare database table for testing
        $cemeteryBlockToRemove = $this->cemeteryBlockRepo->findById(new CemeteryBlockId('CB002'));
        $this->cemeteryBlockRepo->remove($cemeteryBlockToRemove);
        $removedCemeteryBlockId = $cemeteryBlockToRemove->id()->value();

        // Testing itself
        $this->expectExceptionForNotFoundCemeteryBlockById($removedCemeteryBlockId);
        $this->cemeteryBlockFetcher->getViewById($removedCemeteryBlockId);
    }

    public function testItReturnsCemeteryBlockListItems(): void
    {
        // All at once
        $listForAll = $this->cemeteryBlockFetcher->findAll();
        $this->assertInstanceOf(CemeteryBlockList::class, $listForAll);
        $this->assertIsArray($listForAll->listItems);
        $this->assertContainsOnlyInstancesOf(CemeteryBlockListItem::class, $listForAll->listItems);
        $this->assertCount(4, $listForAll->listItems);
        $this->assertItemEqualsCB001($listForAll->listItems[0]);  // Items are ordered by name
        $this->assertItemEqualsCB004($listForAll->listItems[1]);
        $this->assertItemEqualsCB002($listForAll->listItems[2]);
        $this->assertItemEqualsCB003($listForAll->listItems[3]);
    }

    public function testItReturnsCemeteryBlockTotalCount(): void
    {
        $this->assertSame(4, $this->cemeteryBlockFetcher->countTotal());
    }

    public function testItDoesNotCountRemovedCemeteryBlockWhenCalculatingTotalCount(): void
    {
        // Prepare database table for testing
        $cemeteryBlockToRemove = $this->cemeteryBlockRepo->findById(new CemeteryBlockId('CB002'));
        $this->cemeteryBlockRepo->remove($cemeteryBlockToRemove);

        // Testing itself
        $this->assertSame(3, $this->cemeteryBlockFetcher->countTotal());
    }

    protected function loadFixtures(): void
    {
        $this->databaseTool->loadFixtures([
            CemeteryBlockFixtures::class,
        ]);
    }

    private function assertItemEqualsCB001(CemeteryBlockListItem $item): void
    {
        $this->assertSame('CB001',    $item->id);
        $this->assertSame('воинский', $item->name);
    }

    private function assertItemEqualsCB002(CemeteryBlockListItem $item): void
    {
        $this->assertSame('CB002',   $item->id);
        $this->assertSame('общий А', $item->name);
    }

    private function assertItemEqualsCB003(CemeteryBlockListItem $item): void
    {
        $this->assertSame('CB003',   $item->id);
        $this->assertSame('общий Б', $item->name);
    }

    private function assertItemEqualsCB004(CemeteryBlockListItem $item): void
    {
        $this->assertSame('CB004',         $item->id);
        $this->assertSame('мусульманский', $item->name);
    }

    private function testItReturnsCemeteryBlockViewForCB001(): void
    {
        $cemeteryBlockView = $this->cemeteryBlockFetcher->getViewById('CB001');
        $this->assertInstanceOf(CemeteryBlockView::class, $cemeteryBlockView);
        $this->assertSame('CB001',    $cemeteryBlockView->id);
        $this->assertSame('воинский', $cemeteryBlockView->name);
        $this->assertValidDateTimeValue($cemeteryBlockView->createdAt);
        $this->assertValidDateTimeValue($cemeteryBlockView->updatedAt);
    }

    private function expectExceptionForNotFoundCemeteryBlockById(string $cemeteryBlockId): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(\sprintf('Квартал с ID "%s" не найден.', $cemeteryBlockId));
    }
}
