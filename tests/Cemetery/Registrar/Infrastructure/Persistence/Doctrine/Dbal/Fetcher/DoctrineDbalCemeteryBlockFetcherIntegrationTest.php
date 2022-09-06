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
        $this->testItReturnsCemeteryBlockViewForCB002();
        $this->testItReturnsCemeteryBlockViewForCB003();
        $this->testItReturnsCemeteryBlockViewForCB004();
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

    public function testItReturnsCemeteryBlockListFull(): void  // TODO rename
    {
        $this->markTestIncomplete();
//        $list = $this->fetcher->findAll();
//        $this->assertInstanceOf(CemeteryBlockList::class, $list);
//        $this->assertIsArray($list->items);
//        $this->assertContainsOnlyInstancesOf(CemeteryBlockListItem::class, $list->items);
//        $this->assertCount(4,                      $list->items);
//        $this->assertSame(null,                    $list->page);
//        $this->assertSame(self::DEFAULT_PAGE_SIZE, $list->pageSize);
//        $this->assertSame(null,                    $list->term);
//        $this->assertSame(4,                       $list->totalCount);
//        $this->assertSame(null,                    $list->totalPages);
//        $this->assertListItemEqualsCB001($list->items[0]);  // Items are ordered by name
//        $this->assertListItemEqualsCB004($list->items[1]);
//        $this->assertListItemEqualsCB002($list->items[2]);
//        $this->assertListItemEqualsCB003($list->items[3]);
    }

    public function testItReturnsCemeteryBlockListByPage(): void
    {
        $customPageSize = 3;

        // First page
        $listForFirstPage = $this->fetcher->findAll(1, null, $customPageSize);
        $this->assertInstanceOf(CemeteryBlockList::class, $listForFirstPage);
        $this->assertIsArray($listForFirstPage->items);
        $this->assertContainsOnlyInstancesOf(CemeteryBlockListItem::class, $listForFirstPage->items);
        $this->assertCount(3,              $listForFirstPage->items);
        $this->assertSame(1,               $listForFirstPage->page);
        $this->assertSame($customPageSize, $listForFirstPage->pageSize);
        $this->assertSame(null,            $listForFirstPage->term);
        $this->assertSame(4,               $listForFirstPage->totalCount);
        $this->assertSame(2,               $listForFirstPage->totalPages);
        $this->assertListItemEqualsCB001($listForFirstPage->items[0]);  // Items are ordered by name
        $this->assertListItemEqualsCB004($listForFirstPage->items[1]);
        $this->assertListItemEqualsCB002($listForFirstPage->items[2]);

        // Second page
        $listForSecondPage = $this->fetcher->findAll(2, null, $customPageSize);
        $this->assertInstanceOf(CemeteryBlockList::class, $listForSecondPage);
        $this->assertIsArray($listForSecondPage->items);
        $this->assertContainsOnlyInstancesOf(CemeteryBlockListItem::class, $listForSecondPage->items);
        $this->assertCount(1,              $listForSecondPage->items);
        $this->assertSame(2,               $listForSecondPage->page);
        $this->assertSame($customPageSize, $listForSecondPage->pageSize);
        $this->assertSame(null,            $listForSecondPage->term);
        $this->assertSame(4,               $listForSecondPage->totalCount);
        $this->assertSame(2,               $listForSecondPage->totalPages);
        $this->assertListItemEqualsCB003($listForSecondPage->items[0]);

        // Third page
        $listForThirdPage = $this->fetcher->findAll(3, null, $customPageSize);
        $this->assertInstanceOf(CemeteryBlockList::class, $listForThirdPage);
        $this->assertIsArray($listForThirdPage->items);
        $this->assertContainsOnlyInstancesOf(CemeteryBlockListItem::class, $listForThirdPage->items);
        $this->assertCount(0,              $listForThirdPage->items);
        $this->assertSame(3,               $listForThirdPage->page);
        $this->assertSame($customPageSize, $listForThirdPage->pageSize);
        $this->assertSame(null,            $listForThirdPage->term);
        $this->assertSame(4,               $listForThirdPage->totalCount);
        $this->assertSame(2,               $listForThirdPage->totalPages);

        // Default page size
        $listForDefaultPageSize = $this->fetcher->findAll(1);
        $this->assertInstanceOf(CemeteryBlockList::class, $listForDefaultPageSize);
        $this->assertIsArray($listForDefaultPageSize->items);
        $this->assertContainsOnlyInstancesOf(CemeteryBlockListItem::class, $listForDefaultPageSize->items);
        $this->assertCount(4,                      $listForDefaultPageSize->items);
        $this->assertSame(1,                       $listForDefaultPageSize->page);
        $this->assertSame(self::DEFAULT_PAGE_SIZE, $listForDefaultPageSize->pageSize);
        $this->assertSame(null,                    $listForDefaultPageSize->term);
        $this->assertSame(4,                       $listForDefaultPageSize->totalCount);
        $this->assertSame(1,                       $listForDefaultPageSize->totalPages);
    }

    public function testItReturnsCemeteryBlockListByPageAndTerm(): void
    {
        $customPageSize = 3;

        $list = $this->fetcher->findAll(1, 'иЙ', $customPageSize);
        $this->assertInstanceOf(CemeteryBlockList::class, $list);
        $this->assertIsArray($list->items);
        $this->assertContainsOnlyInstancesOf(CemeteryBlockListItem::class, $list->items);
        $this->assertCount(3,              $list->items);
        $this->assertSame(1,               $list->page);
        $this->assertSame($customPageSize, $list->pageSize);
        $this->assertSame('иЙ',            $list->term);
        $this->assertSame(4,               $list->totalCount);
        $this->assertSame(2,               $list->totalPages);
        $list = $this->fetcher->findAll(2, 'иЙ', $customPageSize);
        $this->assertInstanceOf(CemeteryBlockList::class, $list);
        $this->assertIsArray($list->items);
        $this->assertContainsOnlyInstancesOf(CemeteryBlockListItem::class, $list->items);
        $this->assertCount(1,              $list->items);
        $this->assertSame(2,               $list->page);
        $this->assertSame($customPageSize, $list->pageSize);
        $this->assertSame('иЙ',            $list->term);
        $this->assertSame(4,               $list->totalCount);
        $this->assertSame(2,               $list->totalPages);
        $list = $this->fetcher->findAll(3, 'иЙ', $customPageSize);
        $this->assertInstanceOf(CemeteryBlockList::class, $list);
        $this->assertIsArray($list->items);
        $this->assertContainsOnlyInstancesOf(CemeteryBlockListItem::class, $list->items);
        $this->assertCount(0,              $list->items);
        $this->assertSame(3,               $list->page);
        $this->assertSame($customPageSize, $list->pageSize);
        $this->assertSame('иЙ',            $list->term);
        $this->assertSame(4,               $list->totalCount);
        $this->assertSame(2,               $list->totalPages);

        $list = $this->fetcher->findAll(1, 'об', $customPageSize);
        $this->assertInstanceOf(CemeteryBlockList::class, $list);
        $this->assertIsArray($list->items);
        $this->assertContainsOnlyInstancesOf(CemeteryBlockListItem::class, $list->items);
        $this->assertCount(2,              $list->items);
        $this->assertSame(1,               $list->page);
        $this->assertSame($customPageSize, $list->pageSize);
        $this->assertSame('об',            $list->term);
        $this->assertSame(2,               $list->totalCount);
        $this->assertSame(1,               $list->totalPages);

        $list = $this->fetcher->findAll(1, 'МУСУЛЬман', $customPageSize);
        $this->assertInstanceOf(CemeteryBlockList::class, $list);
        $this->assertIsArray($list->items);
        $this->assertContainsOnlyInstancesOf(CemeteryBlockListItem::class, $list->items);
        $this->assertCount(1,              $list->items);
        $this->assertSame(1,               $list->page);
        $this->assertSame($customPageSize, $list->pageSize);
        $this->assertSame('МУСУЛЬман',     $list->term);
        $this->assertSame(1,               $list->totalCount);
        $this->assertSame(1,               $list->totalPages);
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

    private function testItReturnsCemeteryBlockViewForCB002(): void
    {
        $view = $this->fetcher->findViewById('CB002');
        $this->assertInstanceOf(CemeteryBlockView::class, $view);
        $this->assertSame('CB002',   $view->id);
        $this->assertSame('общий А', $view->name);
        $this->assertValidDateTimeValue($view->createdAt);
        $this->assertValidDateTimeValue($view->updatedAt);
    }

    private function testItReturnsCemeteryBlockViewForCB003(): void
    {
        $view = $this->fetcher->findViewById('CB003');
        $this->assertInstanceOf(CemeteryBlockView::class, $view);
        $this->assertSame('CB003',   $view->id);
        $this->assertSame('общий Б', $view->name);
        $this->assertValidDateTimeValue($view->createdAt);
        $this->assertValidDateTimeValue($view->updatedAt);
    }

    private function testItReturnsCemeteryBlockViewForCB004(): void
    {
        $view = $this->fetcher->findViewById('CB004');
        $this->assertInstanceOf(CemeteryBlockView::class, $view);
        $this->assertSame('CB004',         $view->id);
        $this->assertSame('мусульманский', $view->name);
        $this->assertValidDateTimeValue($view->createdAt);
        $this->assertValidDateTimeValue($view->updatedAt);
    }
}
