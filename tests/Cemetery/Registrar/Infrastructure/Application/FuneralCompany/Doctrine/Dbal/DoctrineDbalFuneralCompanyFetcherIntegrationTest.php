<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Application\FuneralCompany\Doctrine\Dbal;

use Cemetery\Registrar\Application\FuneralCompany\FuneralCompanyFetcher;
use Cemetery\Registrar\Application\FuneralCompany\FuneralCompanyViewList;
use Cemetery\Registrar\Application\FuneralCompany\FuneralCompanyViewListItem;
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
        $funeralCompanyViewListForFirstPage = $this->funeralCompanyFetcher->findAll(1, null, $customPageSize);
        $this->assertInstanceOf(FuneralCompanyViewList::class, $funeralCompanyViewListForFirstPage);
        $this->assertCount(3,              $funeralCompanyViewListForFirstPage->funeralCompanyViewListItems);
        $this->assertSame(1,               $funeralCompanyViewListForFirstPage->page);
        $this->assertSame($customPageSize, $funeralCompanyViewListForFirstPage->pageSize);
        $this->assertSame(null,            $funeralCompanyViewListForFirstPage->term);
        $this->assertSame(4,               $funeralCompanyViewListForFirstPage->totalCount);
        $this->assertSame(2,               $funeralCompanyViewListForFirstPage->totalPages);
        $this->assertIsArray($funeralCompanyViewListForFirstPage->funeralCompanyViewListItems);
        $this->assertContainsOnlyInstancesOf(FuneralCompanyViewListItem::class, $funeralCompanyViewListForFirstPage->funeralCompanyViewListItems);
        $this->assertItemForFirstPageEqualsFC002($funeralCompanyViewListForFirstPage->funeralCompanyViewListItems[0]);  // Items are ordered by name
        $this->assertItemForFirstPageEqualsFC003($funeralCompanyViewListForFirstPage->funeralCompanyViewListItems[1]);
        $this->assertItemForFirstPageEqualsFC001($funeralCompanyViewListForFirstPage->funeralCompanyViewListItems[2]);

        // Second page
        $funeralCompanyViewListForSecondPage = $this->funeralCompanyFetcher->findAll(2, null, $customPageSize);
        $this->assertInstanceOf(FuneralCompanyViewList::class, $funeralCompanyViewListForSecondPage);
        $this->assertCount(1,              $funeralCompanyViewListForSecondPage->funeralCompanyViewListItems);
        $this->assertSame(2,               $funeralCompanyViewListForSecondPage->page);
        $this->assertSame($customPageSize, $funeralCompanyViewListForSecondPage->pageSize);
        $this->assertSame(null,            $funeralCompanyViewListForSecondPage->term);
        $this->assertSame(4,               $funeralCompanyViewListForSecondPage->totalCount);
        $this->assertSame(2,               $funeralCompanyViewListForSecondPage->totalPages);
        $this->assertIsArray($funeralCompanyViewListForSecondPage->funeralCompanyViewListItems);
        $this->assertContainsOnlyInstancesOf(FuneralCompanyViewListItem::class, $funeralCompanyViewListForSecondPage->funeralCompanyViewListItems);
        $this->assertItemForSecondPageEqualsFC004($funeralCompanyViewListForSecondPage->funeralCompanyViewListItems[0]);

        // Third page
        $funeralCompanyViewListForThirdPage = $this->funeralCompanyFetcher->findAll(3, null, $customPageSize);
        $this->assertInstanceOf(FuneralCompanyViewList::class, $funeralCompanyViewListForThirdPage);
        $this->assertCount(0,              $funeralCompanyViewListForThirdPage->funeralCompanyViewListItems);
        $this->assertSame(3,               $funeralCompanyViewListForThirdPage->page);
        $this->assertSame($customPageSize, $funeralCompanyViewListForThirdPage->pageSize);
        $this->assertSame(null,            $funeralCompanyViewListForThirdPage->term);
        $this->assertSame(4,               $funeralCompanyViewListForThirdPage->totalCount);
        $this->assertSame(2,               $funeralCompanyViewListForThirdPage->totalPages);

        // All at once
        $funeralCompanyViewListForDefaultPageSize = $this->funeralCompanyFetcher->findAll(1);
        $this->assertInstanceOf(FuneralCompanyViewList::class, $funeralCompanyViewListForDefaultPageSize);
        $this->assertCount(4,                      $funeralCompanyViewListForDefaultPageSize->funeralCompanyViewListItems);
        $this->assertSame(1,                       $funeralCompanyViewListForDefaultPageSize->page);
        $this->assertSame(self::DEFAULT_PAGE_SIZE, $funeralCompanyViewListForDefaultPageSize->pageSize);
        $this->assertSame(null,                    $funeralCompanyViewListForDefaultPageSize->term);
        $this->assertSame(4,                       $funeralCompanyViewListForDefaultPageSize->totalCount);
        $this->assertSame(1,                       $funeralCompanyViewListForDefaultPageSize->totalPages);
        $this->assertIsArray($funeralCompanyViewListForDefaultPageSize->funeralCompanyViewListItems);
        $this->assertContainsOnlyInstancesOf(FuneralCompanyViewListItem::class, $funeralCompanyViewListForDefaultPageSize->funeralCompanyViewListItems);
    }

    public function testItReturnsFuneralCompanyViewListItemsByPageAndTerm(): void
    {
        $customPageSize = 3;

        $funeralCompanyViewList = $this->funeralCompanyFetcher->findAll(1, '44', $customPageSize);
        $this->assertInstanceOf(FuneralCompanyViewList::class, $funeralCompanyViewList);
        $this->assertCount(1,              $funeralCompanyViewList->funeralCompanyViewListItems);
        $this->assertSame(1,               $funeralCompanyViewList->page);
        $this->assertSame($customPageSize, $funeralCompanyViewList->pageSize);
        $this->assertSame('44',            $funeralCompanyViewList->term);
        $this->assertSame(1,               $funeralCompanyViewList->totalCount);
        $this->assertSame(1,               $funeralCompanyViewList->totalPages);
        $this->assertIsArray($funeralCompanyViewList->funeralCompanyViewListItems);
        $this->assertContainsOnlyInstancesOf(FuneralCompanyViewListItem::class, $funeralCompanyViewList->funeralCompanyViewListItems);

        $funeralCompanyViewList = $this->funeralCompanyFetcher->findAll(1, 'Кемеров', $customPageSize);
        $this->assertInstanceOf(FuneralCompanyViewList::class, $funeralCompanyViewList);
        $this->assertCount(2,              $funeralCompanyViewList->funeralCompanyViewListItems);
        $this->assertSame(1,               $funeralCompanyViewList->page);
        $this->assertSame($customPageSize, $funeralCompanyViewList->pageSize);
        $this->assertSame('Кемеров',       $funeralCompanyViewList->term);
        $this->assertSame(2,               $funeralCompanyViewList->totalCount);
        $this->assertSame(1,               $funeralCompanyViewList->totalPages);
        $this->assertIsArray($funeralCompanyViewList->funeralCompanyViewListItems);
        $this->assertContainsOnlyInstancesOf(FuneralCompanyViewListItem::class, $funeralCompanyViewList->funeralCompanyViewListItems);

        $funeralCompanyViewList = $this->funeralCompanyFetcher->findAll(1, 'ро', $customPageSize);
        $this->assertInstanceOf(FuneralCompanyViewList::class, $funeralCompanyViewList);
        $this->assertCount(3,              $funeralCompanyViewList->funeralCompanyViewListItems);
        $this->assertSame(1,               $funeralCompanyViewList->page);
        $this->assertSame($customPageSize, $funeralCompanyViewList->pageSize);
        $this->assertSame('ро',            $funeralCompanyViewList->term);
        $this->assertSame(4,               $funeralCompanyViewList->totalCount);
        $this->assertSame(2,               $funeralCompanyViewList->totalPages);
        $this->assertIsArray($funeralCompanyViewList->funeralCompanyViewListItems);
        $this->assertContainsOnlyInstancesOf(FuneralCompanyViewListItem::class, $funeralCompanyViewList->funeralCompanyViewListItems);
        $funeralCompanyViewList = $this->funeralCompanyFetcher->findAll(2, 'ро', $customPageSize);
        $this->assertInstanceOf(FuneralCompanyViewList::class, $funeralCompanyViewList);
        $this->assertCount(1,              $funeralCompanyViewList->funeralCompanyViewListItems);
        $this->assertSame(2,               $funeralCompanyViewList->page);
        $this->assertSame($customPageSize, $funeralCompanyViewList->pageSize);
        $this->assertSame('ро',            $funeralCompanyViewList->term);
        $this->assertSame(4,               $funeralCompanyViewList->totalCount);
        $this->assertSame(2,               $funeralCompanyViewList->totalPages);
        $this->assertIsArray($funeralCompanyViewList->funeralCompanyViewListItems);
        $this->assertContainsOnlyInstancesOf(FuneralCompanyViewListItem::class, $funeralCompanyViewList->funeralCompanyViewListItems);

        $funeralCompanyViewList = $this->funeralCompanyFetcher->findAll(1, '133', $customPageSize);
        $this->assertInstanceOf(FuneralCompanyViewList::class, $funeralCompanyViewList);
        $this->assertCount(1,              $funeralCompanyViewList->funeralCompanyViewListItems);
        $this->assertSame(1,               $funeralCompanyViewList->page);
        $this->assertSame($customPageSize, $funeralCompanyViewList->pageSize);
        $this->assertSame('133',           $funeralCompanyViewList->term);
        $this->assertSame(1,               $funeralCompanyViewList->totalCount);
        $this->assertSame(1,               $funeralCompanyViewList->totalPages);
        $this->assertIsArray($funeralCompanyViewList->funeralCompanyViewListItems);
        $this->assertContainsOnlyInstancesOf(FuneralCompanyViewListItem::class, $funeralCompanyViewList->funeralCompanyViewListItems);
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

    private function assertItemForFirstPageEqualsFC002(FuneralCompanyViewListItem $item): void
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

    private function assertItemForFirstPageEqualsFC003(FuneralCompanyViewListItem $item): void
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

    private function assertItemForFirstPageEqualsFC001(FuneralCompanyViewListItem $item): void
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

    private function assertItemForSecondPageEqualsFC004(FuneralCompanyViewListItem $item): void
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
