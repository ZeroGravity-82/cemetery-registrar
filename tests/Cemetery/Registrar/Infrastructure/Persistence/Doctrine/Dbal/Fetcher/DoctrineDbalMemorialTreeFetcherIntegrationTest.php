<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Fetcher;

use Cemetery\Registrar\Domain\Model\BurialPlace\MemorialTree\MemorialTreeId;
use Cemetery\Registrar\Domain\Model\BurialPlace\MemorialTree\MemorialTreeRepository;
use Cemetery\Registrar\Domain\View\BurialPlace\MemorialTree\MemorialTreeList;
use Cemetery\Registrar\Domain\View\BurialPlace\MemorialTree\MemorialTreeListItem;
use Cemetery\Registrar\Domain\View\BurialPlace\MemorialTree\MemorialTreeView;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Fetcher\DoctrineDbalMemorialTreeFetcher;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Orm\Repository\DoctrineOrmMemorialTreeRepository;
use DataFixtures\BurialPlace\MemorialTree\MemorialTreeFixtures;
use DataFixtures\NaturalPerson\NaturalPersonFixtures;

/**
 * @group database
 *
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class DoctrineDbalMemorialTreeFetcherIntegrationTest extends DoctrineDbalFetcherIntegrationTest
{
    private MemorialTreeRepository $repo;

    public function setUp(): void
    {
        parent::setUp();

        $this->repo    = new DoctrineOrmMemorialTreeRepository($this->entityManager);
        $this->fetcher = new DoctrineDbalMemorialTreeFetcher($this->connection);
        $this->loadFixtures();
    }

    public function testItReturnsMemorialTreeViewById(): void
    {
        $this->testItReturnsMemorialTreeViewForMT001();
        $this->testItReturnsMemorialTreeViewForMT002();
        $this->testItReturnsMemorialTreeViewForMT003();
        $this->testItReturnsMemorialTreeViewForMT004();
    }

    public function testItReturnsNullForRemovedMemorialTree(): void
    {
        // Prepare database table for testing
        $memorialTreeToRemove = $this->repo->findById(new MemorialTreeId('MT004'));
        $this->repo->remove($memorialTreeToRemove);
        $removedMemorialTreeId = $memorialTreeToRemove->id()->value();

        // Testing itself
        $view = $this->fetcher->findViewById($removedMemorialTreeId);
        $this->assertNull($view);
    }

    public function testItReturnsMemorialTreeListByPage(): void
    {
        $customPageSize = 3;

        // First page
        $listForFirstPage = $this->fetcher->findAll(1, null, $customPageSize);
        $this->assertInstanceOf(MemorialTreeList::class, $listForFirstPage);
        $this->assertIsArray($listForFirstPage->items);
        $this->assertContainsOnlyInstancesOf(MemorialTreeListItem::class, $listForFirstPage->items);
        $this->assertCount(3,              $listForFirstPage->items);
        $this->assertSame(1,               $listForFirstPage->page);
        $this->assertSame($customPageSize, $listForFirstPage->pageSize);
        $this->assertSame(null,            $listForFirstPage->term);
        $this->assertSame(4,               $listForFirstPage->totalCount);
        $this->assertSame(2,               $listForFirstPage->totalPages);
        $this->assertListItemEqualsMT001($listForFirstPage->items[0]);  // Items are ordered by memorial tree,
        $this->assertListItemEqualsMT002($listForFirstPage->items[1]);  // number.
        $this->assertListItemEqualsMT004($listForFirstPage->items[2]);

        // Second page
        $listForSecondPage = $this->fetcher->findAll(2, null, $customPageSize);
        $this->assertInstanceOf(MemorialTreeList::class, $listForSecondPage);
        $this->assertIsArray($listForSecondPage->items);
        $this->assertContainsOnlyInstancesOf(MemorialTreeListItem::class, $listForSecondPage->items);
        $this->assertCount(1,              $listForSecondPage->items);
        $this->assertSame(2,               $listForSecondPage->page);
        $this->assertSame($customPageSize, $listForSecondPage->pageSize);
        $this->assertSame(null,            $listForSecondPage->term);
        $this->assertSame(4,               $listForSecondPage->totalCount);
        $this->assertSame(2,               $listForSecondPage->totalPages);
        $this->assertListItemEqualsMT003($listForSecondPage->items[0]);

        // Default page size
        $listForDefaultPageSize = $this->fetcher->findAll(1);
        $this->assertInstanceOf(MemorialTreeList::class, $listForDefaultPageSize);
        $this->assertIsArray($listForDefaultPageSize->items);
        $this->assertContainsOnlyInstancesOf(MemorialTreeListItem::class, $listForDefaultPageSize->items);
        $this->assertCount(4,                      $listForDefaultPageSize->items);
        $this->assertSame(1,                       $listForDefaultPageSize->page);
        $this->assertSame(self::DEFAULT_PAGE_SIZE, $listForDefaultPageSize->pageSize);
        $this->assertSame(null,                    $listForDefaultPageSize->term);
        $this->assertSame(4,                       $listForDefaultPageSize->totalCount);
        $this->assertSame(1,                       $listForDefaultPageSize->totalPages);
    }

    public function testItReturnsMemorialTreeListByPageAndTerm(): void
    {
        $customPageSize = 3;

        $list = $this->fetcher->findAll(1, '00', $customPageSize);
        $this->assertInstanceOf(MemorialTreeList::class, $list);
        $this->assertIsArray($list->items);
        $this->assertContainsOnlyInstancesOf(MemorialTreeListItem::class, $list->items);
        $this->assertCount(3,              $list->items);
        $this->assertSame(1,               $list->page);
        $this->assertSame($customPageSize, $list->pageSize);
        $this->assertSame('00',            $list->term);
        $this->assertSame(4,               $list->totalCount);
        $this->assertSame(2,               $list->totalPages);
        $list = $this->fetcher->findAll(2, '00', $customPageSize);
        $this->assertInstanceOf(MemorialTreeList::class, $list);
        $this->assertIsArray($list->items);
        $this->assertContainsOnlyInstancesOf(MemorialTreeListItem::class, $list->items);
        $this->assertCount(1,              $list->items);
        $this->assertSame(2,               $list->page);
        $this->assertSame($customPageSize, $list->pageSize);
        $this->assertSame('00',            $list->term);
        $this->assertSame(4,               $list->totalCount);
        $this->assertSame(2,               $list->totalPages);

        $list = $this->fetcher->findAll(1, '4', $customPageSize);
        $this->assertInstanceOf(MemorialTreeList::class, $list);
        $this->assertIsArray($list->items);
        $this->assertContainsOnlyInstancesOf(MemorialTreeListItem::class, $list->items);
        $this->assertCount(1,              $list->items);
        $this->assertSame(1,               $list->page);
        $this->assertSame($customPageSize, $list->pageSize);
        $this->assertSame('4',             $list->term);
        $this->assertSame(1,               $list->totalCount);
        $this->assertSame(1,               $list->totalPages);
    }

    public function testItReturnsMemorialTreeTotalCount(): void
    {
        $this->assertSame(4, $this->fetcher->countTotal());
    }

    public function testItDoesNotCountRemovedMemorialTreeWhenCalculatingTotalCount(): void
    {
        // Prepare database table for testing
        $memorialTreeToRemove = $this->repo->findById(new MemorialTreeId('MT004'));
        $this->repo->remove($memorialTreeToRemove);

        // Testing itself
        $this->assertSame(3, $this->fetcher->countTotal());
    }

    protected function loadFixtures(): void
    {
        $this->databaseTool->loadFixtures([
            MemorialTreeFixtures::class,
            NaturalPersonFixtures::class,
        ]);
    }

    private function assertListItemEqualsMT001(MemorialTreeListItem $listItem): void
    {
        $this->assertSame('MT001', $listItem->id);
        $this->assertSame('001',   $listItem->treeNumber);
        $this->assertSame(null,    $listItem->personInChargeId);
        $this->assertSame(null,    $listItem->personInChargeFullName);
    }

    private function assertListItemEqualsMT002(MemorialTreeListItem $listItem): void
    {
        $this->assertSame('MT002',                      $listItem->id);
        $this->assertSame('002',                        $listItem->treeNumber);
        $this->assertSame('NP007',                      $listItem->personInChargeId);
        $this->assertSame('Громов Никифор Рудольфович', $listItem->personInChargeFullName);
    }

    private function assertListItemEqualsMT003(MemorialTreeListItem $listItem): void
    {
        $this->assertSame('MT003', $listItem->id);
        $this->assertSame('004',   $listItem->treeNumber);
        $this->assertSame(null,    $listItem->personInChargeId);
        $this->assertSame(null,    $listItem->personInChargeFullName);
    }

    private function assertListItemEqualsMT004(MemorialTreeListItem $listItem): void
    {
        $this->assertSame('MT004', $listItem->id);
        $this->assertSame('003',   $listItem->treeNumber);
        $this->assertSame(null,    $listItem->personInChargeId);
        $this->assertSame(null,    $listItem->personInChargeFullName);
    }

    private function testItReturnsMemorialTreeViewForMT001(): void
    {
        $view = $this->fetcher->findViewById('MT001');
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
        $view = $this->fetcher->findViewById('MT002');
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
        $view = $this->fetcher->findViewById('MT003');
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
        $view = $this->fetcher->findViewById('MT004');
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
