<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Fetcher;

use Cemetery\Registrar\Domain\Model\NaturalPerson\NaturalPersonId;
use Cemetery\Registrar\Domain\Model\NaturalPerson\NaturalPersonRepository;
use Cemetery\Registrar\Domain\View\NaturalPerson\NaturalPersonList;
use Cemetery\Registrar\Domain\View\NaturalPerson\NaturalPersonListItem;
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
        // TODO implement
        $this->markTestIncomplete();
    }

    public function testItReturnsNullForRemovedNaturalPerson(): void
    {
        // TODO implement
        $this->markTestIncomplete();
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

    public function testItReturnsNaturalPersonListByPage(): void
    {
        $customPageSize = 4;

        // First page
        $listForFirstPage = $this->fetcher->findAll(1, null, $customPageSize);
        $this->assertInstanceOf(NaturalPersonList::class, $listForFirstPage);
        $this->assertIsArray($listForFirstPage->items);
        $this->assertContainsOnlyInstancesOf(NaturalPersonListItem::class, $listForFirstPage->items);
        $this->assertCount(4,              $listForFirstPage->items);
        $this->assertSame(1,               $listForFirstPage->page);
        $this->assertSame($customPageSize, $listForFirstPage->pageSize);
        $this->assertSame(null,            $listForFirstPage->term);
        $this->assertSame(13,              $listForFirstPage->totalCount);
        $this->assertSame(4,               $listForFirstPage->totalPages);
        $this->assertListItemEqualsNP008($listForFirstPage->items[0]);  // Items are ordered by full name,
        $this->assertListItemEqualsNP006($listForFirstPage->items[1]);  // then by date of birth,
        $this->assertListItemEqualsNP007($listForFirstPage->items[2]);  // and finally by date of death.
        $this->assertListItemEqualsNP001($listForFirstPage->items[3]);

        // Second page
        $listForSecondPage = $this->fetcher->findAll(2, null, $customPageSize);
        $this->assertInstanceOf(NaturalPersonList::class, $listForSecondPage);
        $this->assertIsArray($listForSecondPage->items);
        $this->assertContainsOnlyInstancesOf(NaturalPersonListItem::class, $listForSecondPage->items);
        $this->assertCount(4,              $listForSecondPage->items);
        $this->assertSame(2,               $listForSecondPage->page);
        $this->assertSame($customPageSize, $listForSecondPage->pageSize);
        $this->assertSame(null,            $listForSecondPage->term);
        $this->assertSame(13,              $listForSecondPage->totalCount);
        $this->assertSame(4,               $listForSecondPage->totalPages);
        $this->assertListItemEqualsNP005($listForSecondPage->items[0]);
        $this->assertListItemEqualsNP011($listForSecondPage->items[1]);
        $this->assertListItemEqualsNP012($listForSecondPage->items[2]);
        $this->assertListItemEqualsNP010($listForSecondPage->items[3]);

        // Third page
        $listForThirdPage = $this->fetcher->findAll(3, null, $customPageSize);
        $this->assertInstanceOf(NaturalPersonList::class, $listForThirdPage);
        $this->assertIsArray($listForThirdPage->items);
        $this->assertContainsOnlyInstancesOf(NaturalPersonListItem::class, $listForThirdPage->items);
        $this->assertCount(4,              $listForThirdPage->items);
        $this->assertSame(3,               $listForThirdPage->page);
        $this->assertSame($customPageSize, $listForThirdPage->pageSize);
        $this->assertSame(null,            $listForThirdPage->term);
        $this->assertSame(13,              $listForThirdPage->totalCount);
        $this->assertSame(4,               $listForThirdPage->totalPages);
        $this->assertListItemEqualsNP009($listForThirdPage->items[0]);
        $this->assertListItemEqualsNP013($listForThirdPage->items[1]);
        $this->assertListItemEqualsNP004($listForThirdPage->items[2]);
        $this->assertListItemEqualsNP002($listForThirdPage->items[3]);

        // Fourth page
        $listForFourthPage = $this->fetcher->findAll(4, null, $customPageSize);
        $this->assertInstanceOf(NaturalPersonList::class, $listForFourthPage);
        $this->assertIsArray($listForFourthPage->items);
        $this->assertCount(1,              $listForFourthPage->items);
        $this->assertSame(4,               $listForFourthPage->page);
        $this->assertSame($customPageSize, $listForFourthPage->pageSize);
        $this->assertSame(null,            $listForFourthPage->term);
        $this->assertSame(13,              $listForFourthPage->totalCount);
        $this->assertSame(4,               $listForFourthPage->totalPages);
        $this->assertListItemEqualsNP003($listForFourthPage->items[0]);

        // Default page size
        $listForDefaultPageSize = $this->fetcher->findAll(1);
        $this->assertInstanceOf(NaturalPersonList::class, $listForDefaultPageSize);
        $this->assertIsArray($listForDefaultPageSize->items);
        $this->assertContainsOnlyInstancesOf(NaturalPersonListItem::class, $listForDefaultPageSize->items);
        $this->assertCount(13,                     $listForDefaultPageSize->items);
        $this->assertSame(1,                       $listForDefaultPageSize->page);
        $this->assertSame(self::DEFAULT_PAGE_SIZE, $listForDefaultPageSize->pageSize);
        $this->assertSame(null,                    $listForDefaultPageSize->term);
        $this->assertSame(13,                      $listForDefaultPageSize->totalCount);
        $this->assertSame(1,                       $listForDefaultPageSize->totalPages);
    }

    public function testItReturnsNaturalPersonListByPageAndTerm(): void
    {
        $customPageSize = 3;

        $list = $this->fetcher->findAll(1, 'ИваноВИч', $customPageSize);
        $this->assertInstanceOf(NaturalPersonList::class, $list);
        $this->assertIsArray($list->items);
        $this->assertContainsOnlyInstancesOf(NaturalPersonListItem::class, $list->items);
        $this->assertCount(3,              $list->items);
        $this->assertSame(1,               $list->page);
        $this->assertSame($customPageSize, $list->pageSize);
        $this->assertSame('ИваноВИч',         $list->term);
        $this->assertSame(3,               $list->totalCount);
        $this->assertSame(1,               $list->totalPages);

        $list = $this->fetcher->findAll(1, '12', $customPageSize);
        $this->assertInstanceOf(NaturalPersonList::class, $list);
        $this->assertIsArray($list->items);
        $this->assertContainsOnlyInstancesOf(NaturalPersonListItem::class, $list->items);
        $this->assertCount(3,              $list->items);
        $this->assertSame(1,               $list->page);
        $this->assertSame($customPageSize, $list->pageSize);
        $this->assertSame('12',            $list->term);
        $this->assertSame(8,               $list->totalCount);
        $this->assertSame(3,               $list->totalPages);
        $list = $this->fetcher->findAll(2, '12', $customPageSize);
        $this->assertInstanceOf(NaturalPersonList::class, $list);
        $this->assertIsArray($list->items);
        $this->assertContainsOnlyInstancesOf(NaturalPersonListItem::class, $list->items);
        $this->assertCount(3,              $list->items);
        $this->assertSame(2,               $list->page);
        $this->assertSame($customPageSize, $list->pageSize);
        $this->assertSame('12',            $list->term);
        $this->assertSame(8,               $list->totalCount);
        $this->assertSame(3,               $list->totalPages);
        $list = $this->fetcher->findAll(3, '12', $customPageSize);
        $this->assertInstanceOf(NaturalPersonList::class, $list);
        $this->assertIsArray($list->items);
        $this->assertContainsOnlyInstancesOf(NaturalPersonListItem::class, $list->items);
        $this->assertCount(2,              $list->items);
        $this->assertSame(3,               $list->page);
        $this->assertSame($customPageSize, $list->pageSize);
        $this->assertSame('12',            $list->term);
        $this->assertSame(8,               $list->totalCount);
        $this->assertSame(3,               $list->totalPages);

        $list = $this->fetcher->findAll(1, 'Новосиб', $customPageSize);
        $this->assertInstanceOf(NaturalPersonList::class, $list);
        $this->assertIsArray($list->items);
        $this->assertContainsOnlyInstancesOf(NaturalPersonListItem::class, $list->items);
        $this->assertCount(3,              $list->items);
        $this->assertSame(1,               $list->page);
        $this->assertSame($customPageSize, $list->pageSize);
        $this->assertSame('Новосиб',       $list->term);
        $this->assertSame(3,               $list->totalCount);
        $this->assertSame(1,               $list->totalPages);

        $list = $this->fetcher->findAll(1, 'Ленин', $customPageSize);
        $this->assertInstanceOf(NaturalPersonList::class, $list);
        $this->assertIsArray($list->items);
        $this->assertContainsOnlyInstancesOf(NaturalPersonListItem::class, $list->items);
        $this->assertCount(2,              $list->items);
        $this->assertSame(1,               $list->page);
        $this->assertSame($customPageSize, $list->pageSize);
        $this->assertSame('Ленин',         $list->term);
        $this->assertSame(2,               $list->totalCount);
        $this->assertSame(1,               $list->totalPages);

        $list = $this->fetcher->findAll(1, 'gmail.com', $customPageSize);
        $this->assertInstanceOf(NaturalPersonList::class, $list);
        $this->assertIsArray($list->items);
        $this->assertContainsOnlyInstancesOf(NaturalPersonListItem::class, $list->items);
        $this->assertCount(1,              $list->items);
        $this->assertSame(1,               $list->page);
        $this->assertSame($customPageSize, $list->pageSize);
        $this->assertSame('gmail.com',     $list->term);
        $this->assertSame(1,               $list->totalCount);
        $this->assertSame(1,               $list->totalPages);

        $list = $this->fetcher->findAll(1, '12964', $customPageSize);
        $this->assertInstanceOf(NaturalPersonList::class, $list);
        $this->assertIsArray($list->items);
        $this->assertContainsOnlyInstancesOf(NaturalPersonListItem::class, $list->items);
        $this->assertCount(1,              $list->items);
        $this->assertSame(1,               $list->page);
        $this->assertSame($customPageSize, $list->pageSize);
        $this->assertSame('12964',         $list->term);
        $this->assertSame(1,               $list->totalCount);
        $this->assertSame(1,               $list->totalPages);

        $list = $this->fetcher->findAll(1, 'V-МЮ', $customPageSize);
        $this->assertInstanceOf(NaturalPersonList::class, $list);
        $this->assertIsArray($list->items);
        $this->assertContainsOnlyInstancesOf(NaturalPersonListItem::class, $list->items);
        $this->assertCount(1,              $list->items);
        $this->assertSame(1,               $list->page);
        $this->assertSame($customPageSize, $list->pageSize);
        $this->assertSame('V-МЮ',          $list->term);
        $this->assertSame(1,               $list->totalCount);
        $this->assertSame(1,               $list->totalPages);

        $list = $this->fetcher->findAll(1, '03', $customPageSize);
        $this->assertInstanceOf(NaturalPersonList::class, $list);
        $this->assertIsArray($list->items);
        $this->assertContainsOnlyInstancesOf(NaturalPersonListItem::class, $list->items);
        $this->assertCount(3,              $list->items);
        $this->assertSame(1,               $list->page);
        $this->assertSame($customPageSize, $list->pageSize);
        $this->assertSame('03',            $list->term);
        $this->assertSame(4,               $list->totalCount);
        $this->assertSame(2,               $list->totalPages);
        $list = $this->fetcher->findAll(2, '03', $customPageSize);
        $this->assertInstanceOf(NaturalPersonList::class, $list);
        $this->assertIsArray($list->items);
        $this->assertContainsOnlyInstancesOf(NaturalPersonListItem::class, $list->items);
        $this->assertCount(1,              $list->items);
        $this->assertSame(2,               $list->page);
        $this->assertSame($customPageSize, $list->pageSize);
        $this->assertSame('03',            $list->term);
        $this->assertSame(4,               $list->totalCount);
        $this->assertSame(2,               $list->totalPages);

        $list = $this->fetcher->findAll(1, '69', $customPageSize);
        $this->assertInstanceOf(NaturalPersonList::class, $list);
        $this->assertIsArray($list->items);
        $this->assertContainsOnlyInstancesOf(NaturalPersonListItem::class, $list->items);
        $this->assertCount(2,              $list->items);
        $this->assertSame(1,               $list->page);
        $this->assertSame($customPageSize, $list->pageSize);
        $this->assertSame('69',            $list->term);
        $this->assertSame(2,               $list->totalCount);
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

    private function assertListItemEqualsNP001(NaturalPersonListItem $listItem): void
    {
        $this->assertSame('NP001',                   $listItem->id);
        $this->assertSame('Егоров Абрам Даниилович', $listItem->fullName);
        $this->assertSame(null,                      $listItem->address);
        $this->assertSame(null,                      $listItem->phone);
        $this->assertSame(null,                      $listItem->email);
        $this->assertSame(null,                      $listItem->bornAt);
        $this->assertSame(null,                      $listItem->placeOfBirth);
        $this->assertSame(null,                      $listItem->passport);
        $this->assertSame('01.12.2021',              $listItem->diedAt);
        $this->assertSame(69,                        $listItem->age);
        $this->assertSame(null,                      $listItem->causeOfDeathName);
        $this->assertSame(null,                      $listItem->deathCertificate);
        $this->assertSame('№ 12964 от 03.12.2021',   $listItem->cremationCertificate);
    }

    private function assertListItemEqualsNP002(NaturalPersonListItem $listItem): void
    {
        $this->assertSame('NP002',                                 $listItem->id);
        $this->assertSame('Устинов Арсений Максович',              $listItem->fullName);
        $this->assertSame(null,                                    $listItem->address);
        $this->assertSame(null,                                    $listItem->phone);
        $this->assertSame(null,                                    $listItem->email);
        $this->assertSame('30.12.1918',                            $listItem->bornAt);
        $this->assertSame(null,                                    $listItem->placeOfBirth);
        $this->assertSame(null,                                    $listItem->passport);
        $this->assertSame('12.02.2001',                            $listItem->diedAt);
        $this->assertSame(82,                                      $listItem->age);
        $this->assertSame('Болезнь сердечно-легочная хроническая', $listItem->causeOfDeathName);
        $this->assertSame('V-МЮ № 532515 от 15.02.2001',           $listItem->deathCertificate);
        $this->assertSame(null,                                    $listItem->cremationCertificate);
    }

    private function assertListItemEqualsNP003(NaturalPersonListItem $listItem): void
    {
        $this->assertSame('NP003',                       $listItem->id);
        $this->assertSame('Шилов Александр Михаилович',  $listItem->fullName);
        $this->assertSame(null,                          $listItem->address);
        $this->assertSame(null,                          $listItem->phone);
        $this->assertSame(null,                          $listItem->email);
        $this->assertSame('20.05.1969',                  $listItem->bornAt);
        $this->assertSame(null,                          $listItem->placeOfBirth);
        $this->assertSame(
            '4581 № 684214, выдан МВД России по Кемеровской области 23.03.2001 (681-225)',
            $listItem->passport
        );
        $this->assertSame('13.05.2012',                  $listItem->diedAt);
        $this->assertSame(42,                            $listItem->age);
        $this->assertSame('Онкология',                   $listItem->causeOfDeathName);
        $this->assertSame('I-BC № 785066 от 23.03.2011', $listItem->deathCertificate);
        $this->assertSame(null,                          $listItem->cremationCertificate);
    }

    private function assertListItemEqualsNP004(NaturalPersonListItem $listItem): void
    {
        $this->assertSame('NP004',                   $listItem->id);
        $this->assertSame('Соколов Герман Маркович', $listItem->fullName);
        $this->assertSame(null,                      $listItem->address);
        $this->assertSame(null,                      $listItem->phone);
        $this->assertSame(null,                      $listItem->email);
        $this->assertSame(null,                      $listItem->bornAt);
        $this->assertSame(null,                      $listItem->placeOfBirth);
        $this->assertSame(
            '1235 № 567891, выдан Отделом УФМС России по Новосибирской области в Заельцовском районе 23.02.2001 (541-001)',
            $listItem->passport
        );
        $this->assertSame('26.01.2010',              $listItem->diedAt);
        $this->assertSame(null,                      $listItem->age);
        $this->assertSame('Онкология',               $listItem->causeOfDeathName);
        $this->assertSame(null,                      $listItem->deathCertificate);
        $this->assertSame(null,                      $listItem->cremationCertificate);
    }

    private function assertListItemEqualsNP005(NaturalPersonListItem $listItem): void
    {
        $this->assertSame('NP005',                    $listItem->id);
        $this->assertSame('Жданова Инга Григорьевна', $listItem->fullName);
        $this->assertSame('Новосибирск, Ленина 1',    $listItem->address);
        $this->assertSame('8-913-771-22-33',          $listItem->phone);
        $this->assertSame(null,                       $listItem->email);
        $this->assertSame('12.02.1980',               $listItem->bornAt);
        $this->assertSame(null,                       $listItem->placeOfBirth);
        $this->assertSame(
            '1234 № 567890, выдан УВД Кировского района города Новосибирска 28.10.2002 (540-001)',
            $listItem->passport
        );
        $this->assertSame('10.03.2022',               $listItem->diedAt);
        $this->assertSame(42,                         $listItem->age);
        $this->assertSame(null,                       $listItem->causeOfDeathName);
        $this->assertSame(null,                       $listItem->deathCertificate);
        $this->assertSame(null,                       $listItem->cremationCertificate);
    }

    private function assertListItemEqualsNP006(NaturalPersonListItem $listItem): void
    {
        $this->assertSame('NP006',                       $listItem->id);
        $this->assertSame('Гришина Устинья Ярославовна', $listItem->fullName);
        $this->assertSame(null,                          $listItem->address);
        $this->assertSame(null,                          $listItem->phone);
        $this->assertSame(null,                          $listItem->email);
        $this->assertSame(null,                          $listItem->bornAt);
        $this->assertSame(null,                          $listItem->placeOfBirth);
        $this->assertSame(null,                          $listItem->passport);
        $this->assertSame('03.12.2021',                  $listItem->diedAt);
        $this->assertSame(null,                          $listItem->age);
        $this->assertSame(null,                          $listItem->causeOfDeathName);
        $this->assertSame(null,                          $listItem->deathCertificate);
        $this->assertSame(null,                          $listItem->cremationCertificate);
    }

    private function assertListItemEqualsNP007(NaturalPersonListItem $listItem): void
    {
        $this->assertSame('NP007',                            $listItem->id);
        $this->assertSame('Громов Никифор Рудольфович',       $listItem->fullName);
        $this->assertSame('Новосибирск, Н.-Данченко 18 - 17', $listItem->address);
        $this->assertSame(null,                               $listItem->phone);
        $this->assertSame(null,                               $listItem->email);
        $this->assertSame('24.09.1915',                       $listItem->bornAt);
        $this->assertSame(null,                               $listItem->placeOfBirth);
        $this->assertSame(null,                               $listItem->passport);
        $this->assertSame(null,                               $listItem->diedAt);
        $this->assertSame(null,                               $listItem->age);
        $this->assertSame(null,                               $listItem->causeOfDeathName);
        $this->assertSame(null,                               $listItem->deathCertificate);
        $this->assertSame(null,                               $listItem->cremationCertificate);
    }

    private function assertListItemEqualsNP008(NaturalPersonListItem $listItem): void
    {
        $this->assertSame('NP008',                       $listItem->id);
        $this->assertSame('Беляев Мечеслав Федорович',   $listItem->fullName);
        $this->assertSame(null,                          $listItem->address);
        $this->assertSame(null,                          $listItem->phone);
        $this->assertSame('mecheslav.belyaev@gmail.com', $listItem->email);
        $this->assertSame(null,                          $listItem->bornAt);
        $this->assertSame(null,                          $listItem->placeOfBirth);
        $this->assertSame(
            '2345 № 162354, выдан Отделом МВД Ленинского района г. Пензы 20.10.1981',
            $listItem->passport
        );
        $this->assertSame(null,                          $listItem->diedAt);
        $this->assertSame(null,                          $listItem->age);
        $this->assertSame(null,                          $listItem->causeOfDeathName);
        $this->assertSame(null,                          $listItem->deathCertificate);
        $this->assertSame(null,                          $listItem->cremationCertificate);
    }

    private function assertListItemEqualsNP009(NaturalPersonListItem $listItem): void
    {
        $this->assertSame('NP009',                       $listItem->id);
        $this->assertSame('Никонов Родион Митрофанович', $listItem->fullName);
        $this->assertSame(null,                          $listItem->address);
        $this->assertSame(null,                          $listItem->phone);
        $this->assertSame(null,                          $listItem->email);
        $this->assertSame(null,                          $listItem->bornAt);
        $this->assertSame(null,                          $listItem->placeOfBirth);
        $this->assertSame(null,                          $listItem->passport);
        $this->assertSame('26.05.1980',                  $listItem->diedAt);
        $this->assertSame(null,                          $listItem->age);
        $this->assertSame(null,                          $listItem->causeOfDeathName);
        $this->assertSame(null,                          $listItem->deathCertificate);
        $this->assertSame(null,                          $listItem->cremationCertificate);
    }

    private function assertListItemEqualsNP010(NaturalPersonListItem $listItem): void
    {
        $this->assertSame('NP010',                $listItem->id);
        $this->assertSame('Иванов Иван Иванович', $listItem->fullName);
        $this->assertSame(null,                   $listItem->address);
        $this->assertSame(null,                   $listItem->phone);
        $this->assertSame(null,                   $listItem->email);
        $this->assertSame('04.11.1930',           $listItem->bornAt);
        $this->assertSame(null,                   $listItem->placeOfBirth);
        $this->assertSame(null,                   $listItem->passport);
        $this->assertSame('22.11.2002',           $listItem->diedAt);
        $this->assertSame(72,                     $listItem->age);
        $this->assertSame(null,                   $listItem->causeOfDeathName);
        $this->assertSame(null,                   $listItem->deathCertificate);
        $this->assertSame(null,                   $listItem->cremationCertificate);
    }

    private function assertListItemEqualsNP011(NaturalPersonListItem $listItem): void
    {
        $this->assertSame('NP011',                $listItem->id);
        $this->assertSame('Иванов Иван Иванович', $listItem->fullName);
        $this->assertSame(null,                   $listItem->address);
        $this->assertSame(null,                   $listItem->phone);
        $this->assertSame(null,                   $listItem->email);
        $this->assertSame('12.04.1925',           $listItem->bornAt);
        $this->assertSame(null,                   $listItem->placeOfBirth);
        $this->assertSame(null,                   $listItem->passport);
        $this->assertSame('11.05.2004',           $listItem->diedAt);
        $this->assertSame(79,                     $listItem->age);
        $this->assertSame(null,                   $listItem->causeOfDeathName);
        $this->assertSame(null,                   $listItem->deathCertificate);
        $this->assertSame(null,                   $listItem->cremationCertificate);
    }

    private function assertListItemEqualsNP012(NaturalPersonListItem $listItem): void
    {
        $this->assertSame('NP012',                $listItem->id);
        $this->assertSame('Иванов Иван Иванович', $listItem->fullName);
        $this->assertSame(null,                   $listItem->address);
        $this->assertSame(null,                   $listItem->phone);
        $this->assertSame(null,                   $listItem->email);
        $this->assertSame('12.04.1925',           $listItem->bornAt);
        $this->assertSame(null,                   $listItem->placeOfBirth);
        $this->assertSame(null,                   $listItem->passport);
        $this->assertSame('29.10.2005',           $listItem->diedAt);
        $this->assertSame(80,                     $listItem->age);
        $this->assertSame(null,                   $listItem->causeOfDeathName);
        $this->assertSame(null,                   $listItem->deathCertificate);
        $this->assertSame(null,                   $listItem->cremationCertificate);
    }

    private function assertListItemEqualsNP013(NaturalPersonListItem $listItem): void
    {
        $this->assertSame('NP013',                $listItem->id);
        $this->assertSame('Петров Пётр Петрович', $listItem->fullName);
        $this->assertSame(null,                   $listItem->address);
        $this->assertSame(null,                   $listItem->phone);
        $this->assertSame(null,                   $listItem->email);
        $this->assertSame(null,                   $listItem->bornAt);
        $this->assertSame(null,                   $listItem->placeOfBirth);
        $this->assertSame(null,                   $listItem->passport);
        $this->assertSame(null,                   $listItem->diedAt);
        $this->assertSame(null,                   $listItem->age);
        $this->assertSame(null,                   $listItem->causeOfDeathName);
        $this->assertSame(null,                   $listItem->deathCertificate);
        $this->assertSame(null,                   $listItem->cremationCertificate);
    }
}
