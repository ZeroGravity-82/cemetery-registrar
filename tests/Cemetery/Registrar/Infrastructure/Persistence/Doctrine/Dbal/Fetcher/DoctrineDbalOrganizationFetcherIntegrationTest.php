<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Fetcher;

use Cemetery\Registrar\Domain\Model\Organization\JuristicPerson\JuristicPerson;
use Cemetery\Registrar\Domain\Model\Organization\JuristicPerson\JuristicPersonId;
use Cemetery\Registrar\Domain\Model\Organization\JuristicPerson\JuristicPersonRepositoryInterface;
use Cemetery\Registrar\Domain\Model\Organization\SoleProprietor\SoleProprietor;
use Cemetery\Registrar\Domain\Model\Organization\SoleProprietor\SoleProprietorId;
use Cemetery\Registrar\Domain\Model\Organization\SoleProprietor\SoleProprietorRepositoryInterface;
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
class DoctrineDbalOrganizationFetcherIntegrationTest extends AbstractDoctrineDbalFetcherIntegrationTest
{
    private JuristicPersonRepositoryInterface $juristicPersonRepo;
    private SoleProprietorRepositoryInterface $soleProprietorRepo;

    public function setUp(): void
    {
        parent::setUp();

        $this->juristicPersonRepo = new DoctrineOrmJuristicPersonRepository($this->entityManager);
        $this->soleProprietorRepo = new DoctrineOrmSoleProprietorRepository($this->entityManager);
        $this->fetcher            = new DoctrineDbalOrganizationFetcher($this->connection);
        $this->loadFixtures();
    }

    public function testItReturnsViewById(): void
    {
        // Organization fetcher's findViewById() method must always return null because there is no such entity
        // as an organization.
        $view = $this->fetcher->findViewById('JP001');
        $this->assertNull($view);

        $view = $this->fetcher->findViewById('SP001');
        $this->assertNull($view);

        $view = $this->fetcher->findViewById('unknown_id');
        $this->assertNull($view);
    }

    public function testItChecksExistenceById(): void
    {
        // Organization fetcher's doesExistById() method must always return false because there is no such entity
        // as an organization.
        $this->assertFalse($this->fetcher->doesExistById('JP001'));
        $this->assertFalse($this->fetcher->doesExistById('SP001'));
        $this->assertFalse($this->fetcher->doesExistById('unknown_id'));
    }

