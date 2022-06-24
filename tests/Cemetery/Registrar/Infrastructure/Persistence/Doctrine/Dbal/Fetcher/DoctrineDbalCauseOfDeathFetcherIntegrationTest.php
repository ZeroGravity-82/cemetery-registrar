<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Fetcher;

use Cemetery\Registrar\Domain\Model\Deceased\CauseOfDeath\CauseOfDeathId;
use Cemetery\Registrar\Domain\Model\Deceased\CauseOfDeath\CauseOfDeathRepository;
use Cemetery\Registrar\Domain\View\CauseOfDeath\CauseOfDeathFetcher;
use Cemetery\Registrar\Domain\View\CauseOfDeath\CauseOfDeathList;
use Cemetery\Registrar\Domain\View\CauseOfDeath\CauseOfDeathListItem;
use Cemetery\Registrar\Domain\View\CauseOfDeath\CauseOfDeathView;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Fetcher\DoctrineDbalCauseOfDeathFetcher;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Orm\Repository\DoctrineOrmCauseOfDeathRepository;
use DataFixtures\Deceased\CauseOfDeath\CauseOfDeathFixtures;

/**
 * @group database
 *
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class DoctrineDbalCauseOfDeathFetcherIntegrationTest extends FetcherIntegrationTest
{
    private const DEFAULT_PAGE_SIZE = 20;

    private CauseOfDeathRepository $causeOfDeathRepo;
    private CauseOfDeathFetcher    $causeOfDeathFetcher;

    public function setUp(): void
    {
        parent::setUp();

        $this->causeOfDeathRepo    = new DoctrineOrmCauseOfDeathRepository($this->entityManager);
        $this->causeOfDeathFetcher = new DoctrineDbalCauseOfDeathFetcher($this->connection);
        $this->loadFixtures();
    }

    public function testItHasValidPageSizeConstant(): void
    {
        $this->assertSame(self::DEFAULT_PAGE_SIZE, CauseOfDeathFetcher::DEFAULT_PAGE_SIZE);
    }

    public function testItReturnsCauseOfDeathViewById(): void
    {
        $this->testItReturnsCauseOfDeathViewForCD001();
    }

    public function testItFailsToReturnCauseOfDeathViewByUnknownId(): void
    {
        $this->expectExceptionForNotFoundCauseOfDeathById('unknown_id');
        $this->causeOfDeathFetcher->getViewById('unknown_id');
    }

    public function testItFailsToReturnCauseOfDeathViewForRemovedCauseOfDeath(): void
    {
        // Prepare database table for testing
        $causeOfDeathToRemove = $this->causeOfDeathRepo->findById(new CauseOfDeathId('CD004'));
        $this->causeOfDeathRepo->remove($causeOfDeathToRemove);
        $removedCauseOfDeathId = $causeOfDeathToRemove->id()->value();

        // Testing itself
        $this->expectExceptionForNotFoundCauseOfDeathById($removedCauseOfDeathId);
        $this->causeOfDeathFetcher->getViewById($removedCauseOfDeathId);
    }

    public function testItReturnsCauseOfDeathListByPage(): void
    {
        $customPageSize = 3;

        // First page
        $listForFirstPage = $this->causeOfDeathFetcher->findAll(1, null, $customPageSize);
        $this->assertInstanceOf(CauseOfDeathList::class, $listForFirstPage);
        $this->assertIsArray($listForFirstPage->listItems);
        $this->assertContainsOnlyInstancesOf(CauseOfDeathListItem::class, $listForFirstPage->listItems);
        $this->assertCount(3,              $listForFirstPage->listItems);
        $this->assertSame(1,               $listForFirstPage->page);
        $this->assertSame($customPageSize, $listForFirstPage->pageSize);
        $this->assertSame(null,            $listForFirstPage->term);
        $this->assertSame(8,               $listForFirstPage->totalCount);
        $this->assertSame(3,               $listForFirstPage->totalPages);
        $this->assertListItemEqualsCD001($listForFirstPage->listItems[0]);  // Items are ordered by name
        $this->assertListItemEqualsCD007($listForFirstPage->listItems[1]);
        $this->assertListItemEqualsCD005($listForFirstPage->listItems[2]);

        // Second page
        $listForSecondPage = $this->causeOfDeathFetcher->findAll(2, null, $customPageSize);
        $this->assertInstanceOf(CauseOfDeathList::class, $listForSecondPage);
        $this->assertIsArray($listForSecondPage->listItems);
        $this->assertContainsOnlyInstancesOf(CauseOfDeathListItem::class, $listForSecondPage->listItems);
        $this->assertCount(3,              $listForSecondPage->listItems);
        $this->assertSame(2,               $listForSecondPage->page);
        $this->assertSame($customPageSize, $listForSecondPage->pageSize);
        $this->assertSame(null,            $listForSecondPage->term);
        $this->assertSame(8,               $listForSecondPage->totalCount);
        $this->assertSame(3,               $listForSecondPage->totalPages);
        $this->assertListItemEqualsCD006($listForSecondPage->listItems[0]);
        $this->assertListItemEqualsCD003($listForSecondPage->listItems[1]);
        $this->assertListItemEqualsCD008($listForSecondPage->listItems[2]);

        // Third page
        $listForThirdPage = $this->causeOfDeathFetcher->findAll(3, null, $customPageSize);
        $this->assertInstanceOf(CauseOfDeathList::class, $listForThirdPage);
        $this->assertIsArray($listForThirdPage->listItems);
        $this->assertContainsOnlyInstancesOf(CauseOfDeathListItem::class, $listForThirdPage->listItems);
        $this->assertCount(2,              $listForThirdPage->listItems);
        $this->assertSame(3,               $listForThirdPage->page);
        $this->assertSame($customPageSize, $listForThirdPage->pageSize);
        $this->assertSame(null,            $listForThirdPage->term);
        $this->assertSame(8,               $listForThirdPage->totalCount);
        $this->assertSame(3,               $listForThirdPage->totalPages);
        $this->assertListItemEqualsCD002($listForThirdPage->listItems[0]);
        $this->assertListItemEqualsCD004($listForThirdPage->listItems[1]);

        // Fourth page
        $listForFourthPage = $this->causeOfDeathFetcher->findAll(4, null, $customPageSize);
        $this->assertInstanceOf(CauseOfDeathList::class, $listForFourthPage);
        $this->assertIsArray($listForFourthPage->listItems);
        $this->assertCount(0,              $listForFourthPage->listItems);
        $this->assertSame(4,               $listForFourthPage->page);
        $this->assertSame($customPageSize, $listForFourthPage->pageSize);
        $this->assertSame(null,            $listForFourthPage->term);
        $this->assertSame(8,               $listForFourthPage->totalCount);
        $this->assertSame(3,               $listForFourthPage->totalPages);

        // Default page size
        $listForDefaultPageSize = $this->causeOfDeathFetcher->findAll(1);
        $this->assertInstanceOf(CauseOfDeathList::class, $listForDefaultPageSize);
        $this->assertIsArray($listForDefaultPageSize->listItems);
        $this->assertContainsOnlyInstancesOf(CauseOfDeathListItem::class, $listForDefaultPageSize->listItems);
        $this->assertCount(8,                      $listForDefaultPageSize->listItems);
        $this->assertSame(1,                       $listForDefaultPageSize->page);
        $this->assertSame(self::DEFAULT_PAGE_SIZE, $listForDefaultPageSize->pageSize);
        $this->assertSame(null,                    $listForDefaultPageSize->term);
        $this->assertSame(8,                       $listForDefaultPageSize->totalCount);
        $this->assertSame(1,                       $listForDefaultPageSize->totalPages);
    }

    public function testItReturnsCauseOfDeathListByPageAndTerm(): void
    {
        $customPageSize = 3;

        $list = $this->causeOfDeathFetcher->findAll(1, 'Болезнь', $customPageSize);
        $this->assertInstanceOf(CauseOfDeathList::class, $list);
        $this->assertIsArray($list->listItems);
        $this->assertContainsOnlyInstancesOf(CauseOfDeathListItem::class, $list->listItems);
        $this->assertCount(3,              $list->listItems);
        $this->assertSame(1,               $list->page);
        $this->assertSame($customPageSize, $list->pageSize);
        $this->assertSame('Болезнь',       $list->term);
        $this->assertSame(3,               $list->totalCount);
        $this->assertSame(1,               $list->totalPages);

        $list = $this->causeOfDeathFetcher->findAll(1, 'серд', $customPageSize);
        $this->assertInstanceOf(CauseOfDeathList::class, $list);
        $this->assertIsArray($list->listItems);
        $this->assertContainsOnlyInstancesOf(CauseOfDeathListItem::class, $list->listItems);
        $this->assertCount(2,              $list->listItems);
        $this->assertSame(1,               $list->page);
        $this->assertSame($customPageSize, $list->pageSize);
        $this->assertSame('серд',          $list->term);
        $this->assertSame(2,               $list->totalCount);
        $this->assertSame(1,               $list->totalPages);

        $list = $this->causeOfDeathFetcher->findAll(1, 'coV', $customPageSize);
        $this->assertInstanceOf(CauseOfDeathList::class, $list);
        $this->assertIsArray($list->listItems);
        $this->assertContainsOnlyInstancesOf(CauseOfDeathListItem::class, $list->listItems);
        $this->assertCount(1,              $list->listItems);
        $this->assertSame(1,               $list->page);
        $this->assertSame($customPageSize, $list->pageSize);
        $this->assertSame('coV',           $list->term);
        $this->assertSame(1,               $list->totalCount);
        $this->assertSame(1,               $list->totalPages);
    }

    public function testItReturnsCauseOfDeathTotalCount(): void
    {
        $this->assertSame(8, $this->causeOfDeathFetcher->countTotal());
    }

    public function testItDoesNotCountRemovedCauseOfDeathWhenCalculatingTotalCount(): void
    {
        // Prepare database table for testing
        $causeOfDeathToRemove = $this->causeOfDeathRepo->findById(new CauseOfDeathId('CD004'));
        $this->causeOfDeathRepo->remove($causeOfDeathToRemove);

        // Testing itself
        $this->assertSame(7, $this->causeOfDeathFetcher->countTotal());
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
        $view = $this->causeOfDeathFetcher->getViewById('CD001');
        $this->assertInstanceOf(CauseOfDeathView::class, $view);
        $this->assertSame('CD001',    $view->id);
        $this->assertSame('COVID-19', $view->name);
        $this->assertValidDateTimeValue($view->createdAt);
        $this->assertValidDateTimeValue($view->updatedAt);
    }

    private function expectExceptionForNotFoundCauseOfDeathById(string $causeOfDeathId): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(\sprintf('Причина смерти с ID "%s" не найдена.', $causeOfDeathId));
    }
}
