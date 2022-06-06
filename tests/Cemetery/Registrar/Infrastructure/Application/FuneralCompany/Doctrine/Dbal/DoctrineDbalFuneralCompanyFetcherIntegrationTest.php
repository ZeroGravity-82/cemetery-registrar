<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Application\FuneralCompany\Doctrine\Dbal;

use Cemetery\Registrar\Application\FuneralCompany\FuneralCompanyFetcher;
use Cemetery\Registrar\Application\FuneralCompany\FuneralCompanyViewList;
use Cemetery\Registrar\Application\FuneralCompany\FuneralCompanyViewListItem;
use Cemetery\Registrar\Domain\FuneralCompany\FuneralCompany;
use Cemetery\Registrar\Domain\FuneralCompany\FuneralCompanyCollection;
use Cemetery\Registrar\Domain\FuneralCompany\FuneralCompanyId;
use Cemetery\Registrar\Domain\FuneralCompany\FuneralCompanyRepository;
use Cemetery\Registrar\Domain\Organization\JuristicPerson\JuristicPerson;
use Cemetery\Registrar\Domain\Organization\JuristicPerson\JuristicPersonCollection;
use Cemetery\Registrar\Domain\Organization\SoleProprietor\SoleProprietor;
use Cemetery\Registrar\Domain\Organization\SoleProprietor\SoleProprietorCollection;
use Cemetery\Registrar\Infrastructure\Application\FuneralCompany\Doctrine\Dbal\DoctrineDbalFuneralCompanyFetcher;
use Cemetery\Registrar\Infrastructure\Domain\Burial\Doctrine\Orm\DoctrineOrmBurialRepository;
use Cemetery\Registrar\Infrastructure\Domain\FuneralCompany\Doctrine\Orm\DoctrineOrmFuneralCompanyRepository;
use Cemetery\Registrar\Infrastructure\Domain\Organization\JuristicPerson\Doctrine\Orm\DoctrineOrmJuristicPersonRepository;
use Cemetery\Registrar\Infrastructure\Domain\Organization\SoleProprietor\Doctrine\Orm\DoctrineOrmSoleProprietorRepository;
use Cemetery\Tests\Registrar\Domain\FuneralCompany\FuneralCompanyProvider;
use Cemetery\Tests\Registrar\Domain\Organization\JuristicPerson\JuristicPersonProvider;
use Cemetery\Tests\Registrar\Domain\Organization\SoleProprietor\SoleProprietorProvider;
use Cemetery\Tests\Registrar\Infrastructure\Application\FetcherIntegrationTest;

