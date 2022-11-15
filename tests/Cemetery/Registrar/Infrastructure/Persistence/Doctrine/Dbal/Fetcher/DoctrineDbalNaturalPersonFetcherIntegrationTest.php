<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Fetcher;

use Cemetery\Registrar\Domain\Model\NaturalPerson\NaturalPersonId;
use Cemetery\Registrar\Domain\Model\NaturalPerson\NaturalPersonRepository;
use Cemetery\Registrar\Domain\View\NaturalPerson\NaturalPersonPaginatedList;
use Cemetery\Registrar\Domain\View\NaturalPerson\NaturalPersonPaginatedListItem;
use Cemetery\Registrar\Domain\View\NaturalPerson\NaturalPersonSimpleList;
use Cemetery\Registrar\Domain\View\NaturalPerson\NaturalPersonSimpleListItem;
use Cemetery\Registrar\Domain\View\NaturalPerson\NaturalPersonView;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Fetcher\DoctrineDbalNaturalPersonFetcher;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Orm\Repository\DoctrineOrmNaturalPersonRepository;
use DataFixtures\CauseOfDeath\CauseOfDeathFixtures;
use DataFixtures\NaturalPerson\NaturalPersonFixtures;

/**
 * @group database
 *
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class DoctrineDbalNaturalPersonFetcherIntegrationTest extends DoctrineDbalFetcherIntegrationTest
{
    private NaturalPersonRepository $repo;

    public function setUp(): void
    {
        parent::setUp();

        $this->repo    = new DoctrineOrmNaturalPersonRepository($this->entityManager);
        $this->fetcher = new DoctrineDbalNaturalPersonFetcher($this->connection);
        $this->loadFixtures();
    }

    public function testItReturnsNaturalPersonViewById(): void
    {
        $this->testItReturnsNaturalPersonViewForNP001();
        $this->testItReturnsNaturalPersonViewForNP002();
        $this->testItReturnsNaturalPersonViewForNP003();
        $this->testItReturnsNaturalPersonViewForNP004();
        $this->testItReturnsNaturalPersonViewForNP005();
        $this->testItReturnsNaturalPersonViewForNP006();
        $this->testItReturnsNaturalPersonViewForNP007();
        $this->testItReturnsNaturalPersonViewForNP008();
        $this->testItReturnsNaturalPersonViewForNP009();
        $this->testItReturnsNaturalPersonViewForNP010();
        $this->testItReturnsNaturalPersonViewForNP011();
        $this->testItReturnsNaturalPersonViewForNP012();
        $this->testItReturnsNaturalPersonViewForNP013();
    }

    public function testItReturnsNullForRemovedNaturalPerson(): void
    {
        // Prepare database table for testing
        $naturalPersonToRemove = $this->repo->findById(new NaturalPersonId('NP002'));
        $this->repo->remove($naturalPersonToRemove);
        $removedNaturalPersonId = $naturalPersonToRemove->id()->value();

        // Testing itself
        $view = $this->fetcher->findViewById($removedNaturalPersonId);
        $this->assertNull($view);
    }

    public function testItChecksExistenceById(): void
    {
        // Prepare database table for testing
        $naturalPersonToRemove = $this->repo->findById(new NaturalPersonId('NP002'));
        $this->repo->remove($naturalPersonToRemove);
        $removedNaturalPersonId = $naturalPersonToRemove->id()->value();

        $this->assertTrue($this->fetcher->doesExistById('NP001'));
        $this->assertFalse($this->fetcher->doesExistById('unknown_id'));
        $this->assertFalse($this->fetcher->doesExistById($removedNaturalPersonId));
    }

    public function testItReturnsNaturalPersonListAll(): void
    {
        $listAll = $this->fetcher->findAll();
        $this->assertInstanceOf(NaturalPersonSimpleList::class, $listAll);
        $this->assertIsArray($listAll->items);
        $this->assertContainsOnlyInstancesOf(NaturalPersonSimpleListItem::class, $listAll->items);
        $this->assertCount(13, $listAll->items);
        $this->assertSimpleListItemEqualsNP008($listAll->items[0]);  // Items are ordered by full name,
        $this->assertSimpleListItemEqualsNP006($listAll->items[1]);  // then by date of birth,
        $this->assertSimpleListItemEqualsNP007($listAll->items[2]);  // and finally by date of death.
        $this->assertSimpleListItemEqualsNP001($listAll->items[3]);
        $this->assertSimpleListItemEqualsNP005($listAll->items[4]);
        $this->assertSimpleListItemEqualsNP011($listAll->items[5]);
        $this->assertSimpleListItemEqualsNP012($listAll->items[6]);
        $this->assertSimpleListItemEqualsNP010($listAll->items[7]);
        $this->assertSimpleListItemEqualsNP009($listAll->items[8]);
        $this->assertSimpleListItemEqualsNP013($listAll->items[9]);
        $this->assertSimpleListItemEqualsNP004($listAll->items[10]);
        $this->assertSimpleListItemEqualsNP002($listAll->items[11]);
        $this->assertSimpleListItemEqualsNP003($listAll->items[12]);
    }

    public function testItReturnsNaturalPersonListAllWhereFullNameStartsWithTerm(): void
    {
        $listAll = $this->fetcher->findAll('ИваН');
        $this->assertInstanceOf(NaturalPersonSimpleList::class, $listAll);
        $this->assertIsArray($listAll->items);
        $this->assertContainsOnlyInstancesOf(NaturalPersonSimpleListItem::class, $listAll->items);
        $this->assertCount(3, $listAll->items);
        $this->assertSimpleListItemEqualsNP011($listAll->items[0]);
        $this->assertSimpleListItemEqualsNP012($listAll->items[1]);
        $this->assertSimpleListItemEqualsNP010($listAll->items[2]);

        $listAll = $this->fetcher->findAll('гР');
        $this->assertInstanceOf(NaturalPersonSimpleList::class, $listAll);
        $this->assertIsArray($listAll->items);
        $this->assertContainsOnlyInstancesOf(NaturalPersonSimpleListItem::class, $listAll->items);
        $this->assertCount(2, $listAll->items);
        $this->assertSimpleListItemEqualsNP006($listAll->items[0]);
        $this->assertSimpleListItemEqualsNP007($listAll->items[1]);

        $listAll = $this->fetcher->findAll('инГА');
        $this->assertInstanceOf(NaturalPersonSimpleList::class, $listAll);
        $this->assertIsArray($listAll->items);
        $this->assertCount(0, $listAll->items);
    }

    public function testItReturnsNaturalPersonListAlive(): void
    {
        $listAlive = $this->fetcher->findAlive();
        $this->assertInstanceOf(NaturalPersonSimpleList::class, $listAlive);
        $this->assertIsArray($listAlive->items);
        $this->assertContainsOnlyInstancesOf(NaturalPersonSimpleListItem::class, $listAlive->items);
        $this->assertCount(3, $listAlive->items);
        $this->assertSimpleListItemEqualsNP008($listAlive->items[0]);
        $this->assertSimpleListItemEqualsNP007($listAlive->items[1]);
        $this->assertSimpleListItemEqualsNP013($listAlive->items[2]);
    }

    public function testItReturnsNaturalPersonListAliveWhereFullNameStartsWithTerm(): void
    {
        $listAlive = $this->fetcher->findAlive('РОВич');
        $this->assertInstanceOf(NaturalPersonSimpleList::class, $listAlive);
        $this->assertIsArray($listAlive->items);
        $this->assertCount(0, $listAlive->items);

        $listAlive = $this->fetcher->findAlive('БЕЛ');
        $this->assertInstanceOf(NaturalPersonSimpleList::class, $listAlive);
        $this->assertIsArray($listAlive->items);
        $this->assertContainsOnlyInstancesOf(NaturalPersonSimpleListItem::class, $listAlive->items);
        $this->assertCount(1, $listAlive->items);
        $this->assertSimpleListItemEqualsNP008($listAlive->items[0]);
    }

    public function testItReturnsNaturalPersonPaginatedListByPage(): void
    {
        $customPageSize = 4;

        // First page
        $listForFirstPage = $this->fetcher->paginate(1, null, $customPageSize);
        $this->assertInstanceOf(NaturalPersonPaginatedList::class, $listForFirstPage);
        $this->assertIsArray($listForFirstPage->items);
        $this->assertContainsOnlyInstancesOf(NaturalPersonPaginatedListItem::class, $listForFirstPage->items);
        $this->assertCount(4,              $listForFirstPage->items);
        $this->assertSame(1,               $listForFirstPage->page);
        $this->assertSame($customPageSize, $listForFirstPage->pageSize);
        $this->assertSame(null,            $listForFirstPage->term);
        $this->assertSame(13,              $listForFirstPage->totalCount);
        $this->assertSame(4,               $listForFirstPage->totalPages);
        $this->assertPaginatedListItemEqualsNP008($listForFirstPage->items[0]);  // Items are ordered by full name,
        $this->assertPaginatedListItemEqualsNP006($listForFirstPage->items[1]);  // then by date of birth,
        $this->assertPaginatedListItemEqualsNP007($listForFirstPage->items[2]);  // and finally by date of death.
        $this->assertPaginatedListItemEqualsNP001($listForFirstPage->items[3]);

        // Second page
        $listForSecondPage = $this->fetcher->paginate(2, null, $customPageSize);
        $this->assertInstanceOf(NaturalPersonPaginatedList::class, $listForSecondPage);
        $this->assertIsArray($listForSecondPage->items);
        $this->assertContainsOnlyInstancesOf(NaturalPersonPaginatedListItem::class, $listForSecondPage->items);
        $this->assertCount(4,              $listForSecondPage->items);
        $this->assertSame(2,               $listForSecondPage->page);
        $this->assertSame($customPageSize, $listForSecondPage->pageSize);
        $this->assertSame(null,            $listForSecondPage->term);
        $this->assertSame(13,              $listForSecondPage->totalCount);
        $this->assertSame(4,               $listForSecondPage->totalPages);
        $this->assertPaginatedListItemEqualsNP005($listForSecondPage->items[0]);
        $this->assertPaginatedListItemEqualsNP011($listForSecondPage->items[1]);
        $this->assertPaginatedListItemEqualsNP012($listForSecondPage->items[2]);
        $this->assertPaginatedListItemEqualsNP010($listForSecondPage->items[3]);

        // Third page
        $listForThirdPage = $this->fetcher->paginate(3, null, $customPageSize);
        $this->assertInstanceOf(NaturalPersonPaginatedList::class, $listForThirdPage);
        $this->assertIsArray($listForThirdPage->items);
        $this->assertContainsOnlyInstancesOf(NaturalPersonPaginatedListItem::class, $listForThirdPage->items);
        $this->assertCount(4,              $listForThirdPage->items);
        $this->assertSame(3,               $listForThirdPage->page);
        $this->assertSame($customPageSize, $listForThirdPage->pageSize);
        $this->assertSame(null,            $listForThirdPage->term);
        $this->assertSame(13,              $listForThirdPage->totalCount);
        $this->assertSame(4,               $listForThirdPage->totalPages);
        $this->assertPaginatedListItemEqualsNP009($listForThirdPage->items[0]);
        $this->assertPaginatedListItemEqualsNP013($listForThirdPage->items[1]);
        $this->assertPaginatedListItemEqualsNP004($listForThirdPage->items[2]);
        $this->assertPaginatedListItemEqualsNP002($listForThirdPage->items[3]);

        // Fourth page
        $listForFourthPage = $this->fetcher->paginate(4, null, $customPageSize);
        $this->assertInstanceOf(NaturalPersonPaginatedList::class, $listForFourthPage);
        $this->assertIsArray($listForFourthPage->items);
        $this->assertCount(1,              $listForFourthPage->items);
        $this->assertSame(4,               $listForFourthPage->page);
        $this->assertSame($customPageSize, $listForFourthPage->pageSize);
        $this->assertSame(null,            $listForFourthPage->term);
        $this->assertSame(13,              $listForFourthPage->totalCount);
        $this->assertSame(4,               $listForFourthPage->totalPages);
        $this->assertPaginatedListItemEqualsNP003($listForFourthPage->items[0]);

        // Default page size
        $listForFirstPageAndDefaultPageSize = $this->fetcher->paginate(1);
        $this->assertInstanceOf(NaturalPersonPaginatedList::class, $listForFirstPageAndDefaultPageSize);
        $this->assertIsArray($listForFirstPageAndDefaultPageSize->items);
        $this->assertContainsOnlyInstancesOf(NaturalPersonPaginatedListItem::class, $listForFirstPageAndDefaultPageSize->items);
        $this->assertCount(13,                     $listForFirstPageAndDefaultPageSize->items);
        $this->assertSame(1,                       $listForFirstPageAndDefaultPageSize->page);
        $this->assertSame(self::DEFAULT_PAGE_SIZE, $listForFirstPageAndDefaultPageSize->pageSize);
        $this->assertSame(null,                    $listForFirstPageAndDefaultPageSize->term);
        $this->assertSame(13,                      $listForFirstPageAndDefaultPageSize->totalCount);
        $this->assertSame(1,                       $listForFirstPageAndDefaultPageSize->totalPages);
        $listForSecondPageAndDefaultPageSize = $this->fetcher->paginate(2);
        $this->assertInstanceOf(NaturalPersonPaginatedList::class, $listForSecondPageAndDefaultPageSize);
        $this->assertIsArray($listForSecondPageAndDefaultPageSize->items);
        $this->assertContainsOnlyInstancesOf(NaturalPersonPaginatedListItem::class, $listForSecondPageAndDefaultPageSize->items);
        $this->assertCount(0,                      $listForSecondPageAndDefaultPageSize->items);
        $this->assertSame(2,                       $listForSecondPageAndDefaultPageSize->page);
        $this->assertSame(self::DEFAULT_PAGE_SIZE, $listForSecondPageAndDefaultPageSize->pageSize);
        $this->assertSame(null,                    $listForSecondPageAndDefaultPageSize->term);
        $this->assertSame(13,                      $listForSecondPageAndDefaultPageSize->totalCount);
        $this->assertSame(1,                       $listForSecondPageAndDefaultPageSize->totalPages);
    }

    public function testItReturnsNaturalPersonPaginatedListByPageAndTerm(): void
    {
        $customPageSize = 3;

        $list = $this->fetcher->paginate(1, 'ИваноВИч', $customPageSize);
        $this->assertInstanceOf(NaturalPersonPaginatedList::class, $list);
        $this->assertIsArray($list->items);
        $this->assertContainsOnlyInstancesOf(NaturalPersonPaginatedListItem::class, $list->items);
        $this->assertCount(3,              $list->items);
        $this->assertSame(1,               $list->page);
        $this->assertSame($customPageSize, $list->pageSize);
        $this->assertSame('ИваноВИч',      $list->term);
        $this->assertSame(3,               $list->totalCount);
        $this->assertSame(1,               $list->totalPages);

        $list = $this->fetcher->paginate(1, '12', $customPageSize);
        $this->assertInstanceOf(NaturalPersonPaginatedList::class, $list);
        $this->assertIsArray($list->items);
        $this->assertContainsOnlyInstancesOf(NaturalPersonPaginatedListItem::class, $list->items);
        $this->assertCount(3,              $list->items);
        $this->assertSame(1,               $list->page);
        $this->assertSame($customPageSize, $list->pageSize);
        $this->assertSame('12',            $list->term);
        $this->assertSame(8,               $list->totalCount);
        $this->assertSame(3,               $list->totalPages);
        $list = $this->fetcher->paginate(2, '12', $customPageSize);
        $this->assertInstanceOf(NaturalPersonPaginatedList::class, $list);
        $this->assertIsArray($list->items);
        $this->assertContainsOnlyInstancesOf(NaturalPersonPaginatedListItem::class, $list->items);
        $this->assertCount(3,              $list->items);
        $this->assertSame(2,               $list->page);
        $this->assertSame($customPageSize, $list->pageSize);
        $this->assertSame('12',            $list->term);
        $this->assertSame(8,               $list->totalCount);
        $this->assertSame(3,               $list->totalPages);
        $list = $this->fetcher->paginate(3, '12', $customPageSize);
        $this->assertInstanceOf(NaturalPersonPaginatedList::class, $list);
        $this->assertIsArray($list->items);
        $this->assertContainsOnlyInstancesOf(NaturalPersonPaginatedListItem::class, $list->items);
        $this->assertCount(2,              $list->items);
        $this->assertSame(3,               $list->page);
        $this->assertSame($customPageSize, $list->pageSize);
        $this->assertSame('12',            $list->term);
        $this->assertSame(8,               $list->totalCount);
        $this->assertSame(3,               $list->totalPages);

        $list = $this->fetcher->paginate(1, '13', $customPageSize);
        $this->assertInstanceOf(NaturalPersonPaginatedList::class, $list);
        $this->assertIsArray($list->items);
        $this->assertContainsOnlyInstancesOf(NaturalPersonPaginatedListItem::class, $list->items);
        $this->assertCount(2,              $list->items);
        $this->assertSame(1,               $list->page);
        $this->assertSame($customPageSize, $list->pageSize);
        $this->assertSame('13',            $list->term);
        $this->assertSame(2,               $list->totalCount);
        $this->assertSame(1,               $list->totalPages);

        $list = $this->fetcher->paginate(1, 'Новосиб', $customPageSize);
        $this->assertInstanceOf(NaturalPersonPaginatedList::class, $list);
        $this->assertIsArray($list->items);
        $this->assertContainsOnlyInstancesOf(NaturalPersonPaginatedListItem::class, $list->items);
        $this->assertCount(3,              $list->items);
        $this->assertSame(1,               $list->page);
        $this->assertSame($customPageSize, $list->pageSize);
        $this->assertSame('Новосиб',       $list->term);
        $this->assertSame(3,               $list->totalCount);
        $this->assertSame(1,               $list->totalPages);

        $list = $this->fetcher->paginate(1, 'ноВ', $customPageSize);
        $this->assertInstanceOf(NaturalPersonPaginatedList::class, $list);
        $this->assertIsArray($list->items);
        $this->assertContainsOnlyInstancesOf(NaturalPersonPaginatedListItem::class, $list->items);
        $this->assertCount(3,              $list->items);
        $this->assertSame(1,               $list->page);
        $this->assertSame($customPageSize, $list->pageSize);
        $this->assertSame('ноВ',           $list->term);
        $this->assertSame(8,               $list->totalCount);
        $this->assertSame(3,               $list->totalPages);
        $list = $this->fetcher->paginate(2, 'ноВ', $customPageSize);
        $this->assertInstanceOf(NaturalPersonPaginatedList::class, $list);
        $this->assertIsArray($list->items);
        $this->assertContainsOnlyInstancesOf(NaturalPersonPaginatedListItem::class, $list->items);
        $this->assertCount(3,              $list->items);
        $this->assertSame(2,               $list->page);
        $this->assertSame($customPageSize, $list->pageSize);
        $this->assertSame('ноВ',           $list->term);
        $this->assertSame(8,               $list->totalCount);
        $this->assertSame(3,               $list->totalPages);
        $list = $this->fetcher->paginate(3, 'ноВ', $customPageSize);
        $this->assertInstanceOf(NaturalPersonPaginatedList::class, $list);
        $this->assertIsArray($list->items);
        $this->assertContainsOnlyInstancesOf(NaturalPersonPaginatedListItem::class, $list->items);
        $this->assertCount(2,              $list->items);
        $this->assertSame(3,               $list->page);
        $this->assertSame($customPageSize, $list->pageSize);
        $this->assertSame('ноВ',           $list->term);
        $this->assertSame(8,               $list->totalCount);
        $this->assertSame(3,               $list->totalPages);

        $list = $this->fetcher->paginate(1, 'ленИН', $customPageSize);
        $this->assertInstanceOf(NaturalPersonPaginatedList::class, $list);
        $this->assertIsArray($list->items);
        $this->assertContainsOnlyInstancesOf(NaturalPersonPaginatedListItem::class, $list->items);
        $this->assertCount(2,              $list->items);
        $this->assertSame(1,               $list->page);
        $this->assertSame($customPageSize, $list->pageSize);
        $this->assertSame('ленИН',         $list->term);
        $this->assertSame(2,               $list->totalCount);
        $this->assertSame(1,               $list->totalPages);

        $list = $this->fetcher->paginate(1, 'GMail.com', $customPageSize);
        $this->assertInstanceOf(NaturalPersonPaginatedList::class, $list);
        $this->assertIsArray($list->items);
        $this->assertContainsOnlyInstancesOf(NaturalPersonPaginatedListItem::class, $list->items);
        $this->assertCount(1,              $list->items);
        $this->assertSame(1,               $list->page);
        $this->assertSame($customPageSize, $list->pageSize);
        $this->assertSame('GMail.com',     $list->term);
        $this->assertSame(1,               $list->totalCount);
        $this->assertSame(1,               $list->totalPages);

        $list = $this->fetcher->paginate(1, '12964', $customPageSize);
        $this->assertInstanceOf(NaturalPersonPaginatedList::class, $list);
        $this->assertIsArray($list->items);
        $this->assertContainsOnlyInstancesOf(NaturalPersonPaginatedListItem::class, $list->items);
        $this->assertCount(1,              $list->items);
        $this->assertSame(1,               $list->page);
        $this->assertSame($customPageSize, $list->pageSize);
        $this->assertSame('12964',         $list->term);
        $this->assertSame(1,               $list->totalCount);
        $this->assertSame(1,               $list->totalPages);

        $list = $this->fetcher->paginate(1, 'v-мЮ', $customPageSize);
        $this->assertInstanceOf(NaturalPersonPaginatedList::class, $list);
        $this->assertIsArray($list->items);
        $this->assertContainsOnlyInstancesOf(NaturalPersonPaginatedListItem::class, $list->items);
        $this->assertCount(1,              $list->items);
        $this->assertSame(1,               $list->page);
        $this->assertSame($customPageSize, $list->pageSize);
        $this->assertSame('v-мЮ',          $list->term);
        $this->assertSame(1,               $list->totalCount);
        $this->assertSame(1,               $list->totalPages);

        $list = $this->fetcher->paginate(1, '03', $customPageSize);
        $this->assertInstanceOf(NaturalPersonPaginatedList::class, $list);
        $this->assertIsArray($list->items);
        $this->assertContainsOnlyInstancesOf(NaturalPersonPaginatedListItem::class, $list->items);
        $this->assertCount(3,              $list->items);
        $this->assertSame(1,               $list->page);
        $this->assertSame($customPageSize, $list->pageSize);
        $this->assertSame('03',            $list->term);
        $this->assertSame(4,               $list->totalCount);
        $this->assertSame(2,               $list->totalPages);
        $list = $this->fetcher->paginate(2, '03', $customPageSize);
        $this->assertInstanceOf(NaturalPersonPaginatedList::class, $list);
        $this->assertIsArray($list->items);
        $this->assertContainsOnlyInstancesOf(NaturalPersonPaginatedListItem::class, $list->items);
        $this->assertCount(1,              $list->items);
        $this->assertSame(2,               $list->page);
        $this->assertSame($customPageSize, $list->pageSize);
        $this->assertSame('03',            $list->term);
        $this->assertSame(4,               $list->totalCount);
        $this->assertSame(2,               $list->totalPages);

        $list = $this->fetcher->paginate(1, '69', $customPageSize);
        $this->assertInstanceOf(NaturalPersonPaginatedList::class, $list);
        $this->assertIsArray($list->items);
        $this->assertContainsOnlyInstancesOf(NaturalPersonPaginatedListItem::class, $list->items);
        $this->assertCount(2,              $list->items);
        $this->assertSame(1,               $list->page);
        $this->assertSame($customPageSize, $list->pageSize);
        $this->assertSame('69',            $list->term);
        $this->assertSame(2,               $list->totalCount);
        $this->assertSame(1,               $list->totalPages);

        $list = $this->fetcher->paginate(1, '42', $customPageSize);
        $this->assertInstanceOf(NaturalPersonPaginatedList::class, $list);
        $this->assertIsArray($list->items);
        $this->assertContainsOnlyInstancesOf(NaturalPersonPaginatedListItem::class, $list->items);
        $this->assertCount(2,              $list->items);
        $this->assertSame(1,               $list->page);
        $this->assertSame($customPageSize, $list->pageSize);
        $this->assertSame('42',            $list->term);
        $this->assertSame(2,               $list->totalCount);
        $this->assertSame(1,               $list->totalPages);

        $list = $this->fetcher->paginate(1, '82', $customPageSize);
        $this->assertInstanceOf(NaturalPersonPaginatedList::class, $list);
        $this->assertIsArray($list->items);
        $this->assertContainsOnlyInstancesOf(NaturalPersonPaginatedListItem::class, $list->items);
        $this->assertCount(1,              $list->items);
        $this->assertSame(1,               $list->page);
        $this->assertSame($customPageSize, $list->pageSize);
        $this->assertSame('82',            $list->term);
        $this->assertSame(1,               $list->totalCount);
        $this->assertSame(1,               $list->totalPages);

        $list = $this->fetcher->paginate(1, '.200', $customPageSize);
        $this->assertInstanceOf(NaturalPersonPaginatedList::class, $list);
        $this->assertIsArray($list->items);
        $this->assertContainsOnlyInstancesOf(NaturalPersonPaginatedListItem::class, $list->items);
        $this->assertCount(3,              $list->items);
        $this->assertSame(1,               $list->page);
        $this->assertSame($customPageSize, $list->pageSize);
        $this->assertSame('.200',          $list->term);
        $this->assertSame(7,               $list->totalCount);
        $this->assertSame(3,               $list->totalPages);
        $list = $this->fetcher->paginate(2, '.200', $customPageSize);
        $this->assertInstanceOf(NaturalPersonPaginatedList::class, $list);
        $this->assertIsArray($list->items);
        $this->assertContainsOnlyInstancesOf(NaturalPersonPaginatedListItem::class, $list->items);
        $this->assertCount(3,              $list->items);
        $this->assertSame(2,               $list->page);
        $this->assertSame($customPageSize, $list->pageSize);
        $this->assertSame('.200',          $list->term);
        $this->assertSame(7,               $list->totalCount);
        $this->assertSame(3,               $list->totalPages);
        $list = $this->fetcher->paginate(3, '.200', $customPageSize);
        $this->assertInstanceOf(NaturalPersonPaginatedList::class, $list);
        $this->assertIsArray($list->items);
        $this->assertContainsOnlyInstancesOf(NaturalPersonPaginatedListItem::class, $list->items);
        $this->assertCount(1,              $list->items);
        $this->assertSame(3,               $list->page);
        $this->assertSame($customPageSize, $list->pageSize);
        $this->assertSame('.200',          $list->term);
        $this->assertSame(7,               $list->totalCount);
        $this->assertSame(3,               $list->totalPages);

        $list = $this->fetcher->paginate(1, '24.09.1915', $customPageSize);
        $this->assertInstanceOf(NaturalPersonPaginatedList::class, $list);
        $this->assertIsArray($list->items);
        $this->assertContainsOnlyInstancesOf(NaturalPersonPaginatedListItem::class, $list->items);
        $this->assertCount(1,              $list->items);
        $this->assertSame(1,               $list->page);
        $this->assertSame($customPageSize, $list->pageSize);
        $this->assertSame('24.09.1915',    $list->term);
        $this->assertSame(1,               $list->totalCount);
        $this->assertSame(1,               $list->totalPages);

        $list = $this->fetcher->paginate(1, '12.02.2001', $customPageSize);
        $this->assertInstanceOf(NaturalPersonPaginatedList::class, $list);
        $this->assertIsArray($list->items);
        $this->assertContainsOnlyInstancesOf(NaturalPersonPaginatedListItem::class, $list->items);
        $this->assertCount(1,              $list->items);
        $this->assertSame(1,               $list->page);
        $this->assertSame($customPageSize, $list->pageSize);
        $this->assertSame('12.02.2001',    $list->term);
        $this->assertSame(1,               $list->totalCount);
        $this->assertSame(1,               $list->totalPages);

        $list = $this->fetcher->paginate(1, 'онК', $customPageSize);
        $this->assertInstanceOf(NaturalPersonPaginatedList::class, $list);
        $this->assertIsArray($list->items);
        $this->assertContainsOnlyInstancesOf(NaturalPersonPaginatedListItem::class, $list->items);
        $this->assertCount(2,              $list->items);
        $this->assertSame(1,               $list->page);
        $this->assertSame($customPageSize, $list->pageSize);
        $this->assertSame('онК',           $list->term);
        $this->assertSame(2,               $list->totalCount);
        $this->assertSame(1,               $list->totalPages);

        $list = $this->fetcher->paginate(1, '532515', $customPageSize);
        $this->assertInstanceOf(NaturalPersonPaginatedList::class, $list);
        $this->assertIsArray($list->items);
        $this->assertContainsOnlyInstancesOf(NaturalPersonPaginatedListItem::class, $list->items);
        $this->assertCount(1,              $list->items);
        $this->assertSame(1,               $list->page);
        $this->assertSame($customPageSize, $list->pageSize);
        $this->assertSame('532515',        $list->term);
        $this->assertSame(1,               $list->totalCount);
        $this->assertSame(1,               $list->totalPages);

        $list = $this->fetcher->paginate(1, '23.03.2011', $customPageSize);
        $this->assertInstanceOf(NaturalPersonPaginatedList::class, $list);
        $this->assertIsArray($list->items);
        $this->assertContainsOnlyInstancesOf(NaturalPersonPaginatedListItem::class, $list->items);
        $this->assertCount(1,              $list->items);
        $this->assertSame(1,               $list->page);
        $this->assertSame($customPageSize, $list->pageSize);
        $this->assertSame('23.03.2011',    $list->term);
        $this->assertSame(1,               $list->totalCount);
        $this->assertSame(1,               $list->totalPages);

        $list = $this->fetcher->paginate(1, '12964', $customPageSize);
        $this->assertInstanceOf(NaturalPersonPaginatedList::class, $list);
        $this->assertIsArray($list->items);
        $this->assertContainsOnlyInstancesOf(NaturalPersonPaginatedListItem::class, $list->items);
        $this->assertCount(1,              $list->items);
        $this->assertSame(1,               $list->page);
        $this->assertSame($customPageSize, $list->pageSize);
        $this->assertSame('12964',         $list->term);
        $this->assertSame(1,               $list->totalCount);
        $this->assertSame(1,               $list->totalPages);

        $list = $this->fetcher->paginate(1, '03.12.2021', $customPageSize);
        $this->assertInstanceOf(NaturalPersonPaginatedList::class, $list);
        $this->assertIsArray($list->items);
        $this->assertContainsOnlyInstancesOf(NaturalPersonPaginatedListItem::class, $list->items);
        $this->assertCount(2,              $list->items);
        $this->assertSame(1,               $list->page);
        $this->assertSame($customPageSize, $list->pageSize);
        $this->assertSame('03.12.2021',    $list->term);
        $this->assertSame(2,               $list->totalCount);
        $this->assertSame(1,               $list->totalPages);

        $list = $this->fetcher->paginate(1, '1234', $customPageSize);
        $this->assertInstanceOf(NaturalPersonPaginatedList::class, $list);
        $this->assertIsArray($list->items);
        $this->assertContainsOnlyInstancesOf(NaturalPersonPaginatedListItem::class, $list->items);
        $this->assertCount(1,              $list->items);
        $this->assertSame(1,               $list->page);
        $this->assertSame($customPageSize, $list->pageSize);
        $this->assertSame('1234',          $list->term);
        $this->assertSame(1,               $list->totalCount);
        $this->assertSame(1,               $list->totalPages);

        $list = $this->fetcher->paginate(1, '162354', $customPageSize);
        $this->assertInstanceOf(NaturalPersonPaginatedList::class, $list);
        $this->assertIsArray($list->items);
        $this->assertContainsOnlyInstancesOf(NaturalPersonPaginatedListItem::class, $list->items);
        $this->assertCount(1,              $list->items);
        $this->assertSame(1,               $list->page);
        $this->assertSame($customPageSize, $list->pageSize);
        $this->assertSame('162354',        $list->term);
        $this->assertSame(1,               $list->totalCount);
        $this->assertSame(1,               $list->totalPages);

        $list = $this->fetcher->paginate(1, '20.10.1981', $customPageSize);
        $this->assertInstanceOf(NaturalPersonPaginatedList::class, $list);
        $this->assertIsArray($list->items);
        $this->assertContainsOnlyInstancesOf(NaturalPersonPaginatedListItem::class, $list->items);
        $this->assertCount(1,              $list->items);
        $this->assertSame(1,               $list->page);
        $this->assertSame($customPageSize, $list->pageSize);
        $this->assertSame('20.10.1981',    $list->term);
        $this->assertSame(1,               $list->totalCount);
        $this->assertSame(1,               $list->totalPages);

        $list = $this->fetcher->paginate(1, '540-', $customPageSize);
        $this->assertInstanceOf(NaturalPersonPaginatedList::class, $list);
        $this->assertIsArray($list->items);
        $this->assertContainsOnlyInstancesOf(NaturalPersonPaginatedListItem::class, $list->items);
        $this->assertCount(1,              $list->items);
        $this->assertSame(1,               $list->page);
        $this->assertSame($customPageSize, $list->pageSize);
        $this->assertSame('540-',          $list->term);
        $this->assertSame(1,               $list->totalCount);
        $this->assertSame(1,               $list->totalPages);
    }

    public function testItReturnsNaturalPersonTotalCount(): void
    {
        $this->assertSame(13, $this->fetcher->countTotal());
    }

    public function testItDoesNotCountRemovedNaturalPersonWhenCalculatingTotalCount(): void
    {
        // Prepare database table for testing
        $naturalPersonToRemove = $this->repo->findById(new NaturalPersonId('NP002'));
        $this->repo->remove($naturalPersonToRemove);

        // Testing itself
        $this->assertSame(12, $this->fetcher->countTotal());
    }

    protected function loadFixtures(): void
    {
        $this->databaseTool->loadFixtures([
            CauseOfDeathFixtures::class,
            NaturalPersonFixtures::class,
        ]);
    }

    private function assertPaginatedListItemEqualsNP001(NaturalPersonPaginatedListItem $listItem): void
    {
        $this->assertSame('NP001',                   $listItem->id);
        $this->assertSame('Егоров Абрам Даниилович', $listItem->fullName);
        $this->assertSame(null,                      $listItem->address);
        $this->assertSame(null,                      $listItem->phone);
        $this->assertSame(null,                      $listItem->email);
        $this->assertSame(null,                      $listItem->bornAt);
        $this->assertSame(null,                      $listItem->placeOfBirth);
        $this->assertSame(null,                      $listItem->passportSeries);
        $this->assertSame(null,                      $listItem->passportNumber);
        $this->assertSame(null,                      $listItem->passportIssuedAt);
        $this->assertSame(null,                      $listItem->passportIssuedBy);
        $this->assertSame(null,                      $listItem->passportDivisionCode);
        $this->assertSame('01.12.2021',              $listItem->diedAt);
        $this->assertSame(69,                        $listItem->age);
        $this->assertSame(null,                      $listItem->causeOfDeathName);
        $this->assertSame(null,                      $listItem->deathCertificateSeries);
        $this->assertSame(null,                      $listItem->deathCertificateNumber);
        $this->assertSame(null,                      $listItem->deathCertificateIssuedAt);
        $this->assertSame('12964',                   $listItem->cremationCertificateNumber);
        $this->assertSame('03.12.2021',              $listItem->cremationCertificateIssuedAt);
    }

    private function assertSimpleListItemEqualsNP001(NaturalPersonSimpleListItem $listItem): void
    {
        $this->assertSame('NP001',                   $listItem->id);
        $this->assertSame('Егоров Абрам Даниилович', $listItem->fullName);
        $this->assertSame(null,                      $listItem->bornAt);
        $this->assertSame('01.12.2021',              $listItem->diedAt);
    }

    private function assertPaginatedListItemEqualsNP002(NaturalPersonPaginatedListItem $listItem): void
    {
        $this->assertSame('NP002',                                 $listItem->id);
        $this->assertSame('Устинов Иван Максович',                 $listItem->fullName);
        $this->assertSame(null,                                    $listItem->address);
        $this->assertSame(null,                                    $listItem->phone);
        $this->assertSame(null,                                    $listItem->email);
        $this->assertSame('30.12.1918',                            $listItem->bornAt);
        $this->assertSame(null,                                    $listItem->placeOfBirth);
        $this->assertSame(null,                                    $listItem->passportSeries);
        $this->assertSame(null,                                    $listItem->passportNumber);
        $this->assertSame(null,                                    $listItem->passportIssuedAt);
        $this->assertSame(null,                                    $listItem->passportIssuedBy);
        $this->assertSame(null,                                    $listItem->passportDivisionCode);
        $this->assertSame('12.02.2001',                            $listItem->diedAt);
        $this->assertSame(82,                                      $listItem->age);
        $this->assertSame('Болезнь сердечно-легочная хроническая', $listItem->causeOfDeathName);
        $this->assertSame('V-МЮ',                                  $listItem->deathCertificateSeries);
        $this->assertSame('532515',                                $listItem->deathCertificateNumber);
        $this->assertSame('15.02.2001',                            $listItem->deathCertificateIssuedAt);
        $this->assertSame(null,                                    $listItem->cremationCertificateNumber);
        $this->assertSame(null,                                    $listItem->cremationCertificateIssuedAt);
    }

    private function assertSimpleListItemEqualsNP002(NaturalPersonSimpleListItem $listItem): void
    {
        $this->assertSame('NP002',                 $listItem->id);
        $this->assertSame('Устинов Иван Максович', $listItem->fullName);
        $this->assertSame('30.12.1918',            $listItem->bornAt);
        $this->assertSame('12.02.2001',            $listItem->diedAt);
    }

    private function assertPaginatedListItemEqualsNP003(NaturalPersonPaginatedListItem $listItem): void
    {
        $this->assertSame('NP003',                             $listItem->id);
        $this->assertSame('Шилов Александр Михаилович',        $listItem->fullName);
        $this->assertSame(null,                                $listItem->address);
        $this->assertSame(null,                                $listItem->phone);
        $this->assertSame(null,                                $listItem->email);
        $this->assertSame('20.05.1969',                        $listItem->bornAt);
        $this->assertSame(null,                                $listItem->placeOfBirth);
        $this->assertSame('4581',                              $listItem->passportSeries);
        $this->assertSame('684214',                            $listItem->passportNumber);
        $this->assertSame('23.03.2001',                        $listItem->passportIssuedAt);
        $this->assertSame('МВД России по Кемеровской области', $listItem->passportIssuedBy);
        $this->assertSame('681-225',                           $listItem->passportDivisionCode);
        $this->assertSame('13.05.2012',                        $listItem->diedAt);
        $this->assertSame(42,                                  $listItem->age);
        $this->assertSame('Онкология',                         $listItem->causeOfDeathName);
        $this->assertSame('I-BC',                              $listItem->deathCertificateSeries);
        $this->assertSame('785066',                            $listItem->deathCertificateNumber);
        $this->assertSame('23.03.2011',                        $listItem->deathCertificateIssuedAt);
        $this->assertSame(null,                                $listItem->cremationCertificateNumber);
        $this->assertSame(null,                                $listItem->cremationCertificateIssuedAt);
    }

    private function assertSimpleListItemEqualsNP003(NaturalPersonSimpleListItem $listItem): void
    {
        $this->assertSame('NP003',                      $listItem->id);
        $this->assertSame('Шилов Александр Михаилович', $listItem->fullName);
        $this->assertSame('20.05.1969',                 $listItem->bornAt);
        $this->assertSame('13.05.2012',                 $listItem->diedAt);
    }

    private function assertPaginatedListItemEqualsNP004(NaturalPersonPaginatedListItem $listItem): void
    {
        $this->assertSame('NP004',                                                              $listItem->id);
        $this->assertSame('Соколов Герман Маркович',                                            $listItem->fullName);
        $this->assertSame(null,                                                                 $listItem->address);
        $this->assertSame(null,                                                                 $listItem->phone);
        $this->assertSame(null,                                                                 $listItem->email);
        $this->assertSame(null,                                                                 $listItem->bornAt);
        $this->assertSame(null,                                                                 $listItem->placeOfBirth);
        $this->assertSame('1235',                                                               $listItem->passportSeries);
        $this->assertSame('567891',                                                             $listItem->passportNumber);
        $this->assertSame('23.02.2001',                                                         $listItem->passportIssuedAt);
        $this->assertSame('Отделом УФМС России по Новосибирской области в Заельцовском районе', $listItem->passportIssuedBy);
        $this->assertSame('541-001',                                                            $listItem->passportDivisionCode);
        $this->assertSame('26.01.2010',                                                         $listItem->diedAt);
        $this->assertSame(null,                                                                 $listItem->age);
        $this->assertSame('Онкология',                                                          $listItem->causeOfDeathName);
        $this->assertSame(null,                                                                 $listItem->deathCertificateSeries);
        $this->assertSame(null,                                                                 $listItem->deathCertificateNumber);
        $this->assertSame(null,                                                                 $listItem->deathCertificateIssuedAt);
        $this->assertSame(null,                                                                 $listItem->cremationCertificateNumber);
        $this->assertSame(null,                                                                 $listItem->cremationCertificateIssuedAt);
    }

    private function assertSimpleListItemEqualsNP004(NaturalPersonSimpleListItem $listItem): void
    {
        $this->assertSame('NP004',                   $listItem->id);
        $this->assertSame('Соколов Герман Маркович', $listItem->fullName);
        $this->assertSame(null,                      $listItem->bornAt);
        $this->assertSame('26.01.2010',              $listItem->diedAt);
    }

    private function assertPaginatedListItemEqualsNP005(NaturalPersonPaginatedListItem $listItem): void
    {
        $this->assertSame('NP005',                                     $listItem->id);
        $this->assertSame('Жданова Инга Григорьевна',                  $listItem->fullName);
        $this->assertSame('Новосибирск, Ленина 1',                     $listItem->address);
        $this->assertSame('8-913-771-22-33',                           $listItem->phone);
        $this->assertSame(null,                                        $listItem->email);
        $this->assertSame('12.02.1980',                                $listItem->bornAt);
        $this->assertSame(null,                                        $listItem->placeOfBirth);
        $this->assertSame('1234',                                      $listItem->passportSeries);
        $this->assertSame('567890',                                    $listItem->passportNumber);
        $this->assertSame('28.10.2002',                                $listItem->passportIssuedAt);
        $this->assertSame('УВД Кировского района города Новосибирска', $listItem->passportIssuedBy);
        $this->assertSame('540-001',                                   $listItem->passportDivisionCode);
        $this->assertSame('10.03.2022',                                $listItem->diedAt);
        $this->assertSame(42,                                          $listItem->age);
        $this->assertSame(null,                                        $listItem->causeOfDeathName);
        $this->assertSame(null,                                        $listItem->deathCertificateSeries);
        $this->assertSame(null,                                        $listItem->deathCertificateNumber);
        $this->assertSame(null,                                        $listItem->deathCertificateIssuedAt);
        $this->assertSame(null,                                        $listItem->cremationCertificateNumber);
        $this->assertSame(null,                                        $listItem->cremationCertificateIssuedAt);
    }

    private function assertSimpleListItemEqualsNP005(NaturalPersonSimpleListItem $listItem): void
    {
        $this->assertSame('NP005',                    $listItem->id);
        $this->assertSame('Жданова Инга Григорьевна', $listItem->fullName);
        $this->assertSame('12.02.1980',               $listItem->bornAt);
        $this->assertSame('10.03.2022',               $listItem->diedAt);
    }

    private function assertPaginatedListItemEqualsNP006(NaturalPersonPaginatedListItem $listItem): void
    {
        $this->assertSame('NP006',                       $listItem->id);
        $this->assertSame('Гришина Устинья Ярославовна', $listItem->fullName);
        $this->assertSame(null,                          $listItem->address);
        $this->assertSame(null,                          $listItem->phone);
        $this->assertSame(null,                          $listItem->email);
        $this->assertSame(null,                          $listItem->bornAt);
        $this->assertSame(null,                          $listItem->placeOfBirth);
        $this->assertSame(null,                          $listItem->passportSeries);
        $this->assertSame(null,                          $listItem->passportNumber);
        $this->assertSame(null,                          $listItem->passportIssuedAt);
        $this->assertSame(null,                          $listItem->passportIssuedBy);
        $this->assertSame(null,                          $listItem->passportDivisionCode);
        $this->assertSame('03.12.2021',                  $listItem->diedAt);
        $this->assertSame(null,                          $listItem->age);
        $this->assertSame(null,                          $listItem->causeOfDeathName);
        $this->assertSame(null,                          $listItem->deathCertificateSeries);
        $this->assertSame(null,                          $listItem->deathCertificateNumber);
        $this->assertSame(null,                          $listItem->deathCertificateIssuedAt);
        $this->assertSame(null,                          $listItem->cremationCertificateNumber);
        $this->assertSame(null,                          $listItem->cremationCertificateIssuedAt);
    }

    private function assertSimpleListItemEqualsNP006(NaturalPersonSimpleListItem $listItem): void
    {
        $this->assertSame('NP006',                       $listItem->id);
        $this->assertSame('Гришина Устинья Ярославовна', $listItem->fullName);
        $this->assertSame(null,                          $listItem->bornAt);
        $this->assertSame('03.12.2021',                  $listItem->diedAt);
    }

    private function assertPaginatedListItemEqualsNP007(NaturalPersonPaginatedListItem $listItem): void
    {
        $this->assertSame('NP007',                            $listItem->id);
        $this->assertSame('Громов Никифор Рудольфович',       $listItem->fullName);
        $this->assertSame('Новосибирск, Н.-Данченко 18 - 17', $listItem->address);
        $this->assertSame(null,                               $listItem->phone);
        $this->assertSame(null,                               $listItem->email);
        $this->assertSame('24.09.1915',                       $listItem->bornAt);
        $this->assertSame(null,                               $listItem->placeOfBirth);
        $this->assertSame(null,                               $listItem->passportSeries);
        $this->assertSame(null,                               $listItem->passportNumber);
        $this->assertSame(null,                               $listItem->passportIssuedAt);
        $this->assertSame(null,                               $listItem->passportIssuedBy);
        $this->assertSame(null,                               $listItem->passportDivisionCode);
        $this->assertSame(null,                               $listItem->diedAt);
        $this->assertSame(null,                               $listItem->age);
        $this->assertSame(null,                               $listItem->causeOfDeathName);
        $this->assertSame(null,                               $listItem->deathCertificateSeries);
        $this->assertSame(null,                               $listItem->deathCertificateNumber);
        $this->assertSame(null,                               $listItem->deathCertificateIssuedAt);
        $this->assertSame(null,                               $listItem->cremationCertificateNumber);
        $this->assertSame(null,                               $listItem->cremationCertificateIssuedAt);
    }

    private function assertSimpleListItemEqualsNP007(NaturalPersonSimpleListItem $listItem): void
    {
        $this->assertSame('NP007',                      $listItem->id);
        $this->assertSame('Громов Никифор Рудольфович', $listItem->fullName);
        $this->assertSame('24.09.1915',                 $listItem->bornAt);
        $this->assertSame(null,                         $listItem->diedAt);
    }

    private function assertPaginatedListItemEqualsNP008(NaturalPersonPaginatedListItem $listItem): void
    {
        $this->assertSame('NP008',                                  $listItem->id);
        $this->assertSame('Беляев Мечеслав Федорович',              $listItem->fullName);
        $this->assertSame(null,                                     $listItem->address);
        $this->assertSame(null,                                     $listItem->phone);
        $this->assertSame('mecheslav.belyaev@gmail.com',            $listItem->email);
        $this->assertSame(null,                                     $listItem->bornAt);
        $this->assertSame(null,                                     $listItem->placeOfBirth);
        $this->assertSame('2345',                                   $listItem->passportSeries);
        $this->assertSame('162354',                                 $listItem->passportNumber);
        $this->assertSame('20.10.1981',                             $listItem->passportIssuedAt);
        $this->assertSame('Отделом МВД Ленинского района г. Пензы', $listItem->passportIssuedBy);
        $this->assertSame(null,                                     $listItem->passportDivisionCode);
        $this->assertSame(null,                                     $listItem->diedAt);
        $this->assertSame(null,                                     $listItem->age);
        $this->assertSame(null,                                     $listItem->causeOfDeathName);
        $this->assertSame(null,                                     $listItem->deathCertificateSeries);
        $this->assertSame(null,                                     $listItem->deathCertificateNumber);
        $this->assertSame(null,                                     $listItem->deathCertificateIssuedAt);
        $this->assertSame(null,                                     $listItem->cremationCertificateNumber);
        $this->assertSame(null,                                     $listItem->cremationCertificateIssuedAt);
    }

    private function assertSimpleListItemEqualsNP008(NaturalPersonSimpleListItem $listItem): void
    {
        $this->assertSame('NP008',                     $listItem->id);
        $this->assertSame('Беляев Мечеслав Федорович', $listItem->fullName);
        $this->assertSame(null,                        $listItem->bornAt);
        $this->assertSame(null,                        $listItem->diedAt);
    }

    private function assertPaginatedListItemEqualsNP009(NaturalPersonPaginatedListItem $listItem): void
    {
        $this->assertSame('NP009',                       $listItem->id);
        $this->assertSame('Никонов Родион Митрофанович', $listItem->fullName);
        $this->assertSame(null,                          $listItem->address);
        $this->assertSame(null,                          $listItem->phone);
        $this->assertSame(null,                          $listItem->email);
        $this->assertSame(null,                          $listItem->bornAt);
        $this->assertSame(null,                          $listItem->placeOfBirth);
        $this->assertSame(null,                          $listItem->passportSeries);
        $this->assertSame(null,                          $listItem->passportNumber);
        $this->assertSame(null,                          $listItem->passportIssuedAt);
        $this->assertSame(null,                          $listItem->passportIssuedBy);
        $this->assertSame(null,                          $listItem->passportDivisionCode);
        $this->assertSame('26.05.1980',                  $listItem->diedAt);
        $this->assertSame(null,                          $listItem->age);
        $this->assertSame(null,                          $listItem->causeOfDeathName);
        $this->assertSame(null,                          $listItem->deathCertificateSeries);
        $this->assertSame(null,                          $listItem->deathCertificateNumber);
        $this->assertSame(null,                          $listItem->deathCertificateIssuedAt);
        $this->assertSame(null,                          $listItem->cremationCertificateNumber);
        $this->assertSame(null,                          $listItem->cremationCertificateIssuedAt);
    }

    private function assertSimpleListItemEqualsNP009(NaturalPersonSimpleListItem $listItem): void
    {
        $this->assertSame('NP009',                       $listItem->id);
        $this->assertSame('Никонов Родион Митрофанович', $listItem->fullName);
        $this->assertSame(null,                          $listItem->bornAt);
        $this->assertSame('26.05.1980',                  $listItem->diedAt);
    }

    private function assertPaginatedListItemEqualsNP010(NaturalPersonPaginatedListItem $listItem): void
    {
        $this->assertSame('NP010',                $listItem->id);
        $this->assertSame('Иванов Иван Иванович', $listItem->fullName);
        $this->assertSame(null,                   $listItem->address);
        $this->assertSame(null,                   $listItem->phone);
        $this->assertSame(null,                   $listItem->email);
        $this->assertSame('04.11.1930',           $listItem->bornAt);
        $this->assertSame(null,                   $listItem->placeOfBirth);
        $this->assertSame(null,                   $listItem->passportSeries);
        $this->assertSame(null,                   $listItem->passportNumber);
        $this->assertSame(null,                   $listItem->passportIssuedAt);
        $this->assertSame(null,                   $listItem->passportIssuedBy);
        $this->assertSame(null,                   $listItem->passportDivisionCode);
        $this->assertSame('22.11.2002',           $listItem->diedAt);
        $this->assertSame(72,                     $listItem->age);
        $this->assertSame(null,                   $listItem->causeOfDeathName);
        $this->assertSame(null,                   $listItem->deathCertificateSeries);
        $this->assertSame(null,                   $listItem->deathCertificateNumber);
        $this->assertSame(null,                   $listItem->deathCertificateIssuedAt);
        $this->assertSame(null,                   $listItem->cremationCertificateNumber);
        $this->assertSame(null,                   $listItem->cremationCertificateIssuedAt);
    }

    private function assertSimpleListItemEqualsNP010(NaturalPersonSimpleListItem $listItem): void
    {
        $this->assertSame('NP010',                $listItem->id);
        $this->assertSame('Иванов Иван Иванович', $listItem->fullName);
        $this->assertSame('04.11.1930',           $listItem->bornAt);
        $this->assertSame('22.11.2002',           $listItem->diedAt);
    }

    private function assertPaginatedListItemEqualsNP011(NaturalPersonPaginatedListItem $listItem): void
    {
        $this->assertSame('NP011',                $listItem->id);
        $this->assertSame('Иванов Иван Иванович', $listItem->fullName);
        $this->assertSame(null,                   $listItem->address);
        $this->assertSame(null,                   $listItem->phone);
        $this->assertSame(null,                   $listItem->email);
        $this->assertSame('12.04.1925',           $listItem->bornAt);
        $this->assertSame(null,                   $listItem->placeOfBirth);
        $this->assertSame(null,                   $listItem->passportSeries);
        $this->assertSame(null,                   $listItem->passportNumber);
        $this->assertSame(null,                   $listItem->passportIssuedAt);
        $this->assertSame(null,                   $listItem->passportIssuedBy);
        $this->assertSame(null,                   $listItem->passportDivisionCode);
        $this->assertSame('11.05.2004',           $listItem->diedAt);
        $this->assertSame(79,                     $listItem->age);
        $this->assertSame(null,                   $listItem->causeOfDeathName);
        $this->assertSame(null,                   $listItem->deathCertificateSeries);
        $this->assertSame(null,                   $listItem->deathCertificateNumber);
        $this->assertSame(null,                   $listItem->deathCertificateIssuedAt);
        $this->assertSame(null,                   $listItem->cremationCertificateNumber);
        $this->assertSame(null,                   $listItem->cremationCertificateIssuedAt);
    }

    private function assertSimpleListItemEqualsNP011(NaturalPersonSimpleListItem $listItem): void
    {
        $this->assertSame('NP011',                $listItem->id);
        $this->assertSame('Иванов Иван Иванович', $listItem->fullName);
        $this->assertSame('12.04.1925',           $listItem->bornAt);
        $this->assertSame('11.05.2004',           $listItem->diedAt);
    }

    private function assertPaginatedListItemEqualsNP012(NaturalPersonPaginatedListItem $listItem): void
    {
        $this->assertSame('NP012',                $listItem->id);
        $this->assertSame('Иванов Иван Иванович', $listItem->fullName);
        $this->assertSame(null,                   $listItem->address);
        $this->assertSame(null,                   $listItem->phone);
        $this->assertSame(null,                   $listItem->email);
        $this->assertSame('12.04.1925',           $listItem->bornAt);
        $this->assertSame(null,                   $listItem->placeOfBirth);
        $this->assertSame(null,                   $listItem->passportSeries);
        $this->assertSame(null,                   $listItem->passportNumber);
        $this->assertSame(null,                   $listItem->passportIssuedAt);
        $this->assertSame(null,                   $listItem->passportIssuedBy);
        $this->assertSame(null,                   $listItem->passportDivisionCode);
        $this->assertSame('29.10.2005',           $listItem->diedAt);
        $this->assertSame(80,                     $listItem->age);
        $this->assertSame(null,                   $listItem->causeOfDeathName);
        $this->assertSame(null,                   $listItem->deathCertificateSeries);
        $this->assertSame(null,                   $listItem->deathCertificateNumber);
        $this->assertSame(null,                   $listItem->deathCertificateIssuedAt);
        $this->assertSame(null,                   $listItem->cremationCertificateNumber);
        $this->assertSame(null,                   $listItem->cremationCertificateIssuedAt);
    }

    private function assertSimpleListItemEqualsNP012(NaturalPersonSimpleListItem $listItem): void
    {
        $this->assertSame('NP012',                $listItem->id);
        $this->assertSame('Иванов Иван Иванович', $listItem->fullName);
        $this->assertSame('12.04.1925',           $listItem->bornAt);
        $this->assertSame('29.10.2005',           $listItem->diedAt);
    }

    private function assertPaginatedListItemEqualsNP013(NaturalPersonPaginatedListItem $listItem): void
    {
        $this->assertSame('NP013',                $listItem->id);
        $this->assertSame('Петров Пётр Петрович', $listItem->fullName);
        $this->assertSame(null,                   $listItem->address);
        $this->assertSame(null,                   $listItem->phone);
        $this->assertSame(null,                   $listItem->email);
        $this->assertSame(null,                   $listItem->bornAt);
        $this->assertSame(null,                   $listItem->placeOfBirth);
        $this->assertSame(null,                   $listItem->passportSeries);
        $this->assertSame(null,                   $listItem->passportNumber);
        $this->assertSame(null,                   $listItem->passportIssuedAt);
        $this->assertSame(null,                   $listItem->passportIssuedBy);
        $this->assertSame(null,                   $listItem->passportDivisionCode);
        $this->assertSame(null,                   $listItem->diedAt);
        $this->assertSame(null,                   $listItem->age);
        $this->assertSame(null,                   $listItem->causeOfDeathName);
        $this->assertSame(null,                   $listItem->deathCertificateSeries);
        $this->assertSame(null,                   $listItem->deathCertificateNumber);
        $this->assertSame(null,                   $listItem->deathCertificateIssuedAt);
        $this->assertSame(null,                   $listItem->cremationCertificateNumber);
        $this->assertSame(null,                   $listItem->cremationCertificateIssuedAt);
    }

    private function assertSimpleListItemEqualsNP013(NaturalPersonSimpleListItem $listItem): void
    {
        $this->assertSame('NP013',                $listItem->id);
        $this->assertSame('Петров Пётр Петрович', $listItem->fullName);
        $this->assertSame(null,                   $listItem->bornAt);
        $this->assertSame(null,                   $listItem->diedAt);
    }

    private function testItReturnsNaturalPersonViewForNP001(): void
    {
        $view = $this->fetcher->findViewById('NP001');
        $this->assertInstanceOf(NaturalPersonView::class, $view);
        $this->assertSame('NP001',                   $view->id);
        $this->assertSame('Егоров Абрам Даниилович', $view->fullName);
        $this->assertSame(null,                      $view->phone);
        $this->assertSame(null,                      $view->phoneAdditional);
        $this->assertSame(null,                      $view->address);
        $this->assertSame(null,                      $view->email);
        $this->assertSame(null,                      $view->bornAt);
        $this->assertSame(null,                      $view->placeOfBirth);
        $this->assertSame(null,                      $view->passportSeries);
        $this->assertSame(null,                      $view->passportNumber);
        $this->assertSame(null,                      $view->passportIssuedAt);
        $this->assertSame(null,                      $view->passportIssuedBy);
        $this->assertSame(null,                      $view->passportDivisionCode);
        $this->assertSame('01.12.2021',              $view->diedAt);
        $this->assertSame(69,                        $view->age);
        $this->assertSame(null,                      $view->causeOfDeathId);
        $this->assertSame(null,                      $view->causeOfDeathName);
        $this->assertSame(null,                      $view->deathCertificateSeries);
        $this->assertSame(null,                      $view->deathCertificateNumber);
        $this->assertSame(null,                      $view->deathCertificateIssuedAt);
        $this->assertSame('12964',                   $view->cremationCertificateNumber);
        $this->assertSame('03.12.2021',              $view->cremationCertificateIssuedAt);
        $this->assertValidDateTimeValue($view->createdAt);
        $this->assertValidDateTimeValue($view->updatedAt);
    }

    private function testItReturnsNaturalPersonViewForNP002(): void
    {
        $view = $this->fetcher->findViewById('NP002');
        $this->assertInstanceOf(NaturalPersonView::class, $view);
        $this->assertSame('NP002',                                 $view->id);
        $this->assertSame('Устинов Иван Максович',                 $view->fullName);
        $this->assertSame(null,                                    $view->phone);
        $this->assertSame(null,                                    $view->phoneAdditional);
        $this->assertSame(null,                                    $view->address);
        $this->assertSame(null,                                    $view->email);
        $this->assertSame('30.12.1918',                            $view->bornAt);
        $this->assertSame(null,                                    $view->placeOfBirth);
        $this->assertSame(null,                                    $view->passportSeries);
        $this->assertSame(null,                                    $view->passportNumber);
        $this->assertSame(null,                                    $view->passportIssuedAt);
        $this->assertSame(null,                                    $view->passportIssuedBy);
        $this->assertSame(null,                                    $view->passportDivisionCode);
        $this->assertSame('12.02.2001',                            $view->diedAt);
        $this->assertSame(82,                                      $view->age);
        $this->assertSame('CD008',                                 $view->causeOfDeathId);
        $this->assertSame('Болезнь сердечно-легочная хроническая', $view->causeOfDeathName);
        $this->assertSame('V-МЮ',                                  $view->deathCertificateSeries);
        $this->assertSame('532515',                                $view->deathCertificateNumber);
        $this->assertSame('15.02.2001',                            $view->deathCertificateIssuedAt);
        $this->assertSame(null,                                    $view->cremationCertificateNumber);
        $this->assertSame(null,                                    $view->cremationCertificateIssuedAt);
        $this->assertValidDateTimeValue($view->createdAt);
        $this->assertValidDateTimeValue($view->updatedAt);
    }

    private function testItReturnsNaturalPersonViewForNP003(): void
    {
        $view = $this->fetcher->findViewById('NP003');
        $this->assertInstanceOf(NaturalPersonView::class, $view);
        $this->assertSame('NP003',                             $view->id);
        $this->assertSame('Шилов Александр Михаилович',        $view->fullName);
        $this->assertSame(null,                                $view->phone);
        $this->assertSame(null,                                $view->phoneAdditional);
        $this->assertSame(null,                                $view->address);
        $this->assertSame(null,                                $view->email);
        $this->assertSame('20.05.1969',                        $view->bornAt);
        $this->assertSame(null,                                $view->placeOfBirth);
        $this->assertSame('4581',                              $view->passportSeries);
        $this->assertSame('684214',                            $view->passportNumber);
        $this->assertSame('23.03.2001',                        $view->passportIssuedAt);
        $this->assertSame('МВД России по Кемеровской области', $view->passportIssuedBy);
        $this->assertSame('681-225',                           $view->passportDivisionCode);
        $this->assertSame('13.05.2012',                        $view->diedAt);
        $this->assertSame(42,                                  $view->age);
        $this->assertSame('CD004',                             $view->causeOfDeathId);
        $this->assertSame('Онкология',                         $view->causeOfDeathName);
        $this->assertSame('I-BC',                              $view->deathCertificateSeries);
        $this->assertSame('785066',                            $view->deathCertificateNumber);
        $this->assertSame('23.03.2011',                        $view->deathCertificateIssuedAt);
        $this->assertSame(null,                                $view->cremationCertificateNumber);
        $this->assertSame(null,                                $view->cremationCertificateIssuedAt);
        $this->assertValidDateTimeValue($view->createdAt);
        $this->assertValidDateTimeValue($view->updatedAt);
    }

    private function testItReturnsNaturalPersonViewForNP004(): void
    {
        $view = $this->fetcher->findViewById('NP004');
        $this->assertInstanceOf(NaturalPersonView::class, $view);
        $this->assertSame('NP004',                                                              $view->id);
        $this->assertSame('Соколов Герман Маркович',                                            $view->fullName);
        $this->assertSame(null,                                                                 $view->phone);
        $this->assertSame(null,                                                                 $view->phoneAdditional);
        $this->assertSame(null,                                                                 $view->address);
        $this->assertSame(null,                                                                 $view->email);
        $this->assertSame(null,                                                                 $view->bornAt);
        $this->assertSame(null,                                                                 $view->placeOfBirth);
        $this->assertSame('1235',                                                               $view->passportSeries);
        $this->assertSame('567891',                                                             $view->passportNumber);
        $this->assertSame('23.02.2001',                                                         $view->passportIssuedAt);
        $this->assertSame('Отделом УФМС России по Новосибирской области в Заельцовском районе', $view->passportIssuedBy);
        $this->assertSame('541-001',                                                            $view->passportDivisionCode);
        $this->assertSame('26.01.2010',                                                         $view->diedAt);
        $this->assertSame(null,                                                                 $view->age);
        $this->assertSame('CD004',                                                              $view->causeOfDeathId);
        $this->assertSame('Онкология',                                                          $view->causeOfDeathName);
        $this->assertSame(null,                                                                 $view->deathCertificateSeries);
        $this->assertSame(null,                                                                 $view->deathCertificateNumber);
        $this->assertSame(null,                                                                 $view->deathCertificateIssuedAt);
        $this->assertSame(null,                                                                 $view->cremationCertificateNumber);
        $this->assertSame(null,                                                                 $view->cremationCertificateIssuedAt);
        $this->assertValidDateTimeValue($view->createdAt);
        $this->assertValidDateTimeValue($view->updatedAt);
    }

    private function testItReturnsNaturalPersonViewForNP005(): void
    {
        $view = $this->fetcher->findViewById('NP005');
        $this->assertInstanceOf(NaturalPersonView::class, $view);
        $this->assertSame('NP005',                                     $view->id);
        $this->assertSame('Жданова Инга Григорьевна',                  $view->fullName);
        $this->assertSame('8-913-771-22-33',                           $view->phone);
        $this->assertSame(null,                                        $view->phoneAdditional);
        $this->assertSame('Новосибирск, Ленина 1',                     $view->address);
        $this->assertSame(null,                                        $view->email);
        $this->assertSame('12.02.1980',                                $view->bornAt);
        $this->assertSame(null,                                        $view->placeOfBirth);
        $this->assertSame('1234',                                      $view->passportSeries);
        $this->assertSame('567890',                                    $view->passportNumber);
        $this->assertSame('28.10.2002',                                $view->passportIssuedAt);
        $this->assertSame('УВД Кировского района города Новосибирска', $view->passportIssuedBy);
        $this->assertSame('540-001',                                   $view->passportDivisionCode);
        $this->assertSame('10.03.2022',                                $view->diedAt);
        $this->assertSame(42,                                          $view->age);
        $this->assertSame(null,                                        $view->causeOfDeathId);
        $this->assertSame(null,                                        $view->causeOfDeathName);
        $this->assertSame(null,                                        $view->deathCertificateSeries);
        $this->assertSame(null,                                        $view->deathCertificateNumber);
        $this->assertSame(null,                                        $view->deathCertificateIssuedAt);
        $this->assertSame(null,                                        $view->cremationCertificateNumber);
        $this->assertSame(null,                                        $view->cremationCertificateIssuedAt);
        $this->assertValidDateTimeValue($view->createdAt);
        $this->assertValidDateTimeValue($view->updatedAt);
    }

    private function testItReturnsNaturalPersonViewForNP006(): void
    {
        $view = $this->fetcher->findViewById('NP006');
        $this->assertInstanceOf(NaturalPersonView::class, $view);
        $this->assertSame('NP006',                       $view->id);
        $this->assertSame('Гришина Устинья Ярославовна', $view->fullName);
        $this->assertSame(null,                          $view->phone);
        $this->assertSame(null,                          $view->phoneAdditional);
        $this->assertSame(null,                          $view->address);
        $this->assertSame(null,                          $view->email);
        $this->assertSame(null,                          $view->bornAt);
        $this->assertSame(null,                          $view->placeOfBirth);
        $this->assertSame(null,                          $view->passportSeries);
        $this->assertSame(null,                          $view->passportNumber);
        $this->assertSame(null,                          $view->passportIssuedAt);
        $this->assertSame(null,                          $view->passportIssuedBy);
        $this->assertSame(null,                          $view->passportDivisionCode);
        $this->assertSame('03.12.2021',                  $view->diedAt);
        $this->assertSame(null,                          $view->age);
        $this->assertSame(null,                          $view->causeOfDeathId);
        $this->assertSame(null,                          $view->causeOfDeathName);
        $this->assertSame(null,                          $view->deathCertificateSeries);
        $this->assertSame(null,                          $view->deathCertificateNumber);
        $this->assertSame(null,                          $view->deathCertificateIssuedAt);
        $this->assertSame(null,                          $view->cremationCertificateNumber);
        $this->assertSame(null,                          $view->cremationCertificateIssuedAt);
        $this->assertValidDateTimeValue($view->createdAt);
        $this->assertValidDateTimeValue($view->updatedAt);
    }

    private function testItReturnsNaturalPersonViewForNP007(): void
    {
        $view = $this->fetcher->findViewById('NP007');
        $this->assertInstanceOf(NaturalPersonView::class, $view);
        $this->assertSame('NP007',                            $view->id);
        $this->assertSame('Громов Никифор Рудольфович',       $view->fullName);
        $this->assertSame(null,                               $view->phone);
        $this->assertSame(null,                               $view->phoneAdditional);
        $this->assertSame('Новосибирск, Н.-Данченко 18 - 17', $view->address);
        $this->assertSame(null,                               $view->email);
        $this->assertSame('24.09.1915',                       $view->bornAt);
        $this->assertSame(null,                               $view->placeOfBirth);
        $this->assertSame(null,                               $view->passportSeries);
        $this->assertSame(null,                               $view->passportNumber);
        $this->assertSame(null,                               $view->passportIssuedAt);
        $this->assertSame(null,                               $view->passportIssuedBy);
        $this->assertSame(null,                               $view->passportDivisionCode);
        $this->assertSame(null,                               $view->diedAt);
        $this->assertSame(null,                               $view->age);
        $this->assertSame(null,                               $view->causeOfDeathId);
        $this->assertSame(null,                               $view->causeOfDeathName);
        $this->assertSame(null,                               $view->deathCertificateSeries);
        $this->assertSame(null,                               $view->deathCertificateNumber);
        $this->assertSame(null,                               $view->deathCertificateIssuedAt);
        $this->assertSame(null,                               $view->cremationCertificateNumber);
        $this->assertSame(null,                               $view->cremationCertificateIssuedAt);
        $this->assertValidDateTimeValue($view->createdAt);
        $this->assertValidDateTimeValue($view->updatedAt);
    }

    private function testItReturnsNaturalPersonViewForNP008(): void
    {
        $view = $this->fetcher->findViewById('NP008');
        $this->assertInstanceOf(NaturalPersonView::class, $view);
        $this->assertSame('NP008',                                  $view->id);
        $this->assertSame('Беляев Мечеслав Федорович',              $view->fullName);
        $this->assertSame(null,                                     $view->phone);
        $this->assertSame(null,                                     $view->phoneAdditional);
        $this->assertSame(null,                                     $view->address);
        $this->assertSame('mecheslav.belyaev@gmail.com',            $view->email);
        $this->assertSame(null,                                     $view->bornAt);
        $this->assertSame(null,                                     $view->placeOfBirth);
        $this->assertSame('2345',                                   $view->passportSeries);
        $this->assertSame('162354',                                 $view->passportNumber);
        $this->assertSame('20.10.1981',                             $view->passportIssuedAt);
        $this->assertSame('Отделом МВД Ленинского района г. Пензы', $view->passportIssuedBy);
        $this->assertSame(null,                                     $view->passportDivisionCode);
        $this->assertSame(null,                                     $view->diedAt);
        $this->assertSame(null,                                     $view->age);
        $this->assertSame(null,                                     $view->causeOfDeathId);
        $this->assertSame(null,                                     $view->causeOfDeathName);
        $this->assertSame(null,                                     $view->deathCertificateSeries);
        $this->assertSame(null,                                     $view->deathCertificateNumber);
        $this->assertSame(null,                                     $view->deathCertificateIssuedAt);
        $this->assertSame(null,                                     $view->cremationCertificateNumber);
        $this->assertSame(null,                                     $view->cremationCertificateIssuedAt);
        $this->assertValidDateTimeValue($view->createdAt);
        $this->assertValidDateTimeValue($view->updatedAt);
    }

    private function testItReturnsNaturalPersonViewForNP009(): void
    {
        $view = $this->fetcher->findViewById('NP009');
        $this->assertInstanceOf(NaturalPersonView::class, $view);
        $this->assertSame('NP009',                       $view->id);
        $this->assertSame('Никонов Родион Митрофанович', $view->fullName);
        $this->assertSame(null,                          $view->phone);
        $this->assertSame(null,                          $view->phoneAdditional);
        $this->assertSame(null,                          $view->address);
        $this->assertSame(null,                          $view->email);
        $this->assertSame(null,                          $view->bornAt);
        $this->assertSame(null,                          $view->placeOfBirth);
        $this->assertSame(null,                          $view->passportSeries);
        $this->assertSame(null,                          $view->passportNumber);
        $this->assertSame(null,                          $view->passportIssuedAt);
        $this->assertSame(null,                          $view->passportIssuedBy);
        $this->assertSame(null,                          $view->passportDivisionCode);
        $this->assertSame('26.05.1980',                  $view->diedAt);
        $this->assertSame(null,                          $view->age);
        $this->assertSame(null,                          $view->causeOfDeathId);
        $this->assertSame(null,                          $view->causeOfDeathName);
        $this->assertSame(null,                          $view->deathCertificateSeries);
        $this->assertSame(null,                          $view->deathCertificateNumber);
        $this->assertSame(null,                          $view->deathCertificateIssuedAt);
        $this->assertSame(null,                          $view->cremationCertificateNumber);
        $this->assertSame(null,                          $view->cremationCertificateIssuedAt);
        $this->assertValidDateTimeValue($view->createdAt);
        $this->assertValidDateTimeValue($view->updatedAt);
    }

    private function testItReturnsNaturalPersonViewForNP010(): void
    {
        $view = $this->fetcher->findViewById('NP010');
        $this->assertInstanceOf(NaturalPersonView::class, $view);
        $this->assertSame('NP010',                $view->id);
        $this->assertSame('Иванов Иван Иванович', $view->fullName);
        $this->assertSame(null,                   $view->phone);
        $this->assertSame(null,                   $view->phoneAdditional);
        $this->assertSame(null,                   $view->address);
        $this->assertSame(null,                   $view->email);
        $this->assertSame('04.11.1930',           $view->bornAt);
        $this->assertSame(null,                   $view->placeOfBirth);
        $this->assertSame(null,                   $view->passportSeries);
        $this->assertSame(null,                   $view->passportNumber);
        $this->assertSame(null,                   $view->passportIssuedAt);
        $this->assertSame(null,                   $view->passportIssuedBy);
        $this->assertSame(null,                   $view->passportDivisionCode);
        $this->assertSame('22.11.2002',           $view->diedAt);
        $this->assertSame(72,                     $view->age);
        $this->assertSame(null,                   $view->causeOfDeathId);
        $this->assertSame(null,                   $view->causeOfDeathName);
        $this->assertSame(null,                   $view->deathCertificateSeries);
        $this->assertSame(null,                   $view->deathCertificateNumber);
        $this->assertSame(null,                   $view->deathCertificateIssuedAt);
        $this->assertSame(null,                   $view->cremationCertificateNumber);
        $this->assertSame(null,                   $view->cremationCertificateIssuedAt);
        $this->assertValidDateTimeValue($view->createdAt);
        $this->assertValidDateTimeValue($view->updatedAt);
    }

    private function testItReturnsNaturalPersonViewForNP011(): void
    {
        $view = $this->fetcher->findViewById('NP011');
        $this->assertInstanceOf(NaturalPersonView::class, $view);
        $this->assertSame('NP011',                $view->id);
        $this->assertSame('Иванов Иван Иванович', $view->fullName);
        $this->assertSame(null,                   $view->phone);
        $this->assertSame(null,                   $view->phoneAdditional);
        $this->assertSame(null,                   $view->address);
        $this->assertSame(null,                   $view->email);
        $this->assertSame('12.04.1925',           $view->bornAt);
        $this->assertSame(null,                   $view->placeOfBirth);
        $this->assertSame(null,                   $view->passportSeries);
        $this->assertSame(null,                   $view->passportNumber);
        $this->assertSame(null,                   $view->passportIssuedAt);
        $this->assertSame(null,                   $view->passportIssuedBy);
        $this->assertSame(null,                   $view->passportDivisionCode);
        $this->assertSame('11.05.2004',           $view->diedAt);
        $this->assertSame(79,                     $view->age);
        $this->assertSame(null,                   $view->causeOfDeathId);
        $this->assertSame(null,                   $view->causeOfDeathName);
        $this->assertSame(null,                   $view->deathCertificateSeries);
        $this->assertSame(null,                   $view->deathCertificateNumber);
        $this->assertSame(null,                   $view->deathCertificateIssuedAt);
        $this->assertSame(null,                   $view->cremationCertificateNumber);
        $this->assertSame(null,                   $view->cremationCertificateIssuedAt);
        $this->assertValidDateTimeValue($view->createdAt);
        $this->assertValidDateTimeValue($view->updatedAt);
    }

    private function testItReturnsNaturalPersonViewForNP012(): void
    {
        $view = $this->fetcher->findViewById('NP012');
        $this->assertInstanceOf(NaturalPersonView::class, $view);
        $this->assertSame('NP012',                $view->id);
        $this->assertSame('Иванов Иван Иванович', $view->fullName);
        $this->assertSame(null,                   $view->phone);
        $this->assertSame(null,                   $view->phoneAdditional);
        $this->assertSame(null,                   $view->address);
        $this->assertSame(null,                   $view->email);
        $this->assertSame('12.04.1925',           $view->bornAt);
        $this->assertSame(null,                   $view->placeOfBirth);
        $this->assertSame(null,                   $view->passportSeries);
        $this->assertSame(null,                   $view->passportNumber);
        $this->assertSame(null,                   $view->passportIssuedAt);
        $this->assertSame(null,                   $view->passportIssuedBy);
        $this->assertSame(null,                   $view->passportDivisionCode);
        $this->assertSame('29.10.2005',           $view->diedAt);
        $this->assertSame(80,                     $view->age);
        $this->assertSame(null,                   $view->causeOfDeathId);
        $this->assertSame(null,                   $view->causeOfDeathName);
        $this->assertSame(null,                   $view->deathCertificateSeries);
        $this->assertSame(null,                   $view->deathCertificateNumber);
        $this->assertSame(null,                   $view->deathCertificateIssuedAt);
        $this->assertSame(null,                   $view->cremationCertificateNumber);
        $this->assertSame(null,                   $view->cremationCertificateIssuedAt);
        $this->assertValidDateTimeValue($view->createdAt);
        $this->assertValidDateTimeValue($view->updatedAt);
    }

    private function testItReturnsNaturalPersonViewForNP013(): void
    {
        $view = $this->fetcher->findViewById('NP013');
        $this->assertInstanceOf(NaturalPersonView::class, $view);
        $this->assertSame('NP013',                $view->id);
        $this->assertSame('Петров Пётр Петрович', $view->fullName);
        $this->assertSame(null,                   $view->phone);
        $this->assertSame(null,                   $view->phoneAdditional);
        $this->assertSame(null,                   $view->address);
        $this->assertSame(null,                   $view->email);
        $this->assertSame(null,                   $view->bornAt);
        $this->assertSame(null,                   $view->placeOfBirth);
        $this->assertSame(null,                   $view->passportSeries);
        $this->assertSame(null,                   $view->passportNumber);
        $this->assertSame(null,                   $view->passportIssuedAt);
        $this->assertSame(null,                   $view->passportIssuedBy);
        $this->assertSame(null,                   $view->passportDivisionCode);
        $this->assertSame(null,                   $view->diedAt);
        $this->assertSame(null,                   $view->age);
        $this->assertSame(null,                   $view->causeOfDeathId);
        $this->assertSame(null,                   $view->causeOfDeathName);
        $this->assertSame(null,                   $view->deathCertificateSeries);
        $this->assertSame(null,                   $view->deathCertificateNumber);
        $this->assertSame(null,                   $view->deathCertificateIssuedAt);
        $this->assertSame(null,                   $view->cremationCertificateNumber);
        $this->assertSame(null,                   $view->cremationCertificateIssuedAt);
        $this->assertValidDateTimeValue($view->createdAt);
        $this->assertValidDateTimeValue($view->updatedAt);
    }
}
