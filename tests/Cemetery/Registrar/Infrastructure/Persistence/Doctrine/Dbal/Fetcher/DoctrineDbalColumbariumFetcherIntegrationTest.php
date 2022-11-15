<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Fetcher;

use Cemetery\Registrar\Domain\Model\BurialPlace\ColumbariumNiche\ColumbariumId;
use Cemetery\Registrar\Domain\Model\BurialPlace\ColumbariumNiche\ColumbariumRepositoryInterface;
use Cemetery\Registrar\Domain\View\BurialPlace\ColumbariumNiche\ColumbariumList;
use Cemetery\Registrar\Domain\View\BurialPlace\ColumbariumNiche\ColumbariumListItem;
use Cemetery\Registrar\Domain\View\BurialPlace\ColumbariumNiche\ColumbariumView;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Fetcher\DoctrineDbalColumbariumFetcher;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Orm\Repository\DoctrineOrmColumbariumRepository;
use DataFixtures\BurialPlace\ColumbariumNiche\ColumbariumFixtures;

/**
 * @group database
 *
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class DoctrineDbalColumbariumFetcherIntegrationTest extends DoctrineDbalFetcherIntegrationTest
{
    private ColumbariumRepositoryInterface $repo;

    public function setUp(): void
    {
        parent::setUp();

        $this->repo    = new DoctrineOrmColumbariumRepository($this->entityManager);
        $this->fetcher = new DoctrineDbalColumbariumFetcher($this->connection);
        $this->loadFixtures();
    }

    public function testItReturnsColumbariumViewById(): void
    {
        $this->testItReturnsColumbariumViewForC001();
        $this->testItReturnsColumbariumViewForC002();
        $this->testItReturnsColumbariumViewForC003();
        $this->testItReturnsColumbariumViewForC004();
    }

    public function testItReturnsNullForRemovedColumbarium(): void
    {
        // Prepare database table for testing
        $columbariumToRemove = $this->repo->findById(new ColumbariumId('C002'));
        $this->repo->remove($columbariumToRemove);
        $removedColumbariumId = $columbariumToRemove->id()->value();

        // Testing itself
        $view = $this->fetcher->findViewById($removedColumbariumId);
        $this->assertNull($view);
    }

    public function testItChecksExistenceById(): void
    {
        // Prepare database table for testing
        $columbariumToRemove = $this->repo->findById(new ColumbariumId('C002'));
        $this->repo->remove($columbariumToRemove);
        $removedColumbariumId = $columbariumToRemove->id()->value();

        $this->assertTrue($this->fetcher->doesExistById('C001'));
        $this->assertFalse($this->fetcher->doesExistById('unknown_id'));
        $this->assertFalse($this->fetcher->doesExistById($removedColumbariumId));
    }

    public function testItChecksExistenceByName(): void
    {
        // Prepare database table for testing
        $columbariumToRemove = $this->repo->findById(new ColumbariumId('C002'));
        $this->repo->remove($columbariumToRemove);
        $removedColumbariumName = $columbariumToRemove->name()->value();

        $this->assertTrue($this->fetcher->doesExistByName('восточный'));
        $this->assertFalse($this->fetcher->doesExistByName('unknown_name'));
        $this->assertFalse($this->fetcher->doesExistByName($removedColumbariumName));
    }

    public function testItReturnsColumbariumListFull(): void    // TODO rename
    {
        $this->markTestIncomplete();
//        $list = $this->fetcher->findAll();
//        $this->assertInstanceOf(ColumbariumList::class, $list);
//        $this->assertIsArray($list->items);
//        $this->assertContainsOnlyInstancesOf(ColumbariumListItem::class, $list->items);
//        $this->assertCount(4,                      $list->items);
//        $this->assertSame(null,                    $list->page);
//        $this->assertSame(self::DEFAULT_PAGE_SIZE, $list->pageSize);
//        $this->assertSame(null,                    $list->term);
//        $this->assertSame(4,                       $list->totalCount);
//        $this->assertSame(null,                    $list->totalPages);
//        $this->assertPaginatedListItemEqualsC003($list->items[0]);  // Items are ordered by name
//        $this->assertPaginatedListItemEqualsC001($list->items[1]);
//        $this->assertPaginatedListItemEqualsC004($list->items[2]);
//        $this->assertPaginatedListItemEqualsC002($list->items[3]);
    }

    public function testItReturnsColumbariumPaginatedListByPage(): void
    {
        $customPageSize = 3;

        // First page
        $listForFirstPage = $this->fetcher->paginate(1, null, $customPageSize);
        $this->assertInstanceOf(ColumbariumList::class, $listForFirstPage);
        $this->assertIsArray($listForFirstPage->items);
        $this->assertContainsOnlyInstancesOf(ColumbariumListItem::class, $listForFirstPage->items);
        $this->assertCount(3,              $listForFirstPage->items);
        $this->assertSame(1,               $listForFirstPage->page);
        $this->assertSame($customPageSize, $listForFirstPage->pageSize);
        $this->assertSame(null,            $listForFirstPage->term);
        $this->assertSame(4,               $listForFirstPage->totalCount);
        $this->assertSame(2,               $listForFirstPage->totalPages);
        $this->assertPaginatedListItemEqualsC003($listForFirstPage->items[0]);   // Items are ordered by name
        $this->assertPaginatedListItemEqualsC001($listForFirstPage->items[1]);
        $this->assertPaginatedListItemEqualsC004($listForFirstPage->items[2]);

        // Second page
        $listForSecondPage = $this->fetcher->paginate(2, null, $customPageSize);
        $this->assertInstanceOf(ColumbariumList::class, $listForSecondPage);
        $this->assertIsArray($listForSecondPage->items);
        $this->assertContainsOnlyInstancesOf(ColumbariumListItem::class, $listForSecondPage->items);
        $this->assertCount(1,              $listForSecondPage->items);
        $this->assertSame(2,               $listForSecondPage->page);
        $this->assertSame($customPageSize, $listForSecondPage->pageSize);
        $this->assertSame(null,            $listForSecondPage->term);
        $this->assertSame(4,               $listForSecondPage->totalCount);
        $this->assertSame(2,               $listForSecondPage->totalPages);
        $this->assertPaginatedListItemEqualsC002($listForSecondPage->items[0]);

        // Third page
        $listForThirdPage = $this->fetcher->paginate(3, null, $customPageSize);
        $this->assertInstanceOf(ColumbariumList::class, $listForThirdPage);
        $this->assertIsArray($listForThirdPage->items);
        $this->assertContainsOnlyInstancesOf(ColumbariumListItem::class, $listForThirdPage->items);
        $this->assertCount(0,              $listForThirdPage->items);
        $this->assertSame(3,               $listForThirdPage->page);
        $this->assertSame($customPageSize, $listForThirdPage->pageSize);
        $this->assertSame(null,            $listForThirdPage->term);
        $this->assertSame(4,               $listForThirdPage->totalCount);
        $this->assertSame(2,               $listForThirdPage->totalPages);

        // Default page size
        $listForDefaultPageSize = $this->fetcher->paginate(1);
        $this->assertInstanceOf(ColumbariumList::class, $listForDefaultPageSize);
        $this->assertIsArray($listForDefaultPageSize->items);
        $this->assertContainsOnlyInstancesOf(ColumbariumListItem::class, $listForDefaultPageSize->items);
        $this->assertCount(4,                      $listForDefaultPageSize->items);
        $this->assertSame(1,                       $listForDefaultPageSize->page);
        $this->assertSame(self::DEFAULT_PAGE_SIZE, $listForDefaultPageSize->pageSize);
        $this->assertSame(null,                    $listForDefaultPageSize->term);
        $this->assertSame(4,                       $listForDefaultPageSize->totalCount);
        $this->assertSame(1,                       $listForDefaultPageSize->totalPages);
    }

    public function testItReturnsColumbariumPaginatedListByPageAndTerm(): void
    {
        $customPageSize = 3;

        $list = $this->fetcher->paginate(1, 'воСТОчный', $customPageSize);
        $this->assertInstanceOf(ColumbariumList::class, $list);
        $this->assertIsArray($list->items);
        $this->assertContainsOnlyInstancesOf(ColumbariumListItem::class, $list->items);
        $this->assertCount(1,              $list->items);
        $this->assertSame(1,               $list->page);
        $this->assertSame($customPageSize, $list->pageSize);
        $this->assertSame('воСТОчный',     $list->term);
        $this->assertSame(1,               $list->totalCount);
        $this->assertSame(1,               $list->totalPages);

        $list = $this->fetcher->paginate(1, 'ыЙ', $customPageSize);
        $this->assertInstanceOf(ColumbariumList::class, $list);
        $this->assertIsArray($list->items);
        $this->assertContainsOnlyInstancesOf(ColumbariumListItem::class, $list->items);
        $this->assertCount(3,              $list->items);
        $this->assertSame(1,               $list->page);
        $this->assertSame($customPageSize, $list->pageSize);
        $this->assertSame('ыЙ',            $list->term);
        $this->assertSame(4,               $list->totalCount);
        $this->assertSame(2,               $list->totalPages);
        $list = $this->fetcher->paginate(2, 'ыЙ', $customPageSize);
        $this->assertInstanceOf(ColumbariumList::class, $list);
        $this->assertIsArray($list->items);
        $this->assertContainsOnlyInstancesOf(ColumbariumListItem::class, $list->items);
        $this->assertCount(1,              $list->items);
        $this->assertSame(2,               $list->page);
        $this->assertSame($customPageSize, $list->pageSize);
        $this->assertSame('ыЙ',            $list->term);
        $this->assertSame(4,               $list->totalCount);
        $this->assertSame(2,               $list->totalPages);
    }

    public function testItReturnsColumbariumTotalCount(): void
    {
        $this->assertSame(4, $this->fetcher->countTotal());
    }

    public function testItDoesNotCountRemovedColumbariumWhenCalculatingTotalCount(): void
    {
        // Prepare database table for testing
        $columbariumToRemove = $this->repo->findById(new ColumbariumId('C002'));
        $this->repo->remove($columbariumToRemove);

        // Testing itself
        $this->assertSame(3, $this->fetcher->countTotal());
    }

    protected function loadFixtures(): void
    {
        $this->databaseTool->loadFixtures([
            ColumbariumFixtures::class,
        ]);
    }

    private function assertPaginatedListItemEqualsC001(ColumbariumListItem $listItem): void
    {
        $this->assertSame('C001',     $listItem->id);
        $this->assertSame('западный', $listItem->name);
    }

    private function assertPaginatedListItemEqualsC002(ColumbariumListItem $listItem): void
    {
        $this->assertSame('C002',  $listItem->id);
        $this->assertSame('южный', $listItem->name);
    }

    private function assertPaginatedListItemEqualsC003(ColumbariumListItem $listItem): void
    {
        $this->assertSame('C003',      $listItem->id);
        $this->assertSame('восточный', $listItem->name);
    }

    private function assertPaginatedListItemEqualsC004(ColumbariumListItem $listItem): void
    {
        $this->assertSame('C004',     $listItem->id);
        $this->assertSame('северный', $listItem->name);
    }

    private function testItReturnsColumbariumViewForC001(): void
    {
        $view = $this->fetcher->findViewById('C001');
        $this->assertInstanceOf(ColumbariumView::class, $view);
        $this->assertSame('C001',     $view->id);
        $this->assertSame('западный', $view->name);
        $this->assertSame(null,       $view->geoPositionLatitude);
        $this->assertSame(null,       $view->geoPositionLongitude);
        $this->assertSame(null,       $view->geoPositionError);
        $this->assertValidDateTimeValue($view->createdAt);
        $this->assertValidDateTimeValue($view->updatedAt);
    }

    private function testItReturnsColumbariumViewForC002(): void
    {
        $view = $this->fetcher->findViewById('C002');
        $this->assertInstanceOf(ColumbariumView::class, $view);
        $this->assertSame('C002',        $view->id);
        $this->assertSame('южный',       $view->name);
        $this->assertSame('54.95035712', $view->geoPositionLatitude);
        $this->assertSame('82.79252',    $view->geoPositionLongitude);
        $this->assertSame('0.5',         $view->geoPositionError);
        $this->assertValidDateTimeValue($view->createdAt);
        $this->assertValidDateTimeValue($view->updatedAt);
    }

    private function testItReturnsColumbariumViewForC003(): void
    {
        $view = $this->fetcher->findViewById('C003');
        $this->assertInstanceOf(ColumbariumView::class, $view);
        $this->assertSame('C003',         $view->id);
        $this->assertSame('восточный',    $view->name);
        $this->assertSame('-50.95',       $view->geoPositionLatitude);
        $this->assertSame('-179.7972252', $view->geoPositionLongitude);
        $this->assertSame(null,           $view->geoPositionError);
        $this->assertValidDateTimeValue($view->createdAt);
        $this->assertValidDateTimeValue($view->updatedAt);
    }

    private function testItReturnsColumbariumViewForC004(): void
    {
        $view = $this->fetcher->findViewById('C004');
        $this->assertInstanceOf(ColumbariumView::class, $view);
        $this->assertSame('C004',     $view->id);
        $this->assertSame('северный', $view->name);
        $this->assertSame(null,       $view->geoPositionLatitude);
        $this->assertSame(null,       $view->geoPositionLongitude);
        $this->assertSame(null,       $view->geoPositionError);
        $this->assertValidDateTimeValue($view->createdAt);
        $this->assertValidDateTimeValue($view->updatedAt);
    }
}