/**
 * @group database
 *
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class DoctrineDbalFuneralCompanyFetcherIntegrationTest extends FetcherIntegrationTest
{
    private const DEFAULT_PAGE_SIZE = 20;

    private FuneralCompanyRepository $funeralCompanyRepo;
    private FuneralCompany           $entityA;
    private FuneralCompany           $entityB;
    private FuneralCompany           $entityC;
    private FuneralCompany           $entityD;
    private FuneralCompanyFetcher    $funeralCompanyFetcher;

    public function setUp(): void
    {
        parent::setUp();

        $this->funeralCompanyRepo = new DoctrineOrmFuneralCompanyRepository($this->entityManager);
        $this->entityA            = FuneralCompanyProvider::getFuneralCompanyA();
        $this->entityB            = FuneralCompanyProvider::getFuneralCompanyB();
        $this->entityC            = FuneralCompanyProvider::getFuneralCompanyC();
        $this->entityD            = FuneralCompanyProvider::getFuneralCompanyD();
        $this->fillDatabase();
        $this->funeralCompanyFetcher = new DoctrineDbalFuneralCompanyFetcher($this->connection);
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

    public function testItFailsToReturnFuneralCompanyFormViewForRemovedBurial(): void
    {
        // Prepare database table for testing
        $this->funeralCompanyRepo->remove($this->entityD);
        $funeralCompanyIdD = $this->entityD->id()->value();

        // Testing itself
//         $this->expectExceptionForNotFoundFuneralCompanyById($funeralCompanyIdD);
//        $this->funeralCompanyFetcher->getFormViewById($funeralCompanyIdD);
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
        $burialViewListForSecondPage = $this->funeralCompanyFetcher->findAll(2, null, $customPageSize);
        $this->assertInstanceOf(FuneralCompanyViewList::class, $burialViewListForSecondPage);
        $this->assertCount(1,              $burialViewListForSecondPage->funeralCompanyViewListItems);
        $this->assertSame(2,               $burialViewListForSecondPage->page);
        $this->assertSame($customPageSize, $burialViewListForSecondPage->pageSize);
        $this->assertSame(null,            $burialViewListForSecondPage->term);
        $this->assertSame(4,               $burialViewListForSecondPage->totalCount);
        $this->assertSame(2,               $burialViewListForSecondPage->totalPages);
        $this->assertIsArray($burialViewListForSecondPage->funeralCompanyViewListItems);
        $this->assertContainsOnlyInstancesOf(FuneralCompanyViewListItem::class, $burialViewListForSecondPage->funeralCompanyViewListItems);
        $this->assertItemForSecondPageEqualsFC004($burialViewListForSecondPage->funeralCompanyViewListItems[0]);

        // Third page
        $burialViewListForThirdPage = $this->funeralCompanyFetcher->findAll(3, null, $customPageSize);
        $this->assertInstanceOf(FuneralCompanyViewList::class, $burialViewListForThirdPage);
        $this->assertCount(0,              $burialViewListForThirdPage->funeralCompanyViewListItems);
        $this->assertSame(3,               $burialViewListForThirdPage->page);
        $this->assertSame($customPageSize, $burialViewListForThirdPage->pageSize);
        $this->assertSame(null,            $burialViewListForThirdPage->term);
        $this->assertSame(4,               $burialViewListForThirdPage->totalCount);
        $this->assertSame(2,               $burialViewListForThirdPage->totalPages);

        // All at once
        $burialViewListForDefaultPageSize = $this->funeralCompanyFetcher->findAll(1);
        $this->assertInstanceOf(FuneralCompanyViewList::class, $burialViewListForDefaultPageSize);
        $this->assertCount(4,                      $burialViewListForDefaultPageSize->funeralCompanyViewListItems);
        $this->assertSame(1,                       $burialViewListForDefaultPageSize->page);
        $this->assertSame(self::DEFAULT_PAGE_SIZE, $burialViewListForDefaultPageSize->pageSize);
        $this->assertSame(null,                    $burialViewListForDefaultPageSize->term);
        $this->assertSame(4,                       $burialViewListForDefaultPageSize->totalCount);
        $this->assertSame(1,                       $burialViewListForDefaultPageSize->totalPages);
        $this->assertIsArray($burialViewListForDefaultPageSize->funeralCompanyViewListItems);
        $this->assertContainsOnlyInstancesOf(FuneralCompanyViewListItem::class, $burialViewListForDefaultPageSize->funeralCompanyViewListItems);
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
        $this->funeralCompanyRepo->remove($this->entityD);

        // Testing itself
        $this->assertSame(3, $this->funeralCompanyFetcher->getTotalCount());
    }

    private function fillDatabase(): void
    {
        $this->fillFuneralCompanyTable();
        $this->fillSoleProprietorTable();
        $this->fillJuristicPersonTable();
    }

    private function fillFuneralCompanyTable(): void
    {
        $this->funeralCompanyRepo
            ->saveAll(new FuneralCompanyCollection([
                $this->entityA,
                $this->entityB,
                $this->entityC,
                $this->entityD,
            ]));
    }

    private function fillJuristicPersonTable(): void
    {
        (new DoctrineOrmJuristicPersonRepository($this->entityManager))
            ->saveAll(new JuristicPersonCollection([
                JuristicPersonProvider::getJuristicPersonA(),
                JuristicPersonProvider::getJuristicPersonB(),
                JuristicPersonProvider::getJuristicPersonC(),
                JuristicPersonProvider::getJuristicPersonD(),
            ]));
    }

    private function fillSoleProprietorTable(): void
    {
        (new DoctrineOrmSoleProprietorRepository($this->entityManager))
            ->saveAll(new SoleProprietorCollection([
                SoleProprietorProvider::getSoleProprietorA(),
                SoleProprietorProvider::getSoleProprietorB(),
                SoleProprietorProvider::getSoleProprietorC(),
                SoleProprietorProvider::getSoleProprietorD(),
            ]));
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
