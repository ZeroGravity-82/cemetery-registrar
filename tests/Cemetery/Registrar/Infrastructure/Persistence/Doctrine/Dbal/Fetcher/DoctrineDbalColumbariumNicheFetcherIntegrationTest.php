<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Fetcher;

use Cemetery\Registrar\Domain\Model\BurialPlace\ColumbariumNiche\ColumbariumNicheId;
use Cemetery\Registrar\Domain\Model\BurialPlace\ColumbariumNiche\ColumbariumNicheRepository;
use Cemetery\Registrar\Domain\View\BurialPlace\ColumbariumNiche\ColumbariumNicheFetcher;
use Cemetery\Registrar\Domain\View\BurialPlace\ColumbariumNiche\ColumbariumNicheList;
use Cemetery\Registrar\Domain\View\BurialPlace\ColumbariumNiche\ColumbariumNicheListItem;
use Cemetery\Registrar\Domain\View\BurialPlace\ColumbariumNiche\ColumbariumNicheView;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Fetcher\DoctrineDbalColumbariumNicheFetcher;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Orm\Repository\DoctrineOrmColumbariumNicheRepository;
use DataFixtures\BurialPlace\ColumbariumNiche\ColumbariumFixtures;
use DataFixtures\BurialPlace\ColumbariumNiche\ColumbariumNicheFixtures;