    public function testItReturnsOrganizationPaginatedListByPage(): void
    {
        $customPageSize = 4;

        // First page
        $listForFirstPage = $this->fetcher->paginate(1, null, $customPageSize);
        $this->assertInstanceOf(OrganizationList::class, $listForFirstPage);
        $this->assertIsArray($listForFirstPage->items);
        $this->assertContainsOnlyInstancesOf(OrganizationListItem::class, $listForFirstPage->items);
        $this->assertCount(4,              $listForFirstPage->items);
        $this->assertSame(1,               $listForFirstPage->page);
        $this->assertSame($customPageSize, $listForFirstPage->pageSize);
        $this->assertSame(null,            $listForFirstPage->term);
        $this->assertSame(9,               $listForFirstPage->totalCount);
        $this->assertSame(3,               $listForFirstPage->totalPages);
        $this->assertPaginatedListItemEqualsSP001($listForFirstPage->items[0]);  // Items are ordered by name
        $this->assertPaginatedListItemEqualsSP002($listForFirstPage->items[1]);
        $this->assertPaginatedListItemEqualsSP003($listForFirstPage->items[2]);
        $this->assertPaginatedListItemEqualsSP004($listForFirstPage->items[3]);

        // Second page
        $listForSecondPage = $this->fetcher->paginate(2, null, $customPageSize);
        $this->assertInstanceOf(OrganizationList::class, $listForSecondPage);
        $this->assertIsArray($listForSecondPage->items);
        $this->assertContainsOnlyInstancesOf(OrganizationListItem::class, $listForSecondPage->items);
        $this->assertCount(4,              $listForSecondPage->items);
        $this->assertSame(2,               $listForSecondPage->page);
        $this->assertSame($customPageSize, $listForSecondPage->pageSize);
        $this->assertSame(null,            $listForSecondPage->term);
        $this->assertSame(9,               $listForSecondPage->totalCount);
        $this->assertSame(3,               $listForSecondPage->totalPages);
        $this->assertPaginatedListItemEqualsJP004($listForSecondPage->items[0]);
        $this->assertPaginatedListItemEqualsJP005($listForSecondPage->items[1]);
        $this->assertPaginatedListItemEqualsJP001($listForSecondPage->items[2]);
        $this->assertPaginatedListItemEqualsJP002($listForSecondPage->items[3]);

        // Third page
        $listForThirdPage = $this->fetcher->paginate(3, null, $customPageSize);
        $this->assertInstanceOf(OrganizationList::class, $listForThirdPage);
        $this->assertIsArray($listForThirdPage->items);
        $this->assertContainsOnlyInstancesOf(OrganizationListItem::class, $listForThirdPage->items);
        $this->assertCount(1,              $listForThirdPage->items);
        $this->assertSame(3,               $listForThirdPage->page);
        $this->assertSame($customPageSize, $listForThirdPage->pageSize);
        $this->assertSame(null,            $listForThirdPage->term);
        $this->assertSame(9,               $listForThirdPage->totalCount);
        $this->assertSame(3,               $listForThirdPage->totalPages);
        $this->assertPaginatedListItemEqualsJP003($listForThirdPage->items[0]);

        // Fourth page
        $listForFourthPage = $this->fetcher->paginate(4, null, $customPageSize);
        $this->assertInstanceOf(OrganizationList::class, $listForFourthPage);
        $this->assertIsArray($listForFourthPage->items);
        $this->assertCount(0,              $listForFourthPage->items);
        $this->assertSame(4,               $listForFourthPage->page);
        $this->assertSame($customPageSize, $listForFourthPage->pageSize);
        $this->assertSame(null,            $listForFourthPage->term);
        $this->assertSame(9,               $listForFourthPage->totalCount);
        $this->assertSame(3,               $listForFourthPage->totalPages);

        // Default page size
        $listForDefaultPageSize = $this->fetcher->paginate(1);
        $this->assertInstanceOf(OrganizationList::class, $listForDefaultPageSize);
        $this->assertIsArray($listForDefaultPageSize->items);
        $this->assertContainsOnlyInstancesOf(OrganizationListItem::class, $listForDefaultPageSize->items);
        $this->assertCount(9,                      $listForDefaultPageSize->items);
        $this->assertSame(1,                       $listForDefaultPageSize->page);
        $this->assertSame(self::DEFAULT_PAGE_SIZE, $listForDefaultPageSize->pageSize);
        $this->assertSame(null,                    $listForDefaultPageSize->term);
        $this->assertSame(9,                       $listForDefaultPageSize->totalCount);
        $this->assertSame(1,                       $listForDefaultPageSize->totalPages);
    }

