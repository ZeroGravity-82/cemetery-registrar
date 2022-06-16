<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Fetcher;

use Cemetery\Registrar\Domain\Model\FuneralCompany\FuneralCompanyId;
use Cemetery\Registrar\Domain\Model\Organization\JuristicPerson\JuristicPerson;
use Cemetery\Registrar\Domain\Model\Organization\JuristicPerson\JuristicPersonId;
use Cemetery\Registrar\Domain\Model\Organization\JuristicPerson\JuristicPersonRepository;
use Cemetery\Registrar\Domain\Model\Organization\SoleProprietor\SoleProprietor;
use Cemetery\Registrar\Domain\Model\Organization\SoleProprietor\SoleProprietorId;
use Cemetery\Registrar\Domain\Model\Organization\SoleProprietor\SoleProprietorRepository;
use Cemetery\Registrar\Domain\View\Organization\OrganizationFetcher;
use Cemetery\Registrar\Domain\View\Organization\OrganizationList;
use Cemetery\Registrar\Domain\View\Organization\OrganizationListItem;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Orm\Repository\DoctrineOrmJuristicPersonRepository;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Orm\Repository\DoctrineOrmSoleProprietorRepository;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Fetcher\DoctrineDbalOrganizationFetcher;
use DataFixtures\Organization\JuristicPerson\JuristicPersonFixtures;
use DataFixtures\Organization\SoleProprietor\SoleProprietorFixtures;

