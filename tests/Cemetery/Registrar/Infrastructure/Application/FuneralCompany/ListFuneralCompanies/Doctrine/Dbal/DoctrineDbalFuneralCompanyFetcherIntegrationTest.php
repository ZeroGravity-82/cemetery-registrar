<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Application\FuneralCompany\ListFuneralCompanies\Doctrine\Dbal;

use Cemetery\Registrar\Application\FuneralCompany\ListFuneralCompanies\FuneralCompanyFetcher;
use Cemetery\Registrar\Application\FuneralCompany\ListFuneralCompanies\FuneralCompanyViewList;
use Cemetery\Registrar\Application\FuneralCompany\ListFuneralCompanies\FuneralCompanyViewListItem;
use Cemetery\Registrar\Domain\FuneralCompany\FuneralCompanyId;
use Cemetery\Registrar\Domain\FuneralCompany\FuneralCompanyRepository;
use Cemetery\Registrar\Domain\Organization\JuristicPerson\JuristicPerson;
use Cemetery\Registrar\Domain\Organization\SoleProprietor\SoleProprietor;
use Cemetery\Registrar\Infrastructure\Application\FuneralCompany\Doctrine\Dbal\DoctrineDbalFuneralCompanyFetcher;
use Cemetery\Registrar\Infrastructure\Domain\FuneralCompany\Doctrine\Orm\DoctrineOrmFuneralCompanyRepository;
use Cemetery\Tests\Registrar\Infrastructure\Application\FetcherIntegrationTest;
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

    public function testItReturnsFuneralCompanyFormViewById(): void
    {
        $this->markTestIncomplete();
//        $this->testItReturnsFuneralCompanyFormViewForFC001();
//        $this->testItReturnsFuneralCompanyFormViewForFC002();
//        $this->testItReturnsFuneralCompanyFormViewForFC003();
//        $this->testItReturnsFuneralCompanyFormViewForFC004();
    }

    public function testItFailsToReturnFuneralCompanyFormViewByUnknownId(): void
    {
        $this->markTestIncomplete();
//        $this->expectExceptionForNotFoundFuneralCompanyById('unknown_id');
//        $this->funeralCompanyFetcher->getFormViewById('unknown_id');
    }

    public function testItFailsToReturnFuneralCompanyFormViewForRemovedFuneralCompany(): void
    {
        // Prepare database table for testing
        $funeralCompanyToRemove = $this->funeralCompanyRepo->findById(new FuneralCompanyId('FC004'));
        $this->funeralCompanyRepo->remove($funeralCompanyToRemove);
        $removedFuneralCompanyId = $funeralCompanyToRemove->id()->value();

        // Testing itself
//         $this->expectExceptionForNotFoundFuneralCompanyById($removedFuneralCompanyId);
//        $this->funeralCompanyFetcher->getFormViewById($removedFuneralCompanyId);
    }

    public function testItReturnsFuneralCompanyViewListItemsByPage(): void
    {
        $customPageSize = 3;

        // First page
        $listForFirstPage = $this->funeralCompanyFetcher->findAll(1, null, $customPageSize);
        $this->assertInstanceOf(FuneralCompanyViewList::class, $listForFirstPage);
        $this->assertCount(3,              $listForFirstPage->listItems);
        $this->assertSame(1,               $listForFirstPage->page);
        $this->assertSame($customPageSize, $listForFirstPage->pageSize);
        $this->assertSame(null,            $listForFirstPage->term);
        $this->assertSame(4,               $listForFirstPage->totalCount);
        $this->assertSame(2,               $listForFirstPage->totalPages);
        $this->assertIsArray($listForFirstPage->listItems);
        $this->assertContainsOnlyInstancesOf(FuneralCompanyViewListItem::class, $listForFirstPage->listItems);
        $this->assertItemEqualsFC002($listForFirstPage->listItems[0]);  // Items are ordered by name
        $this->assertItemEqualsFC003($listForFirstPage->listItems[1]);
        $this->assertItemEqualsFC001($listForFirstPage->listItems[2]);

        // Second page
        $listForSecondPage = $this->funeralCompanyFetcher->findAll(2, null, $customPageSize);
        $this->assertInstanceOf(FuneralCompanyViewList::class, $listForSecondPage);
        $this->assertCount(1,              $listForSecondPage->listItems);
        $this->assertSame(2,               $listForSecondPage->page);
        $this->assertSame($customPageSize, $listForSecondPage->pageSize);
        $this->assertSame(null,            $listForSecondPage->term);
        $this->assertSame(4,               $listForSecondPage->totalCount);
        $this->assertSame(2,               $listForSecondPage->totalPages);
        $this->assertIsArray($listForSecondPage->listItems);
        $this->assertContainsOnlyInstancesOf(FuneralCompanyViewListItem::class, $listForSecondPage->listItems);
        $this->assertItemEqualsFC004($listForSecondPage->listItems[0]);

        // Third page
        $listForThirdPage = $this->funeralCompanyFetcher->findAll(3, null, $customPageSize);
        $this->assertInstanceOf(FuneralCompanyViewList::class, $listForThirdPage);
        $this->assertCount(0,              $listForThirdPage->listItems);
        $this->assertSame(3,               $listForThirdPage->page);
        $this->assertSame($customPageSize, $listForThirdPage->pageSize);
        $this->assertSame(null,            $listForThirdPage->term);
        $this->assertSame(4,               $listForThirdPage->totalCount);
        $this->assertSame(2,               $listForThirdPage->totalPages);

        // All at once
        $listForDefaultPageSize = $this->funeralCompanyFetcher->findAll(1);
        $this->assertInstanceOf(FuneralCompanyViewList::class, $listForDefaultPageSize);
        $this->assertCount(4,                      $listForDefaultPageSize->listItems);
        $this->assertSame(1,                       $listForDefaultPageSize->page);
        $this->assertSame(self::DEFAULT_PAGE_SIZE, $listForDefaultPageSize->pageSize);
        $this->assertSame(null,                    $listForDefaultPageSize->term);
        $this->assertSame(4,                       $listForDefaultPageSize->totalCount);
        $this->assertSame(1,                       $listForDefaultPageSize->totalPages);
        $this->assertIsArray($listForDefaultPageSize->listItems);
        $this->assertContainsOnlyInstancesOf(FuneralCompanyViewListItem::class, $listForDefaultPageSize->listItems);
    }

    public function testItReturnsFuneralCompanyViewListItemsByPageAndTerm(): void
    {
        $customPageSize = 3;

        $list = $this->funeralCompanyFetcher->findAll(1, '44', $customPageSize);
        $this->assertInstanceOf(FuneralCompanyViewList::class, $list);
        $this->assertCount(1,              $list->listItems);
        $this->assertSame(1,               $list->page);
        $this->assertSame($customPageSize, $list->pageSize);
        $this->assertSame('44',            $list->term);
        $this->assertSame(1,               $list->totalCount);
        $this->assertSame(1,               $list->totalPages);
        $this->assertIsArray($list->listItems);
        $this->assertContainsOnlyInstancesOf(FuneralCompanyViewListItem::class, $list->listItems);

        $list = $this->funeralCompanyFetcher->findAll(1, 'Кемеров', $customPageSize);
        $this->assertInstanceOf(FuneralCompanyViewList::class, $list);
        $this->assertCount(2,              $list->listItems);
        $this->assertSame(1,               $list->page);
        $this->assertSame($customPageSize, $list->pageSize);
        $this->assertSame('Кемеров',       $list->term);
        $this->assertSame(2,               $list->totalCount);
        $this->assertSame(1,               $list->totalPages);
        $this->assertIsArray($list->listItems);
        $this->assertContainsOnlyInstancesOf(FuneralCompanyViewListItem::class, $list->listItems);

        $list = $this->funeralCompanyFetcher->findAll(1, 'ро', $customPageSize);
        $this->assertInstanceOf(FuneralCompanyViewList::class, $list);
        $this->assertCount(3,              $list->listItems);
        $this->assertSame(1,               $list->page);
        $this->assertSame($customPageSize, $list->pageSize);
        $this->assertSame('ро',            $list->term);
        $this->assertSame(4,               $list->totalCount);
        $this->assertSame(2,               $list->totalPages);
        $this->assertIsArray($list->listItems);
        $this->assertContainsOnlyInstancesOf(FuneralCompanyViewListItem::class, $list->listItems);
        $list = $this->funeralCompanyFetcher->findAll(2, 'ро', $customPageSize);
        $this->assertInstanceOf(FuneralCompanyViewList::class, $list);
        $this->assertCount(1,              $list->listItems);
        $this->assertSame(2,               $list->page);
        $this->assertSame($customPageSize, $list->pageSize);
        $this->assertSame('ро',            $list->term);
        $this->assertSame(4,               $list->totalCount);
        $this->assertSame(2,               $list->totalPages);
        $this->assertIsArray($list->listItems);
        $this->assertContainsOnlyInstancesOf(FuneralCompanyViewListItem::class, $list->listItems);

        $list = $this->funeralCompanyFetcher->findAll(1, '133', $customPageSize);
        $this->assertInstanceOf(FuneralCompanyViewList::class, $list);
        $this->assertCount(1,              $list->listItems);
        $this->assertSame(1,               $list->page);
        $this->assertSame($customPageSize, $list->pageSize);
        $this->assertSame('133',           $list->term);
        $this->assertSame(1,               $list->totalCount);
        $this->assertSame(1,               $list->totalPages);
        $this->assertIsArray($list->listItems);
        $this->assertContainsOnlyInstancesOf(FuneralCompanyViewListItem::class, $list->listItems);
    }

    public function testItReturnsFuneralCompanyTotalCount(): void
    {
        $this->assertSame(4, $this->funeralCompanyFetcher->getTotalCount());
    }

    public function testItDoesNotCountRemovedFuneralCompanyWhenCalculatingTotalCount(): void
    {
        // Prepare database table for testing
        $funeralCompanyToRemove = $this->funeralCompanyRepo->findById(new FuneralCompanyId('FC004'));
        $this->funeralCompanyRepo->remove($funeralCompanyToRemove);

        // Testing itself
        $this->assertSame(3, $this->funeralCompanyFetcher->getTotalCount());
    }

    protected function loadFixtures(): void
    {
        $this->databaseTool->loadFixtures([
            JuristicPersonFixtures::class,
            SoleProprietorFixtures::class,
            FuneralCompanyFixtures::class,
        ]);
    }

    private function assertItemEqualsFC001(FuneralCompanyViewListItem $item): void
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

    private function assertItemEqualsFC002(FuneralCompanyViewListItem $item): void
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

    private function assertItemEqualsFC003(FuneralCompanyViewListItem $item): void
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

    private function assertItemEqualsFC004(FuneralCompanyViewListItem $item): void
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
