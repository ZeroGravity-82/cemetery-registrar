<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Fetcher;

use Cemetery\Registrar\Domain\Model\CauseOfDeath\CauseOfDeathId;
use Cemetery\Registrar\Domain\Model\CauseOfDeath\CauseOfDeathRepository;
use Cemetery\Registrar\Domain\View\CauseOfDeath\CauseOfDeathList;
use Cemetery\Registrar\Domain\View\CauseOfDeath\CauseOfDeathListItem;
use Cemetery\Registrar\Domain\View\CauseOfDeath\CauseOfDeathView;
use Cemetery\Registrar\Domain\View\Fetcher;
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

    public function testItReturnsCauseOfDeathListFull(): void
    {
        $list = $this->fetcher->findAll();
        $this->assertInstanceOf(CauseOfDeathList::class, $list);
        $this->assertIsArray($list->items);
        $this->assertContainsOnlyInstancesOf(CauseOfDeathListItem::class, $list->items);
        $this->assertCount(8,                      $list->items);
        $this->assertSame(null,                    $list->page);
        $this->assertSame(self::DEFAULT_PAGE_SIZE, $list->pageSize);
        $this->assertSame(null,                    $list->term);
        $this->assertSame(8,                       $list->totalCount);
        $this->assertSame(null,                    $list->totalPages);
        $this->assertListItemEqualsCD001($list->items[0]);  // Items are ordered by name
        $this->assertListItemEqualsCD007($list->items[1]);
        $this->assertListItemEqualsCD005($list->items[2]);
        $this->assertListItemEqualsCD006($list->items[3]);
        $this->assertListItemEqualsCD003($list->items[4]);
        $this->assertListItemEqualsCD008($list->items[5]);
        $this->assertListItemEqualsCD002($list->items[6]);
        $this->assertListItemEqualsCD004($list->items[7]);
    }

    public function testItReturnsCauseOfDeathListByTerm(): void
    {
        $list = $this->fetcher->findAll(null, 'ая');
        $this->assertInstanceOf(CauseOfDeathList::class, $list);
        $this->assertIsArray($list->items);
        $this->assertContainsOnlyInstancesOf(CauseOfDeathListItem::class, $list->items);
        $this->assertCount(5,                      $list->items);
        $this->assertSame(null,                    $list->page);
        $this->assertSame(self::DEFAULT_PAGE_SIZE, $list->pageSize);
        $this->assertSame('ая',                    $list->term);
        $this->assertSame(5,                       $list->totalCount);
        $this->assertSame(null,                    $list->totalPages);
    }

    public function testItReturnsCauseOfDeathListByPage(): void
    {
        $customPageSize = 3;

        // First page
        $listForFirstPage = $this->fetcher->findAll(1, null, $customPageSize);
        $this->assertInstanceOf(CauseOfDeathList::class, $listForFirstPage);
        $this->assertIsArray($listForFirstPage->items);
        $this->assertContainsOnlyInstancesOf(CauseOfDeathListItem::class, $listForFirstPage->items);
        $this->assertCount(3,              $listForFirstPage->items);
        $this->assertSame(1,               $listForFirstPage->page);
        $this->assertSame($customPageSize, $listForFirstPage->pageSize);
        $this->assertSame(null,            $listForFirstPage->term);
        $this->assertSame(8,               $listForFirstPage->totalCount);
        $this->assertSame(3,               $listForFirstPage->totalPages);
        $this->assertListItemEqualsCD001($listForFirstPage->items[0]);  // Items are ordered by name
        $this->assertListItemEqualsCD007($listForFirstPage->items[1]);
        $this->assertListItemEqualsCD005($listForFirstPage->items[2]);

        // Second page
        $listForSecondPage = $this->fetcher->findAll(2, null, $customPageSize);
        $this->assertInstanceOf(CauseOfDeathList::class, $listForSecondPage);
        $this->assertIsArray($listForSecondPage->items);
        $this->assertContainsOnlyInstancesOf(CauseOfDeathListItem::class, $listForSecondPage->items);
        $this->assertCount(3,              $listForSecondPage->items);
        $this->assertSame(2,               $listForSecondPage->page);
        $this->assertSame($customPageSize, $listForSecondPage->pageSize);
        $this->assertSame(null,            $listForSecondPage->term);
        $this->assertSame(8,               $listForSecondPage->totalCount);
        $this->assertSame(3,               $listForSecondPage->totalPages);
        $this->assertListItemEqualsCD006($listForSecondPage->items[0]);
        $this->assertListItemEqualsCD003($listForSecondPage->items[1]);
        $this->assertListItemEqualsCD008($listForSecondPage->items[2]);

        // Third page
        $listForThirdPage = $this->fetcher->findAll(3, null, $customPageSize);
        $this->assertInstanceOf(CauseOfDeathList::class, $listForThirdPage);
        $this->assertIsArray($listForThirdPage->items);
        $this->assertContainsOnlyInstancesOf(CauseOfDeathListItem::class, $listForThirdPage->items);
        $this->assertCount(2,              $listForThirdPage->items);
        $this->assertSame(3,               $listForThirdPage->page);
        $this->assertSame($customPageSize, $listForThirdPage->pageSize);
        $this->assertSame(null,            $listForThirdPage->term);
        $this->assertSame(8,               $listForThirdPage->totalCount);
        $this->assertSame(3,               $listForThirdPage->totalPages);
        $this->assertListItemEqualsCD002($listForThirdPage->items[0]);
        $this->assertListItemEqualsCD004($listForThirdPage->items[1]);

        // Fourth page
        $listForFourthPage = $this->fetcher->findAll(4, null, $customPageSize);
        $this->assertInstanceOf(CauseOfDeathList::class, $listForFourthPage);
        $this->assertIsArray($listForFourthPage->items);
        $this->assertCount(0,              $listForFourthPage->items);
        $this->assertSame(4,               $listForFourthPage->page);
        $this->assertSame($customPageSize, $listForFourthPage->pageSize);
        $this->assertSame(null,            $listForFourthPage->term);
        $this->assertSame(8,               $listForFourthPage->totalCount);
        $this->assertSame(3,               $listForFourthPage->totalPages);

        // Default page size
        $listForDefaultPageSize = $this->fetcher->findAll(1);
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

    public function testItReturnsCauseOfDeathListByPageAndTerm(): void
    {
        $customPageSize = 3;

        $list = $this->fetcher->findAll(1, 'Болезнь', $customPageSize);
        $this->assertInstanceOf(CauseOfDeathList::class, $list);
        $this->assertIsArray($list->items);
        $this->assertContainsOnlyInstancesOf(CauseOfDeathListItem::class, $list->items);
        $this->assertCount(3,              $list->items);
        $this->assertSame(1,               $list->page);
        $this->assertSame($customPageSize, $list->pageSize);
        $this->assertSame('Болезнь',       $list->term);
        $this->assertSame(3,               $list->totalCount);
        $this->assertSame(1,               $list->totalPages);

        $list = $this->fetcher->findAll(1, 'серд', $customPageSize);
        $this->assertInstanceOf(CauseOfDeathList::class, $list);
        $this->assertIsArray($list->items);
        $this->assertContainsOnlyInstancesOf(CauseOfDeathListItem::class, $list->items);
        $this->assertCount(2,              $list->items);
        $this->assertSame(1,               $list->page);
        $this->assertSame($customPageSize, $list->pageSize);
        $this->assertSame('серд',          $list->term);
        $this->assertSame(2,               $list->totalCount);
        $this->assertSame(1,               $list->totalPages);

        $list = $this->fetcher->findAll(1, 'coV', $customPageSize);
        $this->assertInstanceOf(CauseOfDeathList::class, $list);
        $this->assertIsArray($list->items);
        $this->assertContainsOnlyInstancesOf(CauseOfDeathListItem::class, $list->items);
        $this->assertCount(1,              $list->items);
        $this->assertSame(1,               $list->page);
        $this->assertSame($customPageSize, $list->pageSize);
        $this->assertSame('coV',           $list->term);
        $this->assertSame(1,               $list->totalCount);
        $this->assertSame(1,               $list->totalPages);
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

    private function assertListItemEqualsCD001(CauseOfDeathListItem $listItem): void
    {
        $this->assertSame('CD001',    $listItem->id);
        $this->assertSame('COVID-19', $listItem->name);
    }

    private function assertListItemEqualsCD002(CauseOfDeathListItem $listItem): void
    {
        $this->assertSame('CD002',                        $listItem->id);
        $this->assertSame('Обструктивная болезнь легких', $listItem->name);
    }

    private function assertListItemEqualsCD003(CauseOfDeathListItem $listItem): void
    {
        $this->assertSame('CD003',                              $listItem->id);
        $this->assertSame('Атеросклеротическая болезнь сердца', $listItem->name);
    }

    private function assertListItemEqualsCD004(CauseOfDeathListItem $listItem): void
    {
        $this->assertSame('CD004',     $listItem->id);
        $this->assertSame('Онкология', $listItem->name);
    }

    private function assertListItemEqualsCD005(CauseOfDeathListItem $listItem): void
    {
        $this->assertSame('CD005',             $listItem->id);
        $this->assertSame('Астма кардиальная', $listItem->name);
    }

    private function assertListItemEqualsCD006(CauseOfDeathListItem $listItem): void
    {
        $this->assertSame('CD006',    $listItem->id);
        $this->assertSame('Асфиксия', $listItem->name);
    }

    private function assertListItemEqualsCD007(CauseOfDeathListItem $listItem): void
    {
        $this->assertSame('CD007',                               $listItem->id);
        $this->assertSame('Аневризма брюшной аорты разорванная', $listItem->name);
    }

    private function assertListItemEqualsCD008(CauseOfDeathListItem $listItem): void
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
}