    public function testItReturnsOrganizationPaginatedListByPageAndTerm(): void
    {
        $customPageSize = 3;

        $list = $this->fetcher->paginate(1, 'АльФа', $customPageSize);
        $this->assertInstanceOf(OrganizationList::class, $list);
        $this->assertIsArray($list->items);
        $this->assertContainsOnlyInstancesOf(OrganizationListItem::class, $list->items);
        $this->assertCount(1,              $list->items);
        $this->assertSame(1,               $list->page);
        $this->assertSame($customPageSize, $list->pageSize);
        $this->assertSame('АльФа',         $list->term);
        $this->assertSame(1,               $list->totalCount);
        $this->assertSame(1,               $list->totalPages);

        $list = $this->fetcher->paginate(1, '44', $customPageSize);
        $this->assertInstanceOf(OrganizationList::class, $list);
        $this->assertIsArray($list->items);
        $this->assertContainsOnlyInstancesOf(OrganizationListItem::class, $list->items);
        $this->assertCount(2,              $list->items);
        $this->assertSame(1,               $list->page);
        $this->assertSame($customPageSize, $list->pageSize);
        $this->assertSame('44',            $list->term);
        $this->assertSame(2,               $list->totalCount);
        $this->assertSame(1,               $list->totalPages);

        $list = $this->fetcher->paginate(1, 'кемЕРов', $customPageSize);
        $this->assertInstanceOf(OrganizationList::class, $list);
        $this->assertIsArray($list->items);
        $this->assertContainsOnlyInstancesOf(OrganizationListItem::class, $list->items);
        $this->assertCount(1,              $list->items);
        $this->assertSame(1,               $list->page);
        $this->assertSame($customPageSize, $list->pageSize);
        $this->assertSame('кемЕРов',       $list->term);
        $this->assertSame(1,               $list->totalCount);
        $this->assertSame(1,               $list->totalPages);

        $list = $this->fetcher->paginate(1, 'Ро', $customPageSize);
        $this->assertInstanceOf(OrganizationList::class, $list);
        $this->assertIsArray($list->items);
        $this->assertContainsOnlyInstancesOf(OrganizationListItem::class, $list->items);
        $this->assertCount(3,              $list->items);
        $this->assertSame(1,               $list->page);
        $this->assertSame($customPageSize, $list->pageSize);
        $this->assertSame('Ро',            $list->term);
        $this->assertSame(7,               $list->totalCount);
        $this->assertSame(3,               $list->totalPages);
        $list = $this->fetcher->paginate(2, 'Ро', $customPageSize);
        $this->assertInstanceOf(OrganizationList::class, $list);
        $this->assertIsArray($list->items);
        $this->assertContainsOnlyInstancesOf(OrganizationListItem::class, $list->items);
        $this->assertCount(3,              $list->items);
        $this->assertSame(2,               $list->page);
        $this->assertSame($customPageSize, $list->pageSize);
        $this->assertSame('Ро',            $list->term);
        $this->assertSame(7,               $list->totalCount);
        $this->assertSame(3,               $list->totalPages);
        $list = $this->fetcher->paginate(3, 'Ро', $customPageSize);
        $this->assertInstanceOf(OrganizationList::class, $list);
        $this->assertIsArray($list->items);
        $this->assertContainsOnlyInstancesOf(OrganizationListItem::class, $list->items);
        $this->assertCount(1,              $list->items);
        $this->assertSame(3,               $list->page);
        $this->assertSame($customPageSize, $list->pageSize);
        $this->assertSame('Ро',            $list->term);
        $this->assertSame(7,               $list->totalCount);
        $this->assertSame(3,               $list->totalPages);

        $list = $this->fetcher->paginate(1, '133', $customPageSize);
        $this->assertInstanceOf(OrganizationList::class, $list);
        $this->assertIsArray($list->items);
        $this->assertContainsOnlyInstancesOf(OrganizationListItem::class, $list->items);
        $this->assertCount(1,              $list->items);
        $this->assertSame(1,               $list->page);
        $this->assertSame($customPageSize, $list->pageSize);
        $this->assertSame('133',           $list->term);
        $this->assertSame(1,               $list->totalCount);
        $this->assertSame(1,               $list->totalPages);

        $list = $this->fetcher->paginate(1, '10', $customPageSize);
        $this->assertInstanceOf(OrganizationList::class, $list);
        $this->assertIsArray($list->items);
        $this->assertContainsOnlyInstancesOf(OrganizationListItem::class, $list->items);
        $this->assertCount(3,              $list->items);
        $this->assertSame(1,               $list->page);
        $this->assertSame($customPageSize, $list->pageSize);
        $this->assertSame('10',            $list->term);
        $this->assertSame(4,               $list->totalCount);
        $this->assertSame(2 ,              $list->totalPages);
        $list = $this->fetcher->paginate(2, '10', $customPageSize);
        $this->assertInstanceOf(OrganizationList::class, $list);
        $this->assertIsArray($list->items);
        $this->assertContainsOnlyInstancesOf(OrganizationListItem::class, $list->items);
        $this->assertCount(1,              $list->items);
        $this->assertSame(2,               $list->page);
        $this->assertSame($customPageSize, $list->pageSize);
        $this->assertSame('10',            $list->term);
        $this->assertSame(4,               $list->totalCount);
        $this->assertSame(2 ,              $list->totalPages);

        $list = $this->fetcher->paginate(1, '14', $customPageSize);
        $this->assertInstanceOf(OrganizationList::class, $list);
        $this->assertIsArray($list->items);
        $this->assertContainsOnlyInstancesOf(OrganizationListItem::class, $list->items);
        $this->assertCount(2,              $list->items);
        $this->assertSame(1,               $list->page);
        $this->assertSame($customPageSize, $list->pageSize);
        $this->assertSame('14',            $list->term);
        $this->assertSame(2,               $list->totalCount);
        $this->assertSame(1 ,              $list->totalPages);

        $list = $this->fetcher->paginate(1, 'юрлиЦ', $customPageSize);
        $this->assertInstanceOf(OrganizationList::class, $list);
        $this->assertIsArray($list->items);
        $this->assertContainsOnlyInstancesOf(OrganizationListItem::class, $list->items);
        $this->assertCount(3,              $list->items);
        $this->assertSame(1,               $list->page);
        $this->assertSame($customPageSize, $list->pageSize);
        $this->assertSame('юрлиЦ',         $list->term);
        $this->assertSame(5,               $list->totalCount);
        $this->assertSame(2,               $list->totalPages);
        $list = $this->fetcher->paginate(2, 'юрлиЦ', $customPageSize);
        $this->assertInstanceOf(OrganizationList::class, $list);
        $this->assertIsArray($list->items);
        $this->assertContainsOnlyInstancesOf(OrganizationListItem::class, $list->items);
        $this->assertCount(2,              $list->items);
        $this->assertSame(2,               $list->page);
        $this->assertSame($customPageSize, $list->pageSize);
        $this->assertSame('юрлиЦ',         $list->term);
        $this->assertSame(5,               $list->totalCount);
        $this->assertSame(2,               $list->totalPages);

        $list = $this->fetcher->paginate(1, 'СергЕЙ', $customPageSize);
        $this->assertInstanceOf(OrganizationList::class, $list);
        $this->assertIsArray($list->items);
        $this->assertContainsOnlyInstancesOf(OrganizationListItem::class, $list->items);
        $this->assertCount(1,              $list->items);
        $this->assertSame(1,               $list->page);
        $this->assertSame($customPageSize, $list->pageSize);
        $this->assertSame('СергЕЙ',      $list->term);
        $this->assertSame(1,               $list->totalCount);
        $this->assertSame(1,               $list->totalPages);

        $list = $this->fetcher->paginate(1, '54044476', $customPageSize);
        $this->assertInstanceOf(OrganizationList::class, $list);
        $this->assertIsArray($list->items);
        $this->assertContainsOnlyInstancesOf(OrganizationListItem::class, $list->items);
        $this->assertCount(1,              $list->items);
        $this->assertSame(1,               $list->page);
        $this->assertSame($customPageSize, $list->pageSize);
        $this->assertSame('54044476',      $list->term);
        $this->assertSame(1,               $list->totalCount);
        $this->assertSame(1,               $list->totalPages);

        $list = $this->fetcher->paginate(1, '770301001', $customPageSize);
        $this->assertInstanceOf(OrganizationList::class, $list);
        $this->assertIsArray($list->items);
        $this->assertContainsOnlyInstancesOf(OrganizationListItem::class, $list->items);
        $this->assertCount(1,              $list->items);
        $this->assertSame(1,               $list->page);
        $this->assertSame($customPageSize, $list->pageSize);
        $this->assertSame('770301001',     $list->term);
        $this->assertSame(1,               $list->totalCount);
        $this->assertSame(1,               $list->totalPages);

        $list = $this->fetcher->paginate(1, '044525593', $customPageSize);
        $this->assertInstanceOf(OrganizationList::class, $list);
        $this->assertIsArray($list->items);
        $this->assertContainsOnlyInstancesOf(OrganizationListItem::class, $list->items);
        $this->assertCount(1,              $list->items);
        $this->assertSame(1,               $list->page);
        $this->assertSame($customPageSize, $list->pageSize);
        $this->assertSame('044525593',     $list->term);
        $this->assertSame(1,               $list->totalCount);
        $this->assertSame(1,               $list->totalPages);

        $list = $this->fetcher->paginate(1, 'nfo@FUNEral54.r', $customPageSize);
        $this->assertInstanceOf(OrganizationList::class, $list);
        $this->assertIsArray($list->items);
        $this->assertContainsOnlyInstancesOf(OrganizationListItem::class, $list->items);
        $this->assertCount(1,                $list->items);
        $this->assertSame(1,                 $list->page);
        $this->assertSame($customPageSize,   $list->pageSize);
        $this->assertSame('nfo@FUNEral54.r', $list->term);
        $this->assertSame(1,                 $list->totalCount);
        $this->assertSame(1,                 $list->totalPages);

        $list = $this->fetcher->paginate(1, 'FUNERAL', $customPageSize);
        $this->assertInstanceOf(OrganizationList::class, $list);
        $this->assertIsArray($list->items);
        $this->assertContainsOnlyInstancesOf(OrganizationListItem::class, $list->items);
        $this->assertCount(1,              $list->items);
        $this->assertSame(1,               $list->page);
        $this->assertSame($customPageSize, $list->pageSize);
        $this->assertSame('FUNERAL',       $list->term);
        $this->assertSame(1,               $list->totalCount);
        $this->assertSame(1 ,              $list->totalPages);

        $list = $this->fetcher->paginate(1, 'роССИИ', $customPageSize);
        $this->assertInstanceOf(OrganizationList::class, $list);
        $this->assertIsArray($list->items);
        $this->assertContainsOnlyInstancesOf(OrganizationListItem::class, $list->items);
        $this->assertCount(1,              $list->items);
        $this->assertSame(1,               $list->page);
        $this->assertSame($customPageSize, $list->pageSize);
        $this->assertSame('роССИИ',        $list->term);
        $this->assertSame(1,               $list->totalCount);
        $this->assertSame(1,               $list->totalPages);
    }

