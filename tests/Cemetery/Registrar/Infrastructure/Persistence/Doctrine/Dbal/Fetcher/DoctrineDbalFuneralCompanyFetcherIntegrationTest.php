<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Fetcher;

use Cemetery\Registrar\Domain\Model\FuneralCompany\FuneralCompanyId;
use Cemetery\Registrar\Domain\Model\FuneralCompany\FuneralCompanyRepository;
use Cemetery\Registrar\Domain\Model\Organization\JuristicPerson\JuristicPerson;
use Cemetery\Registrar\Domain\Model\Organization\SoleProprietor\SoleProprietor;
use Cemetery\Registrar\Domain\View\FuneralCompany\FuneralCompanyFetcher;
use Cemetery\Registrar\Domain\View\FuneralCompany\FuneralCompanyList;
use Cemetery\Registrar\Domain\View\FuneralCompany\FuneralCompanyListItem;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Fetcher\DoctrineDbalFuneralCompanyFetcher;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Orm\Repository\DoctrineOrmFuneralCompanyRepository;
use DataFixtures\FuneralCompany\FuneralCompanyFixtures;
use DataFixtures\Organization\JuristicPerson\JuristicPersonFixtures;
use DataFixtures\Organization\SoleProprietor\SoleProprietorFixtures;