/**
 * @group database
 *
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class DoctrineDbalOrganizationFetcherIntegrationTest extends FetcherIntegrationTest
{
    private const DEFAULT_PAGE_SIZE = 20;

    private JuristicPersonRepository $juristicPersonRepo;
    private SoleProprietorRepository $soleProprietorRepo;
    private OrganizationFetcher      $organizationFetcher;

    public function setUp(): void
    {
        parent::setUp();

        $this->juristicPersonRepo    = new DoctrineOrmJuristicPersonRepository($this->entityManager);
        $this->soleProprietorRepo    = new DoctrineOrmSoleProprietorRepository($this->entityManager);
        $this->organizationFetcher = new DoctrineDbalOrganizationFetcher($this->connection);
        $this->loadFixtures();
    }

    public function testItHasValidPageSizeConstant(): void
    {
        $this->assertSame(self::DEFAULT_PAGE_SIZE, OrganizationFetcher::DEFAULT_PAGE_SIZE);
    }

    public function testItReturnsOrganizationViewById(): void
    {
        $this->markTestIncomplete();
//        $this->testItReturnsOrganizationViewForFC001();
//        $this->testItReturnsOrganizationViewForFC002();
//        $this->testItReturnsOrganizationViewForFC003();
//        $this->testItReturnsOrganizationViewForFC004();
    }

    public function testItFailsToReturnOrganizationViewByUnknownId(): void
    {
        $this->markTestIncomplete();
//        $this->expectExceptionForNotFoundOrganizationById('unknown_id');
//        $this->funeralCompanyFetcher->getViewById('unknown_id');
    }

    public function testItFailsToReturnOrganizationViewForRemovedJuristicPerson(): void
    {
        // Prepare database table for testing
        $juristicPersonToRemove = $this->juristicPersonRepo->findById(new JuristicPersonId('JP003'));
        $this->juristicPersonRepo->remove($juristicPersonToRemove);
        $removedJuristicPersonId = $juristicPersonToRemove->id()->value();

        // Testing itself
        $this->expectExceptionForNotFoundOrganizationById($removedJuristicPersonId);
        $this->organizationFetcher->getViewById($removedJuristicPersonId);
    }

    public function testItFailsToReturnOrganizationViewForRemovedSoleProprietor(): void
    {
        // Prepare database table for testing
        $soleProprietorToRemove = $this->soleProprietorRepo->findById(new SoleProprietorId('SP002'));
        $this->soleProprietorRepo->remove($soleProprietorToRemove);
        $removedSoleProprietorId = $soleProprietorToRemove->id()->value();

        // Testing itself
        $this->expectExceptionForNotFoundOrganizationById($removedSoleProprietorId);
        $this->organizationFetcher->getViewById($removedSoleProprietorId);
    }

    public function testItReturnsOrganizationListItemsByPage(): void
    {
        $customPageSize = 4;

        // First page
        $listForFirstPage = $this->organizationFetcher->findAll(1, null, $customPageSize);
        $this->assertInstanceOf(OrganizationList::class, $listForFirstPage);
        $this->assertCount(4,              $listForFirstPage->listItems);
        $this->assertSame(1,               $listForFirstPage->page);
        $this->assertSame($customPageSize, $listForFirstPage->pageSize);
        $this->assertSame(null,            $listForFirstPage->term);
        $this->assertSame(9,               $listForFirstPage->totalCount);
        $this->assertSame(3,               $listForFirstPage->totalPages);
        $this->assertIsArray($listForFirstPage->listItems);
        $this->assertContainsOnlyInstancesOf(OrganizationListItem::class, $listForFirstPage->listItems);
        $this->assertItemEqualsSP001($listForFirstPage->listItems[0]);  // Items are ordered by name
        $this->assertItemEqualsSP002($listForFirstPage->listItems[1]);
        $this->assertItemEqualsSP003($listForFirstPage->listItems[2]);
        $this->assertItemEqualsSP004($listForFirstPage->listItems[3]);

        // Second page
        $listForSecondPage = $this->organizationFetcher->findAll(2, null, $customPageSize);
        $this->assertInstanceOf(OrganizationList::class, $listForSecondPage);
        $this->assertCount(4,              $listForSecondPage->listItems);
        $this->assertSame(2,               $listForSecondPage->page);
        $this->assertSame($customPageSize, $listForSecondPage->pageSize);
        $this->assertSame(null,            $listForSecondPage->term);
        $this->assertSame(9,               $listForSecondPage->totalCount);
        $this->assertSame(3,               $listForSecondPage->totalPages);
        $this->assertIsArray($listForSecondPage->listItems);
        $this->assertContainsOnlyInstancesOf(OrganizationListItem::class, $listForSecondPage->listItems);
        $this->assertItemEqualsJP004($listForSecondPage->listItems[0]);
        $this->assertItemEqualsJP005($listForSecondPage->listItems[1]);
        $this->assertItemEqualsJP001($listForSecondPage->listItems[2]);
        $this->assertItemEqualsJP002($listForSecondPage->listItems[3]);

        // Third page
        $listForThirdPage = $this->organizationFetcher->findAll(3, null, $customPageSize);
        $this->assertInstanceOf(OrganizationList::class, $listForThirdPage);
        $this->assertCount(1,              $listForThirdPage->listItems);
        $this->assertSame(3,               $listForThirdPage->page);
        $this->assertSame($customPageSize, $listForThirdPage->pageSize);
        $this->assertSame(null,            $listForThirdPage->term);
        $this->assertSame(9,               $listForThirdPage->totalCount);
        $this->assertSame(3,               $listForThirdPage->totalPages);
        $this->assertIsArray($listForThirdPage->listItems);
        $this->assertContainsOnlyInstancesOf(OrganizationListItem::class, $listForThirdPage->listItems);
        $this->assertItemEqualsJP003($listForThirdPage->listItems[0]);

        // Fourth page
        $listForFourthPage = $this->organizationFetcher->findAll(4, null, $customPageSize);
        $this->assertInstanceOf(OrganizationList::class, $listForFourthPage);
        $this->assertCount(0,              $listForFourthPage->listItems);
        $this->assertSame(4,               $listForFourthPage->page);
        $this->assertSame($customPageSize, $listForFourthPage->pageSize);
        $this->assertSame(null,            $listForFourthPage->term);
        $this->assertSame(9,               $listForFourthPage->totalCount);
        $this->assertSame(3,               $listForFourthPage->totalPages);

        // All at once
        $listForDefaultPageSize = $this->organizationFetcher->findAll(1);
        $this->assertInstanceOf(OrganizationList::class, $listForDefaultPageSize);
        $this->assertCount(9,                      $listForDefaultPageSize->listItems);
        $this->assertSame(1,                       $listForDefaultPageSize->page);
        $this->assertSame(self::DEFAULT_PAGE_SIZE, $listForDefaultPageSize->pageSize);
        $this->assertSame(null,                    $listForDefaultPageSize->term);
        $this->assertSame(9,                       $listForDefaultPageSize->totalCount);
        $this->assertSame(1,                       $listForDefaultPageSize->totalPages);
        $this->assertIsArray($listForDefaultPageSize->listItems);
        $this->assertContainsOnlyInstancesOf(OrganizationListItem::class, $listForDefaultPageSize->listItems);
    }

    public function testItReturnsOrganizationListItemsByPageAndTerm(): void
    {
        $customPageSize = 3;

        $list = $this->organizationFetcher->findAll(1, '44', $customPageSize);
        $this->assertInstanceOf(OrganizationList::class, $list);
        $this->assertCount(1,              $list->listItems);
        $this->assertSame(1,               $list->page);
        $this->assertSame($customPageSize, $list->pageSize);
        $this->assertSame('44',            $list->term);
        $this->assertSame(1,               $list->totalCount);
        $this->assertSame(1,               $list->totalPages);
        $this->assertIsArray($list->listItems);
        $this->assertContainsOnlyInstancesOf(OrganizationListItem::class, $list->listItems);

        $list = $this->organizationFetcher->findAll(1, 'Кемеров', $customPageSize);
        $this->assertInstanceOf(OrganizationList::class, $list);
        $this->assertCount(2,              $list->listItems);
        $this->assertSame(1,               $list->page);
        $this->assertSame($customPageSize, $list->pageSize);
        $this->assertSame('Кемеров',       $list->term);
        $this->assertSame(2,               $list->totalCount);
        $this->assertSame(1,               $list->totalPages);
        $this->assertIsArray($list->listItems);
        $this->assertContainsOnlyInstancesOf(OrganizationListItem::class, $list->listItems);

        $list = $this->organizationFetcher->findAll(1, 'ро', $customPageSize);
        $this->assertInstanceOf(OrganizationList::class, $list);
        $this->assertCount(3,              $list->listItems);
        $this->assertSame(1,               $list->page);
        $this->assertSame($customPageSize, $list->pageSize);
        $this->assertSame('ро',            $list->term);
        $this->assertSame(4,               $list->totalCount);
        $this->assertSame(2,               $list->totalPages);
        $this->assertIsArray($list->listItems);
        $this->assertContainsOnlyInstancesOf(OrganizationListItem::class, $list->listItems);
        $list = $this->organizationFetcher->findAll(2, 'ро', $customPageSize);
        $this->assertInstanceOf(OrganizationList::class, $list);
        $this->assertCount(1,              $list->listItems);
        $this->assertSame(2,               $list->page);
        $this->assertSame($customPageSize, $list->pageSize);
        $this->assertSame('ро',            $list->term);
        $this->assertSame(4,               $list->totalCount);
        $this->assertSame(2,               $list->totalPages);
        $this->assertIsArray($list->listItems);
        $this->assertContainsOnlyInstancesOf(OrganizationListItem::class, $list->listItems);

        $list = $this->organizationFetcher->findAll(1, '133', $customPageSize);
        $this->assertInstanceOf(OrganizationList::class, $list);
        $this->assertCount(1,              $list->listItems);
        $this->assertSame(1,               $list->page);
        $this->assertSame($customPageSize, $list->pageSize);
        $this->assertSame('133',           $list->term);
        $this->assertSame(1,               $list->totalCount);
        $this->assertSame(1,               $list->totalPages);
        $this->assertIsArray($list->listItems);
        $this->assertContainsOnlyInstancesOf(OrganizationListItem::class, $list->listItems);
    }

    public function testItReturnsOrganizationTotalCount(): void
    {
        $this->assertSame(9, $this->organizationFetcher->countTotal());
    }

    public function testItDoesNotCountRemovedOrganizationsWhenCalculatingTotalCount(): void
    {
        // Prepare database table for testing
        $juristicPersonToRemove = $this->juristicPersonRepo->findById(new JuristicPersonId('JP003'));
        $this->juristicPersonRepo->remove($juristicPersonToRemove);
        $soleProprietorToRemove = $this->soleProprietorRepo->findById(new SoleProprietorId('SP002'));
        $this->soleProprietorRepo->remove($soleProprietorToRemove);

        // Testing itself
        $this->assertSame(7, $this->organizationFetcher->countTotal());
    }

    protected function loadFixtures(): void
    {
        $this->databaseTool->loadFixtures([
            JuristicPersonFixtures::class,
            SoleProprietorFixtures::class,
        ]);
    }

    private function assertItemEqualsSP001(OrganizationListItem $item): void
    {
        $this->assertSame('SP001',                        $item->id);
        $this->assertSame(SoleProprietor::CLASS_SHORTCUT, $item->typeShortcut);
        $this->assertSame(SoleProprietor::CLASS_LABEL,    $item->typeLabel);
        $this->assertSame(null,                           $item->juristicPersonName);
        $this->assertSame(null,                           $item->juristicPersonInn);
        $this->assertSame(null,                           $item->juristicPersonKpp);
        $this->assertSame(null,                           $item->juristicPersonOgrn);
        $this->assertSame(null,                           $item->juristicPersonOkpo);
        $this->assertSame(null,                           $item->juristicPersonOkved);
        $this->assertSame(null,                           $item->juristicPersonLegalAddress);
        $this->assertSame(null,                           $item->juristicPersonPostalAddress);
        $this->assertSame(null,                           $item->juristicPersonBankDetails);
        $this->assertSame(null,                           $item->juristicPersonPhone);
        $this->assertSame(null,                           $item->juristicPersonPhoneAdditional);
        $this->assertSame(null,                           $item->juristicPersonFax);
        $this->assertSame(null,                           $item->juristicPersonGeneralDirector);
        $this->assertSame(null,                           $item->juristicPersonEmail);
        $this->assertSame(null,                           $item->juristicPersonWebsite);
        $this->assertSame('ИП Иванов Иван Иванович',      $item->soleProprietorName);
        $this->assertSame(null,                           $item->soleProprietorInn);
        $this->assertSame(null,                           $item->soleProprietorOgrnip);
        $this->assertSame(null,                           $item->soleProprietorOkpo);
        $this->assertSame(null,                           $item->soleProprietorOkved);
        $this->assertSame(null,                           $item->soleProprietorRegistrationAddress);
        $this->assertSame(null,                           $item->soleProprietorActualLocationAddress);
        $this->assertSame(null,                           $item->soleProprietorBankDetails);
        $this->assertSame(null,                           $item->soleProprietorPhone);
        $this->assertSame(null,                           $item->soleProprietorPhoneAdditional);
        $this->assertSame(null,                           $item->soleProprietorFax);
        $this->assertSame(null,                           $item->soleProprietorEmail);
        $this->assertSame(null,                           $item->soleProprietorWebsite);
    }

    private function assertItemEqualsSP002(OrganizationListItem $item): void
    {
        $this->assertSame('SP002',                        $item->id);
        $this->assertSame(SoleProprietor::CLASS_SHORTCUT, $item->typeShortcut);
        $this->assertSame(SoleProprietor::CLASS_LABEL,    $item->typeLabel);
        $this->assertSame(null,                           $item->juristicPersonName);
        $this->assertSame(null,                           $item->juristicPersonInn);
        $this->assertSame(null,                           $item->juristicPersonKpp);
        $this->assertSame(null,                           $item->juristicPersonOgrn);
        $this->assertSame(null,                           $item->juristicPersonOkpo);
        $this->assertSame(null,                           $item->juristicPersonOkved);
        $this->assertSame(null,                           $item->juristicPersonLegalAddress);
        $this->assertSame(null,                           $item->juristicPersonPostalAddress);
        $this->assertSame(null,                           $item->juristicPersonBankDetails);
        $this->assertSame(null,                           $item->juristicPersonPhone);
        $this->assertSame(null,                           $item->juristicPersonPhoneAdditional);
        $this->assertSame(null,                           $item->juristicPersonFax);
        $this->assertSame(null,                           $item->juristicPersonGeneralDirector);
        $this->assertSame(null,                           $item->juristicPersonEmail);
        $this->assertSame(null,                           $item->juristicPersonWebsite);
        $this->assertSame('ИП Петров Пётр Петрович',      $item->soleProprietorName);
        $this->assertSame('772208786091',                 $item->soleProprietorInn);
        $this->assertSame(null,                           $item->soleProprietorOgrnip);
        $this->assertSame(null,                           $item->soleProprietorOkpo);
        $this->assertSame(null,                           $item->soleProprietorOkved);
        $this->assertSame(null,                           $item->soleProprietorRegistrationAddress);
        $this->assertSame(null,                           $item->soleProprietorActualLocationAddress);
        $this->assertSame(
            'АО "АЛЬФА-БАНК", р/счёт 40701810401400000014, к/счёт 30101810200000000593, БИК 044525593',
            $item->soleProprietorBankDetails
        );
        $this->assertSame('8(383)133-22-33',              $item->soleProprietorPhone);
        $this->assertSame('8(383)133-22-44',              $item->soleProprietorPhoneAdditional);
        $this->assertSame('8(383)133-22-55',              $item->soleProprietorFax);
        $this->assertSame('info@funeral54.ru',            $item->soleProprietorEmail);
        $this->assertSame('funeral54.ru',                 $item->soleProprietorWebsite);
    }

    private function assertItemEqualsSP003(OrganizationListItem $item): void
    {
        $this->assertSame('SP003',                        $item->id);
        $this->assertSame(SoleProprietor::CLASS_SHORTCUT, $item->typeShortcut);
        $this->assertSame(SoleProprietor::CLASS_LABEL,    $item->typeLabel);
        $this->assertSame(null,                           $item->juristicPersonName);
        $this->assertSame(null,                           $item->juristicPersonInn);
        $this->assertSame(null,                           $item->juristicPersonKpp);
        $this->assertSame(null,                           $item->juristicPersonOgrn);
        $this->assertSame(null,                           $item->juristicPersonOkpo);
        $this->assertSame(null,                           $item->juristicPersonOkved);
        $this->assertSame(null,                           $item->juristicPersonLegalAddress);
        $this->assertSame(null,                           $item->juristicPersonPostalAddress);
        $this->assertSame(null,                           $item->juristicPersonBankDetails);
        $this->assertSame(null,                           $item->juristicPersonPhone);
        $this->assertSame(null,                           $item->juristicPersonPhoneAdditional);
        $this->assertSame(null,                           $item->juristicPersonFax);
        $this->assertSame(null,                           $item->juristicPersonGeneralDirector);
        $this->assertSame(null,                           $item->juristicPersonEmail);
        $this->assertSame(null,                           $item->juristicPersonWebsite);
        $this->assertSame('ИП Сидоров Сидр Сидорович',    $item->soleProprietorName);
        $this->assertSame('391600743661',                 $item->soleProprietorInn);
        $this->assertSame(null,                           $item->soleProprietorOgrnip);
        $this->assertSame(null,                           $item->soleProprietorOkpo);
        $this->assertSame(null,                           $item->soleProprietorOkved);
        $this->assertSame(null,                           $item->soleProprietorRegistrationAddress);
        $this->assertSame('с. Каменка, д. 14',            $item->soleProprietorActualLocationAddress);
        $this->assertSame(null,                           $item->soleProprietorBankDetails);
        $this->assertSame('8(383)147-22-33',              $item->soleProprietorPhone);
        $this->assertSame(null,                           $item->soleProprietorPhoneAdditional);
        $this->assertSame(null,                           $item->soleProprietorFax);
        $this->assertSame(null,                           $item->soleProprietorEmail);
        $this->assertSame(null,                           $item->soleProprietorWebsite);
    }

    private function assertItemEqualsSP004(OrganizationListItem $item): void
    {
        $this->assertSame('SP004',                        $item->id);
        $this->assertSame(SoleProprietor::CLASS_SHORTCUT, $item->typeShortcut);
        $this->assertSame(SoleProprietor::CLASS_LABEL,    $item->typeLabel);
        $this->assertSame(null,                           $item->juristicPersonName);
        $this->assertSame(null,                           $item->juristicPersonInn);
        $this->assertSame(null,                           $item->juristicPersonKpp);
        $this->assertSame(null,                           $item->juristicPersonOgrn);
        $this->assertSame(null,                           $item->juristicPersonOkpo);
        $this->assertSame(null,                           $item->juristicPersonOkved);
        $this->assertSame(null,                           $item->juristicPersonLegalAddress);
        $this->assertSame(null,                           $item->juristicPersonPostalAddress);
        $this->assertSame(null,                           $item->juristicPersonBankDetails);
        $this->assertSame(null,                           $item->juristicPersonPhone);
        $this->assertSame(null,                           $item->juristicPersonPhoneAdditional);
        $this->assertSame(null,                           $item->juristicPersonFax);
        $this->assertSame(null,                           $item->juristicPersonGeneralDirector);
        $this->assertSame(null,                           $item->juristicPersonEmail);
        $this->assertSame(null,                           $item->juristicPersonWebsite);
        $this->assertSame('ИП Соколов Герман Маркович',   $item->soleProprietorName);
        $this->assertSame(null,                           $item->soleProprietorInn);
        $this->assertSame(null,                           $item->soleProprietorOgrnip);
        $this->assertSame(null,                           $item->soleProprietorOkpo);
        $this->assertSame(null,                           $item->soleProprietorOkved);
        $this->assertSame(null,                           $item->soleProprietorRegistrationAddress);
        $this->assertSame(null,                           $item->soleProprietorActualLocationAddress);
        $this->assertSame(null,                           $item->soleProprietorBankDetails);
        $this->assertSame(null,                           $item->soleProprietorPhone);
        $this->assertSame(null,                           $item->soleProprietorPhoneAdditional);
        $this->assertSame(null,                           $item->soleProprietorFax);
        $this->assertSame(null,                           $item->soleProprietorEmail);
        $this->assertSame(null,                           $item->soleProprietorWebsite);
    }

    private function assertItemEqualsJP004(OrganizationListItem $item): void
    {
        $this->assertSame('JP004',                            $item->id);
        $this->assertSame(JuristicPerson::CLASS_SHORTCUT,     $item->typeShortcut);
        $this->assertSame(JuristicPerson::CLASS_LABEL,        $item->typeLabel);
        $this->assertSame('МУП "Новосибирский метрополитен"', $item->juristicPersonName);
        $this->assertSame(null,                               $item->juristicPersonInn);
        $this->assertSame(null,                               $item->juristicPersonKpp);
        $this->assertSame(null,                               $item->juristicPersonOgrn);
        $this->assertSame(null,                               $item->juristicPersonOkpo);
        $this->assertSame(null,                               $item->juristicPersonOkved);
        $this->assertSame(null,                               $item->juristicPersonLegalAddress);
        $this->assertSame(null,                               $item->juristicPersonPostalAddress);
        $this->assertSame(null,                               $item->juristicPersonBankDetails);
        $this->assertSame(null,                               $item->juristicPersonPhone);
        $this->assertSame(null,                               $item->juristicPersonPhoneAdditional);
        $this->assertSame(null,                               $item->juristicPersonFax);
        $this->assertSame(null,                               $item->juristicPersonGeneralDirector);
        $this->assertSame(null,                               $item->juristicPersonEmail);
        $this->assertSame(null,                               $item->juristicPersonWebsite);
        $this->assertSame(null,                               $item->soleProprietorName);
        $this->assertSame(null,                               $item->soleProprietorInn);
        $this->assertSame(null,                               $item->soleProprietorOgrnip);
        $this->assertSame(null,                               $item->soleProprietorOkpo);
        $this->assertSame(null,                               $item->soleProprietorOkved);
        $this->assertSame(null,                               $item->soleProprietorRegistrationAddress);
        $this->assertSame(null,                               $item->soleProprietorActualLocationAddress);
        $this->assertSame(null,                               $item->soleProprietorBankDetails);
        $this->assertSame(null,                               $item->soleProprietorPhone);
        $this->assertSame(null,                               $item->soleProprietorPhoneAdditional);
        $this->assertSame(null,                               $item->soleProprietorFax);
        $this->assertSame(null,                               $item->soleProprietorEmail);
        $this->assertSame(null,                               $item->soleProprietorWebsite);
    }

    private function assertItemEqualsJP005(OrganizationListItem $item): void
    {
        $this->assertSame('JP005',                            $item->id);
        $this->assertSame(JuristicPerson::CLASS_SHORTCUT,     $item->typeShortcut);
        $this->assertSame(JuristicPerson::CLASS_LABEL,        $item->typeLabel);
        $this->assertSame('МУП Похоронный Дом "ИМИ"',         $item->juristicPersonName);
        $this->assertSame('5402103598',                       $item->juristicPersonInn);
        $this->assertSame('540201001',                        $item->juristicPersonKpp);
        $this->assertSame(null,                               $item->juristicPersonOgrn);
        $this->assertSame(null,                               $item->juristicPersonOkpo);
        $this->assertSame(null,                               $item->juristicPersonOkved);
        $this->assertSame(null,                               $item->juristicPersonLegalAddress);
        $this->assertSame(null,                               $item->juristicPersonPostalAddress);
        $this->assertSame(null,                               $item->juristicPersonBankDetails);
        $this->assertSame(null,                               $item->juristicPersonPhone);
        $this->assertSame(null,                               $item->juristicPersonPhoneAdditional);
        $this->assertSame(null,                               $item->juristicPersonFax);
        $this->assertSame('Бондаренко Сергей Валентинович',   $item->juristicPersonGeneralDirector);
        $this->assertSame(null,                               $item->juristicPersonEmail);
        $this->assertSame(null,                               $item->juristicPersonWebsite);
        $this->assertSame(null,                               $item->soleProprietorName);
        $this->assertSame(null,                               $item->soleProprietorInn);
        $this->assertSame(null,                               $item->soleProprietorOgrnip);
        $this->assertSame(null,                               $item->soleProprietorOkpo);
        $this->assertSame(null,                               $item->soleProprietorOkved);
        $this->assertSame(null,                               $item->soleProprietorRegistrationAddress);
        $this->assertSame(null,                               $item->soleProprietorActualLocationAddress);
        $this->assertSame(null,                               $item->soleProprietorBankDetails);
        $this->assertSame(null,                               $item->soleProprietorPhone);
        $this->assertSame(null,                               $item->soleProprietorPhoneAdditional);
        $this->assertSame(null,                               $item->soleProprietorFax);
        $this->assertSame(null,                               $item->soleProprietorEmail);
        $this->assertSame(null,                               $item->soleProprietorWebsite);
    }

    private function assertItemEqualsJP001(OrganizationListItem $item): void
    {
        $this->assertSame('JP001',                                       $item->id);
        $this->assertSame(JuristicPerson::CLASS_SHORTCUT,                $item->typeShortcut);
        $this->assertSame(JuristicPerson::CLASS_LABEL,                   $item->typeLabel);
        $this->assertSame('ООО "Рога и копыта"',                         $item->juristicPersonName);
        $this->assertSame(null,                                          $item->juristicPersonInn);
        $this->assertSame(null,                                          $item->juristicPersonKpp);
        $this->assertSame(null,                                          $item->juristicPersonOgrn);
        $this->assertSame(null,                                          $item->juristicPersonOkpo);
        $this->assertSame(null,                                          $item->juristicPersonOkved);
        $this->assertSame(null,                                          $item->juristicPersonLegalAddress);
        $this->assertSame('г. Кемерово, пр. Строителей, д. 5, офис 102', $item->juristicPersonPostalAddress);
        $this->assertSame(null,                                          $item->juristicPersonBankDetails);
        $this->assertSame(null,                                          $item->juristicPersonPhone);
        $this->assertSame(null,                                          $item->juristicPersonPhoneAdditional);
        $this->assertSame(null,                                          $item->juristicPersonFax);
        $this->assertSame(null,                                          $item->juristicPersonGeneralDirector);
        $this->assertSame(null,                                          $item->juristicPersonEmail);
        $this->assertSame(null,                                          $item->juristicPersonWebsite);
        $this->assertSame(null,                                          $item->soleProprietorName);
        $this->assertSame(null,                                          $item->soleProprietorInn);
        $this->assertSame(null,                                          $item->soleProprietorOgrnip);
        $this->assertSame(null,                                          $item->soleProprietorOkpo);
        $this->assertSame(null,                                          $item->soleProprietorOkved);
        $this->assertSame(null,                                          $item->soleProprietorRegistrationAddress);
        $this->assertSame(null,                                          $item->soleProprietorActualLocationAddress);
        $this->assertSame(null,                                          $item->soleProprietorBankDetails);
        $this->assertSame(null,                                          $item->soleProprietorPhone);
        $this->assertSame(null,                                          $item->soleProprietorPhoneAdditional);
        $this->assertSame(null,                                          $item->soleProprietorFax);
        $this->assertSame(null,                                          $item->soleProprietorEmail);
        $this->assertSame(null,                                          $item->soleProprietorWebsite);
    }

    private function assertItemEqualsJP002(OrganizationListItem $item): void
    {
        $this->assertSame('JP002',                        $item->id);
        $this->assertSame(JuristicPerson::CLASS_SHORTCUT, $item->typeShortcut);
        $this->assertSame(JuristicPerson::CLASS_LABEL,    $item->typeLabel);
        $this->assertSame('ООО Ромашка',                  $item->juristicPersonName);
        $this->assertSame('5404447629',                   $item->juristicPersonInn);
        $this->assertSame(null,                           $item->juristicPersonKpp);
        $this->assertSame(null,                           $item->juristicPersonOgrn);
        $this->assertSame(null,                           $item->juristicPersonOkpo);
        $this->assertSame(null,                           $item->juristicPersonOkved);
        $this->assertSame(null,                           $item->juristicPersonLegalAddress);
        $this->assertSame(null,                           $item->juristicPersonPostalAddress);
        $this->assertSame(
            'ОТДЕЛЕНИЕ ЛЕНИНГРАДСКОЕ БАНКА РОССИИ, р/счёт 40601810900001000022, БИК 044106001',
            $item->juristicPersonBankDetails
        );
        $this->assertSame(null,                           $item->juristicPersonPhone);
        $this->assertSame(null,                           $item->juristicPersonPhoneAdditional);
        $this->assertSame(null,                           $item->juristicPersonFax);
        $this->assertSame(null,                           $item->juristicPersonGeneralDirector);
        $this->assertSame(null,                           $item->juristicPersonEmail);
        $this->assertSame(null,                           $item->juristicPersonWebsite);
        $this->assertSame(null,                           $item->soleProprietorName);
        $this->assertSame(null,                           $item->soleProprietorInn);
        $this->assertSame(null,                           $item->soleProprietorOgrnip);
        $this->assertSame(null,                           $item->soleProprietorOkpo);
        $this->assertSame(null,                           $item->soleProprietorOkved);
        $this->assertSame(null,                           $item->soleProprietorRegistrationAddress);
        $this->assertSame(null,                           $item->soleProprietorActualLocationAddress);
        $this->assertSame(null,                           $item->soleProprietorBankDetails);
        $this->assertSame(null,                           $item->soleProprietorPhone);
        $this->assertSame(null,                           $item->soleProprietorPhoneAdditional);
        $this->assertSame(null,                           $item->soleProprietorFax);
        $this->assertSame(null,                           $item->soleProprietorEmail);
        $this->assertSame(null,                           $item->soleProprietorWebsite);
    }

    private function assertItemEqualsJP003(OrganizationListItem $item): void
    {
        $this->assertSame('JP003',                        $item->id);
        $this->assertSame(JuristicPerson::CLASS_SHORTCUT, $item->typeShortcut);
        $this->assertSame(JuristicPerson::CLASS_LABEL,    $item->typeLabel);
        $this->assertSame('ПАО "ГАЗПРОМ"',                $item->juristicPersonName);
        $this->assertSame('7736050003',                   $item->juristicPersonInn);
        $this->assertSame(null,                           $item->juristicPersonKpp);
        $this->assertSame(null,                           $item->juristicPersonOgrn);
        $this->assertSame(null,                           $item->juristicPersonOkpo);
        $this->assertSame(null,                           $item->juristicPersonOkved);
        $this->assertSame(null,                           $item->juristicPersonLegalAddress);
        $this->assertSame(null,                           $item->juristicPersonPostalAddress);
        $this->assertSame(null,                           $item->juristicPersonBankDetails);
        $this->assertSame(null,                           $item->juristicPersonPhone);
        $this->assertSame(null,                           $item->juristicPersonPhoneAdditional);
        $this->assertSame(null,                           $item->juristicPersonFax);
        $this->assertSame(null,                           $item->juristicPersonGeneralDirector);
        $this->assertSame(null,                           $item->juristicPersonEmail);
        $this->assertSame(null,                           $item->juristicPersonWebsite);
        $this->assertSame(null,                           $item->soleProprietorName);
        $this->assertSame(null,                           $item->soleProprietorInn);
        $this->assertSame(null,                           $item->soleProprietorOgrnip);
        $this->assertSame(null,                           $item->soleProprietorOkpo);
        $this->assertSame(null,                           $item->soleProprietorOkved);
        $this->assertSame(null,                           $item->soleProprietorRegistrationAddress);
        $this->assertSame(null,                           $item->soleProprietorActualLocationAddress);
        $this->assertSame(null,                           $item->soleProprietorBankDetails);
        $this->assertSame(null,                           $item->soleProprietorPhone);
        $this->assertSame(null,                           $item->soleProprietorPhoneAdditional);
        $this->assertSame(null,                           $item->soleProprietorFax);
        $this->assertSame(null,                           $item->soleProprietorEmail);
        $this->assertSame(null,                           $item->soleProprietorWebsite);
    }

    private function expectExceptionForNotFoundOrganizationById(string $funeralCompanyId): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(\sprintf('Похоронная фирма с ID "%s" не найдена.', $funeralCompanyId));
    }
}