    public function testItReturnsOrganizationTotalCount(): void
    {
        $this->assertSame(9, $this->fetcher->countTotal());
    }

    public function testItDoesNotCountRemovedOrganizationsWhenCalculatingTotalCount(): void
    {
        // Prepare database table for testing
        $juristicPersonToRemove = $this->juristicPersonRepo->findById(new JuristicPersonId('JP003'));
        $this->juristicPersonRepo->remove($juristicPersonToRemove);
        $soleProprietorToRemove = $this->soleProprietorRepo->findById(new SoleProprietorId('SP002'));
        $this->soleProprietorRepo->remove($soleProprietorToRemove);

        // Testing itself
        $this->assertSame(7, $this->fetcher->countTotal());
    }

    protected function loadFixtures(): void
    {
        $this->databaseTool->loadFixtures([
            JuristicPersonFixtures::class,
            SoleProprietorFixtures::class,
        ]);
    }

    private function assertPaginatedListItemEqualsJP001(OrganizationListItem $listItem): void
    {
        $this->assertSame('JP001',                             $listItem->id);
        $this->assertSame(JuristicPerson::CLASS_SHORTCUT,      $listItem->typeShortcut);
        $this->assertSame(JuristicPerson::CLASS_LABEL,         $listItem->typeLabel);
        $this->assertSame('ООО "Рога и копыта"',               $listItem->name);
        $this->assertSame(null,                                $listItem->inn);
        $this->assertSame(null,                                $listItem->kpp);
        $this->assertSame(null,                                $listItem->ogrn);
        $this->assertSame(null,                                $listItem->okpo);
        $this->assertSame(null,                                $listItem->okved);
        $this->assertSame(null,                                $listItem->address1);
        $this->assertSame('Кемерово, пр. Строителей, 5 - 102', $listItem->address2);
        $this->assertSame(null,                                $listItem->bankDetailsBankName);
        $this->assertSame(null,                                $listItem->bankDetailsBik);
        $this->assertSame(null,                                $listItem->bankDetailsCorrespondentAccount);
        $this->assertSame(null,                                $listItem->bankDetailsCurrentAccount);
        $this->assertSame(null,                                $listItem->phone);
        $this->assertSame(null,                                $listItem->phoneAdditional);
        $this->assertSame(null,                                $listItem->fax);
        $this->assertSame(null,                                $listItem->generalDirector);
        $this->assertSame(null,                                $listItem->email);
        $this->assertSame(null,                                $listItem->website);
    }

