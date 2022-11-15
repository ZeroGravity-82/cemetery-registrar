<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Fetcher;

use Cemetery\Registrar\Domain\Model\BurialPlace\ColumbariumNiche\ColumbariumNicheId;
use Cemetery\Registrar\Domain\Model\BurialPlace\ColumbariumNiche\ColumbariumNicheRepositoryInterface;
use Cemetery\Registrar\Domain\View\BurialPlace\ColumbariumNiche\ColumbariumNicheList;
use Cemetery\Registrar\Domain\View\BurialPlace\ColumbariumNiche\ColumbariumNicheListItem;
use Cemetery\Registrar\Domain\View\BurialPlace\ColumbariumNiche\ColumbariumNicheView;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Fetcher\DoctrineDbalColumbariumNicheFetcher;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Orm\Repository\DoctrineOrmColumbariumNicheRepository;
use DataFixtures\BurialPlace\ColumbariumNiche\ColumbariumFixtures;
use DataFixtures\BurialPlace\ColumbariumNiche\ColumbariumNicheFixtures;
use DataFixtures\NaturalPerson\NaturalPersonFixtures;

/**
 * @group database
 *
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class DoctrineDbalColumbariumNicheFetcherIntegrationTest extends DoctrineDbalFetcherIntegrationTest
{
    private ColumbariumNicheRepositoryInterface $repo;

    public function setUp(): void
    {
        parent::setUp();

        $this->repo    = new DoctrineOrmColumbariumNicheRepository($this->entityManager);
        $this->fetcher = new DoctrineDbalColumbariumNicheFetcher($this->connection);
        $this->loadFixtures();
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
        $this->testItReturnsColumbariumNicheViewForCN008();
    }

    public function testItReturnsNullForRemovedColumbariumNiche(): void
    {
        // Prepare database table for testing
        $columbariumNicheToRemove = $this->repo->findById(new ColumbariumNicheId('CN004'));
        $this->repo->remove($columbariumNicheToRemove);
        $removedColumbariumNicheId = $columbariumNicheToRemove->id()->value();

        // Testing itself
        $view = $this->fetcher->findViewById($removedColumbariumNicheId);
        $this->assertNull($view);
    }

    public function testItChecksExistenceById(): void
    {
        // Prepare database table for testing
        $columbariumNicheToRemove = $this->repo->findById(new ColumbariumNicheId('CN004'));
        $this->repo->remove($columbariumNicheToRemove);
        $removedColumbariumNicheId = $columbariumNicheToRemove->id()->value();

        $this->assertTrue($this->fetcher->doesExistById('CN001'));
        $this->assertFalse($this->fetcher->doesExistById('unknown_id'));
        $this->assertFalse($this->fetcher->doesExistById($removedColumbariumNicheId));
    }

    public function testItChecksExistenceByColumbariumIdAndNicheNumber(): void
    {
        $columbariumNicheToRemove = $this->repo->findById(new ColumbariumNicheId('CN004'));
        $this->repo->remove($columbariumNicheToRemove);
        $removedColumbariumNicheColumbariumId = $columbariumNicheToRemove->columbariumId()->value();
        $removedColumbariumNicheNumber        = $columbariumNicheToRemove->nicheNumber()->value();

        $this->assertTrue($this->fetcher->doesExistByColumbariumIdAndNicheNumber('C001', '001'));
        $this->assertFalse($this->fetcher->doesExistByColumbariumIdAndNicheNumber('unknown_columbarium_id', '001'));
        $this->assertFalse($this->fetcher->doesExistByColumbariumIdAndNicheNumber('C001', 'unknown_niche_number'));
        $this->assertFalse($this->fetcher->doesExistByColumbariumIdAndNicheNumber(
            $removedColumbariumNicheColumbariumId,
            $removedColumbariumNicheNumber,
        ));
    }

    public function testItReturnsColumbariumNichePaginatedListByPage(): void
    {
        $customPageSize = 3;

        // First page
        $listForFirstPage = $this->fetcher->paginate(1, null, $customPageSize);
        $this->assertInstanceOf(ColumbariumNicheList::class, $listForFirstPage);
        $this->assertIsArray($listForFirstPage->items);
        $this->assertContainsOnlyInstancesOf(ColumbariumNicheListItem::class, $listForFirstPage->items);
        $this->assertCount(3,              $listForFirstPage->items);
        $this->assertSame(1,               $listForFirstPage->page);
        $this->assertSame($customPageSize, $listForFirstPage->pageSize);
        $this->assertSame(null,            $listForFirstPage->term);
        $this->assertSame(8,               $listForFirstPage->totalCount);
        $this->assertSame(3,               $listForFirstPage->totalPages);
        $this->assertPaginatedListItemEqualsCN003($listForFirstPage->items[0]);  // Items are ordered by columbarium name,
        $this->assertPaginatedListItemEqualsCN001($listForFirstPage->items[1]);  // then by columbarium niche number.
        $this->assertPaginatedListItemEqualsCN008($listForFirstPage->items[2]);

        // Second page
        $listForSecondPage = $this->fetcher->paginate(2, null, $customPageSize);
        $this->assertInstanceOf(ColumbariumNicheList::class, $listForSecondPage);
        $this->assertIsArray($listForSecondPage->items);
        $this->assertContainsOnlyInstancesOf(ColumbariumNicheListItem::class, $listForSecondPage->items);
        $this->assertCount(3,              $listForSecondPage->items);
        $this->assertSame(2,               $listForSecondPage->page);
        $this->assertSame($customPageSize, $listForSecondPage->pageSize);
        $this->assertSame(null,            $listForSecondPage->term);
        $this->assertSame(8,               $listForSecondPage->totalCount);
        $this->assertSame(3,               $listForSecondPage->totalPages);
        $this->assertPaginatedListItemEqualsCN004($listForSecondPage->items[0]);
        $this->assertPaginatedListItemEqualsCN006($listForSecondPage->items[1]);
        $this->assertPaginatedListItemEqualsCN005($listForSecondPage->items[2]);

        // Third page
        $listForThirdPage = $this->fetcher->paginate(3, null, $customPageSize);
        $this->assertInstanceOf(ColumbariumNicheList::class, $listForThirdPage);
        $this->assertIsArray($listForThirdPage->items);
        $this->assertContainsOnlyInstancesOf(ColumbariumNicheListItem::class, $listForThirdPage->items);
        $this->assertCount(2,              $listForThirdPage->items);
        $this->assertSame(3,               $listForThirdPage->page);
        $this->assertSame($customPageSize, $listForThirdPage->pageSize);
        $this->assertSame(null,            $listForThirdPage->term);
        $this->assertSame(8,               $listForThirdPage->totalCount);
        $this->assertSame(3,               $listForThirdPage->totalPages);
        $this->assertPaginatedListItemEqualsCN007($listForThirdPage->items[0]);
        $this->assertPaginatedListItemEqualsCN002($listForThirdPage->items[1]);

        // Default page size
        $listForDefaultPageSize = $this->fetcher->paginate(1);
        $this->assertInstanceOf(ColumbariumNicheList::class, $listForDefaultPageSize);
        $this->assertIsArray($listForDefaultPageSize->items);
        $this->assertContainsOnlyInstancesOf(ColumbariumNicheListItem::class, $listForDefaultPageSize->items);
        $this->assertCount(8,                      $listForDefaultPageSize->items);
        $this->assertSame(1,                       $listForDefaultPageSize->page);
        $this->assertSame(self::DEFAULT_PAGE_SIZE, $listForDefaultPageSize->pageSize);
        $this->assertSame(null,                    $listForDefaultPageSize->term);
        $this->assertSame(8,                       $listForDefaultPageSize->totalCount);
        $this->assertSame(1,                       $listForDefaultPageSize->totalPages);
    }

    public function testItReturnsColumbariumNichePaginatedListByPageAndTerm(): void
    {
        $customPageSize = 3;

        $list = $this->fetcher->paginate(1, 'юЖн', $customPageSize);
        $this->assertInstanceOf(ColumbariumNicheList::class, $list);
        $this->assertIsArray($list->items);
        $this->assertContainsOnlyInstancesOf(ColumbariumNicheListItem::class, $list->items);
        $this->assertCount(1,              $list->items);
        $this->assertSame(1,               $list->page);
        $this->assertSame($customPageSize, $list->pageSize);
        $this->assertSame('юЖн',           $list->term);
        $this->assertSame(1,               $list->totalCount);
        $this->assertSame(1,               $list->totalPages);

        $list = $this->fetcher->paginate(1, 'вЕРный', $customPageSize);
        $this->assertInstanceOf(ColumbariumNicheList::class, $list);
        $this->assertIsArray($list->items);
        $this->assertContainsOnlyInstancesOf(ColumbariumNicheListItem::class, $list->items);
        $this->assertCount(3,              $list->items);
        $this->assertSame(1,               $list->page);
        $this->assertSame($customPageSize, $list->pageSize);
        $this->assertSame('вЕРный',        $list->term);
        $this->assertSame(5,               $list->totalCount);
        $this->assertSame(2,               $list->totalPages);
        $list = $this->fetcher->paginate(2, 'вЕРный', $customPageSize);
        $this->assertInstanceOf(ColumbariumNicheList::class, $list);
        $this->assertIsArray($list->items);
        $this->assertContainsOnlyInstancesOf(ColumbariumNicheListItem::class, $list->items);
        $this->assertCount(2,              $list->items);
        $this->assertSame(2,               $list->page);
        $this->assertSame($customPageSize, $list->pageSize);
        $this->assertSame('вЕРный',        $list->term);
        $this->assertSame(5,               $list->totalCount);
        $this->assertSame(2,               $list->totalPages);

        $list = $this->fetcher->paginate(1, 'гроМ', $customPageSize);
        $this->assertInstanceOf(ColumbariumNicheList::class, $list);
        $this->assertIsArray($list->items);
        $this->assertContainsOnlyInstancesOf(ColumbariumNicheListItem::class, $list->items);
        $this->assertCount(1,              $list->items);
        $this->assertSame(1,               $list->page);
        $this->assertSame($customPageSize, $list->pageSize);
        $this->assertSame('гроМ',          $list->term);
        $this->assertSame(1,               $list->totalCount);
        $this->assertSame(1,               $list->totalPages);

        $list = $this->fetcher->paginate(1, 'СЕВЕРный', $customPageSize);
        $this->assertInstanceOf(ColumbariumNicheList::class, $list);
        $this->assertIsArray($list->items);
        $this->assertContainsOnlyInstancesOf(ColumbariumNicheListItem::class, $list->items);
        $this->assertCount(3,              $list->items);
        $this->assertSame(1,               $list->page);
        $this->assertSame($customPageSize, $list->pageSize);
        $this->assertSame('СЕВЕРный',      $list->term);
        $this->assertSame(5,               $list->totalCount);
        $this->assertSame(2,               $list->totalPages);
        $list = $this->fetcher->paginate(2, 'СЕВЕРный', $customPageSize);
        $this->assertInstanceOf(ColumbariumNicheList::class, $list);
        $this->assertIsArray($list->items);
        $this->assertContainsOnlyInstancesOf(ColumbariumNicheListItem::class, $list->items);
        $this->assertCount(2,              $list->items);
        $this->assertSame(2,               $list->page);
        $this->assertSame($customPageSize, $list->pageSize);
        $this->assertSame('СЕВЕРный',      $list->term);
        $this->assertSame(5,               $list->totalCount);
        $this->assertSame(2,               $list->totalPages);

        $list = $this->fetcher->paginate(1, '5', $customPageSize);
        $this->assertInstanceOf(ColumbariumNicheList::class, $list);
        $this->assertIsArray($list->items);
        $this->assertContainsOnlyInstancesOf(ColumbariumNicheListItem::class, $list->items);
        $this->assertCount(3,              $list->items);
        $this->assertSame(1,               $list->page);
        $this->assertSame($customPageSize, $list->pageSize);
        $this->assertSame('5',             $list->term);
        $this->assertSame(3,               $list->totalCount);
        $this->assertSame(1,               $list->totalPages);
        $list = $this->fetcher->paginate(2, '5', $customPageSize);
        $this->assertInstanceOf(ColumbariumNicheList::class, $list);
        $this->assertIsArray($list->items);
        $this->assertContainsOnlyInstancesOf(ColumbariumNicheListItem::class, $list->items);
        $this->assertCount(0,              $list->items);
        $this->assertSame(2,               $list->page);
        $this->assertSame($customPageSize, $list->pageSize);
        $this->assertSame('5',             $list->term);
        $this->assertSame(3,               $list->totalCount);
        $this->assertSame(1,               $list->totalPages);

        $list = $this->fetcher->paginate(1, '00', $customPageSize);
        $this->assertInstanceOf(ColumbariumNicheList::class, $list);
        $this->assertIsArray($list->items);
        $this->assertContainsOnlyInstancesOf(ColumbariumNicheListItem::class, $list->items);
        $this->assertCount(3,              $list->items);
        $this->assertSame(1,               $list->page);
        $this->assertSame($customPageSize, $list->pageSize);
        $this->assertSame('00',            $list->term);
        $this->assertSame(8,               $list->totalCount);
        $this->assertSame(3,               $list->totalPages);
        $list = $this->fetcher->paginate(2, '00', $customPageSize);
        $this->assertInstanceOf(ColumbariumNicheList::class, $list);
        $this->assertIsArray($list->items);
        $this->assertContainsOnlyInstancesOf(ColumbariumNicheListItem::class, $list->items);
        $this->assertCount(3,              $list->items);
        $this->assertSame(2,               $list->page);
        $this->assertSame($customPageSize, $list->pageSize);
        $this->assertSame('00',            $list->term);
        $this->assertSame(8,               $list->totalCount);
        $this->assertSame(3,               $list->totalPages);
        $list = $this->fetcher->paginate(3, '00', $customPageSize);
        $this->assertInstanceOf(ColumbariumNicheList::class, $list);
        $this->assertIsArray($list->items);
        $this->assertContainsOnlyInstancesOf(ColumbariumNicheListItem::class, $list->items);
        $this->assertCount(2,              $list->items);
        $this->assertSame(3,               $list->page);
        $this->assertSame($customPageSize, $list->pageSize);
        $this->assertSame('00',            $list->term);
        $this->assertSame(8,               $list->totalCount);
        $this->assertSame(3,               $list->totalPages);

        $list = $this->fetcher->paginate(1, '001', $customPageSize);
        $this->assertInstanceOf(ColumbariumNicheList::class, $list);
        $this->assertIsArray($list->items);
        $this->assertContainsOnlyInstancesOf(ColumbariumNicheListItem::class, $list->items);
        $this->assertCount(2,              $list->items);
        $this->assertSame(1,               $list->page);
        $this->assertSame($customPageSize, $list->pageSize);
        $this->assertSame('001',           $list->term);
        $this->assertSame(2,               $list->totalCount);
        $this->assertSame(1,               $list->totalPages);
    }

    public function testItReturnsColumbariumNicheTotalCount(): void
    {
        $this->assertSame(8, $this->fetcher->countTotal());
    }

    public function testItDoesNotCountRemovedColumbariumNicheWhenCalculatingTotalCount(): void
    {
        // Prepare database table for testing
        $columbariumNicheToRemove = $this->repo->findById(new ColumbariumNicheId('CN004'));
        $this->repo->remove($columbariumNicheToRemove);

        // Testing itself
        $this->assertSame(7, $this->fetcher->countTotal());
    }

    protected function loadFixtures(): void
    {
        $this->databaseTool->loadFixtures([
            ColumbariumFixtures::class,
            ColumbariumNicheFixtures::class,
            NaturalPersonFixtures::class,
        ]);
    }

    private function assertPaginatedListItemEqualsCN001(ColumbariumNicheListItem $listItem): void
    {
        $this->assertSame('CN001',    $listItem->id);
        $this->assertSame('западный', $listItem->columbariumName);
        $this->assertSame(1,          $listItem->rowInColumbarium);
        $this->assertSame('001',      $listItem->nicheNumber);
        $this->assertSame(null,       $listItem->personInChargeId);
        $this->assertSame(null,       $listItem->personInChargeFullName);
    }

    private function assertPaginatedListItemEqualsCN002(ColumbariumNicheListItem $listItem): void
    {
        $this->assertSame('CN002',                      $listItem->id);
        $this->assertSame('южный',                      $listItem->columbariumName);
        $this->assertSame(2,                            $listItem->rowInColumbarium);
        $this->assertSame('002',                        $listItem->nicheNumber);
        $this->assertSame('NP007',                      $listItem->personInChargeId);
        $this->assertSame('Громов Никифор Рудольфович', $listItem->personInChargeFullName);
    }

    private function assertPaginatedListItemEqualsCN003(ColumbariumNicheListItem $listItem): void
    {
        $this->assertSame('CN003',     $listItem->id);
        $this->assertSame('восточный', $listItem->columbariumName);
        $this->assertSame(3,           $listItem->rowInColumbarium);
        $this->assertSame('003',       $listItem->nicheNumber);
        $this->assertSame(null,        $listItem->personInChargeId);
        $this->assertSame(null,        $listItem->personInChargeFullName);
    }

    private function assertPaginatedListItemEqualsCN004(ColumbariumNicheListItem $listItem): void
    {
        $this->assertSame('CN004',     $listItem->id);
        $this->assertSame('северный',  $listItem->columbariumName);
        $this->assertSame(4,           $listItem->rowInColumbarium);
        $this->assertSame('004',       $listItem->nicheNumber);
        $this->assertSame(null,        $listItem->personInChargeId);
        $this->assertSame(null,        $listItem->personInChargeFullName);
    }

    private function assertPaginatedListItemEqualsCN005(ColumbariumNicheListItem $listItem): void
    {
        $this->assertSame('CN005',    $listItem->id);
        $this->assertSame('северный', $listItem->columbariumName);
        $this->assertSame(5,          $listItem->rowInColumbarium);
        $this->assertSame('006',      $listItem->nicheNumber);
        $this->assertSame(null,       $listItem->personInChargeId);
        $this->assertSame(null,       $listItem->personInChargeFullName);
    }

    private function assertPaginatedListItemEqualsCN006(ColumbariumNicheListItem $listItem): void
    {
        $this->assertSame('CN006',    $listItem->id);
        $this->assertSame('северный', $listItem->columbariumName);
        $this->assertSame(7,          $listItem->rowInColumbarium);
        $this->assertSame('005',      $listItem->nicheNumber);
        $this->assertSame(null,       $listItem->personInChargeId);
        $this->assertSame(null,       $listItem->personInChargeFullName);
    }

    private function assertPaginatedListItemEqualsCN007(ColumbariumNicheListItem $listItem): void
    {
        $this->assertSame('CN007',    $listItem->id);
        $this->assertSame('северный', $listItem->columbariumName);
        $this->assertSame(7,          $listItem->rowInColumbarium);
        $this->assertSame('007',      $listItem->nicheNumber);
        $this->assertSame(null,       $listItem->personInChargeId);
        $this->assertSame(null,       $listItem->personInChargeFullName);
    }

    private function assertPaginatedListItemEqualsCN008(ColumbariumNicheListItem $listItem): void
    {
        $this->assertSame('CN008',    $listItem->id);
        $this->assertSame('северный', $listItem->columbariumName);
        $this->assertSame(5,          $listItem->rowInColumbarium);
        $this->assertSame('001',      $listItem->nicheNumber);
        $this->assertSame(null,       $listItem->personInChargeId);
        $this->assertSame(null,       $listItem->personInChargeFullName);
    }

    private function testItReturnsColumbariumNicheViewForCN001(): void
    {
        $view = $this->fetcher->findViewById('CN001');
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
        $view = $this->fetcher->findViewById('CN002');
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
        $view = $this->fetcher->findViewById('CN003');
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
        $view = $this->fetcher->findViewById('CN004');
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
        $view = $this->fetcher->findViewById('CN005');
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
        $view = $this->fetcher->findViewById('CN006');
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
        $view = $this->fetcher->findViewById('CN007');
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

    private function testItReturnsColumbariumNicheViewForCN008(): void
    {
        $view = $this->fetcher->findViewById('CN008');
        $this->assertInstanceOf(ColumbariumNicheView::class, $view);
        $this->assertSame('CN008',        $view->id);
        $this->assertSame('C004',         $view->columbariumId);
        $this->assertSame(5,              $view->rowInColumbarium);
        $this->assertSame('001',          $view->nicheNumber);
        $this->assertSame(null,           $view->geoPositionLatitude);
        $this->assertSame(null,           $view->geoPositionLongitude);
        $this->assertSame(null,           $view->geoPositionError);
        $this->assertValidDateTimeValue($view->createdAt);
        $this->assertValidDateTimeValue($view->updatedAt);
    }
}
