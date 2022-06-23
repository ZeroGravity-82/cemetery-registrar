<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Fetcher;

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

        $this->juristicPersonRepo  = new DoctrineOrmJuristicPersonRepository($this->entityManager);
        $this->soleProprietorRepo  = new DoctrineOrmSoleProprietorRepository($this->entityManager);
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
        return;

//        $this->testItReturnsOrganizationViewForFC001();
//        $this->testItReturnsOrganizationViewForFC002();
//        $this->testItReturnsOrganizationViewForFC003();
//        $this->testItReturnsOrganizationViewForFC004();
    }

    public function testItFailsToReturnOrganizationViewByUnknownIdForJuristicPerson(): void
    {
        $this->markTestIncomplete();
        return;

        $this->expectExceptionForNotFoundOrganizationById('unknown_id', JuristicPerson::CLASS_SHORTCUT);
        $this->funeralCompanyFetcher->getViewById('unknown_id', JuristicPerson::CLASS_LABEL);
    }

    public function testItFailsToReturnOrganizationViewByUnknownIdForSoleProprietor(): void
    {
        $this->markTestIncomplete();
        return;

        $this->expectExceptionForNotFoundOrganizationById('unknown_id', SoleProprietor::CLASS_SHORTCUT);
        $this->funeralCompanyFetcher->getViewById('unknown_id', SoleProprietor::CLASS_LABEL);
    }

    public function testItFailsToReturnOrganizationViewForRemovedJuristicPerson(): void
    {
        $this->markTestIncomplete();
        return;

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
        $this->markTestIncomplete();
        return;

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
        $this->assertIsArray($listForFirstPage->listItems);
        $this->assertContainsOnlyInstancesOf(OrganizationListItem::class, $listForFirstPage->listItems);
        $this->assertCount(4,              $listForFirstPage->listItems);
        $this->assertSame(1,               $listForFirstPage->page);
        $this->assertSame($customPageSize, $listForFirstPage->pageSize);
        $this->assertSame(null,            $listForFirstPage->term);
        $this->assertSame(9,               $listForFirstPage->totalCount);
        $this->assertSame(3,               $listForFirstPage->totalPages);
        $this->assertListItemEqualsSP001($listForFirstPage->listItems[0]);  // Items are ordered by name
        $this->assertListItemEqualsSP002($listForFirstPage->listItems[1]);
        $this->assertListItemEqualsSP003($listForFirstPage->listItems[2]);
        $this->assertListItemEqualsSP004($listForFirstPage->listItems[3]);

        // Second page
        $listForSecondPage = $this->organizationFetcher->findAll(2, null, $customPageSize);
        $this->assertInstanceOf(OrganizationList::class, $listForSecondPage);
        $this->assertIsArray($listForSecondPage->listItems);
        $this->assertContainsOnlyInstancesOf(OrganizationListItem::class, $listForSecondPage->listItems);
        $this->assertCount(4,              $listForSecondPage->listItems);
        $this->assertSame(2,               $listForSecondPage->page);
        $this->assertSame($customPageSize, $listForSecondPage->pageSize);
        $this->assertSame(null,            $listForSecondPage->term);
        $this->assertSame(9,               $listForSecondPage->totalCount);
        $this->assertSame(3,               $listForSecondPage->totalPages);
        $this->assertListItemEqualsJP004($listForSecondPage->listItems[0]);
        $this->assertListItemEqualsJP005($listForSecondPage->listItems[1]);
        $this->assertListItemEqualsJP001($listForSecondPage->listItems[2]);
        $this->assertListItemEqualsJP002($listForSecondPage->listItems[3]);

        // Third page
        $listForThirdPage = $this->organizationFetcher->findAll(3, null, $customPageSize);
        $this->assertInstanceOf(OrganizationList::class, $listForThirdPage);
        $this->assertIsArray($listForThirdPage->listItems);
        $this->assertContainsOnlyInstancesOf(OrganizationListItem::class, $listForThirdPage->listItems);
        $this->assertCount(1,              $listForThirdPage->listItems);
        $this->assertSame(3,               $listForThirdPage->page);
        $this->assertSame($customPageSize, $listForThirdPage->pageSize);
        $this->assertSame(null,            $listForThirdPage->term);
        $this->assertSame(9,               $listForThirdPage->totalCount);
        $this->assertSame(3,               $listForThirdPage->totalPages);
        $this->assertListItemEqualsJP003($listForThirdPage->listItems[0]);

        // Fourth page
        $listForFourthPage = $this->organizationFetcher->findAll(4, null, $customPageSize);
        $this->assertInstanceOf(OrganizationList::class, $listForFourthPage);
        $this->assertIsArray($listForFourthPage->listItems);
        $this->assertCount(0,              $listForFourthPage->listItems);
        $this->assertSame(4,               $listForFourthPage->page);
        $this->assertSame($customPageSize, $listForFourthPage->pageSize);
        $this->assertSame(null,            $listForFourthPage->term);
        $this->assertSame(9,               $listForFourthPage->totalCount);
        $this->assertSame(3,               $listForFourthPage->totalPages);

        // Default page size
        $listForDefaultPageSize = $this->organizationFetcher->findAll(1);
        $this->assertInstanceOf(OrganizationList::class, $listForDefaultPageSize);
        $this->assertIsArray($listForDefaultPageSize->listItems);
        $this->assertContainsOnlyInstancesOf(OrganizationListItem::class, $listForDefaultPageSize->listItems);
        $this->assertCount(9,                      $listForDefaultPageSize->listItems);
        $this->assertSame(1,                       $listForDefaultPageSize->page);
        $this->assertSame(self::DEFAULT_PAGE_SIZE, $listForDefaultPageSize->pageSize);
        $this->assertSame(null,                    $listForDefaultPageSize->term);
        $this->assertSame(9,                       $listForDefaultPageSize->totalCount);
        $this->assertSame(1,                       $listForDefaultPageSize->totalPages);
    }

    public function testItReturnsOrganizationListItemsByPageAndTerm(): void
    {
        $customPageSize = 3;

        $list = $this->organizationFetcher->findAll(1, 'АльФа', $customPageSize);
        $this->assertInstanceOf(OrganizationList::class, $list);
        $this->assertIsArray($list->listItems);
        $this->assertContainsOnlyInstancesOf(OrganizationListItem::class, $list->listItems);
        $this->assertCount(1,              $list->listItems);
        $this->assertSame(1,               $list->page);
        $this->assertSame($customPageSize, $list->pageSize);
        $this->assertSame('АльФа',         $list->term);
        $this->assertSame(1,               $list->totalCount);
        $this->assertSame(1,               $list->totalPages);

        $list = $this->organizationFetcher->findAll(1, '44', $customPageSize);
        $this->assertInstanceOf(OrganizationList::class, $list);
        $this->assertIsArray($list->listItems);
        $this->assertContainsOnlyInstancesOf(OrganizationListItem::class, $list->listItems);
        $this->assertCount(2,              $list->listItems);
        $this->assertSame(1,               $list->page);
        $this->assertSame($customPageSize, $list->pageSize);
        $this->assertSame('44',            $list->term);
        $this->assertSame(2,               $list->totalCount);
        $this->assertSame(1,               $list->totalPages);

        $list = $this->organizationFetcher->findAll(1, 'Кемеров', $customPageSize);
        $this->assertInstanceOf(OrganizationList::class, $list);
        $this->assertIsArray($list->listItems);
        $this->assertContainsOnlyInstancesOf(OrganizationListItem::class, $list->listItems);
        $this->assertCount(1,              $list->listItems);
        $this->assertSame(1,               $list->page);
        $this->assertSame($customPageSize, $list->pageSize);
        $this->assertSame('Кемеров',       $list->term);
        $this->assertSame(1,               $list->totalCount);
        $this->assertSame(1,               $list->totalPages);

        $list = $this->organizationFetcher->findAll(1, 'ро', $customPageSize);
        $this->assertInstanceOf(OrganizationList::class, $list);
        $this->assertIsArray($list->listItems);
        $this->assertContainsOnlyInstancesOf(OrganizationListItem::class, $list->listItems);
        $this->assertCount(3,              $list->listItems);
        $this->assertSame(1,               $list->page);
        $this->assertSame($customPageSize, $list->pageSize);
        $this->assertSame('ро',            $list->term);
        $this->assertSame(7,               $list->totalCount);
        $this->assertSame(3,               $list->totalPages);
        $list = $this->organizationFetcher->findAll(2, 'ро', $customPageSize);
        $this->assertInstanceOf(OrganizationList::class, $list);
        $this->assertIsArray($list->listItems);
        $this->assertContainsOnlyInstancesOf(OrganizationListItem::class, $list->listItems);
        $this->assertCount(3,              $list->listItems);
        $this->assertSame(2,               $list->page);
        $this->assertSame($customPageSize, $list->pageSize);
        $this->assertSame('ро',            $list->term);
        $this->assertSame(7,               $list->totalCount);
        $this->assertSame(3,               $list->totalPages);
        $list = $this->organizationFetcher->findAll(3, 'ро', $customPageSize);
        $this->assertInstanceOf(OrganizationList::class, $list);
        $this->assertIsArray($list->listItems);
        $this->assertContainsOnlyInstancesOf(OrganizationListItem::class, $list->listItems);
        $this->assertCount(1,              $list->listItems);
        $this->assertSame(3,               $list->page);
        $this->assertSame($customPageSize, $list->pageSize);
        $this->assertSame('ро',            $list->term);
        $this->assertSame(7,               $list->totalCount);
        $this->assertSame(3,               $list->totalPages);

        $list = $this->organizationFetcher->findAll(1, '133', $customPageSize);
        $this->assertInstanceOf(OrganizationList::class, $list);
        $this->assertIsArray($list->listItems);
        $this->assertContainsOnlyInstancesOf(OrganizationListItem::class, $list->listItems);
        $this->assertCount(1,              $list->listItems);
        $this->assertSame(1,               $list->page);
        $this->assertSame($customPageSize, $list->pageSize);
        $this->assertSame('133',           $list->term);
        $this->assertSame(1,               $list->totalCount);
        $this->assertSame(1,               $list->totalPages);

        $list = $this->organizationFetcher->findAll(1, 'юрлиц', $customPageSize);
        $this->assertInstanceOf(OrganizationList::class, $list);
        $this->assertIsArray($list->listItems);
        $this->assertContainsOnlyInstancesOf(OrganizationListItem::class, $list->listItems);
        $this->assertCount(3,              $list->listItems);
        $this->assertSame(1,               $list->page);
        $this->assertSame($customPageSize, $list->pageSize);
        $this->assertSame('юрлиц',         $list->term);
        $this->assertSame(5,               $list->totalCount);
        $this->assertSame(2,               $list->totalPages);
        $list = $this->organizationFetcher->findAll(2, 'юрлиц', $customPageSize);
        $this->assertInstanceOf(OrganizationList::class, $list);
        $this->assertIsArray($list->listItems);
        $this->assertContainsOnlyInstancesOf(OrganizationListItem::class, $list->listItems);
        $this->assertCount(2,              $list->listItems);
        $this->assertSame(2,               $list->page);
        $this->assertSame($customPageSize, $list->pageSize);
        $this->assertSame('юрлиц',         $list->term);
        $this->assertSame(5,               $list->totalCount);
        $this->assertSame(2,               $list->totalPages);

        $list = $this->organizationFetcher->findAll(1, 'ВАЛЕНТИН', $customPageSize);
        $this->assertInstanceOf(OrganizationList::class, $list);
        $this->assertIsArray($list->listItems);
        $this->assertContainsOnlyInstancesOf(OrganizationListItem::class, $list->listItems);
        $this->assertCount(1,              $list->listItems);
        $this->assertSame(1,               $list->page);
        $this->assertSame($customPageSize, $list->pageSize);
        $this->assertSame('ВАЛЕНТИН',      $list->term);
        $this->assertSame(1,               $list->totalCount);
        $this->assertSame(1,               $list->totalPages);

        $list = $this->organizationFetcher->findAll(1, '54044476', $customPageSize);
        $this->assertInstanceOf(OrganizationList::class, $list);
        $this->assertIsArray($list->listItems);
        $this->assertContainsOnlyInstancesOf(OrganizationListItem::class, $list->listItems);
        $this->assertCount(1,              $list->listItems);
        $this->assertSame(1,               $list->page);
        $this->assertSame($customPageSize, $list->pageSize);
        $this->assertSame('54044476',      $list->term);
        $this->assertSame(1,               $list->totalCount);
        $this->assertSame(1,               $list->totalPages);

        $list = $this->organizationFetcher->findAll(1, '540201001', $customPageSize);
        $this->assertInstanceOf(OrganizationList::class, $list);
        $this->assertIsArray($list->listItems);
        $this->assertContainsOnlyInstancesOf(OrganizationListItem::class, $list->listItems);
        $this->assertCount(1,              $list->listItems);
        $this->assertSame(1,               $list->page);
        $this->assertSame($customPageSize, $list->pageSize);
        $this->assertSame('540201001',     $list->term);
        $this->assertSame(1,               $list->totalCount);
        $this->assertSame(1,               $list->totalPages);

        $list = $this->organizationFetcher->findAll(1, '044525593', $customPageSize);
        $this->assertInstanceOf(OrganizationList::class, $list);
        $this->assertIsArray($list->listItems);
        $this->assertContainsOnlyInstancesOf(OrganizationListItem::class, $list->listItems);
        $this->assertCount(1,              $list->listItems);
        $this->assertSame(1,               $list->page);
        $this->assertSame($customPageSize, $list->pageSize);
        $this->assertSame('044525593',     $list->term);
        $this->assertSame(1,               $list->totalCount);
        $this->assertSame(1,               $list->totalPages);

        $list = $this->organizationFetcher->findAll(1, 'nfo@funeral54.r', $customPageSize);
        $this->assertInstanceOf(OrganizationList::class, $list);
        $this->assertIsArray($list->listItems);
        $this->assertContainsOnlyInstancesOf(OrganizationListItem::class, $list->listItems);
        $this->assertCount(1,                $list->listItems);
        $this->assertSame(1,                 $list->page);
        $this->assertSame($customPageSize,   $list->pageSize);
        $this->assertSame('nfo@funeral54.r', $list->term);
        $this->assertSame(1,                 $list->totalCount);
        $this->assertSame(1,                 $list->totalPages);
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

    private function assertListItemEqualsJP001(OrganizationListItem $listItem): void
    {
        $this->assertSame('JP001',                                       $listItem->id);
        $this->assertSame(JuristicPerson::CLASS_SHORTCUT,                $listItem->typeShortcut);
        $this->assertSame(JuristicPerson::CLASS_LABEL,                   $listItem->typeLabel);
        $this->assertSame('ООО "Рога и копыта"',                         $listItem->name);
        $this->assertSame(null,                                          $listItem->innKpp);
        $this->assertSame(null,                                          $listItem->ogrn);
        $this->assertSame(null,                                          $listItem->okpo);
        $this->assertSame(null,                                          $listItem->okved);
        $this->assertSame('г. Кемерово, пр. Строителей, д. 5, офис 102', $listItem->address);
        $this->assertSame(null,                                          $listItem->bankDetails);
        $this->assertSame(null,                                          $listItem->phone);
        $this->assertSame(null,                                          $listItem->generalDirector);
        $this->assertSame(null,                                          $listItem->emailWebsite);
    }

    private function assertListItemEqualsJP002(OrganizationListItem $listItem): void
    {
        $this->assertSame('JP002',                        $listItem->id);
        $this->assertSame(JuristicPerson::CLASS_SHORTCUT, $listItem->typeShortcut);
        $this->assertSame(JuristicPerson::CLASS_LABEL,    $listItem->typeLabel);
        $this->assertSame('ООО Ромашка',                  $listItem->name);
        $this->assertSame('5404447629/-',                 $listItem->innKpp);
        $this->assertSame(null,                           $listItem->ogrn);
        $this->assertSame(null,                           $listItem->okpo);
        $this->assertSame(null,                           $listItem->okved);
        $this->assertSame(null,                           $listItem->address);
        $this->assertSame(
            'ОТДЕЛЕНИЕ ЛЕНИНГРАДСКОЕ БАНКА РОССИИ, р/счёт 40601810900001000022, БИК 044106001',
            $listItem->bankDetails
        );
        $this->assertSame(null,                           $listItem->phone);
        $this->assertSame(null,                           $listItem->generalDirector);
        $this->assertSame(null,                           $listItem->emailWebsite);
    }

    private function assertListItemEqualsJP003(OrganizationListItem $listItem): void
    {
        $this->assertSame('JP003',                        $listItem->id);
        $this->assertSame(JuristicPerson::CLASS_SHORTCUT, $listItem->typeShortcut);
        $this->assertSame(JuristicPerson::CLASS_LABEL,    $listItem->typeLabel);
        $this->assertSame('ПАО "ГАЗПРОМ"',                $listItem->name);
        $this->assertSame('7736050003/-',                 $listItem->innKpp);
        $this->assertSame(null,                           $listItem->ogrn);
        $this->assertSame(null,                           $listItem->okpo);
        $this->assertSame(null,                           $listItem->okved);
        $this->assertSame(null,                           $listItem->address);
        $this->assertSame(null,                           $listItem->bankDetails);
        $this->assertSame(null,                           $listItem->phone);
        $this->assertSame(null,                           $listItem->generalDirector);
        $this->assertSame(null,                           $listItem->emailWebsite);
    }

    private function assertListItemEqualsJP004(OrganizationListItem $listItem): void
    {
        $this->assertSame('JP004',                            $listItem->id);
        $this->assertSame(JuristicPerson::CLASS_SHORTCUT,     $listItem->typeShortcut);
        $this->assertSame(JuristicPerson::CLASS_LABEL,        $listItem->typeLabel);
        $this->assertSame('МУП "Новосибирский метрополитен"', $listItem->name);
        $this->assertSame(null,                               $listItem->innKpp);
        $this->assertSame(null,                               $listItem->ogrn);
        $this->assertSame(null,                               $listItem->okpo);
        $this->assertSame(null,                               $listItem->okved);
        $this->assertSame(null,                               $listItem->address);
        $this->assertSame(null,                               $listItem->bankDetails);
        $this->assertSame(null,                               $listItem->phone);
        $this->assertSame(null,                               $listItem->generalDirector);
        $this->assertSame(null,                               $listItem->emailWebsite);
    }

    private function assertListItemEqualsJP005(OrganizationListItem $listItem): void
    {
        $this->assertSame('JP005',                            $listItem->id);
        $this->assertSame(JuristicPerson::CLASS_SHORTCUT,     $listItem->typeShortcut);
        $this->assertSame(JuristicPerson::CLASS_LABEL,        $listItem->typeLabel);
        $this->assertSame('МУП Похоронный Дом "ИМИ"',         $listItem->name);
        $this->assertSame('5402103598/540201001',             $listItem->innKpp);
        $this->assertSame(null,                               $listItem->ogrn);
        $this->assertSame(null,                               $listItem->okpo);
        $this->assertSame(null,                               $listItem->okved);
        $this->assertSame(null,                               $listItem->address);
        $this->assertSame(null,                               $listItem->bankDetails);
        $this->assertSame(null,                               $listItem->phone);
        $this->assertSame('Бондаренко Сергей Валентинович',   $listItem->generalDirector);
        $this->assertSame(null,                               $listItem->emailWebsite);
    }

    private function assertListItemEqualsSP001(OrganizationListItem $listItem): void
    {
        $this->assertSame('SP001',                        $listItem->id);
        $this->assertSame(SoleProprietor::CLASS_SHORTCUT, $listItem->typeShortcut);
        $this->assertSame(SoleProprietor::CLASS_LABEL,    $listItem->typeLabel);
        $this->assertSame('ИП Иванов Иван Иванович',      $listItem->name);
        $this->assertSame(null,                           $listItem->innKpp);
        $this->assertSame(null,                           $listItem->ogrn);
        $this->assertSame(null,                           $listItem->okpo);
        $this->assertSame(null,                           $listItem->okved);
        $this->assertSame(null,                           $listItem->address);
        $this->assertSame(null,                           $listItem->bankDetails);
        $this->assertSame(null,                           $listItem->phone);
        $this->assertSame(null,                           $listItem->generalDirector);
        $this->assertSame(null,                           $listItem->emailWebsite);
    }

    private function assertListItemEqualsSP002(OrganizationListItem $listItem): void
    {
        $this->assertSame('SP002',                        $listItem->id);
        $this->assertSame(SoleProprietor::CLASS_SHORTCUT, $listItem->typeShortcut);
        $this->assertSame(SoleProprietor::CLASS_LABEL,    $listItem->typeLabel);
        $this->assertSame('ИП Петров Пётр Петрович',      $listItem->name);
        $this->assertSame('772208786091/-',               $listItem->innKpp);
        $this->assertSame(null,                           $listItem->ogrn);
        $this->assertSame(null,                           $listItem->okpo);
        $this->assertSame(null,                           $listItem->okved);
        $this->assertSame(null,                           $listItem->address);
        $this->assertSame(
            'АО "АЛЬФА-БАНК", р/счёт 40701810401400000014, к/счёт 30101810200000000593, БИК 044525593',
            $listItem->bankDetails
        );
        $this->assertSame(
            '8(383)133-22-33, 8(383)133-22-44, 8(383)133-22-55 (факс)',
            $listItem->phone
        );
        $this->assertSame(
            'info@funeral54.ru, funeral54.ru',
            $listItem->emailWebsite
        );
    }

    private function assertListItemEqualsSP003(OrganizationListItem $listItem): void
    {
        $this->assertSame('SP003',                        $listItem->id);
        $this->assertSame(SoleProprietor::CLASS_SHORTCUT, $listItem->typeShortcut);
        $this->assertSame(SoleProprietor::CLASS_LABEL,    $listItem->typeLabel);
        $this->assertSame('ИП Сидоров Сидр Сидорович',    $listItem->name);
        $this->assertSame('391600743661/-',               $listItem->innKpp);
        $this->assertSame(null,                           $listItem->ogrn);
        $this->assertSame(null,                           $listItem->okpo);
        $this->assertSame(null,                           $listItem->okved);
        $this->assertSame('с. Каменка, д. 14',            $listItem->address);
        $this->assertSame(null,                           $listItem->bankDetails);
        $this->assertSame('8(383)147-22-33',              $listItem->phone);
        $this->assertSame(null,                           $listItem->generalDirector);
        $this->assertSame(null,                           $listItem->emailWebsite);
    }

    private function assertListItemEqualsSP004(OrganizationListItem $listItem): void
    {
        $this->assertSame('SP004',                        $listItem->id);
        $this->assertSame(SoleProprietor::CLASS_SHORTCUT, $listItem->typeShortcut);
        $this->assertSame(SoleProprietor::CLASS_LABEL,    $listItem->typeLabel);
        $this->assertSame('ИП Соколов Герман Маркович',   $listItem->name);
        $this->assertSame(null,                           $listItem->innKpp);
        $this->assertSame(null,                           $listItem->ogrn);
        $this->assertSame(null,                           $listItem->okpo);
        $this->assertSame(null,                           $listItem->okved);
        $this->assertSame(null,                           $listItem->address);
        $this->assertSame(null,                           $listItem->bankDetails);
        $this->assertSame(null,                           $listItem->phone);
        $this->assertSame(null,                           $listItem->generalDirector);
        $this->assertSame(null,                           $listItem->emailWebsite);
    }

    private function expectExceptionForNotFoundOrganizationById(string $organizationId, string $organizationType): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(\sprintf(
            'Организация с ID "%s" и типом "%s" не найдена.',
            $organizationId,
            $organizationType,
        ));
    }
}