    private function assertPaginatedListItemEqualsJP002(OrganizationListItem $listItem): void
    {
        $this->assertSame('JP002',                                $listItem->id);
        $this->assertSame(JuristicPerson::CLASS_SHORTCUT,         $listItem->typeShortcut);
        $this->assertSame(JuristicPerson::CLASS_LABEL,            $listItem->typeLabel);
        $this->assertSame('ООО Ромашка',                          $listItem->name);
        $this->assertSame('5404447629',                           $listItem->inn);
        $this->assertSame(null,                                   $listItem->kpp);
        $this->assertSame(null,                                   $listItem->ogrn);
        $this->assertSame(null,                                   $listItem->okpo);
        $this->assertSame(null,                                   $listItem->okved);
        $this->assertSame(null,                                   $listItem->address1);
        $this->assertSame(null,                                   $listItem->address2);
        $this->assertSame('ОТДЕЛЕНИЕ ЛЕНИНГРАДСКОЕ БАНКА РОССИИ', $listItem->bankDetailsBankName);
        $this->assertSame('044106001',                            $listItem->bankDetailsBik);
        $this->assertSame(null,                                   $listItem->bankDetailsCorrespondentAccount);
        $this->assertSame('40601810900001000022',                 $listItem->bankDetailsCurrentAccount);
        $this->assertSame(null,                                   $listItem->phone);
        $this->assertSame(null,                                   $listItem->phoneAdditional);
        $this->assertSame(null,                                   $listItem->fax);
        $this->assertSame(null,                                   $listItem->generalDirector);
        $this->assertSame(null,                                   $listItem->email);
        $this->assertSame(null,                                   $listItem->website);
    }

