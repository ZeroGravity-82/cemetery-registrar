<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Fetcher;

use Cemetery\Registrar\Domain\Model\Deceased\CauseOfDeath\CauseOfDeathId;
use Cemetery\Registrar\Domain\Model\Deceased\CauseOfDeath\CauseOfDeathRepository;
use Cemetery\Registrar\Domain\View\Burial\BurialView;
use Cemetery\Registrar\Domain\View\CauseOfDeath\CauseOfDeathFetcher;
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

    public function testItReturnsCauseOfDeathListItemsByPage(): void
    {
        $customPageSize = 3;

        // First page
        $listForFirstPage = $this->causeOfDeathFetcher->findAll(1, null, $customPageSize);
        $this->assertInstanceOf(FuneralCompanyList::class, $listForFirstPage);
        $this->assertCount(3,              $listForFirstPage->listItems);
        $this->assertSame(1,               $listForFirstPage->page);
        $this->assertSame($customPageSize, $listForFirstPage->pageSize);
        $this->assertSame(null,            $listForFirstPage->term);
        $this->assertSame(4,               $listForFirstPage->totalCount);
        $this->assertSame(2,               $listForFirstPage->totalPages);
        $this->assertIsArray($listForFirstPage->listItems);
        $this->assertContainsOnlyInstancesOf(FuneralCompanyListItem::class, $listForFirstPage->listItems);
        $this->assertItemEqualsFC002($listForFirstPage->listItems[0]);  // Items are ordered by name
        $this->assertItemEqualsFC003($listForFirstPage->listItems[1]);
        $this->assertItemEqualsFC001($listForFirstPage->listItems[2]);

        // Second page
        $listForSecondPage = $this->causeOfDeathFetcher->findAll(2, null, $customPageSize);
        $this->assertInstanceOf(FuneralCompanyList::class, $listForSecondPage);
        $this->assertCount(1,              $listForSecondPage->listItems);
        $this->assertSame(2,               $listForSecondPage->page);
        $this->assertSame($customPageSize, $listForSecondPage->pageSize);
        $this->assertSame(null,            $listForSecondPage->term);
        $this->assertSame(4,               $listForSecondPage->totalCount);
        $this->assertSame(2,               $listForSecondPage->totalPages);
        $this->assertIsArray($listForSecondPage->listItems);
        $this->assertContainsOnlyInstancesOf(FuneralCompanyListItem::class, $listForSecondPage->listItems);
        $this->assertItemEqualsFC004($listForSecondPage->listItems[0]);

        // Third page
        $listForThirdPage = $this->causeOfDeathFetcher->findAll(3, null, $customPageSize);
        $this->assertInstanceOf(FuneralCompanyList::class, $listForThirdPage);
        $this->assertCount(0,              $listForThirdPage->listItems);
        $this->assertSame(3,               $listForThirdPage->page);
        $this->assertSame($customPageSize, $listForThirdPage->pageSize);
        $this->assertSame(null,            $listForThirdPage->term);
        $this->assertSame(4,               $listForThirdPage->totalCount);
        $this->assertSame(2,               $listForThirdPage->totalPages);

        // All at once
        $listForDefaultPageSize = $this->causeOfDeathFetcher->findAll(1);
        $this->assertInstanceOf(FuneralCompanyList::class, $listForDefaultPageSize);
        $this->assertCount(4,                      $listForDefaultPageSize->listItems);
        $this->assertSame(1,                       $listForDefaultPageSize->page);
        $this->assertSame(self::DEFAULT_PAGE_SIZE, $listForDefaultPageSize->pageSize);
        $this->assertSame(null,                    $listForDefaultPageSize->term);
        $this->assertSame(4,                       $listForDefaultPageSize->totalCount);
        $this->assertSame(1,                       $listForDefaultPageSize->totalPages);
        $this->assertIsArray($listForDefaultPageSize->listItems);
        $this->assertContainsOnlyInstancesOf(FuneralCompanyListItem::class, $listForDefaultPageSize->listItems);
    }

    public function testItReturnsCauseOfDeathListItemsByPageAndTerm(): void
    {
        $customPageSize = 3;

        $list = $this->causeOfDeathFetcher->findAll(1, '44', $customPageSize);
        $this->assertInstanceOf(FuneralCompanyList::class, $list);
        $this->assertCount(1,              $list->listItems);
        $this->assertSame(1,               $list->page);
        $this->assertSame($customPageSize, $list->pageSize);
        $this->assertSame('44',            $list->term);
        $this->assertSame(1,               $list->totalCount);
        $this->assertSame(1,               $list->totalPages);
        $this->assertIsArray($list->listItems);
        $this->assertContainsOnlyInstancesOf(FuneralCompanyListItem::class, $list->listItems);

        $list = $this->causeOfDeathFetcher->findAll(1, 'Кемеров', $customPageSize);
        $this->assertInstanceOf(FuneralCompanyList::class, $list);
        $this->assertCount(2,              $list->listItems);
        $this->assertSame(1,               $list->page);
        $this->assertSame($customPageSize, $list->pageSize);
        $this->assertSame('Кемеров',       $list->term);
        $this->assertSame(2,               $list->totalCount);
        $this->assertSame(1,               $list->totalPages);
        $this->assertIsArray($list->listItems);
        $this->assertContainsOnlyInstancesOf(FuneralCompanyListItem::class, $list->listItems);

        $list = $this->causeOfDeathFetcher->findAll(1, 'ро', $customPageSize);
        $this->assertInstanceOf(FuneralCompanyList::class, $list);
        $this->assertCount(3,              $list->listItems);
        $this->assertSame(1,               $list->page);
        $this->assertSame($customPageSize, $list->pageSize);
        $this->assertSame('ро',            $list->term);
        $this->assertSame(4,               $list->totalCount);
        $this->assertSame(2,               $list->totalPages);
        $this->assertIsArray($list->listItems);
        $this->assertContainsOnlyInstancesOf(FuneralCompanyListItem::class, $list->listItems);
        $list = $this->causeOfDeathFetcher->findAll(2, 'ро', $customPageSize);
        $this->assertInstanceOf(FuneralCompanyList::class, $list);
        $this->assertCount(1,              $list->listItems);
        $this->assertSame(2,               $list->page);
        $this->assertSame($customPageSize, $list->pageSize);
        $this->assertSame('ро',            $list->term);
        $this->assertSame(4,               $list->totalCount);
        $this->assertSame(2,               $list->totalPages);
        $this->assertIsArray($list->listItems);
        $this->assertContainsOnlyInstancesOf(FuneralCompanyListItem::class, $list->listItems);

        $list = $this->causeOfDeathFetcher->findAll(1, '133', $customPageSize);
        $this->assertInstanceOf(FuneralCompanyList::class, $list);
        $this->assertCount(1,              $list->listItems);
        $this->assertSame(1,               $list->page);
        $this->assertSame($customPageSize, $list->pageSize);
        $this->assertSame('133',           $list->term);
        $this->assertSame(1,               $list->totalCount);
        $this->assertSame(1,               $list->totalPages);
        $this->assertIsArray($list->listItems);
        $this->assertContainsOnlyInstancesOf(FuneralCompanyListItem::class, $list->listItems);
    }

    public function testItReturnsCauseOfDeathTotalCount(): void
    {
        $this->assertSame(4, $this->causeOfDeathFetcher->countTotal());
    }

    public function testItDoesNotCountRemovedCauseOfDeathWhenCalculatingTotalCount(): void
    {
        // Prepare database table for testing
        $funeralCompanyToRemove = $this->causeOfDeathRepo->findById(new FuneralCompanyId('FC004'));
        $this->causeOfDeathRepo->remove($funeralCompanyToRemove);

        // Testing itself
        $this->assertSame(3, $this->causeOfDeathFetcher->countTotal());
    }

    protected function loadFixtures(): void
    {
        $this->databaseTool->loadFixtures([
            CauseOfDeathFixtures::class,
        ]);
    }

    private function assertItemEqualsFC001(FuneralCompanyListItem $item): void
    {
        $this->assertSame('FC001',                                       $item->id);
        $this->assertSame(JuristicPerson::CLASS_SHORTCUT,                $item->organizationType);
        $this->assertSame('ООО "Рога и копыта"',                         $item->organizationJuristicPersonName);
        $this->assertSame(null,                                          $item->organizationJuristicPersonInn);
        $this->assertSame(null,                                          $item->organizationJuristicPersonLegalAddress);
        $this->assertSame('г. Кемерово, пр. Строителей, д. 5, офис 102', $item->organizationJuristicPersonPostalAddress);
        $this->assertSame(null,                                          $item->organizationJuristicPersonPhone);
        $this->assertSame(null,                                          $item->organizationSoleProprietorName);
        $this->assertSame(null,                                          $item->organizationSoleProprietorInn);
        $this->assertSame(null,                                          $item->organizationSoleProprietorRegistrationAddress);
        $this->assertSame(null,                                          $item->organizationSoleProprietorActualLocationAddress);
        $this->assertSame(null,                                          $item->organizationSoleProprietorPhone);
        $this->assertSame(null,                                          $item->note);
    }

    private function testItReturnsCauseOfDeathViewForCD001(): void
    {
        $causeOfDeathView = $this->causeOfDeathFetcher->getViewById('CD001');
        $this->assertInstanceOf(CauseOfDeathView::class, $causeOfDeathView);
        $this->assertSame('CD001',    $causeOfDeathView->id);
        $this->assertSame('COVID-19', $causeOfDeathView->name);
    }

    private function expectExceptionForNotFoundCauseOfDeathById(string $causeOfDeathId): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(\sprintf('Причина смерти с ID "%s" не найдена.', $causeOfDeathId));
    }
}
