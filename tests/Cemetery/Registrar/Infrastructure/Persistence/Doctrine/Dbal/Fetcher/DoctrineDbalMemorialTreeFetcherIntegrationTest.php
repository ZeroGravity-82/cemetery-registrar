<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Fetcher;

use Cemetery\Registrar\Domain\Model\BurialPlace\MemorialTree\MemorialTreeId;
use Cemetery\Registrar\Domain\Model\BurialPlace\MemorialTree\MemorialTreeRepository;
use Cemetery\Registrar\Domain\View\BurialPlace\MemorialTree\MemorialTreeFetcher;
use Cemetery\Registrar\Domain\View\BurialPlace\MemorialTree\MemorialTreeList;
use Cemetery\Registrar\Domain\View\BurialPlace\MemorialTree\MemorialTreeListItem;
use Cemetery\Registrar\Domain\View\BurialPlace\MemorialTree\MemorialTreeView;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Fetcher\DoctrineDbalMemorialTreeFetcher;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Orm\Repository\DoctrineOrmMemorialTreeRepository;
use DataFixtures\BurialPlace\MemorialTree\MemorialTreeFixtures;

/**
 * @group database
 *
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class DoctrineDbalMemorialTreeFetcherIntegrationTest extends FetcherIntegrationTest
{
    private const DEFAULT_PAGE_SIZE = 20;

    private MemorialTreeRepository $memorialTreeRepo;
    private MemorialTreeFetcher    $memorialTreeFetcher;

    public function setUp(): void
    {
        parent::setUp();

        $this->memorialTreeRepo    = new DoctrineOrmMemorialTreeRepository($this->entityManager);
        $this->memorialTreeFetcher = new DoctrineDbalMemorialTreeFetcher($this->connection);
        $this->loadFixtures();
    }

    public function testItHasValidPageSizeConstant(): void
    {
        $this->assertSame(self::DEFAULT_PAGE_SIZE, MemorialTreeFetcher::DEFAULT_PAGE_SIZE);
    }

    public function testItReturnsMemorialTreeViewById(): void
    {
        $this->testItReturnsMemorialTreeViewForMT001();
        $this->testItReturnsMemorialTreeViewForMT002();
        $this->testItReturnsMemorialTreeViewForMT003();
        $this->testItReturnsMemorialTreeViewForMT004();
    }

    public function testItFailsToReturnMemorialTreeViewByUnknownId(): void
    {
        $this->expectExceptionForNotFoundMemorialTreeById('unknown_id');
        $this->memorialTreeFetcher->getViewById('unknown_id');
    }

    public function testItFailsToReturnMemorialTreeViewForRemovedMemorialTree(): void
    {
        // Prepare database table for testing
        $memorialTreeToRemove = $this->memorialTreeRepo->findById(new MemorialTreeId('MT004'));
        $this->memorialTreeRepo->remove($memorialTreeToRemove);
        $removedMemorialTreeId = $memorialTreeToRemove->id()->value();

        // Testing itself
        $this->expectExceptionForNotFoundMemorialTreeById($removedMemorialTreeId);
        $this->memorialTreeFetcher->getViewById($removedMemorialTreeId);
    }

    public function testItReturnsMemorialTreeListItemsByPage(): void
    {
        $customPageSize = 3;

        // First page
        $listForFirstPage = $this->memorialTreeFetcher->findAll(1, null, $customPageSize);
        $this->assertInstanceOf(MemorialTreeList::class, $listForFirstPage);
        $this->assertIsArray($listForFirstPage->listItems);
        $this->assertContainsOnlyInstancesOf(MemorialTreeListItem::class, $listForFirstPage->listItems);
        $this->assertCount(3,              $listForFirstPage->listItems);
        $this->assertSame(1,               $listForFirstPage->page);
        $this->assertSame($customPageSize, $listForFirstPage->pageSize);
        $this->assertSame(null,            $listForFirstPage->term);
        $this->assertSame(4,               $listForFirstPage->totalCount);
        $this->assertSame(2,               $listForFirstPage->totalPages);
        $this->assertListItemEqualsMT001($listForFirstPage->listItems[0]);  // Items are ordered by memorial tree,
        $this->assertListItemEqualsMT002($listForFirstPage->listItems[1]);  // number.
        $this->assertListItemEqualsMT004($listForFirstPage->listItems[2]);

        // Second page
        $listForSecondPage = $this->memorialTreeFetcher->findAll(2, null, $customPageSize);
        $this->assertInstanceOf(MemorialTreeList::class, $listForSecondPage);
        $this->assertIsArray($listForSecondPage->listItems);
        $this->assertContainsOnlyInstancesOf(MemorialTreeListItem::class, $listForSecondPage->listItems);
        $this->assertCount(1,              $listForSecondPage->listItems);
        $this->assertSame(2,               $listForSecondPage->page);
        $this->assertSame($customPageSize, $listForSecondPage->pageSize);
        $this->assertSame(null,            $listForSecondPage->term);
        $this->assertSame(4,               $listForSecondPage->totalCount);
        $this->assertSame(2,               $listForSecondPage->totalPages);
        $this->assertListItemEqualsMT003($listForSecondPage->listItems[0]);

        // Default page size
        $listForDefaultPageSize = $this->memorialTreeFetcher->findAll(1);
        $this->assertInstanceOf(MemorialTreeList::class, $listForDefaultPageSize);
        $this->assertIsArray($listForDefaultPageSize->listItems);
        $this->assertContainsOnlyInstancesOf(MemorialTreeListItem::class, $listForDefaultPageSize->listItems);
        $this->assertCount(4,                      $listForDefaultPageSize->listItems);
        $this->assertSame(1,                       $listForDefaultPageSize->page);
        $this->assertSame(self::DEFAULT_PAGE_SIZE, $listForDefaultPageSize->pageSize);
        $this->assertSame(null,                    $listForDefaultPageSize->term);
        $this->assertSame(4,                       $listForDefaultPageSize->totalCount);
        $this->assertSame(1,                       $listForDefaultPageSize->totalPages);
    }

    public function testItReturnsMemorialTreeListItemsByPageAndTerm(): void
    {
        $customPageSize = 3;

        $list = $this->memorialTreeFetcher->findAll(1, '00', $customPageSize);
        $this->assertInstanceOf(MemorialTreeList::class, $list);
        $this->assertIsArray($list->listItems);
        $this->assertContainsOnlyInstancesOf(MemorialTreeListItem::class, $list->listItems);
        $this->assertCount(3,              $list->listItems);
        $this->assertSame(1,               $list->page);
        $this->assertSame($customPageSize, $list->pageSize);
        $this->assertSame('00',            $list->term);
        $this->assertSame(4,               $list->totalCount);
        $this->assertSame(2,               $list->totalPages);
        $list = $this->memorialTreeFetcher->findAll(2, '00', $customPageSize);
        $this->assertInstanceOf(MemorialTreeList::class, $list);
        $this->assertIsArray($list->listItems);
        $this->assertContainsOnlyInstancesOf(MemorialTreeListItem::class, $list->listItems);
        $this->assertCount(1,              $list->listItems);
        $this->assertSame(2,               $list->page);
        $this->assertSame($customPageSize, $list->pageSize);
        $this->assertSame('00',            $list->term);
        $this->assertSame(4,               $list->totalCount);
        $this->assertSame(2,               $list->totalPages);

        $list = $this->memorialTreeFetcher->findAll(1, '4', $customPageSize);
        $this->assertInstanceOf(MemorialTreeList::class, $list);
        $this->assertIsArray($list->listItems);
        $this->assertContainsOnlyInstancesOf(MemorialTreeListItem::class, $list->listItems);
        $this->assertCount(1,              $list->listItems);
        $this->assertSame(1,               $list->page);
        $this->assertSame($customPageSize, $list->pageSize);
        $this->assertSame('4',             $list->term);
        $this->assertSame(1,               $list->totalCount);
        $this->assertSame(1,               $list->totalPages);
    }

    public function testItReturnsMemorialTreeTotalCount(): void
    {
        $this->assertSame(4, $this->memorialTreeFetcher->countTotal());
    }

    public function testItDoesNotCountRemovedMemorialTreeWhenCalculatingTotalCount(): void
    {
        // Prepare database table for testing
        $memorialTreeToRemove = $this->memorialTreeRepo->findById(new MemorialTreeId('MT004'));
        $this->memorialTreeRepo->remove($memorialTreeToRemove);

        // Testing itself
        $this->assertSame(3, $this->memorialTreeFetcher->countTotal());
    }

    protected function loadFixtures(): void
    {
        $this->databaseTool->loadFixtures([
            MemorialTreeFixtures::class,
        ]);
    }

    private function assertListItemEqualsMT001(MemorialTreeListItem $listItem): void
    {
        $this->assertSame('MT001', $listItem->id);
        $this->assertSame('001',   $listItem->treeNumber);
    }

    private function assertListItemEqualsMT002(MemorialTreeListItem $listItem): void
    {
        $this->assertSame('MT002', $listItem->id);
        $this->assertSame('002',   $listItem->treeNumber);
    }

    private function assertListItemEqualsMT003(MemorialTreeListItem $listItem): void
    {
        $this->assertSame('MT003', $listItem->id);
        $this->assertSame('004',   $listItem->treeNumber);
    }

    private function assertListItemEqualsMT004(MemorialTreeListItem $listItem): void
    {
        $this->assertSame('MT004', $listItem->id);
        $this->assertSame('003',   $listItem->treeNumber);
    }

    private function testItReturnsMemorialTreeViewForMT001(): void
    {
        $view = $this->memorialTreeFetcher->getViewById('MT001');
        $this->assertInstanceOf(MemorialTreeView::class, $view);
        $this->assertSame('MT001',      $view->id);
        $this->assertSame('001',        $view->treeNumber);
        $this->assertSame(null,         $view->geoPositionLatitude);
        $this->assertSame(null,         $view->geoPositionLongitude);
        $this->assertSame(null,         $view->geoPositionError);
        $this->assertValidDateTimeValue($view->createdAt);
        $this->assertValidDateTimeValue($view->updatedAt);
    }

    private function testItReturnsMemorialTreeViewForMT002(): void
    {
        $view = $this->memorialTreeFetcher->getViewById('MT002');
        $this->assertInstanceOf(MemorialTreeView::class, $view);
        $this->assertSame('MT002',      $view->id);
        $this->assertSame('002',        $view->treeNumber);
        $this->assertSame('54.950457',  $view->geoPositionLatitude);
        $this->assertSame('82.7972252', $view->geoPositionLongitude);
        $this->assertSame('0.5',        $view->geoPositionError);
        $this->assertValidDateTimeValue($view->createdAt);
        $this->assertValidDateTimeValue($view->updatedAt);
    }

    private function testItReturnsMemorialTreeViewForMT003(): void
    {
        $view = $this->memorialTreeFetcher->getViewById('MT003');
        $this->assertInstanceOf(MemorialTreeView::class, $view);
        $this->assertSame('MT003',      $view->id);
        $this->assertSame('004',        $view->treeNumber);
        $this->assertSame('50.950357',  $view->geoPositionLatitude);
        $this->assertSame('80.7972252', $view->geoPositionLongitude);
        $this->assertSame(null,         $view->geoPositionError);
        $this->assertValidDateTimeValue($view->createdAt);
        $this->assertValidDateTimeValue($view->updatedAt);
    }

    private function testItReturnsMemorialTreeViewForMT004(): void
    {
        $view = $this->memorialTreeFetcher->getViewById('MT004');
        $this->assertInstanceOf(MemorialTreeView::class, $view);
        $this->assertSame('MT004',      $view->id);
        $this->assertSame('003',        $view->treeNumber);
        $this->assertSame(null,         $view->geoPositionLatitude);
        $this->assertSame(null,         $view->geoPositionLongitude);
        $this->assertSame(null,         $view->geoPositionError);
        $this->assertValidDateTimeValue($view->createdAt);
        $this->assertValidDateTimeValue($view->updatedAt);
    }

    private function expectExceptionForNotFoundMemorialTreeById(string $memorialTreeId): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(\sprintf('Памятное дерево с ID "%s" не найдено.', $memorialTreeId));
    }
}