    private function assertPaginatedListItemEqualsJP003(OrganizationListItem $listItem): void
    {
        $this->assertSame('JP003',                        $listItem->id);
        $this->assertSame(JuristicPerson::CLASS_SHORTCUT, $listItem->typeShortcut);
        $this->assertSame(JuristicPerson::CLASS_LABEL,    $listItem->typeLabel);
        $this->assertSame('ПАО "ГАЗПРОМ"',                $listItem->name);
        $this->assertSame('7736050003',                   $listItem->inn);
        $this->assertSame(null,                           $listItem->kpp);
        $this->assertSame(null,                           $listItem->ogrn);
        $this->assertSame(null,                           $listItem->okpo);
        $this->assertSame(null,                           $listItem->okved);
        $this->assertSame(null,                           $listItem->address1);
        $this->assertSame(null,                           $listItem->address2);
        $this->assertSame(null,                           $listItem->bankDetailsBankName);
        $this->assertSame(null,                           $listItem->bankDetailsBik);
        $this->assertSame(null,                           $listItem->bankDetailsCorrespondentAccount);
        $this->assertSame(null,                           $listItem->bankDetailsCurrentAccount);
        $this->assertSame(null,                           $listItem->phone);
        $this->assertSame(null,                           $listItem->phoneAdditional);
        $this->assertSame(null,                           $listItem->fax);
        $this->assertSame(null,                           $listItem->generalDirector);
        $this->assertSame(null,                           $listItem->email);
        $this->assertSame(null,                           $listItem->website);
    }

    private function assertPaginatedListItemEqualsJP004(OrganizationListItem $listItem): void
    {
        $this->assertSame('JP004',                            $listItem->id);
        $this->assertSame(JuristicPerson::CLASS_SHORTCUT,     $listItem->typeShortcut);
        $this->assertSame(JuristicPerson::CLASS_LABEL,        $listItem->typeLabel);
        $this->assertSame('МУП "Новосибирский метрополитен"', $listItem->name);
        $this->assertSame(null,                               $listItem->inn);
        $this->assertSame(null,                               $listItem->kpp);
        $this->assertSame(null,                               $listItem->ogrn);
        $this->assertSame(null,                               $listItem->okpo);
        $this->assertSame(null,                               $listItem->okved);
        $this->assertSame(null,                               $listItem->address1);
        $this->assertSame(null,                               $listItem->address2);
        $this->assertSame(null,                               $listItem->bankDetailsBankName);
        $this->assertSame(null,                               $listItem->bankDetailsBik);
        $this->assertSame(null,                               $listItem->bankDetailsCorrespondentAccount);
        $this->assertSame(null,                               $listItem->bankDetailsCurrentAccount);
        $this->assertSame(null,                               $listItem->phone);
        $this->assertSame(null,                               $listItem->phoneAdditional);
        $this->assertSame(null,                               $listItem->fax);
        $this->assertSame(null,                               $listItem->generalDirector);
        $this->assertSame(null,                               $listItem->email);
        $this->assertSame(null,                               $listItem->website);
    }

