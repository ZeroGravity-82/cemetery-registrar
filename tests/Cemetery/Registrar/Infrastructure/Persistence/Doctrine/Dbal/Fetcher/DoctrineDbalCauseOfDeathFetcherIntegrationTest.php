<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Fetcher;

use Cemetery\Registrar\Domain\Model\CauseOfDeath\CauseOfDeathId;
use Cemetery\Registrar\Domain\Model\CauseOfDeath\CauseOfDeathRepository;
use Cemetery\Registrar\Domain\View\CauseOfDeath\CauseOfDeathList;
use Cemetery\Registrar\Domain\View\CauseOfDeath\CauseOfDeathListItem;
use Cemetery\Registrar\Domain\View\CauseOfDeath\CauseOfDeathView;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Fetcher\DoctrineDbalCauseOfDeathFetcher;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Orm\Repository\DoctrineOrmCauseOfDeathRepository;
use DataFixtures\CauseOfDeath\CauseOfDeathFixtures;

/**
 * @group database
 *
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class DoctrineDbalCauseOfDeathFetcherIntegrationTest extends DoctrineDbalFetcherIntegrationTest
{
    private CauseOfDeathRepository $repo;

    public function setUp(): void
    {
        parent::setUp();

        $this->repo    = new DoctrineOrmCauseOfDeathRepository($this->entityManager);
        $this->fetcher = new DoctrineDbalCauseOfDeathFetcher($this->connection);
        $this->loadFixtures();
    }

    public function testItReturnsCauseOfDeathViewById(): void
    {
        $this->testItReturnsCauseOfDeathViewForCD001();
        $this->testItReturnsCauseOfDeathViewForCD002();
        $this->testItReturnsCauseOfDeathViewForCD003();
        $this->testItReturnsCauseOfDeathViewForCD004();
        $this->testItReturnsCauseOfDeathViewForCD005();
        $this->testItReturnsCauseOfDeathViewForCD006();
        $this->testItReturnsCauseOfDeathViewForCD007();
        $this->testItReturnsCauseOfDeathViewForCD008();
    }

    public function testItReturnsNullForRemovedCauseOfDeath(): void
    {
        // Prepare database table for testing
        $causeOfDeathToRemove = $this->repo->findById(new CauseOfDeathId('CD004'));
        $this->repo->remove($causeOfDeathToRemove);
        $removedCauseOfDeathId = $causeOfDeathToRemove->id()->value();

        // Testing itself
        $view = $this->fetcher->findViewById($removedCauseOfDeathId);
        $this->assertNull($view);
    }

    public function testItChecksExistenceById(): void
    {
        // Prepare database table for testing
        $causeOfDeathToRemove = $this->repo->findById(new CauseOfDeathId('CD004'));
        $this->repo->remove($causeOfDeathToRemove);
        $removedCauseOfDeathId = $causeOfDeathToRemove->id()->value();

        $this->assertTrue($this->fetcher->doesExistById('CD001'));
        $this->assertFalse($this->fetcher->doesExistById('unknown_id'));
        $this->assertFalse($this->fetcher->doesExistById($removedCauseOfDeathId));
    }

    public function testItChecksExistenceByName(): void
    {
        // Prepare database table for testing
        $causeOfDeathToRemove = $this->repo->findById(new CauseOfDeathId('CD004'));
        $this->repo->remove($causeOfDeathToRemove);
        $removedCauseOfDeathName = $causeOfDeathToRemove->name()->value();

        $this->assertTrue($this->fetcher->doesExistByName('COVID-19'));
        $this->assertFalse($this->fetcher->doesExistByName('unknown_name'));
        $this->assertFalse($this->fetcher->doesExistByName($removedCauseOfDeathName));
    }

    public function testItReturnsCauseOfDeathListFull(): void       // TODO rename
    {
        $this->markTestIncomplete();
//        $list = $this->fetcher->findAll();
//        $this->assertInstanceOf(CauseOfDeathList::class, $list);
//        $this->assertIsArray($list->items);
//        $this->assertContainsOnlyInstancesOf(CauseOfDeathListItem::class, $list->items);
//        $this->assertCount(8,                      $list->items);
//        $this->assertSame(null,                    $list->page);
//        $this->assertSame(self::DEFAULT_PAGE_SIZE, $list->pageSize);
//        $this->assertSame(null,                    $list->term);
//        $this->assertSame(8,                       $list->totalCount);
//        $this->assertSame(null,                    $list->totalPages);
//        $this->assertPaginatedListItemEqualsCD001($list->items[0]);  // Items are ordered by name
//        $this->assertPaginatedListItemEqualsCD007($list->items[1]);
//        $this->assertPaginatedListItemEqualsCD005($list->items[2]);
//        $this->assertPaginatedListItemEqualsCD006($list->items[3]);
//        $this->assertPaginatedListItemEqualsCD003($list->items[4]);
//        $this->assertPaginatedListItemEqualsCD008($list->items[5]);
//        $this->assertPaginatedListItemEqualsCD002($list->items[6]);
//        $this->assertPaginatedListItemEqualsCD004($list->items[7]);
    }

    public function testItReturnsCauseOfDeathPaginatedListByPage(): void
    {
        $customPageSize = 3;

        // First page
        $listForFirstPage = $this->fetcher->paginate(1, null, $customPageSize);
        $this->assertInstanceOf(CauseOfDeathList::class, $listForFirstPage);
        $this->assertIsArray($listForFirstPage->items);
        $this->assertContainsOnlyInstancesOf(CauseOfDeathListItem::class, $listForFirstPage->items);
        $this->assertCount(3,              $listForFirstPage->items);
        $this->assertSame(1,               $listForFirstPage->page);
        $this->assertSame($customPageSize, $listForFirstPage->pageSize);
        $this->assertSame(null,            $listForFirstPage->term);
        $this->assertSame(8,               $listForFirstPage->totalCount);
        $this->assertSame(3,               $listForFirstPage->totalPages);
        $this->assertPaginatedListItemEqualsCD001($listForFirstPage->items[0]);  // Items are ordered by name
        $this->assertPaginatedListItemEqualsCD007($listForFirstPage->items[1]);
        $this->assertPaginatedListItemEqualsCD005($listForFirstPage->items[2]);

        // Second page
        $listForSecondPage = $this->fetcher->paginate(2, null, $customPageSize);
        $this->assertInstanceOf(CauseOfDeathList::class, $listForSecondPage);
        $this->assertIsArray($listForSecondPage->items);
        $this->assertContainsOnlyInstancesOf(CauseOfDeathListItem::class, $listForSecondPage->items);
        $this->assertCount(3,              $listForSecondPage->items);
        $this->assertSame(2,               $listForSecondPage->page);
        $this->assertSame($customPageSize, $listForSecondPage->pageSize);
        $this->assertSame(null,            $listForSecondPage->term);
        $this->assertSame(8,               $listForSecondPage->totalCount);
        $this->assertSame(3,               $listForSecondPage->totalPages);
        $this->assertPaginatedListItemEqualsCD006($listForSecondPage->items[0]);
        $this->assertPaginatedListItemEqualsCD003($listForSecondPage->items[1]);
        $this->assertPaginatedListItemEqualsCD008($listForSecondPage->items[2]);

        // Third page
        $listForThirdPage = $this->fetcher->paginate(3, null, $customPageSize);
        $this->assertInstanceOf(CauseOfDeathList::class, $listForThirdPage);
        $this->assertIsArray($listForThirdPage->items);
        $this->assertContainsOnlyInstancesOf(CauseOfDeathListItem::class, $listForThirdPage->items);
        $this->assertCount(2,              $listForThirdPage->items);
        $this->assertSame(3,               $listForThirdPage->page);
        $this->assertSame($customPageSize, $listForThirdPage->pageSize);
        $this->assertSame(null,            $listForThirdPage->term);
        $this->assertSame(8,               $listForThirdPage->totalCount);
        $this->assertSame(3,               $listForThirdPage->totalPages);
        $this->assertPaginatedListItemEqualsCD002($listForThirdPage->items[0]);
        $this->assertPaginatedListItemEqualsCD004($listForThirdPage->items[1]);

        // Fourth page
        $listForFourthPage = $this->fetcher->paginate(4, null, $customPageSize);
        $this->assertInstanceOf(CauseOfDeathList::class, $listForFourthPage);
        $this->assertIsArray($listForFourthPage->items);
        $this->assertCount(0,              $listForFourthPage->items);
        $this->assertSame(4,               $listForFourthPage->page);
        $this->assertSame($customPageSize, $listForFourthPage->pageSize);
        $this->assertSame(null,            $listForFourthPage->term);
        $this->assertSame(8,               $listForFourthPage->totalCount);
        $this->assertSame(3,               $listForFourthPage->totalPages);

        // Default page size
        $listForDefaultPageSize = $this->fetcher->paginate(1);
        $this->assertInstanceOf(CauseOfDeathList::class, $listForDefaultPageSize);
        $this->assertIsArray($listForDefaultPageSize->items);
        $this->assertContainsOnlyInstancesOf(CauseOfDeathListItem::class, $listForDefaultPageSize->items);
        $this->assertCount(8,                      $listForDefaultPageSize->items);
        $this->assertSame(1,                       $listForDefaultPageSize->page);
        $this->assertSame(self::DEFAULT_PAGE_SIZE, $listForDefaultPageSize->pageSize);
        $this->assertSame(null,                    $listForDefaultPageSize->term);
        $this->assertSame(8,                       $listForDefaultPageSize->totalCount);
        $this->assertSame(1,                       $listForDefaultPageSize->totalPages);
    }

    public function testItReturnsCauseOfDeathPaginatedListByPageAndTerm(): void
    {
        $customPageSize = 3;

        $list = $this->fetcher->paginate(1, 'Болезнь', $customPageSize);
        $this->assertInstanceOf(CauseOfDeathList::class, $list);
        $this->assertIsArray($list->items);
        $this->assertContainsOnlyInstancesOf(CauseOfDeathListItem::class, $list->items);
        $this->assertCount(3,              $list->items);
        $this->assertSame(1,               $list->page);
        $this->assertSame($customPageSize, $list->pageSize);
        $this->assertSame('Болезнь',       $list->term);
        $this->assertSame(3,               $list->totalCount);
        $this->assertSame(1,               $list->totalPages);

        $list = $this->fetcher->paginate(1, 'серд', $customPageSize);
        $this->assertInstanceOf(CauseOfDeathList::class, $list);
        $this->assertIsArray($list->items);
        $this->assertContainsOnlyInstancesOf(CauseOfDeathListItem::class, $list->items);
        $this->assertCount(2,              $list->items);
        $this->assertSame(1,               $list->page);
        $this->assertSame($customPageSize, $list->pageSize);
        $this->assertSame('серд',          $list->term);
        $this->assertSame(2,               $list->totalCount);
        $this->assertSame(1,               $list->totalPages);

        $list = $this->fetcher->paginate(1, 'coV', $customPageSize);
        $this->assertInstanceOf(CauseOfDeathList::class, $list);
        $this->assertIsArray($list->items);
        $this->assertContainsOnlyInstancesOf(CauseOfDeathListItem::class, $list->items);
        $this->assertCount(1,              $list->items);
        $this->assertSame(1,               $list->page);
        $this->assertSame($customPageSize, $list->pageSize);
        $this->assertSame('coV',           $list->term);
        $this->assertSame(1,               $list->totalCount);
        $this->assertSame(1,               $list->totalPages);

        $list = $this->fetcher->paginate(1, 'аЯ', $customPageSize);
        $this->assertInstanceOf(CauseOfDeathList::class, $list);
        $this->assertIsArray($list->items);
        $this->assertContainsOnlyInstancesOf(CauseOfDeathListItem::class, $list->items);
        $this->assertCount(3,              $list->items);
        $this->assertSame(1,               $list->page);
        $this->assertSame($customPageSize, $list->pageSize);
        $this->assertSame('аЯ',            $list->term);
        $this->assertSame(5,               $list->totalCount);
        $this->assertSame(2,               $list->totalPages);
        $list = $this->fetcher->paginate(2, 'аЯ', $customPageSize);
        $this->assertInstanceOf(CauseOfDeathList::class, $list);
        $this->assertIsArray($list->items);
        $this->assertContainsOnlyInstancesOf(CauseOfDeathListItem::class, $list->items);
        $this->assertCount(2,              $list->items);
        $this->assertSame(2,               $list->page);
        $this->assertSame($customPageSize, $list->pageSize);
        $this->assertSame('аЯ',            $list->term);
        $this->assertSame(5,               $list->totalCount);
        $this->assertSame(2,               $list->totalPages);
    }

    public function testItReturnsCauseOfDeathTotalCount(): void
    {
        $this->assertSame(8, $this->fetcher->countTotal());
    }

    public function testItDoesNotCountRemovedCauseOfDeathWhenCalculatingTotalCount(): void
    {
        // Prepare database table for testing
        $causeOfDeathToRemove = $this->repo->findById(new CauseOfDeathId('CD004'));
        $this->repo->remove($causeOfDeathToRemove);

        // Testing itself
        $this->assertSame(7, $this->fetcher->countTotal());
    }

    protected function loadFixtures(): void
    {
        $this->databaseTool->loadFixtures([
            CauseOfDeathFixtures::class,
        ]);
    }

    private function assertPaginatedListItemEqualsCD001(CauseOfDeathListItem $listItem): void
    {
        $this->assertSame('CD001',    $listItem->id);
        $this->assertSame('COVID-19', $listItem->name);
    }

    private function assertPaginatedListItemEqualsCD002(CauseOfDeathListItem $listItem): void
    {
        $this->assertSame('CD002',                        $listItem->id);
        $this->assertSame('Обструктивная болезнь легких', $listItem->name);
    }

    private function assertPaginatedListItemEqualsCD003(CauseOfDeathListItem $listItem): void
    {
        $this->assertSame('CD003',                              $listItem->id);
        $this->assertSame('Атеросклеротическая болезнь сердца', $listItem->name);
    }

    private function assertPaginatedListItemEqualsCD004(CauseOfDeathListItem $listItem): void
    {
        $this->assertSame('CD004',     $listItem->id);
        $this->assertSame('Онкология', $listItem->name);
    }

    private function assertPaginatedListItemEqualsCD005(CauseOfDeathListItem $listItem): void
    {
        $this->assertSame('CD005',             $listItem->id);
        $this->assertSame('Астма кардиальная', $listItem->name);
    }

    private function assertPaginatedListItemEqualsCD006(CauseOfDeathListItem $listItem): void
    {
        $this->assertSame('CD006',    $listItem->id);
        $this->assertSame('Асфиксия', $listItem->name);
    }

    private function assertPaginatedListItemEqualsCD007(CauseOfDeathListItem $listItem): void
    {
        $this->assertSame('CD007',                               $listItem->id);
        $this->assertSame('Аневризма брюшной аорты разорванная', $listItem->name);
    }

    private function assertPaginatedListItemEqualsCD008(CauseOfDeathListItem $listItem): void
    {
        $this->assertSame('CD008',                                 $listItem->id);
        $this->assertSame('Болезнь сердечно-легочная хроническая', $listItem->name);
    }

    private function testItReturnsCauseOfDeathViewForCD001(): void
    {
        $view = $this->fetcher->findViewById('CD001');
        $this->assertInstanceOf(CauseOfDeathView::class, $view);
        $this->assertSame('CD001',    $view->id);
        $this->assertSame('COVID-19', $view->name);
        $this->assertValidDateTimeValue($view->createdAt);
        $this->assertValidDateTimeValue($view->updatedAt);
    }

    private function testItReturnsCauseOfDeathViewForCD002(): void
    {
        $view = $this->fetcher->findViewById('CD002');
        $this->assertInstanceOf(CauseOfDeathView::class, $view);
        $this->assertSame('CD002',                        $view->id);
        $this->assertSame('Обструктивная болезнь легких', $view->name);
        $this->assertValidDateTimeValue($view->createdAt);
        $this->assertValidDateTimeValue($view->updatedAt);
    }

    private function testItReturnsCauseOfDeathViewForCD003(): void
    {
        $view = $this->fetcher->findViewById('CD003');
        $this->assertInstanceOf(CauseOfDeathView::class, $view);
        $this->assertSame('CD003',                              $view->id);
        $this->assertSame('Атеросклеротическая болезнь сердца', $view->name);
        $this->assertValidDateTimeValue($view->createdAt);
        $this->assertValidDateTimeValue($view->updatedAt);
    }

    private function testItReturnsCauseOfDeathViewForCD004(): void
    {
        $view = $this->fetcher->findViewById('CD004');
        $this->assertInstanceOf(CauseOfDeathView::class, $view);
        $this->assertSame('CD004',     $view->id);
        $this->assertSame('Онкология', $view->name);
        $this->assertValidDateTimeValue($view->createdAt);
        $this->assertValidDateTimeValue($view->updatedAt);
    }

    private function testItReturnsCauseOfDeathViewForCD005(): void
    {
        $view = $this->fetcher->findViewById('CD005');
        $this->assertInstanceOf(CauseOfDeathView::class, $view);
        $this->assertSame('CD005',             $view->id);
        $this->assertSame('Астма кардиальная', $view->name);
        $this->assertValidDateTimeValue($view->createdAt);
        $this->assertValidDateTimeValue($view->updatedAt);
    }

    private function testItReturnsCauseOfDeathViewForCD006(): void
    {
        $view = $this->fetcher->findViewById('CD006');
        $this->assertInstanceOf(CauseOfDeathView::class, $view);
        $this->assertSame('CD006',    $view->id);
        $this->assertSame('Асфиксия', $view->name);
        $this->assertValidDateTimeValue($view->createdAt);
        $this->assertValidDateTimeValue($view->updatedAt);
    }

    private function testItReturnsCauseOfDeathViewForCD007(): void
    {
        $view = $this->fetcher->findViewById('CD007');
        $this->assertInstanceOf(CauseOfDeathView::class, $view);
        $this->assertSame('CD007',                               $view->id);
        $this->assertSame('Аневризма брюшной аорты разорванная', $view->name);
        $this->assertValidDateTimeValue($view->createdAt);
        $this->assertValidDateTimeValue($view->updatedAt);
    }

    private function testItReturnsCauseOfDeathViewForCD008(): void
    {
        $view = $this->fetcher->findViewById('CD008');
        $this->assertInstanceOf(CauseOfDeathView::class, $view);
        $this->assertSame('CD008',                                 $view->id);
        $this->assertSame('Болезнь сердечно-легочная хроническая', $view->name);
        $this->assertValidDateTimeValue($view->createdAt);
        $this->assertValidDateTimeValue($view->updatedAt);
    }
}