/**
 * @group database
 *
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class DoctrineDbalColumbariumNicheFetcherIntegrationTest extends FetcherIntegrationTest
{
    private const DEFAULT_PAGE_SIZE = 20;

    private ColumbariumNicheRepository $columbariumNicheRepo;
    private ColumbariumNicheFetcher    $columbariumNicheFetcher;

    public function setUp(): void
    {
        parent::setUp();

        $this->columbariumNicheRepo    = new DoctrineOrmColumbariumNicheRepository($this->entityManager);
        $this->columbariumNicheFetcher = new DoctrineDbalColumbariumNicheFetcher($this->connection);
        $this->loadFixtures();
    }

    public function testItHasValidPageSizeConstant(): void
    {
        $this->assertSame(self::DEFAULT_PAGE_SIZE, ColumbariumNicheFetcher::DEFAULT_PAGE_SIZE);
    }

    public function testItReturnsColumbariumNicheViewById(): void
    {
        $this->testItReturnsColumbariumNicheViewForCN001();
        $this->testItReturnsColumbariumNicheViewForCN002();
        $this->testItReturnsColumbariumNicheViewForCN003();
        $this->testItReturnsColumbariumNicheViewForCN004();
        $this->testItReturnsColumbariumNicheViewForCN005();
        $this->testItReturnsColumbariumNicheViewForCN006();
        $this->testItReturnsColumbariumNicheViewForCN007();
    }

    public function testItFailsToReturnColumbariumNicheViewByUnknownId(): void
    {
        $this->expectExceptionForNotFoundColumbariumNicheById('unknown_id');
        $this->columbariumNicheFetcher->getViewById('unknown_id');
    }

    public function testItFailsToReturnColumbariumNicheViewForRemovedColumbariumNiche(): void
    {
        // Prepare database table for testing
        $columbariumNicheToRemove = $this->columbariumNicheRepo->findById(new ColumbariumNicheId('CN004'));
        $this->columbariumNicheRepo->remove($columbariumNicheToRemove);
        $removedColumbariumNicheId = $columbariumNicheToRemove->id()->value();

        // Testing itself
        $this->expectExceptionForNotFoundColumbariumNicheById($removedColumbariumNicheId);
        $this->columbariumNicheFetcher->getViewById($removedColumbariumNicheId);
    }

    public function testItReturnsColumbariumNicheListByPage(): void
    {
        $customPageSize = 3;

        // First page
        $listForFirstPage = $this->columbariumNicheFetcher->findAll(1, null, $customPageSize);
        $this->assertInstanceOf(ColumbariumNicheList::class, $listForFirstPage);
        $this->assertIsArray($listForFirstPage->listItems);
        $this->assertContainsOnlyInstancesOf(ColumbariumNicheListItem::class, $listForFirstPage->listItems);
        $this->assertCount(3,              $listForFirstPage->listItems);
        $this->assertSame(1,               $listForFirstPage->page);
        $this->assertSame($customPageSize, $listForFirstPage->pageSize);
        $this->assertSame(null,            $listForFirstPage->term);
        $this->assertSame(7,               $listForFirstPage->totalCount);
        $this->assertSame(3,               $listForFirstPage->totalPages);
        $this->assertListItemEqualsCN003($listForFirstPage->listItems[0]);  // Items are ordered by columbarium name,
        $this->assertListItemEqualsCN001($listForFirstPage->listItems[1]);  // then by columbarium niche number.
        $this->assertListItemEqualsCN004($listForFirstPage->listItems[2]);

        // Second page
        $listForSecondPage = $this->columbariumNicheFetcher->findAll(2, null, $customPageSize);
        $this->assertInstanceOf(ColumbariumNicheList::class, $listForSecondPage);
        $this->assertIsArray($listForSecondPage->listItems);
        $this->assertContainsOnlyInstancesOf(ColumbariumNicheListItem::class, $listForSecondPage->listItems);
        $this->assertCount(3,              $listForSecondPage->listItems);
        $this->assertSame(2,               $listForSecondPage->page);
        $this->assertSame($customPageSize, $listForSecondPage->pageSize);
        $this->assertSame(null,            $listForSecondPage->term);
        $this->assertSame(7,               $listForSecondPage->totalCount);
        $this->assertSame(3,               $listForSecondPage->totalPages);
        $this->assertListItemEqualsCN006($listForSecondPage->listItems[0]);
        $this->assertListItemEqualsCN005($listForSecondPage->listItems[1]);
        $this->assertListItemEqualsCN007($listForSecondPage->listItems[2]);


        // Third page
        $listForThirdPage = $this->columbariumNicheFetcher->findAll(3, null, $customPageSize);
        $this->assertInstanceOf(ColumbariumNicheList::class, $listForThirdPage);
        $this->assertIsArray($listForThirdPage->listItems);
        $this->assertContainsOnlyInstancesOf(ColumbariumNicheListItem::class, $listForThirdPage->listItems);
        $this->assertCount(1,              $listForThirdPage->listItems);
        $this->assertSame(3,               $listForThirdPage->page);
        $this->assertSame($customPageSize, $listForThirdPage->pageSize);
        $this->assertSame(null,            $listForThirdPage->term);
        $this->assertSame(7,               $listForThirdPage->totalCount);
        $this->assertSame(3,               $listForThirdPage->totalPages);
        $this->assertListItemEqualsCN002($listForThirdPage->listItems[0]);

        // Default page size
        $listForDefaultPageSize = $this->columbariumNicheFetcher->findAll(1);
        $this->assertInstanceOf(ColumbariumNicheList::class, $listForDefaultPageSize);
        $this->assertIsArray($listForDefaultPageSize->listItems);
        $this->assertContainsOnlyInstancesOf(ColumbariumNicheListItem::class, $listForDefaultPageSize->listItems);
        $this->assertCount(7,                      $listForDefaultPageSize->listItems);
        $this->assertSame(1,                       $listForDefaultPageSize->page);
        $this->assertSame(self::DEFAULT_PAGE_SIZE, $listForDefaultPageSize->pageSize);
        $this->assertSame(null,                    $listForDefaultPageSize->term);
        $this->assertSame(7,                       $listForDefaultPageSize->totalCount);
        $this->assertSame(1,                       $listForDefaultPageSize->totalPages);
    }

    public function testItReturnsColumbariumNicheListByPageAndTerm(): void
    {
        $customPageSize = 3;

        $list = $this->columbariumNicheFetcher->findAll(1, 'южн', $customPageSize);
        $this->assertInstanceOf(ColumbariumNicheList::class, $list);
        $this->assertIsArray($list->listItems);
        $this->assertContainsOnlyInstancesOf(ColumbariumNicheListItem::class, $list->listItems);
        $this->assertCount(1,              $list->listItems);
        $this->assertSame(1,               $list->page);
        $this->assertSame($customPageSize, $list->pageSize);
        $this->assertSame('южн',           $list->term);
        $this->assertSame(1,               $list->totalCount);
        $this->assertSame(1,               $list->totalPages);

        $list = $this->columbariumNicheFetcher->findAll(1, 'СЕВЕРный', $customPageSize);
        $this->assertInstanceOf(ColumbariumNicheList::class, $list);
        $this->assertIsArray($list->listItems);
        $this->assertContainsOnlyInstancesOf(ColumbariumNicheListItem::class, $list->listItems);
        $this->assertCount(3,              $list->listItems);
        $this->assertSame(1,               $list->page);
        $this->assertSame($customPageSize, $list->pageSize);
        $this->assertSame('СЕВЕРный',      $list->term);
        $this->assertSame(4,               $list->totalCount);
        $this->assertSame(2,               $list->totalPages);
        $list = $this->columbariumNicheFetcher->findAll(2, 'СЕВЕРный', $customPageSize);
        $this->assertInstanceOf(ColumbariumNicheList::class, $list);
        $this->assertIsArray($list->listItems);
        $this->assertContainsOnlyInstancesOf(ColumbariumNicheListItem::class, $list->listItems);
        $this->assertCount(1,              $list->listItems);
        $this->assertSame(2,               $list->page);
        $this->assertSame($customPageSize, $list->pageSize);
        $this->assertSame('СЕВЕРный',      $list->term);
        $this->assertSame(4,               $list->totalCount);
        $this->assertSame(2,               $list->totalPages);

        $list = $this->columbariumNicheFetcher->findAll(1, '7', $customPageSize);
        $this->assertInstanceOf(ColumbariumNicheList::class, $list);
        $this->assertIsArray($list->listItems);
        $this->assertContainsOnlyInstancesOf(ColumbariumNicheListItem::class, $list->listItems);
        $this->assertCount(2,              $list->listItems);
        $this->assertSame(1,               $list->page);
        $this->assertSame($customPageSize, $list->pageSize);
        $this->assertSame('7',             $list->term);
        $this->assertSame(2,               $list->totalCount);
        $this->assertSame(1,               $list->totalPages);

        $list = $this->columbariumNicheFetcher->findAll(1, '00', $customPageSize);
        $this->assertInstanceOf(ColumbariumNicheList::class, $list);
        $this->assertIsArray($list->listItems);
        $this->assertContainsOnlyInstancesOf(ColumbariumNicheListItem::class, $list->listItems);
        $this->assertCount(3,              $list->listItems);
        $this->assertSame(1,               $list->page);
        $this->assertSame($customPageSize, $list->pageSize);
        $this->assertSame('00',            $list->term);
        $this->assertSame(7,               $list->totalCount);
        $this->assertSame(3,               $list->totalPages);
        $list = $this->columbariumNicheFetcher->findAll(2, '00', $customPageSize);
        $this->assertInstanceOf(ColumbariumNicheList::class, $list);
        $this->assertIsArray($list->listItems);
        $this->assertContainsOnlyInstancesOf(ColumbariumNicheListItem::class, $list->listItems);
        $this->assertCount(3,              $list->listItems);
        $this->assertSame(2,               $list->page);
        $this->assertSame($customPageSize, $list->pageSize);
        $this->assertSame('00',            $list->term);
        $this->assertSame(7,               $list->totalCount);
        $this->assertSame(3,               $list->totalPages);
        $list = $this->columbariumNicheFetcher->findAll(3, '00', $customPageSize);
        $this->assertInstanceOf(ColumbariumNicheList::class, $list);
        $this->assertIsArray($list->listItems);
        $this->assertContainsOnlyInstancesOf(ColumbariumNicheListItem::class, $list->listItems);
        $this->assertCount(1,              $list->listItems);
        $this->assertSame(3,               $list->page);
        $this->assertSame($customPageSize, $list->pageSize);
        $this->assertSame('00',            $list->term);
        $this->assertSame(7,               $list->totalCount);
        $this->assertSame(3,               $list->totalPages);

        $list = $this->columbariumNicheFetcher->findAll(1, '005', $customPageSize);
        $this->assertInstanceOf(ColumbariumNicheList::class, $list);
        $this->assertIsArray($list->listItems);
        $this->assertContainsOnlyInstancesOf(ColumbariumNicheListItem::class, $list->listItems);
        $this->assertCount(1,              $list->listItems);
        $this->assertSame(1,               $list->page);
        $this->assertSame($customPageSize, $list->pageSize);
        $this->assertSame('005',           $list->term);
        $this->assertSame(1,               $list->totalCount);
        $this->assertSame(1,               $list->totalPages);
    }

    public function testItReturnsColumbariumNicheTotalCount(): void
    {
        $this->assertSame(7, $this->columbariumNicheFetcher->countTotal());
    }

    public function testItDoesNotCountRemovedColumbariumNicheWhenCalculatingTotalCount(): void
    {
        // Prepare database table for testing
        $columbariumNicheToRemove = $this->columbariumNicheRepo->findById(new ColumbariumNicheId('CN004'));
        $this->columbariumNicheRepo->remove($columbariumNicheToRemove);

        // Testing itself
        $this->assertSame(6, $this->columbariumNicheFetcher->countTotal());
    }

    protected function loadFixtures(): void
    {
        $this->databaseTool->loadFixtures([
            ColumbariumFixtures::class,
            ColumbariumNicheFixtures::class,
        ]);
    }

    private function assertListItemEqualsCN001(ColumbariumNicheListItem $listItem): void
    {
        $this->assertSame('CN001',     $listItem->id);
        $this->assertSame('западный',  $listItem->columbariumName);
        $this->assertSame(1,           $listItem->rowInColumbarium);
        $this->assertSame('001',       $listItem->nicheNumber);
    }

    private function assertListItemEqualsCN002(ColumbariumNicheListItem $listItem): void
    {
        $this->assertSame('CN002',     $listItem->id);
        $this->assertSame('южный',     $listItem->columbariumName);
        $this->assertSame(2,           $listItem->rowInColumbarium);
        $this->assertSame('002',       $listItem->nicheNumber);
    }

    private function assertListItemEqualsCN003(ColumbariumNicheListItem $listItem): void
    {
        $this->assertSame('CN003',     $listItem->id);
        $this->assertSame('восточный', $listItem->columbariumName);
        $this->assertSame(3,           $listItem->rowInColumbarium);
        $this->assertSame('003',       $listItem->nicheNumber);
    }

    private function assertListItemEqualsCN004(ColumbariumNicheListItem $listItem): void
    {
        $this->assertSame('CN004',     $listItem->id);
        $this->assertSame('северный',  $listItem->columbariumName);
        $this->assertSame(4,           $listItem->rowInColumbarium);
        $this->assertSame('004',       $listItem->nicheNumber);
    }

    private function assertListItemEqualsCN005(ColumbariumNicheListItem $listItem): void
    {
        $this->assertSame('CN005',     $listItem->id);
        $this->assertSame('северный',  $listItem->columbariumName);
        $this->assertSame(5,           $listItem->rowInColumbarium);
        $this->assertSame('006',       $listItem->nicheNumber);
    }

    private function assertListItemEqualsCN006(ColumbariumNicheListItem $listItem): void
    {
        $this->assertSame('CN006',     $listItem->id);
        $this->assertSame('северный',  $listItem->columbariumName);
        $this->assertSame(7,           $listItem->rowInColumbarium);
        $this->assertSame('005',       $listItem->nicheNumber);
    }

    private function assertListItemEqualsCN007(ColumbariumNicheListItem $listItem): void
    {
        $this->assertSame('CN007',     $listItem->id);
        $this->assertSame('северный',  $listItem->columbariumName);
        $this->assertSame(7,           $listItem->rowInColumbarium);
        $this->assertSame('007',       $listItem->nicheNumber);
    }

    private function testItReturnsColumbariumNicheViewForCN001(): void
    {
        $view = $this->columbariumNicheFetcher->getViewById('CN001');
        $this->assertInstanceOf(ColumbariumNicheView::class, $view);
        $this->assertSame('CN001',        $view->id);
        $this->assertSame('C001',         $view->columbariumId);
        $this->assertSame(1,              $view->rowInColumbarium);
        $this->assertSame('001',          $view->nicheNumber);
        $this->assertSame(null,           $view->geoPositionLatitude);
        $this->assertSame(null,           $view->geoPositionLongitude);
        $this->assertSame(null,           $view->geoPositionError);
        $this->assertValidDateTimeValue($view->createdAt);
        $this->assertValidDateTimeValue($view->updatedAt);
    }

    private function testItReturnsColumbariumNicheViewForCN002(): void
    {
        $view = $this->columbariumNicheFetcher->getViewById('CN002');
        $this->assertInstanceOf(ColumbariumNicheView::class, $view);
        $this->assertSame('CN002',        $view->id);
        $this->assertSame('C002',         $view->columbariumId);
        $this->assertSame(2,              $view->rowInColumbarium);
        $this->assertSame('002',          $view->nicheNumber);
        $this->assertSame('54.95035712',  $view->geoPositionLatitude);
        $this->assertSame('82.79252',     $view->geoPositionLongitude);
        $this->assertSame('0.5',          $view->geoPositionError);
        $this->assertValidDateTimeValue($view->createdAt);
        $this->assertValidDateTimeValue($view->updatedAt);
    }

    private function testItReturnsColumbariumNicheViewForCN003(): void
    {
        $view = $this->columbariumNicheFetcher->getViewById('CN003');
        $this->assertInstanceOf(ColumbariumNicheView::class, $view);
        $this->assertSame('CN003',        $view->id);
        $this->assertSame('C003',         $view->columbariumId);
        $this->assertSame(3,              $view->rowInColumbarium);
        $this->assertSame('003',          $view->nicheNumber);
        $this->assertSame('-50.95',       $view->geoPositionLatitude);
        $this->assertSame('-179.7972252', $view->geoPositionLongitude);
        $this->assertSame(null,           $view->geoPositionError);
        $this->assertValidDateTimeValue($view->createdAt);
        $this->assertValidDateTimeValue($view->updatedAt);
    }

    private function testItReturnsColumbariumNicheViewForCN004(): void
    {
        $view = $this->columbariumNicheFetcher->getViewById('CN004');
        $this->assertInstanceOf(ColumbariumNicheView::class, $view);
        $this->assertSame('CN004',        $view->id);
        $this->assertSame('C004',         $view->columbariumId);
        $this->assertSame(4,              $view->rowInColumbarium);
        $this->assertSame('004',          $view->nicheNumber);
        $this->assertSame(null,           $view->geoPositionLatitude);
        $this->assertSame(null,           $view->geoPositionLongitude);
        $this->assertSame(null,           $view->geoPositionError);
        $this->assertValidDateTimeValue($view->createdAt);
        $this->assertValidDateTimeValue($view->updatedAt);
    }

    private function testItReturnsColumbariumNicheViewForCN005(): void
    {
        $view = $this->columbariumNicheFetcher->getViewById('CN005');
        $this->assertInstanceOf(ColumbariumNicheView::class, $view);
        $this->assertSame('CN005',        $view->id);
        $this->assertSame('C004',         $view->columbariumId);
        $this->assertSame(5,              $view->rowInColumbarium);
        $this->assertSame('006',          $view->nicheNumber);
        $this->assertSame(null,           $view->geoPositionLatitude);
        $this->assertSame(null,           $view->geoPositionLongitude);
        $this->assertSame(null,           $view->geoPositionError);
        $this->assertValidDateTimeValue($view->createdAt);
        $this->assertValidDateTimeValue($view->updatedAt);
    }

    private function testItReturnsColumbariumNicheViewForCN006(): void
    {
        $view = $this->columbariumNicheFetcher->getViewById('CN006');
        $this->assertInstanceOf(ColumbariumNicheView::class, $view);
        $this->assertSame('CN006',        $view->id);
        $this->assertSame('C004',         $view->columbariumId);
        $this->assertSame(7,              $view->rowInColumbarium);
        $this->assertSame('005',          $view->nicheNumber);
        $this->assertSame(null,           $view->geoPositionLatitude);
        $this->assertSame(null,           $view->geoPositionLongitude);
        $this->assertSame(null,           $view->geoPositionError);
        $this->assertValidDateTimeValue($view->createdAt);
        $this->assertValidDateTimeValue($view->updatedAt);
    }

    private function testItReturnsColumbariumNicheViewForCN007(): void
    {
        $view = $this->columbariumNicheFetcher->getViewById('CN007');
        $this->assertInstanceOf(ColumbariumNicheView::class, $view);
        $this->assertSame('CN007',        $view->id);
        $this->assertSame('C004',         $view->columbariumId);
        $this->assertSame(7,              $view->rowInColumbarium);
        $this->assertSame('007',          $view->nicheNumber);
        $this->assertSame(null,           $view->geoPositionLatitude);
        $this->assertSame(null,           $view->geoPositionLongitude);
        $this->assertSame(null,           $view->geoPositionError);
        $this->assertValidDateTimeValue($view->createdAt);
        $this->assertValidDateTimeValue($view->updatedAt);
    }

    private function expectExceptionForNotFoundColumbariumNicheById(string $columbariumNicheId): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(\sprintf('Колумбарная ниша с ID "%s" не найдена.', $columbariumNicheId));
    }
}