    private function assertPaginatedListItemEqualsJP005(OrganizationListItem $listItem): void
    {
        $this->assertSame('JP005',                        $listItem->id);
        $this->assertSame(JuristicPerson::CLASS_SHORTCUT, $listItem->typeShortcut);
        $this->assertSame(JuristicPerson::CLASS_LABEL,    $listItem->typeLabel);
        $this->assertSame('ООО "Интернет Решения"',       $listItem->name);
        $this->assertSame('7704217370',                   $listItem->inn);
        $this->assertSame('770301001',                    $listItem->kpp);
        $this->assertSame(null,                           $listItem->ogrn);
        $this->assertSame(null,                           $listItem->okpo);
        $this->assertSame(null,                           $listItem->okved);
        $this->assertSame(null,                           $listItem->address1);
        $this->assertSame(null,                           $listItem->address2);
        $this->assertSame(null,                           $listItem->bankDetailsBankName);
        $this->assertSame(null,                           $listItem->bankDetailsBik);
        $this->assertSame(null,                           $listItem->bankDetailsCorrespondentAccount);
        $this->assertSame(null,                           $listItem->bankDetailsCurrentAccount);
        $this->assertSame(null,                           $listItem->phone);
        $this->assertSame(null,                           $listItem->phoneAdditional);
        $this->assertSame(null,                           $listItem->fax);
        $this->assertSame('Паньков Сергей Владимирович',  $listItem->generalDirector);
        $this->assertSame(null,                           $listItem->email);
        $this->assertSame(null,                           $listItem->website);
    }

    private function assertPaginatedListItemEqualsSP001(OrganizationListItem $listItem): void
    {
        $this->assertSame('SP001',                        $listItem->id);
        $this->assertSame(SoleProprietor::CLASS_SHORTCUT, $listItem->typeShortcut);
        $this->assertSame(SoleProprietor::CLASS_LABEL,    $listItem->typeLabel);
        $this->assertSame('ИП Иванов Иван Иванович',      $listItem->name);
        $this->assertSame(null,                           $listItem->inn);
        $this->assertSame(null,                           $listItem->kpp);
        $this->assertSame(null,                           $listItem->ogrn);
        $this->assertSame(null,                           $listItem->okpo);
        $this->assertSame(null,                           $listItem->okved);
        $this->assertSame(null,                           $listItem->address1);
        $this->assertSame(null,                           $listItem->address2);
        $this->assertSame(null,                           $listItem->bankDetailsBankName);
        $this->assertSame(null,                           $listItem->bankDetailsBik);
        $this->assertSame(null,                           $listItem->bankDetailsCorrespondentAccount);
        $this->assertSame(null,                           $listItem->bankDetailsCurrentAccount);
        $this->assertSame(null,                           $listItem->phone);
        $this->assertSame(null,                           $listItem->phoneAdditional);
        $this->assertSame(null,                           $listItem->fax);
        $this->assertSame(null,                           $listItem->generalDirector);
        $this->assertSame(null,                           $listItem->email);
        $this->assertSame(null,                           $listItem->website);
    }

