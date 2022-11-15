<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Fetcher;

use Cemetery\Registrar\Domain\Model\FuneralCompany\FuneralCompanyId;
use Cemetery\Registrar\Domain\Model\FuneralCompany\FuneralCompanyRepositoryInterface;
use Cemetery\Registrar\Domain\View\FuneralCompany\FuneralCompanyList;
use Cemetery\Registrar\Domain\View\FuneralCompany\FuneralCompanyListItem;
use Cemetery\Registrar\Domain\View\FuneralCompany\FuneralCompanyView;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Fetcher\DoctrineDbalFuneralCompanyFetcher;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Orm\Repository\DoctrineOrmFuneralCompanyRepository;
use DataFixtures\FuneralCompany\FuneralCompanyFixtures;

/**
 * @group database
 *
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class DoctrineDbalFuneralCompanyFetcherIntegrationTest extends AbstractDoctrineDbalFetcherIntegrationTest
{
    private FuneralCompanyRepositoryInterface $repo;

    public function setUp(): void
    {
        parent::setUp();

        $this->repo    = new DoctrineOrmFuneralCompanyRepository($this->entityManager);
        $this->fetcher = new DoctrineDbalFuneralCompanyFetcher($this->connection);
        $this->loadFixtures();
    }

    public function testItReturnsFuneralCompanyViewById(): void
    {
        $this->testItReturnsFuneralCompanyViewForFC001();
        $this->testItReturnsFuneralCompanyViewForFC002();
        $this->testItReturnsFuneralCompanyViewForFC003();
        $this->testItReturnsFuneralCompanyViewForFC004();
    }

    public function testItReturnsNullForRemovedFuneralCompany(): void
    {
        // Prepare database table for testing
        $funeralCompanyToRemove = $this->repo->findById(new FuneralCompanyId('FC004'));
        $this->repo->remove($funeralCompanyToRemove);
        $removedFuneralCompanyId = $funeralCompanyToRemove->id()->value();

        // Testing itself
        $view = $this->fetcher->findViewById($removedFuneralCompanyId);
        $this->assertNull($view);
    }

    public function testItChecksExistenceById(): void
    {
        // Prepare database table for testing
        $funeralCompanyToRemove = $this->repo->findById(new FuneralCompanyId('FC004'));
        $this->repo->remove($funeralCompanyToRemove);
        $removedFuneralCompanyId = $funeralCompanyToRemove->id()->value();

        $this->assertTrue($this->fetcher->doesExistById('FC001'));
        $this->assertFalse($this->fetcher->doesExistById('unknown_id'));
        $this->assertFalse($this->fetcher->doesExistById($removedFuneralCompanyId));
    }

    public function testItChecksExistenceByName(): void
    {
        // Prepare database table for testing
        $funeralCompanyToRemove = $this->repo->findById(new FuneralCompanyId('FC004'));
        $this->repo->remove($funeralCompanyToRemove);
        $removedCauseOfDeathName = $funeralCompanyToRemove->name()->value();

        $this->assertTrue($this->fetcher->doesExistByName('Апостол'));
        $this->assertFalse($this->fetcher->doesExistByName('unknown_name'));
        $this->assertFalse($this->fetcher->doesExistByName($removedCauseOfDeathName));
    }

    public function testItReturnsFuneralCompanyListFull(): void     // TODO rename
    {
        $this->markTestIncomplete();
//        $list = $this->fetcher->findAll();
//        $this->assertInstanceOf(FuneralCompanyList::class, $list);
//        $this->assertIsArray($list->items);
//        $this->assertContainsOnlyInstancesOf(FuneralCompanyListItem::class, $list->items);
//        $this->assertCount(4,                      $list->items);
//        $this->assertSame(null,                    $list->page);
//        $this->assertSame(self::DEFAULT_PAGE_SIZE, $list->pageSize);
//        $this->assertSame(null,                    $list->term);
//        $this->assertSame(4,                       $list->totalCount);
//        $this->assertSame(null,                    $list->totalPages);
//        $this->assertPaginatedListItemEqualsFC001($list->items[0]);  // Items are ordered by name
//        $this->assertPaginatedListItemEqualsFC003($list->items[1]);
//        $this->assertPaginatedListItemEqualsFC002($list->items[2]);
//        $this->assertPaginatedListItemEqualsFC004($list->items[3]);
    }

    public function testItReturnsFuneralCompanyPaginatedListByPage(): void
    {
        $customPageSize = 3;

        // First page
        $listForFirstPage = $this->fetcher->paginate(1, null, $customPageSize);
        $this->assertInstanceOf(FuneralCompanyList::class, $listForFirstPage);
        $this->assertIsArray($listForFirstPage->items);
        $this->assertContainsOnlyInstancesOf(FuneralCompanyListItem::class, $listForFirstPage->items);
        $this->assertCount(3,              $listForFirstPage->items);
        $this->assertSame(1,               $listForFirstPage->page);
        $this->assertSame($customPageSize, $listForFirstPage->pageSize);
        $this->assertSame(null,            $listForFirstPage->term);
        $this->assertSame(4,               $listForFirstPage->totalCount);
        $this->assertSame(2,               $listForFirstPage->totalPages);
        $this->assertPaginatedListItemEqualsFC001($listForFirstPage->items[0]);  // Items are ordered by name
        $this->assertPaginatedListItemEqualsFC003($listForFirstPage->items[1]);
        $this->assertPaginatedListItemEqualsFC002($listForFirstPage->items[2]);

        // Second page
        $listForSecondPage = $this->fetcher->paginate(2, null, $customPageSize);
        $this->assertInstanceOf(FuneralCompanyList::class, $listForSecondPage);
        $this->assertIsArray($listForSecondPage->items);
        $this->assertContainsOnlyInstancesOf(FuneralCompanyListItem::class, $listForSecondPage->items);
        $this->assertCount(1,              $listForSecondPage->items);
        $this->assertSame(2,               $listForSecondPage->page);
        $this->assertSame($customPageSize, $listForSecondPage->pageSize);
        $this->assertSame(null,            $listForSecondPage->term);
        $this->assertSame(4,               $listForSecondPage->totalCount);
        $this->assertSame(2,               $listForSecondPage->totalPages);
        $this->assertPaginatedListItemEqualsFC004($listForSecondPage->items[0]);

        // Third page
        $listForThirdPage = $this->fetcher->paginate(3, null, $customPageSize);
        $this->assertInstanceOf(FuneralCompanyList::class, $listForThirdPage);
        $this->assertIsArray($listForThirdPage->items);
        $this->assertCount(0,              $listForThirdPage->items);
        $this->assertSame(3,               $listForThirdPage->page);
        $this->assertSame($customPageSize, $listForThirdPage->pageSize);
        $this->assertSame(null,            $listForThirdPage->term);
        $this->assertSame(4,               $listForThirdPage->totalCount);
        $this->assertSame(2,               $listForThirdPage->totalPages);

        // Default page size
        $listForDefaultPageSize = $this->fetcher->paginate(1);
        $this->assertInstanceOf(FuneralCompanyList::class, $listForDefaultPageSize);
        $this->assertIsArray($listForDefaultPageSize->items);
        $this->assertContainsOnlyInstancesOf(FuneralCompanyListItem::class, $listForDefaultPageSize->items);
        $this->assertCount(4,                      $listForDefaultPageSize->items);
        $this->assertSame(1,                       $listForDefaultPageSize->page);
        $this->assertSame(self::DEFAULT_PAGE_SIZE, $listForDefaultPageSize->pageSize);
        $this->assertSame(null,                    $listForDefaultPageSize->term);
        $this->assertSame(4,                       $listForDefaultPageSize->totalCount);
        $this->assertSame(1,                       $listForDefaultPageSize->totalPages);
    }

    public function testItReturnsFuneralCompanyPaginatedListByPageAndTerm(): void
    {
        $customPageSize = 3;

        $list = $this->fetcher->paginate(1, 'Кемеров', $customPageSize);
        $this->assertInstanceOf(FuneralCompanyList::class, $list);
        $this->assertIsArray($list->items);
        $this->assertContainsOnlyInstancesOf(FuneralCompanyListItem::class, $list->items);
        $this->assertCount(1,              $list->items);
        $this->assertSame(1,               $list->page);
        $this->assertSame($customPageSize, $list->pageSize);
        $this->assertSame('Кемеров',       $list->term);
        $this->assertSame(1,               $list->totalCount);
        $this->assertSame(1,               $list->totalPages);

        $list = $this->fetcher->paginate(1, 'По', $customPageSize);
        $this->assertInstanceOf(FuneralCompanyList::class, $list);
        $this->assertIsArray($list->items);
        $this->assertContainsOnlyInstancesOf(FuneralCompanyListItem::class, $list->items);
        $this->assertCount(3,              $list->items);
        $this->assertSame(1,               $list->page);
        $this->assertSame($customPageSize, $list->pageSize);
        $this->assertSame('По',            $list->term);
        $this->assertSame(4,               $list->totalCount);
        $this->assertSame(2,               $list->totalPages);
        $list = $this->fetcher->paginate(2, 'По', $customPageSize);
        $this->assertInstanceOf(FuneralCompanyList::class, $list);
        $this->assertIsArray($list->items);
        $this->assertContainsOnlyInstancesOf(FuneralCompanyListItem::class, $list->items);
        $this->assertCount(1,              $list->items);
        $this->assertSame(2,               $list->page);
        $this->assertSame($customPageSize, $list->pageSize);
        $this->assertSame('По',            $list->term);
        $this->assertSame(4,               $list->totalCount);
        $this->assertSame(2,               $list->totalPages);

        $list = $this->fetcher->paginate(1, '388', $customPageSize);
        $this->assertInstanceOf(FuneralCompanyList::class, $list);
        $this->assertIsArray($list->items);
        $this->assertContainsOnlyInstancesOf(FuneralCompanyListItem::class, $list->items);
        $this->assertCount(1,              $list->items);
        $this->assertSame(1,               $list->page);
        $this->assertSame($customPageSize, $list->pageSize);
        $this->assertSame('388',           $list->term);
        $this->assertSame(1,               $list->totalCount);
        $this->assertSame(1,               $list->totalPages);

        $list = $this->fetcher->paginate(1, 'рО', $customPageSize);
        $this->assertInstanceOf(FuneralCompanyList::class, $list);
        $this->assertIsArray($list->items);
        $this->assertContainsOnlyInstancesOf(FuneralCompanyListItem::class, $list->items);
        $this->assertCount(3,              $list->items);
        $this->assertSame(1,               $list->page);
        $this->assertSame($customPageSize, $list->pageSize);
        $this->assertSame('рО',            $list->term);
        $this->assertSame(3,               $list->totalCount);
        $this->assertSame(1,               $list->totalPages);
    }

    public function testItReturnsFuneralCompanyTotalCount(): void
    {
        $this->assertSame(4, $this->fetcher->countTotal());
    }

    public function testItDoesNotCountRemovedFuneralCompanyWhenCalculatingTotalCount(): void
    {
        // Prepare database table for testing
        $funeralCompanyToRemove = $this->repo->findById(new FuneralCompanyId('FC004'));
        $this->repo->remove($funeralCompanyToRemove);

        // Testing itself
        $this->assertSame(3, $this->fetcher->countTotal());
    }

    protected function loadFixtures(): void
    {
        $this->databaseTool->loadFixtures([
            FuneralCompanyFixtures::class,
        ]);
    }

    private function assertPaginatedListItemEqualsFC001(FuneralCompanyListItem $listItem): void
    {
        $this->assertSame('FC001',   $listItem->id);
        $this->assertSame('Апостол', $listItem->name);
        $this->assertSame(null,      $listItem->note);
    }

    private function assertPaginatedListItemEqualsFC002(FuneralCompanyListItem $listItem): void
    {
        $this->assertSame('FC002',                      $listItem->id);
        $this->assertSame('Мемориал',                   $listItem->name);
        $this->assertSame('Фирма расположена в Кемерове', $listItem->note);
    }

    private function assertPaginatedListItemEqualsFC003(FuneralCompanyListItem $listItem): void
    {
        $this->assertSame('FC003',                               $listItem->id);
        $this->assertSame('Городская ритуальная служба',         $listItem->name);
        $this->assertSame('Покрышкина 29, +7(383)388-85-90', $listItem->note);
    }

    private function assertPaginatedListItemEqualsFC004(FuneralCompanyListItem $listItem): void
    {
        $this->assertSame('FC004',                      $listItem->id);
        $this->assertSame('Похоронный Дом "Некрополь"', $listItem->name);
        $this->assertSame(null,                         $listItem->note);
    }

    private function testItReturnsFuneralCompanyViewForFC001(): void
    {
        $view = $this->fetcher->findViewById('FC001');
        $this->assertInstanceOf(FuneralCompanyView::class, $view);
        $this->assertSame('FC001',   $view->id);
        $this->assertSame('Апостол', $view->name);
        $this->assertSame(null,      $view->note);
        $this->assertValidDateTimeValue($view->createdAt);
        $this->assertValidDateTimeValue($view->updatedAt);
    }

    private function testItReturnsFuneralCompanyViewForFC002(): void
    {
        $view = $this->fetcher->findViewById('FC002');
        $this->assertInstanceOf(FuneralCompanyView::class, $view);
        $this->assertSame('FC002',                        $view->id);
        $this->assertSame('Мемориал',                     $view->name);
        $this->assertSame('Фирма расположена в Кемерове', $view->note);
        $this->assertValidDateTimeValue($view->createdAt);
        $this->assertValidDateTimeValue($view->updatedAt);
    }

    private function testItReturnsFuneralCompanyViewForFC003(): void
    {
        $view = $this->fetcher->findViewById('FC003');
        $this->assertInstanceOf(FuneralCompanyView::class, $view);
        $this->assertSame('FC003',                           $view->id);
        $this->assertSame('Городская ритуальная служба',     $view->name);
        $this->assertSame('Покрышкина 29, +7(383)388-85-90', $view->note);
        $this->assertValidDateTimeValue($view->createdAt);
        $this->assertValidDateTimeValue($view->updatedAt);
    }

    private function testItReturnsFuneralCompanyViewForFC004(): void
    {
        $view = $this->fetcher->findViewById('FC004');
        $this->assertInstanceOf(FuneralCompanyView::class, $view);
        $this->assertSame('FC004',                      $view->id);
        $this->assertSame('Похоронный Дом "Некрополь"', $view->name);
        $this->assertSame(null,                         $view->note);
        $this->assertValidDateTimeValue($view->createdAt);
        $this->assertValidDateTimeValue($view->updatedAt);
    }
}