/**
 * @group database
 *
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class DoctrineDbalFuneralCompanyFetcherIntegrationTest extends FetcherIntegrationTest
{
    private const DEFAULT_PAGE_SIZE = 20;

    private FuneralCompanyRepository $funeralCompanyRepo;
    private FuneralCompanyFetcher    $funeralCompanyFetcher;

    public function setUp(): void
    {
        parent::setUp();

        $this->funeralCompanyRepo    = new DoctrineOrmFuneralCompanyRepository($this->entityManager);
        $this->funeralCompanyFetcher = new DoctrineDbalFuneralCompanyFetcher($this->connection);
        $this->loadFixtures();
    }

    public function testItHasValidPageSizeConstant(): void
    {
        $this->assertSame(self::DEFAULT_PAGE_SIZE, FuneralCompanyFetcher::DEFAULT_PAGE_SIZE);
    }

    public function testItReturnsFuneralCompanyViewById(): void
    {
        $this->markTestIncomplete();
//        $this->testItReturnsFuneralCompanyViewForFC001();
//        $this->testItReturnsFuneralCompanyViewForFC002();
//        $this->testItReturnsFuneralCompanyViewForFC003();
//        $this->testItReturnsFuneralCompanyViewForFC004();
    }

    public function testItFailsToReturnFuneralCompanyViewByUnknownId(): void
    {
        $this->markTestIncomplete();
//        $this->expectExceptionForNotFoundFuneralCompanyById('unknown_id');
//        $this->funeralCompanyFetcher->getViewById('unknown_id');
    }

    public function testItFailsToReturnFuneralCompanyViewForRemovedFuneralCompany(): void
    {
        // Prepare database table for testing
        $funeralCompanyToRemove = $this->funeralCompanyRepo->findById(new FuneralCompanyId('FC004'));
        $this->funeralCompanyRepo->remove($funeralCompanyToRemove);
        $removedFuneralCompanyId = $funeralCompanyToRemove->id()->value();

        // Testing itself
//         $this->expectExceptionForNotFoundFuneralCompanyById($removedFuneralCompanyId);
//        $this->funeralCompanyFetcher->getViewById($removedFuneralCompanyId);
    }

    public function testItReturnsFuneralCompanyListItemsByPage(): void
    {
        $customPageSize = 3;

        // First page
        $listForFirstPage = $this->funeralCompanyFetcher->findAll(1, null, $customPageSize);
        $this->assertInstanceOf(FuneralCompanyList::class, $listForFirstPage);
        $this->assertIsArray($listForFirstPage->listItems);
        $this->assertContainsOnlyInstancesOf(FuneralCompanyListItem::class, $listForFirstPage->listItems);
        $this->assertCount(3,              $listForFirstPage->listItems);
        $this->assertSame(1,               $listForFirstPage->page);
        $this->assertSame($customPageSize, $listForFirstPage->pageSize);
        $this->assertSame(null,            $listForFirstPage->term);
        $this->assertSame(4,               $listForFirstPage->totalCount);
        $this->assertSame(2,               $listForFirstPage->totalPages);
        $this->assertItemEqualsFC002($listForFirstPage->listItems[0]);  // Items are ordered by name
        $this->assertItemEqualsFC003($listForFirstPage->listItems[1]);
        $this->assertItemEqualsFC001($listForFirstPage->listItems[2]);

        // Second page
        $listForSecondPage = $this->funeralCompanyFetcher->findAll(2, null, $customPageSize);
        $this->assertInstanceOf(FuneralCompanyList::class, $listForSecondPage);
        $this->assertIsArray($listForSecondPage->listItems);
        $this->assertContainsOnlyInstancesOf(FuneralCompanyListItem::class, $listForSecondPage->listItems);
        $this->assertCount(1,              $listForSecondPage->listItems);
        $this->assertSame(2,               $listForSecondPage->page);
        $this->assertSame($customPageSize, $listForSecondPage->pageSize);
        $this->assertSame(null,            $listForSecondPage->term);
        $this->assertSame(4,               $listForSecondPage->totalCount);
        $this->assertSame(2,               $listForSecondPage->totalPages);
        $this->assertItemEqualsFC004($listForSecondPage->listItems[0]);

        // Third page
        $listForThirdPage = $this->funeralCompanyFetcher->findAll(3, null, $customPageSize);
        $this->assertInstanceOf(FuneralCompanyList::class, $listForThirdPage);
        $this->assertIsArray($listForThirdPage->listItems);
        $this->assertCount(0,              $listForThirdPage->listItems);
        $this->assertSame(3,               $listForThirdPage->page);
        $this->assertSame($customPageSize, $listForThirdPage->pageSize);
        $this->assertSame(null,            $listForThirdPage->term);
        $this->assertSame(4,               $listForThirdPage->totalCount);
        $this->assertSame(2,               $listForThirdPage->totalPages);

        // All at once
        $listForDefaultPageSize = $this->funeralCompanyFetcher->findAll(1);
        $this->assertInstanceOf(FuneralCompanyList::class, $listForDefaultPageSize);
        $this->assertIsArray($listForDefaultPageSize->listItems);
        $this->assertContainsOnlyInstancesOf(FuneralCompanyListItem::class, $listForDefaultPageSize->listItems);
        $this->assertCount(4,                      $listForDefaultPageSize->listItems);
        $this->assertSame(1,                       $listForDefaultPageSize->page);
        $this->assertSame(self::DEFAULT_PAGE_SIZE, $listForDefaultPageSize->pageSize);
        $this->assertSame(null,                    $listForDefaultPageSize->term);
        $this->assertSame(4,                       $listForDefaultPageSize->totalCount);
        $this->assertSame(1,                       $listForDefaultPageSize->totalPages);
    }

    public function testItReturnsFuneralCompanyListItemsByPageAndTerm(): void
    {
        $customPageSize = 3;

        $list = $this->funeralCompanyFetcher->findAll(1, '44', $customPageSize);
        $this->assertInstanceOf(FuneralCompanyList::class, $list);
        $this->assertIsArray($list->listItems);
        $this->assertContainsOnlyInstancesOf(FuneralCompanyListItem::class, $list->listItems);
        $this->assertCount(1,              $list->listItems);
        $this->assertSame(1,               $list->page);
        $this->assertSame($customPageSize, $list->pageSize);
        $this->assertSame('44',            $list->term);
        $this->assertSame(1,               $list->totalCount);
        $this->assertSame(1,               $list->totalPages);

        $list = $this->funeralCompanyFetcher->findAll(1, 'Кемеров', $customPageSize);
        $this->assertInstanceOf(FuneralCompanyList::class, $list);
        $this->assertIsArray($list->listItems);
        $this->assertContainsOnlyInstancesOf(FuneralCompanyListItem::class, $list->listItems);
        $this->assertCount(2,              $list->listItems);
        $this->assertSame(1,               $list->page);
        $this->assertSame($customPageSize, $list->pageSize);
        $this->assertSame('Кемеров',       $list->term);
        $this->assertSame(2,               $list->totalCount);
        $this->assertSame(1,               $list->totalPages);

        $list = $this->funeralCompanyFetcher->findAll(1, 'ро', $customPageSize);
        $this->assertInstanceOf(FuneralCompanyList::class, $list);
        $this->assertIsArray($list->listItems);
        $this->assertContainsOnlyInstancesOf(FuneralCompanyListItem::class, $list->listItems);
        $this->assertCount(3,              $list->listItems);
        $this->assertSame(1,               $list->page);
        $this->assertSame($customPageSize, $list->pageSize);
        $this->assertSame('ро',            $list->term);
        $this->assertSame(4,               $list->totalCount);
        $this->assertSame(2,               $list->totalPages);
        $list = $this->funeralCompanyFetcher->findAll(2, 'ро', $customPageSize);
        $this->assertInstanceOf(FuneralCompanyList::class, $list);
        $this->assertIsArray($list->listItems);
        $this->assertContainsOnlyInstancesOf(FuneralCompanyListItem::class, $list->listItems);
        $this->assertCount(1,              $list->listItems);
        $this->assertSame(2,               $list->page);
        $this->assertSame($customPageSize, $list->pageSize);
        $this->assertSame('ро',            $list->term);
        $this->assertSame(4,               $list->totalCount);
        $this->assertSame(2,               $list->totalPages);

        $list = $this->funeralCompanyFetcher->findAll(1, '133', $customPageSize);
        $this->assertInstanceOf(FuneralCompanyList::class, $list);
        $this->assertIsArray($list->listItems);
        $this->assertContainsOnlyInstancesOf(FuneralCompanyListItem::class, $list->listItems);
        $this->assertCount(1,              $list->listItems);
        $this->assertSame(1,               $list->page);
        $this->assertSame($customPageSize, $list->pageSize);
        $this->assertSame('133',           $list->term);
        $this->assertSame(1,               $list->totalCount);
        $this->assertSame(1,               $list->totalPages);
    }

    public function testItReturnsFuneralCompanyTotalCount(): void
    {
        $this->assertSame(4, $this->funeralCompanyFetcher->countTotal());
    }

    public function testItDoesNotCountRemovedFuneralCompanyWhenCalculatingTotalCount(): void
    {
        // Prepare database table for testing
        $funeralCompanyToRemove = $this->funeralCompanyRepo->findById(new FuneralCompanyId('FC004'));
        $this->funeralCompanyRepo->remove($funeralCompanyToRemove);

        // Testing itself
        $this->assertSame(3, $this->funeralCompanyFetcher->countTotal());
    }

    protected function loadFixtures(): void
    {
        $this->databaseTool->loadFixtures([
            JuristicPersonFixtures::class,
            SoleProprietorFixtures::class,
            FuneralCompanyFixtures::class,
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

    private function assertItemEqualsFC002(FuneralCompanyListItem $item): void
    {
        $this->assertSame('FC002',                        $item->id);
        $this->assertSame(SoleProprietor::CLASS_SHORTCUT, $item->organizationType);
        $this->assertSame(null,                           $item->organizationJuristicPersonName);
        $this->assertSame(null,                           $item->organizationJuristicPersonInn);
        $this->assertSame(null,                           $item->organizationJuristicPersonLegalAddress);
        $this->assertSame(null,                           $item->organizationJuristicPersonPostalAddress);
        $this->assertSame(null,                           $item->organizationJuristicPersonPhone);
        $this->assertSame('ИП Иванов Иван Иванович',      $item->organizationSoleProprietorName);
        $this->assertSame(null,                           $item->organizationSoleProprietorInn);
        $this->assertSame(null,                           $item->organizationSoleProprietorRegistrationAddress);
        $this->assertSame(null,                           $item->organizationSoleProprietorActualLocationAddress);
        $this->assertSame(null,                           $item->organizationSoleProprietorPhone);
        $this->assertSame('Фирма находится в Кемерове',   $item->note);
    }

    private function assertItemEqualsFC003(FuneralCompanyListItem $item): void
    {
        $this->assertSame('FC003',                        $item->id);
        $this->assertSame(SoleProprietor::CLASS_SHORTCUT, $item->organizationType);
        $this->assertSame(null,                           $item->organizationJuristicPersonName);
        $this->assertSame(null,                           $item->organizationJuristicPersonInn);
        $this->assertSame(null,                           $item->organizationJuristicPersonLegalAddress);
        $this->assertSame(null,                           $item->organizationJuristicPersonPostalAddress);
        $this->assertSame(null,                           $item->organizationJuristicPersonPhone);
        $this->assertSame('ИП Петров Пётр Петрович',      $item->organizationSoleProprietorName);
        $this->assertSame('772208786091',                 $item->organizationSoleProprietorInn);
        $this->assertSame(null,                           $item->organizationSoleProprietorRegistrationAddress);
        $this->assertSame(null,                           $item->organizationSoleProprietorActualLocationAddress);
        $this->assertSame('8(383)133-22-33',              $item->organizationSoleProprietorPhone);
        $this->assertSame('Примечание 2',                 $item->note);
    }

    private function assertItemEqualsFC004(FuneralCompanyListItem $item): void
    {
        $this->assertSame('FC004',                        $item->id);
        $this->assertSame(JuristicPerson::CLASS_SHORTCUT, $item->organizationType);
        $this->assertSame('ООО Ромашка',                  $item->organizationJuristicPersonName);
        $this->assertSame('5404447629',                   $item->organizationJuristicPersonInn);
        $this->assertSame(null,                           $item->organizationJuristicPersonLegalAddress);
        $this->assertSame(null,                           $item->organizationJuristicPersonPostalAddress);
        $this->assertSame(null,                           $item->organizationJuristicPersonPhone);
        $this->assertSame(null,                           $item->organizationSoleProprietorName);
        $this->assertSame(null,                           $item->organizationSoleProprietorInn);
        $this->assertSame(null,                           $item->organizationSoleProprietorRegistrationAddress);
        $this->assertSame(null,                           $item->organizationSoleProprietorActualLocationAddress);
        $this->assertSame(null,                           $item->organizationSoleProprietorPhone);
        $this->assertSame(null,                           $item->note);
    }

    private function expectExceptionForNotFoundFuneralCompanyById(string $funeralCompanyId): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(\sprintf('Похоронная фирма с ID "%s" не найдена.', $funeralCompanyId));
    }
}