    private function assertPaginatedListItemEqualsSP002(OrganizationListItem $listItem): void
    {
        $this->assertSame('SP002',                        $listItem->id);
        $this->assertSame(SoleProprietor::CLASS_SHORTCUT, $listItem->typeShortcut);
        $this->assertSame(SoleProprietor::CLASS_LABEL,    $listItem->typeLabel);
        $this->assertSame('ИП Петров Пётр Петрович',      $listItem->name);
        $this->assertSame('772208786091',                 $listItem->inn);
        $this->assertSame(null,                           $listItem->kpp);
        $this->assertSame(null,                           $listItem->ogrn);
        $this->assertSame(null,                           $listItem->okpo);
        $this->assertSame(null,                           $listItem->okved);
        $this->assertSame(null,                           $listItem->address1);
        $this->assertSame(null,                           $listItem->address2);
        $this->assertSame('АО "АЛЬФА-БАНК"',              $listItem->bankDetailsBankName);
        $this->assertSame('044525593',                    $listItem->bankDetailsBik);
        $this->assertSame('30101810200000000593',         $listItem->bankDetailsCorrespondentAccount);
        $this->assertSame('40701810401400000014',         $listItem->bankDetailsCurrentAccount);
        $this->assertSame('8-383-133-22-33',              $listItem->phone);
        $this->assertSame('8-383-133-22-44',              $listItem->phoneAdditional);
        $this->assertSame('8-383-133-22-55',              $listItem->fax);
        $this->assertSame('info@funeral54.ru',            $listItem->email);
        $this->assertSame('funeral54.ru',                 $listItem->website);
    }

    private function assertPaginatedListItemEqualsSP003(OrganizationListItem $listItem): void
    {
        $this->assertSame('SP003',                        $listItem->id);
        $this->assertSame(SoleProprietor::CLASS_SHORTCUT, $listItem->typeShortcut);
        $this->assertSame(SoleProprietor::CLASS_LABEL,    $listItem->typeLabel);
        $this->assertSame('ИП Сидоров Сидр Сидорович',    $listItem->name);
        $this->assertSame('391600743661',                 $listItem->inn);
        $this->assertSame(null,                           $listItem->kpp);
        $this->assertSame(null,                           $listItem->ogrn);
        $this->assertSame(null,                           $listItem->okpo);
        $this->assertSame(null,                           $listItem->okved);
        $this->assertSame(null,                           $listItem->address1);
        $this->assertSame('с. Каменка, Заводская 14',     $listItem->address2);
        $this->assertSame(null,                           $listItem->bankDetailsBankName);
        $this->assertSame(null,                           $listItem->bankDetailsBik);
        $this->assertSame(null,                           $listItem->bankDetailsCorrespondentAccount);
        $this->assertSame(null,                           $listItem->bankDetailsCurrentAccount);
        $this->assertSame('8-383-147-22-33',              $listItem->phone);
        $this->assertSame(null,                           $listItem->phoneAdditional);
        $this->assertSame(null,                           $listItem->fax);
        $this->assertSame(null,                           $listItem->generalDirector);
        $this->assertSame(null,                           $listItem->email);
        $this->assertSame(null,                           $listItem->website);
    }

    private function assertPaginatedListItemEqualsSP004(OrganizationListItem $listItem): void
    {
        $this->assertSame('SP004',                        $listItem->id);
        $this->assertSame(SoleProprietor::CLASS_SHORTCUT, $listItem->typeShortcut);
        $this->assertSame(SoleProprietor::CLASS_LABEL,    $listItem->typeLabel);
        $this->assertSame('ИП Соколов Герман Маркович',   $listItem->name);
        $this->assertSame(null,                           $listItem->inn);
        $this->assertSame(null,                           $listItem->kpp);
        $this->assertSame(null,                           $listItem->ogrn);
        $this->assertSame(null,                           $listItem->okpo);
        $this->assertSame(null,                           $listItem->okved);
        $this->assertSame(null,                           $listItem->address1);
        $this->assertSame(null,                           $listItem->address2);
        $this->assertSame(null,                           $listItem->bankDetailsBankName);
        $this->assertSame(null,                           $listItem->bankDetailsBik);
        $this->assertSame(null,                           $listItem->bankDetailsCorrespondentAccount);
        $this->assertSame(null,                           $listItem->bankDetailsCurrentAccount);
        $this->assertSame(null,                           $listItem->phone);
        $this->assertSame(null,                           $listItem->phoneAdditional);
        $this->assertSame(null,                           $listItem->fax);
        $this->assertSame(null,                           $listItem->generalDirector);
        $this->assertSame(null,                           $listItem->email);
        $this->assertSame(null,                           $listItem->website);
    }
}
