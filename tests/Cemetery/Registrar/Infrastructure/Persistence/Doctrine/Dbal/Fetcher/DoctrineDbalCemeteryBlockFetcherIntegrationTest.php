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
        $this->assertListItemEqualsCB001($listForAll->listItems[0]);  // Items are ordered by name
        $this->assertListItemEqualsCB004($listForAll->listItems[1]);
        $this->assertListItemEqualsCB002($listForAll->listItems[2]);
        $this->assertListItemEqualsCB003($listForAll->listItems[3]);
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
        $view = $this->cemeteryBlockFetcher->getViewById('CB001');
        $this->assertInstanceOf(CemeteryBlockView::class, $view);
        $this->assertSame('CB001',    $view->id);
        $this->assertSame('воинский', $view->name);
        $this->assertValidDateTimeValue($view->createdAt);
        $this->assertValidDateTimeValue($view->updatedAt);
    }

    private function expectExceptionForNotFoundCemeteryBlockById(string $cemeteryBlockId): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(\sprintf('Квартал с ID "%s" не найден.', $cemeteryBlockId));
    }
}
