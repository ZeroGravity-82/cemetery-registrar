<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Fetcher;

use Cemetery\Registrar\Domain\Model\Burial\BurialId;
use Cemetery\Registrar\Domain\Model\Burial\BurialRepository;
use Cemetery\Registrar\Domain\Model\Burial\BurialType;
use Cemetery\Registrar\Domain\Model\BurialContainer\Coffin;
use Cemetery\Registrar\Domain\Model\BurialContainer\CoffinShape;
use Cemetery\Registrar\Domain\Model\BurialContainer\Urn;
use Cemetery\Registrar\Domain\Model\BurialPlace\ColumbariumNiche\ColumbariumNiche;
use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\GraveSite;
use Cemetery\Registrar\Domain\Model\BurialPlace\MemorialTree\MemorialTree;
use Cemetery\Registrar\Domain\Model\NaturalPerson\NaturalPerson;
use Cemetery\Registrar\Domain\Model\Organization\JuristicPerson\JuristicPerson;
use Cemetery\Registrar\Domain\Model\Organization\SoleProprietor\SoleProprietor;
use Cemetery\Registrar\Domain\View\Burial\BurialFetcher;
use Cemetery\Registrar\Domain\View\Burial\BurialList;
use Cemetery\Registrar\Domain\View\Burial\BurialListItem;
use Cemetery\Registrar\Domain\View\Burial\BurialView;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Fetcher\DoctrineDbalBurialFetcher;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Orm\Repository\DoctrineOrmBurialRepository;
use DataFixtures\Burial\BurialFixtures;
use DataFixtures\BurialPlace\ColumbariumNiche\ColumbariumFixtures;
use DataFixtures\BurialPlace\ColumbariumNiche\ColumbariumNicheFixtures;
use DataFixtures\BurialPlace\GraveSite\CemeteryBlockFixtures;
use DataFixtures\BurialPlace\GraveSite\GraveSiteFixtures;
use DataFixtures\BurialPlace\MemorialTree\MemorialTreeFixtures;
use DataFixtures\Deceased\DeceasedFixtures;
use DataFixtures\FuneralCompany\FuneralCompanyFixtures;
use DataFixtures\NaturalPerson\NaturalPersonFixtures;
use DataFixtures\Organization\JuristicPerson\JuristicPersonFixtures;
use DataFixtures\Organization\SoleProprietor\SoleProprietorFixtures;

/**
 * @group database
 *
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class DoctrineDbalBurialFetcherIntegrationTest extends FetcherIntegrationTest
{
    private const DEFAULT_PAGE_SIZE = 20;

    private BurialRepository $burialRepo;
    private BurialFetcher    $burialFetcher;

    public function setUp(): void
    {
        parent::setUp();

        $this->burialRepo    = new DoctrineOrmBurialRepository($this->entityManager);
        $this->burialFetcher = new DoctrineDbalBurialFetcher($this->connection);
        $this->loadFixtures();
    }

    public function testItHasValidPageSizeConstant(): void
    {
        $this->assertSame(self::DEFAULT_PAGE_SIZE, BurialFetcher::DEFAULT_PAGE_SIZE);
    }

    public function testItReturnsBurialViewById(): void
    {
        $this->testItReturnsBurialViewForB001();
        $this->testItReturnsBurialViewForB002();
        $this->testItReturnsBurialViewForB003();
        $this->testItReturnsBurialViewForB004();
        $this->testItReturnsBurialViewForB005();
        $this->testItReturnsBurialViewForB006();
        $this->testItReturnsBurialViewForB007();
    }

    public function testItFailsToReturnBurialViewByUnknownId(): void
    {
        $this->expectExceptionForNotFoundBurialById('unknown_id');
        $this->burialFetcher->getViewById('unknown_id');
    }

    public function testItFailsToReturnBurialViewForRemovedBurial(): void
    {
        // Prepare database table for testing
        $burialToRemove = $this->burialRepo->findById(new BurialId('B004'));
        $this->burialRepo->remove($burialToRemove);
        $removedBurialId = $burialToRemove->id()->value();

        // Testing itself
        $this->expectExceptionForNotFoundBurialById($removedBurialId);
        $this->burialFetcher->getViewById($removedBurialId);
    }

    public function testItReturnsBurialListItemsByPage(): void
    {
        $customPageSize = 4;

        // First page
        $listForFirstPage = $this->burialFetcher->findAll(1, null, $customPageSize);
        $this->assertInstanceOf(BurialList::class, $listForFirstPage);
        $this->assertIsArray($listForFirstPage->listItems);
        $this->assertContainsOnlyInstancesOf(BurialListItem::class, $listForFirstPage->listItems);
        $this->assertCount(4,              $listForFirstPage->listItems);
        $this->assertSame(1,               $listForFirstPage->page);
        $this->assertSame($customPageSize, $listForFirstPage->pageSize);
        $this->assertSame(null,            $listForFirstPage->term);
        $this->assertSame(7,               $listForFirstPage->totalCount);
        $this->assertSame(2,               $listForFirstPage->totalPages);
        $this->assertItemEqualsB007($listForFirstPage->listItems[0]);  // This item has minimum code value
        $this->assertItemEqualsB001($listForFirstPage->listItems[1]);
        $this->assertItemEqualsB002($listForFirstPage->listItems[2]);
        $this->assertItemEqualsB003($listForFirstPage->listItems[3]);

        // Second page
        $listForSecondPage = $this->burialFetcher->findAll(2, null, $customPageSize);
        $this->assertInstanceOf(BurialList::class, $listForSecondPage);
        $this->assertIsArray($listForSecondPage->listItems);
        $this->assertContainsOnlyInstancesOf(BurialListItem::class, $listForSecondPage->listItems);
        $this->assertCount(3,              $listForSecondPage->listItems);
        $this->assertSame(2,               $listForSecondPage->page);
        $this->assertSame($customPageSize, $listForSecondPage->pageSize);
        $this->assertSame(null,            $listForSecondPage->term);
        $this->assertSame(7,               $listForSecondPage->totalCount);
        $this->assertSame(2,               $listForSecondPage->totalPages);
        $this->assertItemEqualsB005($listForSecondPage->listItems[0]);
        $this->assertItemEqualsB006($listForSecondPage->listItems[1]);
        $this->assertItemEqualsB004($listForSecondPage->listItems[2]);  // This item has maximum code value

        // Third page
        $listForThirdPage = $this->burialFetcher->findAll(3, null, $customPageSize);
        $this->assertInstanceOf(BurialList::class, $listForThirdPage);
        $this->assertIsArray($listForThirdPage->listItems);
        $this->assertCount(0,              $listForThirdPage->listItems);
        $this->assertSame(3,               $listForThirdPage->page);
        $this->assertSame($customPageSize, $listForThirdPage->pageSize);
        $this->assertSame(null,            $listForThirdPage->term);
        $this->assertSame(7,               $listForThirdPage->totalCount);
        $this->assertSame(2,               $listForThirdPage->totalPages);

        // All at once
        $listForAll = $this->burialFetcher->findAll(1, null, PHP_INT_MAX);
        $this->assertInstanceOf(BurialList::class, $listForAll);
        $this->assertIsArray($listForAll->listItems);
        $this->assertContainsOnlyInstancesOf(BurialListItem::class, $listForAll->listItems);
        $this->assertCount(7,                      $listForAll->listItems);
        $this->assertSame(1,                       $listForAll->page);
        $this->assertSame(self::DEFAULT_PAGE_SIZE, $listForAll->pageSize);
        $this->assertSame(null,                    $listForAll->term);
        $this->assertSame(7,                       $listForAll->totalCount);
        $this->assertSame(1,                       $listForAll->totalPages);
    }

    public function testItReturnsBurialListItemsByPageAndTerm(): void
    {
        $customPageSize = 4;

        $list = $this->burialFetcher->findAll(1, 'Ждан', $customPageSize);
        $this->assertInstanceOf(BurialList::class, $list);
        $this->assertIsArray($list->listItems);
        $this->assertContainsOnlyInstancesOf(BurialListItem::class, $list->listItems);
        $this->assertCount(3,              $list->listItems);
        $this->assertSame(1,               $list->page);
        $this->assertSame($customPageSize, $list->pageSize);
        $this->assertSame('Ждан',          $list->term);
        $this->assertSame(3,               $list->totalCount);
        $this->assertSame(1,               $list->totalPages);

        $list = $this->burialFetcher->findAll(1, 'Новос', $customPageSize);
        $this->assertInstanceOf(BurialList::class, $list);
        $this->assertIsArray($list->listItems);
        $this->assertContainsOnlyInstancesOf(BurialListItem::class, $list->listItems);
        $this->assertCount(4,              $list->listItems);
        $this->assertSame(1,               $list->page);
        $this->assertSame($customPageSize, $list->pageSize);
        $this->assertSame('Новос',         $list->term);
        $this->assertSame(4,               $list->totalCount);
        $this->assertSame(1,               $list->totalPages);

        $list = $this->burialFetcher->findAll(1, '11', $customPageSize);
        $this->assertInstanceOf(BurialList::class, $list);
        $this->assertIsArray($list->listItems);
        $this->assertContainsOnlyInstancesOf(BurialListItem::class, $list->listItems);
        $this->assertCount(4,              $list->listItems);
        $this->assertSame(1,               $list->page);
        $this->assertSame($customPageSize, $list->pageSize);
        $this->assertSame('11',            $list->term);
        $this->assertSame(6,               $list->totalCount);
        $this->assertSame(2,               $list->totalPages);
        $list = $this->burialFetcher->findAll(2, '11', $customPageSize);
        $this->assertInstanceOf(BurialList::class, $list);
        $this->assertIsArray($list->listItems);
        $this->assertContainsOnlyInstancesOf(BurialListItem::class, $list->listItems);
        $this->assertCount(2,              $list->listItems);
        $this->assertSame(2,               $list->page);
        $this->assertSame($customPageSize, $list->pageSize);
        $this->assertSame('11',            $list->term);
        $this->assertSame(6,               $list->totalCount);
        $this->assertSame(2,               $list->totalPages);
    }

    public function testItReturnsBurialTotalCount(): void
    {
        $this->assertSame(7, $this->burialFetcher->countTotal());
    }

    public function testItDoesNotCountRemovedBurialsWhenCalculatingTotalCount(): void
    {
        // Prepare database table for testing
        $burialToRemove = $this->burialRepo->findById(new BurialId('B004'));
        $this->burialRepo->remove($burialToRemove);

        // Testing itself
        $this->assertSame(6, $this->burialFetcher->countTotal());
    }

    protected function loadFixtures(): void
    {
        $this->databaseTool->loadFixtures([
            NaturalPersonFixtures::class,
            JuristicPersonFixtures::class,
            SoleProprietorFixtures::class,
            DeceasedFixtures::class,
            CemeteryBlockFixtures::class,
            GraveSiteFixtures::class,
            ColumbariumFixtures::class,
            ColumbariumNicheFixtures::class,
            MemorialTreeFixtures::class,
            FuneralCompanyFixtures::class,
            BurialFixtures::class,
        ]);
    }

    private function assertItemEqualsB001(BurialListItem $item): void
    {
        $this->assertSame('B001',                              $item->id);
        $this->assertSame('11',                                $item->code);
        $this->assertSame('Егоров Абрам Даниилович',           $item->deceasedNaturalPersonFullName);
        $this->assertSame(null,                                $item->deceasedNaturalPersonBornAt);
        $this->assertSame('2021-12-01',                        $item->deceasedDiedAt);
        $this->assertSame(null,                                $item->deceasedAge);
        $this->assertSame('2021-12-03 13:10:00',               $item->buriedAt);
        $this->assertSame(ColumbariumNiche::CLASS_SHORTCUT,    $item->burialPlaceType);
        $this->assertSame(null,                                $item->burialPlaceGraveSiteCemeteryBlockName);
        $this->assertSame(null,                                $item->burialPlaceGraveSiteRowInBlock);
        $this->assertSame(null,                                $item->burialPlaceGraveSitePositionInRow);
        $this->assertSame('южный',                             $item->burialPlaceColumbariumNicheColumbariumName);
        $this->assertSame(2,                                   $item->burialPlaceColumbariumNicheRowInColumbarium);
        $this->assertSame('002',                               $item->burialPlaceColumbariumNicheNumber);
        $this->assertSame(null,                                $item->burialPlaceMemorialTreeNumber);
        $this->assertSame(NaturalPerson::CLASS_SHORTCUT,       $item->customerType);
        $this->assertSame('Жданова Инга Григорьевна',          $item->customerNaturalPersonFullName);
        $this->assertSame('Новосибирск, ул. Ленина, д. 1',     $item->customerNaturalPersonAddress);
        $this->assertSame('+7-913-771-22-33',                  $item->customerNaturalPersonPhone);
        $this->assertSame(null,                                $item->customerSoleProprietorName);
        $this->assertSame(null,                                $item->customerSoleProprietorRegistrationAddress);
        $this->assertSame(null,                                $item->customerSoleProprietorActualLocationAddress);
        $this->assertSame(null,                                $item->customerSoleProprietorPhone);
        $this->assertSame(null,                                $item->customerJuristicPersonName);
        $this->assertSame(null,                                $item->customerJuristicPersonLegalAddress);
        $this->assertSame(null,                                $item->customerJuristicPersonPostalAddress);
        $this->assertSame(null,                                $item->customerJuristicPersonPhone);
    }

    private function assertItemEqualsB002(BurialListItem $item): void
    {
        $this->assertSame('B002',                          $item->id);
        $this->assertSame('11002',                         $item->code);
        $this->assertSame('Устинов Арсений Максович',      $item->deceasedNaturalPersonFullName);
        $this->assertSame('1918-12-30',                    $item->deceasedNaturalPersonBornAt);
        $this->assertSame('2001-02-12',                    $item->deceasedDiedAt);
        $this->assertSame(82,                              $item->deceasedAge);
        $this->assertSame(null,                            $item->buriedAt);
        $this->assertSame(GraveSite::CLASS_SHORTCUT,       $item->burialPlaceType);
        $this->assertSame('общий Б',                       $item->burialPlaceGraveSiteCemeteryBlockName);
        $this->assertSame(7,                               $item->burialPlaceGraveSiteRowInBlock);
        $this->assertSame(null,                            $item->burialPlaceGraveSitePositionInRow);
        $this->assertSame(null,                            $item->burialPlaceColumbariumNicheColumbariumName);
        $this->assertSame(null,                            $item->burialPlaceColumbariumNicheRowInColumbarium);
        $this->assertSame(null,                            $item->burialPlaceColumbariumNicheNumber);
        $this->assertSame(null,                            $item->burialPlaceMemorialTreeNumber);
        $this->assertSame(NaturalPerson::CLASS_SHORTCUT,   $item->customerType);
        $this->assertSame('Жданова Инга Григорьевна',      $item->customerNaturalPersonFullName);
        $this->assertSame('Новосибирск, ул. Ленина, д. 1', $item->customerNaturalPersonAddress);
        $this->assertSame('+7-913-771-22-33',              $item->customerNaturalPersonPhone);
        $this->assertSame(null,                            $item->customerSoleProprietorName);
        $this->assertSame(null,                            $item->customerSoleProprietorRegistrationAddress);
        $this->assertSame(null,                            $item->customerSoleProprietorActualLocationAddress);
        $this->assertSame(null,                            $item->customerSoleProprietorPhone);
        $this->assertSame(null,                            $item->customerJuristicPersonName);
        $this->assertSame(null,                            $item->customerJuristicPersonLegalAddress);
        $this->assertSame(null,                            $item->customerJuristicPersonPostalAddress);
        $this->assertSame(null,                            $item->customerJuristicPersonPhone);
    }

    private function assertItemEqualsB003(BurialListItem $item): void
    {
        $this->assertSame('B003',                        $item->id);
        $this->assertSame('11003',                       $item->code);
        $this->assertSame('Шилов Александр Михаилович',  $item->deceasedNaturalPersonFullName);
        $this->assertSame('1969-05-20',                  $item->deceasedNaturalPersonBornAt);
        $this->assertSame('2012-05-13',                  $item->deceasedDiedAt);
        $this->assertSame(null,                          $item->deceasedAge);
        $this->assertSame(null,                          $item->buriedAt);
        $this->assertSame(MemorialTree::CLASS_SHORTCUT,  $item->burialPlaceType);
        $this->assertSame(null,                          $item->burialPlaceGraveSiteCemeteryBlockName);
        $this->assertSame(null,                          $item->burialPlaceGraveSiteRowInBlock);
        $this->assertSame(null,                          $item->burialPlaceGraveSitePositionInRow);
        $this->assertSame(null,                          $item->burialPlaceColumbariumNicheColumbariumName);
        $this->assertSame(null,                          $item->burialPlaceColumbariumNicheRowInColumbarium);
        $this->assertSame(null,                          $item->burialPlaceColumbariumNicheNumber);
        $this->assertSame('002',                         $item->burialPlaceMemorialTreeNumber);
        $this->assertSame(NaturalPerson::CLASS_SHORTCUT, $item->customerType);
        $this->assertSame('Гришина Устинья Ярославовна', $item->customerNaturalPersonFullName);
        $this->assertSame(null,                          $item->customerNaturalPersonAddress);
        $this->assertSame(null,                          $item->customerNaturalPersonPhone);
        $this->assertSame(null,                          $item->customerSoleProprietorName);
        $this->assertSame(null,                          $item->customerSoleProprietorRegistrationAddress);
        $this->assertSame(null,                          $item->customerSoleProprietorActualLocationAddress);
        $this->assertSame(null,                          $item->customerSoleProprietorPhone);
        $this->assertSame(null,                          $item->customerJuristicPersonName);
        $this->assertSame(null,                          $item->customerJuristicPersonLegalAddress);
        $this->assertSame(null,                          $item->customerJuristicPersonPostalAddress);
        $this->assertSame(null,                          $item->customerJuristicPersonPhone);
    }

    private function assertItemEqualsB004(BurialListItem $item): void
    {
        $this->assertSame('B004',                                        $item->id);
        $this->assertSame('234117890',                                   $item->code);
        $this->assertSame('Жданова Инга Григорьевна',                    $item->deceasedNaturalPersonFullName);
        $this->assertSame('1979-02-12',                                  $item->deceasedNaturalPersonBornAt);
        $this->assertSame('2022-03-10',                                  $item->deceasedDiedAt);
        $this->assertSame(null,                                          $item->deceasedAge);
        $this->assertSame(null,                                          $item->buriedAt);
        $this->assertSame(GraveSite::CLASS_SHORTCUT,                     $item->burialPlaceType);
        $this->assertSame('воинский',                                    $item->burialPlaceGraveSiteCemeteryBlockName);
        $this->assertSame(1,                                             $item->burialPlaceGraveSiteRowInBlock);
        $this->assertSame(null,                                          $item->burialPlaceGraveSitePositionInRow);
        $this->assertSame(null,                                          $item->burialPlaceColumbariumNicheColumbariumName);
        $this->assertSame(null,                                          $item->burialPlaceColumbariumNicheRowInColumbarium);
        $this->assertSame(null,                                          $item->burialPlaceColumbariumNicheNumber);
        $this->assertSame(null,                                          $item->burialPlaceMemorialTreeNumber);
        $this->assertSame(JuristicPerson::CLASS_SHORTCUT,                $item->customerType);
        $this->assertSame(null,                                          $item->customerNaturalPersonFullName);
        $this->assertSame(null,                                          $item->customerNaturalPersonAddress);
        $this->assertSame(null,                                          $item->customerNaturalPersonPhone);
        $this->assertSame(null,                                          $item->customerSoleProprietorName);
        $this->assertSame(null,                                          $item->customerSoleProprietorRegistrationAddress);
        $this->assertSame(null,                                          $item->customerSoleProprietorActualLocationAddress);
        $this->assertSame(null,                                          $item->customerSoleProprietorPhone);
        $this->assertSame('МУП "Новосибирский метрополитен"',            $item->customerJuristicPersonName);
        $this->assertSame(null,                                          $item->customerJuristicPersonLegalAddress);
        $this->assertSame(null,                                          $item->customerJuristicPersonPostalAddress);
        $this->assertSame(null,                                          $item->customerJuristicPersonPhone);
    }

    private function assertItemEqualsB005(BurialListItem $item): void
    {
        $this->assertSame('B005',                         $item->id);
        $this->assertSame('11005',                        $item->code);
        $this->assertSame('Соколов Герман Маркович',      $item->deceasedNaturalPersonFullName);
        $this->assertSame(null,                           $item->deceasedNaturalPersonBornAt);
        $this->assertSame('2010-01-26',                   $item->deceasedDiedAt);
        $this->assertSame(null,                           $item->deceasedAge);
        $this->assertSame('2010-01-28 12:55:00',          $item->buriedAt);
        $this->assertSame(GraveSite::CLASS_SHORTCUT,      $item->burialPlaceType);
        $this->assertSame('общий А',                      $item->burialPlaceGraveSiteCemeteryBlockName);
        $this->assertSame(3,                              $item->burialPlaceGraveSiteRowInBlock);
        $this->assertSame(4,                              $item->burialPlaceGraveSitePositionInRow);
        $this->assertSame(null,                           $item->burialPlaceColumbariumNicheColumbariumName);
        $this->assertSame(null,                           $item->burialPlaceColumbariumNicheRowInColumbarium);
        $this->assertSame(null,                           $item->burialPlaceColumbariumNicheNumber);
        $this->assertSame(null,                           $item->burialPlaceMemorialTreeNumber);
        $this->assertSame(SoleProprietor::CLASS_SHORTCUT, $item->customerType);
        $this->assertSame(null,                           $item->customerNaturalPersonFullName);
        $this->assertSame(null,                           $item->customerNaturalPersonAddress);
        $this->assertSame(null,                           $item->customerNaturalPersonPhone);
        $this->assertSame('ИП Сидоров Сидр Сидорович',    $item->customerSoleProprietorName);
        $this->assertSame(null,                           $item->customerSoleProprietorRegistrationAddress);
        $this->assertSame('с. Каменка, д. 14',            $item->customerSoleProprietorActualLocationAddress);
        $this->assertSame('8(383)147-22-33',              $item->customerSoleProprietorPhone);
        $this->assertSame(null,                           $item->customerJuristicPersonName);
        $this->assertSame(null,                           $item->customerJuristicPersonLegalAddress);
        $this->assertSame(null,                           $item->customerJuristicPersonPostalAddress);
        $this->assertSame(null,                           $item->customerJuristicPersonPhone);
    }

    private function assertItemEqualsB006(BurialListItem $item): void
    {
        $this->assertSame('B006',                                        $item->id);
        $this->assertSame('11006',                                       $item->code);
        $this->assertSame('Гришина Устинья Ярославовна',                 $item->deceasedNaturalPersonFullName);
        $this->assertSame(null,                                          $item->deceasedNaturalPersonBornAt);
        $this->assertSame('2021-12-03',                                  $item->deceasedDiedAt);
        $this->assertSame(null,                                          $item->deceasedAge);
        $this->assertSame(null,                                          $item->buriedAt);
        $this->assertSame(null,                                          $item->burialPlaceType);
        $this->assertSame(null,                                          $item->burialPlaceGraveSiteCemeteryBlockName);
        $this->assertSame(null,                                          $item->burialPlaceGraveSiteRowInBlock);
        $this->assertSame(null,                                          $item->burialPlaceGraveSitePositionInRow);
        $this->assertSame(null,                                          $item->burialPlaceColumbariumNicheColumbariumName);
        $this->assertSame(null,                                          $item->burialPlaceColumbariumNicheRowInColumbarium);
        $this->assertSame(null,                                          $item->burialPlaceColumbariumNicheNumber);
        $this->assertSame(null,                                          $item->burialPlaceMemorialTreeNumber);
        $this->assertSame(NaturalPerson::CLASS_SHORTCUT,                 $item->customerType);
        $this->assertSame('Громов Никифор Рудольфович',                  $item->customerNaturalPersonFullName);
        $this->assertSame('Новосибирск, ул. Н.-Данченко, д. 18, кв. 17', $item->customerNaturalPersonAddress);
        $this->assertSame(null,                                          $item->customerNaturalPersonPhone);
        $this->assertSame(null,                                          $item->customerSoleProprietorName);
        $this->assertSame(null,                                          $item->customerSoleProprietorRegistrationAddress);
        $this->assertSame(null,                                          $item->customerSoleProprietorActualLocationAddress);
        $this->assertSame(null,                                          $item->customerSoleProprietorPhone);
        $this->assertSame(null,                                          $item->customerJuristicPersonName);
        $this->assertSame(null,                                          $item->customerJuristicPersonLegalAddress);
        $this->assertSame(null,                                          $item->customerJuristicPersonPostalAddress);
        $this->assertSame(null,                                          $item->customerJuristicPersonPhone);
    }

    private function assertItemEqualsB007(BurialListItem $item): void
    {
        $this->assertSame('B007',                              $item->id);
        $this->assertSame('01',                                $item->code);
        $this->assertSame('Никонов Родион Митрофанович',       $item->deceasedNaturalPersonFullName);
        $this->assertSame(null,                                $item->deceasedNaturalPersonBornAt);
        $this->assertSame('1980-05-26',                        $item->deceasedDiedAt);
        $this->assertSame(null,                                $item->deceasedAge);
        $this->assertSame(null,                                $item->buriedAt);
        $this->assertSame(GraveSite::CLASS_SHORTCUT,           $item->burialPlaceType);
        $this->assertSame('мусульманский',                     $item->burialPlaceGraveSiteCemeteryBlockName);
        $this->assertSame(3,                                   $item->burialPlaceGraveSiteRowInBlock);
        $this->assertSame(null,                                $item->burialPlaceGraveSitePositionInRow);
        $this->assertSame(null,                                $item->burialPlaceColumbariumNicheColumbariumName);
        $this->assertSame(null,                                $item->burialPlaceColumbariumNicheRowInColumbarium);
        $this->assertSame(null,                                $item->burialPlaceColumbariumNicheNumber);
        $this->assertSame(null,                                $item->burialPlaceMemorialTreeNumber);
        $this->assertSame(null,                                $item->customerType);
        $this->assertSame(null,                                $item->customerNaturalPersonFullName);
        $this->assertSame(null,                                $item->customerNaturalPersonAddress);
        $this->assertSame(null,                                $item->customerNaturalPersonPhone);
        $this->assertSame(null,                                $item->customerSoleProprietorName);
        $this->assertSame(null,                                $item->customerSoleProprietorRegistrationAddress);
        $this->assertSame(null,                                $item->customerSoleProprietorActualLocationAddress);
        $this->assertSame(null,                                $item->customerSoleProprietorPhone);
        $this->assertSame(null,                                $item->customerJuristicPersonName);
        $this->assertSame(null,                                $item->customerJuristicPersonLegalAddress);
        $this->assertSame(null,                                $item->customerJuristicPersonPostalAddress);
        $this->assertSame(null,                                $item->customerJuristicPersonPhone);
    }

    private function testItReturnsBurialViewForB001(): void
    {
        $view = $this->burialFetcher->getViewById('B001');
        $this->assertInstanceOf(BurialView::class, $view);
        $this->assertSame('B001',                                      $view->id);
        $this->assertSame('11',                                        $view->code);
        $this->assertSame(BurialType::URN_IN_COLUMBARIUM_NICHE,        $view->type);
        $this->assertSame('D001',                                      $view->deceasedId);
        $this->assertSame('NP001',                                     $view->deceasedNaturalPersonId);
        $this->assertSame('Егоров Абрам Даниилович',                   $view->deceasedNaturalPersonFullName);
        $this->assertSame(null,                                        $view->deceasedNaturalPersonBornAt);
        $this->assertSame('2021-12-01',                                $view->deceasedDiedAt);
        $this->assertSame(null,                                        $view->deceasedAge);
        $this->assertSame(null,                                        $view->deceasedDeathCertificateId);
        $this->assertSame(null,                                        $view->deceasedCauseOfDeathId);
        $this->assertSame('NP005',                                     $view->customerId);
        $this->assertSame(NaturalPerson::CLASS_SHORTCUT,               $view->customerType);
        $this->assertSame('Жданова Инга Григорьевна',                  $view->customerNaturalPersonFullName);
        $this->assertSame('+7-913-771-22-33',                          $view->customerNaturalPersonPhone);
        $this->assertSame(null,                                        $view->customerNaturalPersonPhoneAdditional);
        $this->assertSame(null,                                        $view->customerNaturalPersonEmail);
        $this->assertSame('Новосибирск, ул. Ленина, д. 1',             $view->customerNaturalPersonAddress);
        $this->assertSame('1979-02-12',                                $view->customerNaturalPersonBornAt);
        $this->assertSame(null,                                        $view->customerNaturalPersonPlaceOfBirth);
        $this->assertSame('1234',                                      $view->customerNaturalPersonPassportSeries);
        $this->assertSame('567890',                                    $view->customerNaturalPersonPassportNumber);
        $this->assertSame('2002-10-28',                                $view->customerNaturalPersonPassportIssuedAt);
        $this->assertSame('УВД Кировского района города Новосибирска', $view->customerNaturalPersonPassportIssuedBy);
        $this->assertSame('540-001',                                   $view->customerNaturalPersonPassportDivisionCode);
        $this->assertSame(null,                                        $view->customerSoleProprietorName);
        $this->assertSame(null,                                        $view->customerSoleProprietorInn);
        $this->assertSame(null,                                        $view->customerSoleProprietorOgrnip);
        $this->assertSame(null,                                        $view->customerSoleProprietorOkpo);
        $this->assertSame(null,                                        $view->customerSoleProprietorOkved);
        $this->assertSame(null,                                        $view->customerSoleProprietorRegistrationAddress);
        $this->assertSame(null,                                        $view->customerSoleProprietorActualLocationAddress);
        $this->assertSame(null,                                        $view->customerSoleProprietorBankDetailsBankName);
        $this->assertSame(null,                                        $view->customerSoleProprietorBankDetailsBik);
        $this->assertSame(null,                                        $view->customerSoleProprietorBankDetailsCorrespondentAccount);
        $this->assertSame(null,                                        $view->customerSoleProprietorBankDetailsCurrentAccount);
        $this->assertSame(null,                                        $view->customerSoleProprietorPhone);
        $this->assertSame(null,                                        $view->customerSoleProprietorPhoneAdditional);
        $this->assertSame(null,                                        $view->customerSoleProprietorFax);
        $this->assertSame(null,                                        $view->customerSoleProprietorEmail);
        $this->assertSame(null,                                        $view->customerSoleProprietorWebsite);
        $this->assertSame(null,                                        $view->customerJuristicPersonName);
        $this->assertSame(null,                                        $view->customerJuristicPersonInn);
        $this->assertSame(null,                                        $view->customerJuristicPersonKpp);
        $this->assertSame(null,                                        $view->customerJuristicPersonOgrn);
        $this->assertSame(null,                                        $view->customerJuristicPersonOkpo);
        $this->assertSame(null,                                        $view->customerJuristicPersonOkved);
        $this->assertSame(null,                                        $view->customerJuristicPersonLegalAddress);
        $this->assertSame(null,                                        $view->customerJuristicPersonPostalAddress);
        $this->assertSame(null,                                        $view->customerJuristicPersonBankDetailsBankName);
        $this->assertSame(null,                                        $view->customerJuristicPersonBankDetailsBik);
        $this->assertSame(null,                                        $view->customerJuristicPersonBankDetailsCorrespondentAccount);
        $this->assertSame(null,                                        $view->customerJuristicPersonBankDetailsCurrentAccount);
        $this->assertSame(null,                                        $view->customerJuristicPersonPhone);
        $this->assertSame(null,                                        $view->customerJuristicPersonPhoneAdditional);
        $this->assertSame(null,                                        $view->customerJuristicPersonFax);
        $this->assertSame(null,                                        $view->customerJuristicPersonGeneralDirector);
        $this->assertSame(null,                                        $view->customerJuristicPersonEmail);
        $this->assertSame(null,                                        $view->customerJuristicPersonWebsite);
        $this->assertSame(null,                                        $view->burialPlaceOwnerId);
        $this->assertSame(null,                                        $view->burialPlaceOwnerFullName);
        $this->assertSame(null,                                        $view->burialPlaceOwnerPhone);
        $this->assertSame(null,                                        $view->burialPlaceOwnerPhoneAdditional);
        $this->assertSame(null,                                        $view->burialPlaceOwnerEmail);
        $this->assertSame(null,                                        $view->burialPlaceOwnerAddress);
        $this->assertSame(null,                                        $view->burialPlaceOwnerBornAt);
        $this->assertSame(null,                                        $view->burialPlaceOwnerPlaceOfBirth);
        $this->assertSame(null,                                        $view->burialPlaceOwnerPassportSeries);
        $this->assertSame(null,                                        $view->burialPlaceOwnerPassportNumber);
        $this->assertSame(null,                                        $view->burialPlaceOwnerPassportIssuedAt);
        $this->assertSame(null,                                        $view->burialPlaceOwnerPassportIssuedBy);
        $this->assertSame(null,                                        $view->burialPlaceOwnerPassportDivisionCode);
        $this->assertSame(null,                                        $view->funeralCompanyId);
        $this->assertSame(null,                                        $view->burialChainId);
        $this->assertSame('CN002',                                     $view->burialPlaceId);
        $this->assertSame(ColumbariumNiche::CLASS_SHORTCUT,            $view->burialPlaceType);
        $this->assertSame(null,                                        $view->burialPlaceGraveSiteCemeteryBlockId);
        $this->assertSame(null,                                        $view->burialPlaceGraveSiteCemeteryBlockName);
        $this->assertSame(null,                                        $view->burialPlaceGraveSiteRowInBlock);
        $this->assertSame(null,                                        $view->burialPlaceGraveSitePositionInRow);
        $this->assertSame(null,                                        $view->burialPlaceGraveSiteSize);
        $this->assertSame(null,                                        $view->burialPlaceGraveSiteGeoPositionLatitude);
        $this->assertSame(null,                                        $view->burialPlaceGraveSiteGeoPositionLongitude);
        $this->assertSame(null,                                        $view->burialPlaceGraveSiteGeoPositionError);
        $this->assertSame('C002',                                      $view->burialPlaceColumbariumNicheColumbariumId);
        $this->assertSame('южный',                                     $view->burialPlaceColumbariumNicheColumbariumName);
        $this->assertSame(2,                                           $view->burialPlaceColumbariumNicheRowInColumbarium);
        $this->assertSame('002',                                       $view->burialPlaceColumbariumNicheNumber);
        $this->assertSame('54.95035712',                               $view->burialPlaceColumbariumNicheGeoPositionLatitude);
        $this->assertSame('82.79252',                                  $view->burialPlaceColumbariumNicheGeoPositionLongitude);
        $this->assertSame('0.5',                                       $view->burialPlaceColumbariumNicheGeoPositionError);
        $this->assertSame(null,                                        $view->burialPlaceMemorialTreeNumber);
        $this->assertSame(null,                                        $view->burialPlaceMemorialTreeGeoPositionLatitude);
        $this->assertSame(null,                                        $view->burialPlaceMemorialTreeGeoPositionLongitude);
        $this->assertSame(null,                                        $view->burialPlaceMemorialTreeGeoPositionError);
        $this->assertSame(Urn::CLASS_SHORTCUT,                         $view->burialContainerType);
        $this->assertSame(null,                                        $view->burialContainerCoffinSize);
        $this->assertSame(null,                                        $view->burialContainerCoffinShape);
        $this->assertSame(null,                                        $view->burialContainerCoffinIsNonStandard);
        $this->assertSame('2021-12-03 13:10:00',                       $view->buriedAt);
        $this->assertValidDateTimeValue($view->createdAt);
        $this->assertValidDateTimeValue($view->updatedAt);
    }

    private function testItReturnsBurialViewForB002(): void
    {
        $view = $this->burialFetcher->getViewById('B002');
        $this->assertInstanceOf(BurialView::class, $view);
        $this->assertSame('B002',                                      $view->id);
        $this->assertSame('11002',                                     $view->code);
        $this->assertSame(BurialType::COFFIN_IN_GRAVE_SITE,            $view->type);
        $this->assertSame('D002',                                      $view->deceasedId);
        $this->assertSame('NP002',                                     $view->deceasedNaturalPersonId);
        $this->assertSame('Устинов Арсений Максович',                  $view->deceasedNaturalPersonFullName);
        $this->assertSame('1918-12-30',                                $view->deceasedNaturalPersonBornAt);
        $this->assertSame('2001-02-12',                                $view->deceasedDiedAt);
        $this->assertSame(82,                                          $view->deceasedAge);
        $this->assertSame('DC001',                                     $view->deceasedDeathCertificateId);
        $this->assertSame('CD008',                                     $view->deceasedCauseOfDeathId);
        $this->assertSame('NP005',                                     $view->customerId);
        $this->assertSame(NaturalPerson::CLASS_SHORTCUT,               $view->customerType);
        $this->assertSame('Жданова Инга Григорьевна',                  $view->customerNaturalPersonFullName);
        $this->assertSame('+7-913-771-22-33',                          $view->customerNaturalPersonPhone);
        $this->assertSame(null,                                        $view->customerNaturalPersonPhoneAdditional);
        $this->assertSame(null,                                        $view->customerNaturalPersonEmail);
        $this->assertSame('Новосибирск, ул. Ленина, д. 1',             $view->customerNaturalPersonAddress);
        $this->assertSame('1979-02-12',                                $view->customerNaturalPersonBornAt);
        $this->assertSame(null,                                        $view->customerNaturalPersonPlaceOfBirth);
        $this->assertSame('1234',                                      $view->customerNaturalPersonPassportSeries);
        $this->assertSame('567890',                                    $view->customerNaturalPersonPassportNumber);
        $this->assertSame('2002-10-28',                                $view->customerNaturalPersonPassportIssuedAt);
        $this->assertSame('УВД Кировского района города Новосибирска', $view->customerNaturalPersonPassportIssuedBy);
        $this->assertSame('540-001',                                   $view->customerNaturalPersonPassportDivisionCode);
        $this->assertSame(null,                                        $view->customerSoleProprietorName);
        $this->assertSame(null,                                        $view->customerSoleProprietorInn);
        $this->assertSame(null,                                        $view->customerSoleProprietorOgrnip);
        $this->assertSame(null,                                        $view->customerSoleProprietorOkpo);
        $this->assertSame(null,                                        $view->customerSoleProprietorOkved);
        $this->assertSame(null,                                        $view->customerSoleProprietorRegistrationAddress);
        $this->assertSame(null,                                        $view->customerSoleProprietorActualLocationAddress);
        $this->assertSame(null,                                        $view->customerSoleProprietorBankDetailsBankName);
        $this->assertSame(null,                                        $view->customerSoleProprietorBankDetailsBik);
        $this->assertSame(null,                                        $view->customerSoleProprietorBankDetailsCorrespondentAccount);
        $this->assertSame(null,                                        $view->customerSoleProprietorBankDetailsCurrentAccount);
        $this->assertSame(null,                                        $view->customerSoleProprietorPhone);
        $this->assertSame(null,                                        $view->customerSoleProprietorPhoneAdditional);
        $this->assertSame(null,                                        $view->customerSoleProprietorFax);
        $this->assertSame(null,                                        $view->customerSoleProprietorEmail);
        $this->assertSame(null,                                        $view->customerSoleProprietorWebsite);
        $this->assertSame(null,                                        $view->customerJuristicPersonName);
        $this->assertSame(null,                                        $view->customerJuristicPersonInn);
        $this->assertSame(null,                                        $view->customerJuristicPersonKpp);
        $this->assertSame(null,                                        $view->customerJuristicPersonOgrn);
        $this->assertSame(null,                                        $view->customerJuristicPersonOkpo);
        $this->assertSame(null,                                        $view->customerJuristicPersonOkved);
        $this->assertSame(null,                                        $view->customerJuristicPersonLegalAddress);
        $this->assertSame(null,                                        $view->customerJuristicPersonPostalAddress);
        $this->assertSame(null,                                        $view->customerJuristicPersonBankDetailsBankName);
        $this->assertSame(null,                                        $view->customerJuristicPersonBankDetailsBik);
        $this->assertSame(null,                                        $view->customerJuristicPersonBankDetailsCorrespondentAccount);
        $this->assertSame(null,                                        $view->customerJuristicPersonBankDetailsCurrentAccount);
        $this->assertSame(null,                                        $view->customerJuristicPersonPhone);
        $this->assertSame(null,                                        $view->customerJuristicPersonPhoneAdditional);
        $this->assertSame(null,                                        $view->customerJuristicPersonFax);
        $this->assertSame(null,                                        $view->customerJuristicPersonGeneralDirector);
        $this->assertSame(null,                                        $view->customerJuristicPersonEmail);
        $this->assertSame(null,                                        $view->customerJuristicPersonWebsite);
        $this->assertSame('NP006',                                     $view->burialPlaceOwnerId);
        $this->assertSame('Гришина Устинья Ярославовна',               $view->burialPlaceOwnerFullName);
        $this->assertSame(null,                                        $view->burialPlaceOwnerPhone);
        $this->assertSame(null,                                        $view->burialPlaceOwnerPhoneAdditional);
        $this->assertSame(null,                                        $view->burialPlaceOwnerEmail);
        $this->assertSame(null,                                        $view->burialPlaceOwnerAddress);
        $this->assertSame(null,                                        $view->burialPlaceOwnerBornAt);
        $this->assertSame(null,                                        $view->burialPlaceOwnerPlaceOfBirth);
        $this->assertSame(null,                                        $view->burialPlaceOwnerPassportSeries);
        $this->assertSame(null,                                        $view->burialPlaceOwnerPassportNumber);
        $this->assertSame(null,                                        $view->burialPlaceOwnerPassportIssuedAt);
        $this->assertSame(null,                                        $view->burialPlaceOwnerPassportIssuedBy);
        $this->assertSame(null,                                        $view->burialPlaceOwnerPassportDivisionCode);
        $this->assertSame(null,                                        $view->funeralCompanyId);
        $this->assertSame(null,                                        $view->burialChainId);
        $this->assertSame('GS003',                                     $view->burialPlaceId);
        $this->assertSame(GraveSite::CLASS_SHORTCUT,                   $view->burialPlaceType);
        $this->assertSame('CB003',                                     $view->burialPlaceGraveSiteCemeteryBlockId);
        $this->assertSame('общий Б',                                   $view->burialPlaceGraveSiteCemeteryBlockName);
        $this->assertSame(7,                                           $view->burialPlaceGraveSiteRowInBlock);
        $this->assertSame(null,                                        $view->burialPlaceGraveSitePositionInRow);
        $this->assertSame('2.5',                                       $view->burialPlaceGraveSiteSize);
        $this->assertSame('50.950357',                                 $view->burialPlaceGraveSiteGeoPositionLatitude);
        $this->assertSame('80.7972252',                                $view->burialPlaceGraveSiteGeoPositionLongitude);
        $this->assertSame(null,                                        $view->burialPlaceGraveSiteGeoPositionError);
        $this->assertSame(null,                                        $view->burialPlaceColumbariumNicheColumbariumId);
        $this->assertSame(null,                                        $view->burialPlaceColumbariumNicheColumbariumName);
        $this->assertSame(null,                                        $view->burialPlaceColumbariumNicheRowInColumbarium);
        $this->assertSame(null,                                        $view->burialPlaceColumbariumNicheNumber);
        $this->assertSame(null,                                        $view->burialPlaceColumbariumNicheGeoPositionLatitude);
        $this->assertSame(null,                                        $view->burialPlaceColumbariumNicheGeoPositionLongitude);
        $this->assertSame(null,                                        $view->burialPlaceColumbariumNicheGeoPositionError);
        $this->assertSame(null,                                        $view->burialPlaceMemorialTreeNumber);
        $this->assertSame(null,                                        $view->burialPlaceMemorialTreeGeoPositionLatitude);
        $this->assertSame(null,                                        $view->burialPlaceMemorialTreeGeoPositionLongitude);
        $this->assertSame(null,                                        $view->burialPlaceMemorialTreeGeoPositionError);
        $this->assertSame(Coffin::CLASS_SHORTCUT,                      $view->burialContainerType);
        $this->assertSame(180,                                         $view->burialContainerCoffinSize);
        $this->assertSame(CoffinShape::TRAPEZOID,                      $view->burialContainerCoffinShape);
        $this->assertSame(false,                                       $view->burialContainerCoffinIsNonStandard);
        $this->assertSame(null,                                        $view->buriedAt);
        $this->assertValidDateTimeValue($view->createdAt);
        $this->assertValidDateTimeValue($view->updatedAt);
    }

    private function testItReturnsBurialViewForB003(): void
    {
        $view = $this->burialFetcher->getViewById('B003');
        $this->assertInstanceOf(BurialView::class, $view);
        $this->assertSame('B003',                                        $view->id);
        $this->assertSame('11003',                                       $view->code);
        $this->assertSame(BurialType::ASHES_UNDER_MEMORIAL_TREE,         $view->type);
        $this->assertSame('D003',                                        $view->deceasedId);
        $this->assertSame('NP003',                                       $view->deceasedNaturalPersonId);
        $this->assertSame('Шилов Александр Михаилович',                  $view->deceasedNaturalPersonFullName);
        $this->assertSame('1969-05-20',                                  $view->deceasedNaturalPersonBornAt);
        $this->assertSame('2012-05-13',                                  $view->deceasedDiedAt);
        $this->assertSame(null,                                          $view->deceasedAge);
        $this->assertSame('DC002',                                       $view->deceasedDeathCertificateId);
        $this->assertSame('CD004',                                       $view->deceasedCauseOfDeathId);
        $this->assertSame('NP006',                                       $view->customerId);
        $this->assertSame(NaturalPerson::CLASS_SHORTCUT,                 $view->customerType);
        $this->assertSame('Гришина Устинья Ярославовна',                 $view->customerNaturalPersonFullName);
        $this->assertSame(null,                                          $view->customerNaturalPersonPhone);
        $this->assertSame(null,                                          $view->customerNaturalPersonPhoneAdditional);
        $this->assertSame(null,                                          $view->customerNaturalPersonEmail);
        $this->assertSame(null,                                          $view->customerNaturalPersonAddress);
        $this->assertSame(null,                                          $view->customerNaturalPersonBornAt);
        $this->assertSame(null,                                          $view->customerNaturalPersonPlaceOfBirth);
        $this->assertSame(null,                                          $view->customerNaturalPersonPassportSeries);
        $this->assertSame(null,                                          $view->customerNaturalPersonPassportNumber);
        $this->assertSame(null,                                          $view->customerNaturalPersonPassportIssuedAt);
        $this->assertSame(null,                                          $view->customerNaturalPersonPassportIssuedBy);
        $this->assertSame(null,                                          $view->customerNaturalPersonPassportDivisionCode);
        $this->assertSame(null,                                          $view->customerSoleProprietorName);
        $this->assertSame(null,                                          $view->customerSoleProprietorInn);
        $this->assertSame(null,                                          $view->customerSoleProprietorOgrnip);
        $this->assertSame(null,                                          $view->customerSoleProprietorOkpo);
        $this->assertSame(null,                                          $view->customerSoleProprietorOkved);
        $this->assertSame(null,                                          $view->customerSoleProprietorRegistrationAddress);
        $this->assertSame(null,                                          $view->customerSoleProprietorActualLocationAddress);
        $this->assertSame(null,                                          $view->customerSoleProprietorBankDetailsBankName);
        $this->assertSame(null,                                          $view->customerSoleProprietorBankDetailsBik);
        $this->assertSame(null,                                          $view->customerSoleProprietorBankDetailsCorrespondentAccount);
        $this->assertSame(null,                                          $view->customerSoleProprietorBankDetailsCurrentAccount);
        $this->assertSame(null,                                          $view->customerSoleProprietorPhone);
        $this->assertSame(null,                                          $view->customerSoleProprietorPhoneAdditional);
        $this->assertSame(null,                                          $view->customerSoleProprietorFax);
        $this->assertSame(null,                                          $view->customerSoleProprietorEmail);
        $this->assertSame(null,                                          $view->customerSoleProprietorWebsite);
        $this->assertSame(null,                                          $view->customerJuristicPersonName);
        $this->assertSame(null,                                          $view->customerJuristicPersonInn);
        $this->assertSame(null,                                          $view->customerJuristicPersonKpp);
        $this->assertSame(null,                                          $view->customerJuristicPersonOgrn);
        $this->assertSame(null,                                          $view->customerJuristicPersonOkpo);
        $this->assertSame(null,                                          $view->customerJuristicPersonOkved);
        $this->assertSame(null,                                          $view->customerJuristicPersonLegalAddress);
        $this->assertSame(null,                                          $view->customerJuristicPersonPostalAddress);
        $this->assertSame(null,                                          $view->customerJuristicPersonBankDetailsBankName);
        $this->assertSame(null,                                          $view->customerJuristicPersonBankDetailsBik);
        $this->assertSame(null,                                          $view->customerJuristicPersonBankDetailsCorrespondentAccount);
        $this->assertSame(null,                                          $view->customerJuristicPersonBankDetailsCurrentAccount);
        $this->assertSame(null,                                          $view->customerJuristicPersonPhone);
        $this->assertSame(null,                                          $view->customerJuristicPersonPhoneAdditional);
        $this->assertSame(null,                                          $view->customerJuristicPersonFax);
        $this->assertSame(null,                                          $view->customerJuristicPersonGeneralDirector);
        $this->assertSame(null,                                          $view->customerJuristicPersonEmail);
        $this->assertSame(null,                                          $view->customerJuristicPersonWebsite);
        $this->assertSame('NP006',                                       $view->burialPlaceOwnerId);
        $this->assertSame('Гришина Устинья Ярославовна',                 $view->burialPlaceOwnerFullName);
        $this->assertSame(null,                                          $view->burialPlaceOwnerPhone);
        $this->assertSame(null,                                          $view->burialPlaceOwnerPhoneAdditional);
        $this->assertSame(null,                                          $view->burialPlaceOwnerEmail);
        $this->assertSame(null,                                          $view->burialPlaceOwnerAddress);
        $this->assertSame(null,                                          $view->burialPlaceOwnerBornAt);
        $this->assertSame(null,                                          $view->burialPlaceOwnerPlaceOfBirth);
        $this->assertSame(null,                                          $view->burialPlaceOwnerPassportSeries);
        $this->assertSame(null,                                          $view->burialPlaceOwnerPassportNumber);
        $this->assertSame(null,                                          $view->burialPlaceOwnerPassportIssuedAt);
        $this->assertSame(null,                                          $view->burialPlaceOwnerPassportIssuedBy);
        $this->assertSame(null,                                          $view->burialPlaceOwnerPassportDivisionCode);
        $this->assertSame('FC001',                                       $view->funeralCompanyId);
        $this->assertSame(null,                                          $view->burialChainId);
        $this->assertSame('MT002',                                       $view->burialPlaceId);
        $this->assertSame(MemorialTree::CLASS_SHORTCUT,                  $view->burialPlaceType);
        $this->assertSame(null,                                          $view->burialPlaceGraveSiteCemeteryBlockId);
        $this->assertSame(null,                                          $view->burialPlaceGraveSiteCemeteryBlockName);
        $this->assertSame(null,                                          $view->burialPlaceGraveSiteRowInBlock);
        $this->assertSame(null,                                          $view->burialPlaceGraveSitePositionInRow);
        $this->assertSame(null,                                          $view->burialPlaceGraveSiteSize);
        $this->assertSame(null,                                          $view->burialPlaceGraveSiteGeoPositionLatitude);
        $this->assertSame(null,                                          $view->burialPlaceGraveSiteGeoPositionLongitude);
        $this->assertSame(null,                                          $view->burialPlaceGraveSiteGeoPositionError);
        $this->assertSame(null,                                          $view->burialPlaceColumbariumNicheColumbariumId);
        $this->assertSame(null,                                          $view->burialPlaceColumbariumNicheColumbariumName);
        $this->assertSame(null,                                          $view->burialPlaceColumbariumNicheRowInColumbarium);
        $this->assertSame(null,                                          $view->burialPlaceColumbariumNicheNumber);
        $this->assertSame(null,                                          $view->burialPlaceColumbariumNicheGeoPositionLatitude);
        $this->assertSame(null,                                          $view->burialPlaceColumbariumNicheGeoPositionLongitude);
        $this->assertSame(null,                                          $view->burialPlaceColumbariumNicheGeoPositionError);
        $this->assertSame('002',                                         $view->burialPlaceMemorialTreeNumber);
        $this->assertSame('54.950457',                                   $view->burialPlaceMemorialTreeGeoPositionLatitude);
        $this->assertSame('82.7972252',                                  $view->burialPlaceMemorialTreeGeoPositionLongitude);
        $this->assertSame('0.5',                                         $view->burialPlaceMemorialTreeGeoPositionError);
        $this->assertSame(null,                                          $view->burialContainerType);
        $this->assertSame(null,                                          $view->burialContainerCoffinSize);
        $this->assertSame(null,                                          $view->burialContainerCoffinShape);
        $this->assertSame(null,                                          $view->burialContainerCoffinIsNonStandard);
        $this->assertSame(null,                                          $view->buriedAt);
        $this->assertValidDateTimeValue($view->createdAt);
        $this->assertValidDateTimeValue($view->updatedAt);
    }

    private function testItReturnsBurialViewForB004(): void
    {
        $view = $this->burialFetcher->getViewById('B004');
        $this->assertInstanceOf(BurialView::class, $view);
        $this->assertSame('B004',                                        $view->id);
        $this->assertSame('234117890',                                   $view->code);
        $this->assertSame(BurialType::COFFIN_IN_GRAVE_SITE,              $view->type);
        $this->assertSame('D004',                                        $view->deceasedId);
        $this->assertSame('NP005',                                       $view->deceasedNaturalPersonId);
        $this->assertSame('Жданова Инга Григорьевна',                    $view->deceasedNaturalPersonFullName);
        $this->assertSame('1979-02-12',                                  $view->deceasedNaturalPersonBornAt);
        $this->assertSame('2022-03-10',                                  $view->deceasedDiedAt);
        $this->assertSame(null,                                          $view->deceasedAge);
        $this->assertSame(null,                                          $view->deceasedDeathCertificateId);
        $this->assertSame(null,                                          $view->deceasedCauseOfDeathId);
        $this->assertSame('JP004',                                       $view->customerId);
        $this->assertSame(JuristicPerson::CLASS_SHORTCUT,                $view->customerType);
        $this->assertSame(null,                                          $view->customerNaturalPersonFullName);
        $this->assertSame(null,                                          $view->customerNaturalPersonPhone);
        $this->assertSame(null,                                          $view->customerNaturalPersonPhoneAdditional);
        $this->assertSame(null,                                          $view->customerNaturalPersonEmail);
        $this->assertSame(null,                                          $view->customerNaturalPersonAddress);
        $this->assertSame(null,                                          $view->customerNaturalPersonBornAt);
        $this->assertSame(null,                                          $view->customerNaturalPersonPlaceOfBirth);
        $this->assertSame(null,                                          $view->customerNaturalPersonPassportSeries);
        $this->assertSame(null,                                          $view->customerNaturalPersonPassportNumber);
        $this->assertSame(null,                                          $view->customerNaturalPersonPassportIssuedAt);
        $this->assertSame(null,                                          $view->customerNaturalPersonPassportIssuedBy);
        $this->assertSame(null,                                          $view->customerNaturalPersonPassportDivisionCode);
        $this->assertSame(null,                                          $view->customerSoleProprietorName);
        $this->assertSame(null,                                          $view->customerSoleProprietorInn);
        $this->assertSame(null,                                          $view->customerSoleProprietorOgrnip);
        $this->assertSame(null,                                          $view->customerSoleProprietorOkpo);
        $this->assertSame(null,                                          $view->customerSoleProprietorOkved);
        $this->assertSame(null,                                          $view->customerSoleProprietorRegistrationAddress);
        $this->assertSame(null,                                          $view->customerSoleProprietorActualLocationAddress);
        $this->assertSame(null,                                          $view->customerSoleProprietorBankDetailsBankName);
        $this->assertSame(null,                                          $view->customerSoleProprietorBankDetailsBik);
        $this->assertSame(null,                                          $view->customerSoleProprietorBankDetailsCorrespondentAccount);
        $this->assertSame(null,                                          $view->customerSoleProprietorBankDetailsCurrentAccount);
        $this->assertSame(null,                                          $view->customerSoleProprietorPhone);
        $this->assertSame(null,                                          $view->customerSoleProprietorPhoneAdditional);
        $this->assertSame(null,                                          $view->customerSoleProprietorFax);
        $this->assertSame(null,                                          $view->customerSoleProprietorEmail);
        $this->assertSame(null,                                          $view->customerSoleProprietorWebsite);
        $this->assertSame('МУП "Новосибирский метрополитен"',            $view->customerJuristicPersonName);
        $this->assertSame(null,                                          $view->customerJuristicPersonInn);
        $this->assertSame(null,                                          $view->customerJuristicPersonKpp);
        $this->assertSame(null,                                          $view->customerJuristicPersonOgrn);
        $this->assertSame(null,                                          $view->customerJuristicPersonOkpo);
        $this->assertSame(null,                                          $view->customerJuristicPersonOkved);
        $this->assertSame(null,                                          $view->customerJuristicPersonLegalAddress);
        $this->assertSame(null,                                          $view->customerJuristicPersonPostalAddress);
        $this->assertSame(null,                                          $view->customerJuristicPersonBankDetailsBankName);
        $this->assertSame(null,                                          $view->customerJuristicPersonBankDetailsBik);
        $this->assertSame(null,                                          $view->customerJuristicPersonBankDetailsCorrespondentAccount);
        $this->assertSame(null,                                          $view->customerJuristicPersonBankDetailsCurrentAccount);
        $this->assertSame(null,                                          $view->customerJuristicPersonPhone);
        $this->assertSame(null,                                          $view->customerJuristicPersonPhoneAdditional);
        $this->assertSame(null,                                          $view->customerJuristicPersonFax);
        $this->assertSame(null,                                          $view->customerJuristicPersonGeneralDirector);
        $this->assertSame(null,                                          $view->customerJuristicPersonEmail);
        $this->assertSame(null,                                          $view->customerJuristicPersonWebsite);
        $this->assertSame(null,                                          $view->burialPlaceOwnerId);
        $this->assertSame(null,                                          $view->burialPlaceOwnerFullName);
        $this->assertSame(null,                                          $view->burialPlaceOwnerPhone);
        $this->assertSame(null,                                          $view->burialPlaceOwnerPhoneAdditional);
        $this->assertSame(null,                                          $view->burialPlaceOwnerEmail);
        $this->assertSame(null,                                          $view->burialPlaceOwnerAddress);
        $this->assertSame(null,                                          $view->burialPlaceOwnerBornAt);
        $this->assertSame(null,                                          $view->burialPlaceOwnerPlaceOfBirth);
        $this->assertSame(null,                                          $view->burialPlaceOwnerPassportSeries);
        $this->assertSame(null,                                          $view->burialPlaceOwnerPassportNumber);
        $this->assertSame(null,                                          $view->burialPlaceOwnerPassportIssuedAt);
        $this->assertSame(null,                                          $view->burialPlaceOwnerPassportIssuedBy);
        $this->assertSame(null,                                          $view->burialPlaceOwnerPassportDivisionCode);
        $this->assertSame('FC001',                                       $view->funeralCompanyId);
        $this->assertSame(null,                                          $view->burialChainId);
        $this->assertSame('GS001',                                       $view->burialPlaceId);
        $this->assertSame(GraveSite::CLASS_SHORTCUT,                     $view->burialPlaceType);
        $this->assertSame('CB001',                                       $view->burialPlaceGraveSiteCemeteryBlockId);
        $this->assertSame('воинский',                                    $view->burialPlaceGraveSiteCemeteryBlockName);
        $this->assertSame(1,                                             $view->burialPlaceGraveSiteRowInBlock);
        $this->assertSame(null,                                          $view->burialPlaceGraveSitePositionInRow);
        $this->assertSame(null,                                          $view->burialPlaceGraveSiteSize);
        $this->assertSame(null,                                          $view->burialPlaceGraveSiteGeoPositionLatitude);
        $this->assertSame(null,                                          $view->burialPlaceGraveSiteGeoPositionLongitude);
        $this->assertSame(null,                                          $view->burialPlaceGraveSiteGeoPositionError);
        $this->assertSame(null,                                          $view->burialPlaceColumbariumNicheColumbariumId);
        $this->assertSame(null,                                          $view->burialPlaceColumbariumNicheColumbariumName);
        $this->assertSame(null,                                          $view->burialPlaceColumbariumNicheRowInColumbarium);
        $this->assertSame(null,                                          $view->burialPlaceColumbariumNicheNumber);
        $this->assertSame(null,                                          $view->burialPlaceColumbariumNicheGeoPositionLatitude);
        $this->assertSame(null,                                          $view->burialPlaceColumbariumNicheGeoPositionLongitude);
        $this->assertSame(null,                                          $view->burialPlaceColumbariumNicheGeoPositionError);
        $this->assertSame(null,                                          $view->burialPlaceMemorialTreeNumber);
        $this->assertSame(null,                                          $view->burialPlaceMemorialTreeGeoPositionLatitude);
        $this->assertSame(null,                                          $view->burialPlaceMemorialTreeGeoPositionLongitude);
        $this->assertSame(null,                                          $view->burialPlaceMemorialTreeGeoPositionError);
        $this->assertSame(null,                                          $view->burialContainerType);
        $this->assertSame(null,                                          $view->burialContainerCoffinSize);
        $this->assertSame(null,                                          $view->burialContainerCoffinShape);
        $this->assertSame(null,                                          $view->burialContainerCoffinIsNonStandard);
        $this->assertSame(null,                                          $view->buriedAt);
        $this->assertValidDateTimeValue($view->createdAt);
        $this->assertValidDateTimeValue($view->updatedAt);
    }

    private function testItReturnsBurialViewForB005(): void
    {
        $view = $this->burialFetcher->getViewById('B005');
        $this->assertInstanceOf(BurialView::class, $view);
        $this->assertSame('B005',                                        $view->id);
        $this->assertSame('11005',                                       $view->code);
        $this->assertSame(BurialType::COFFIN_IN_GRAVE_SITE,              $view->type);
        $this->assertSame('D005',                                        $view->deceasedId);
        $this->assertSame('NP004',                                       $view->deceasedNaturalPersonId);
        $this->assertSame('Соколов Герман Маркович',                     $view->deceasedNaturalPersonFullName);
        $this->assertSame(null,                                          $view->deceasedNaturalPersonBornAt);
        $this->assertSame('2010-01-26',                                  $view->deceasedDiedAt);
        $this->assertSame(null,                                          $view->deceasedAge);
        $this->assertSame(null,                                          $view->deceasedDeathCertificateId);
        $this->assertSame(null,                                          $view->deceasedCauseOfDeathId);
        $this->assertSame('SP003',                                       $view->customerId);
        $this->assertSame(SoleProprietor::CLASS_SHORTCUT,                $view->customerType);
        $this->assertSame(null,                                          $view->customerNaturalPersonFullName);
        $this->assertSame(null,                                          $view->customerNaturalPersonPhone);
        $this->assertSame(null,                                          $view->customerNaturalPersonPhoneAdditional);
        $this->assertSame(null,                                          $view->customerNaturalPersonEmail);
        $this->assertSame(null,                                          $view->customerNaturalPersonAddress);
        $this->assertSame(null,                                          $view->customerNaturalPersonBornAt);
        $this->assertSame(null,                                          $view->customerNaturalPersonPlaceOfBirth);
        $this->assertSame(null,                                          $view->customerNaturalPersonPassportSeries);
        $this->assertSame(null,                                          $view->customerNaturalPersonPassportNumber);
        $this->assertSame(null,                                          $view->customerNaturalPersonPassportIssuedAt);
        $this->assertSame(null,                                          $view->customerNaturalPersonPassportIssuedBy);
        $this->assertSame(null,                                          $view->customerNaturalPersonPassportDivisionCode);
        $this->assertSame('ИП Сидоров Сидр Сидорович',                   $view->customerSoleProprietorName);
        $this->assertSame('391600743661',                                $view->customerSoleProprietorInn);
        $this->assertSame(null,                                          $view->customerSoleProprietorOgrnip);
        $this->assertSame(null,                                          $view->customerSoleProprietorOkpo);
        $this->assertSame(null,                                          $view->customerSoleProprietorOkved);
        $this->assertSame(null,                                          $view->customerSoleProprietorRegistrationAddress);
        $this->assertSame('с. Каменка, д. 14',                           $view->customerSoleProprietorActualLocationAddress);
        $this->assertSame(null,                                          $view->customerSoleProprietorBankDetailsBankName);
        $this->assertSame(null,                                          $view->customerSoleProprietorBankDetailsBik);
        $this->assertSame(null,                                          $view->customerSoleProprietorBankDetailsCorrespondentAccount);
        $this->assertSame(null,                                          $view->customerSoleProprietorBankDetailsCurrentAccount);
        $this->assertSame('8(383)147-22-33',                             $view->customerSoleProprietorPhone);
        $this->assertSame(null,                                          $view->customerSoleProprietorPhoneAdditional);
        $this->assertSame(null,                                          $view->customerSoleProprietorFax);
        $this->assertSame(null,                                          $view->customerSoleProprietorEmail);
        $this->assertSame(null,                                          $view->customerSoleProprietorWebsite);
        $this->assertSame(null,                                          $view->customerJuristicPersonName);
        $this->assertSame(null,                                          $view->customerJuristicPersonInn);
        $this->assertSame(null,                                          $view->customerJuristicPersonKpp);
        $this->assertSame(null,                                          $view->customerJuristicPersonOgrn);
        $this->assertSame(null,                                          $view->customerJuristicPersonOkpo);
        $this->assertSame(null,                                          $view->customerJuristicPersonOkved);
        $this->assertSame(null,                                          $view->customerJuristicPersonLegalAddress);
        $this->assertSame(null,                                          $view->customerJuristicPersonPostalAddress);
        $this->assertSame(null,                                          $view->customerJuristicPersonBankDetailsBankName);
        $this->assertSame(null,                                          $view->customerJuristicPersonBankDetailsBik);
        $this->assertSame(null,                                          $view->customerJuristicPersonBankDetailsCorrespondentAccount);
        $this->assertSame(null,                                          $view->customerJuristicPersonBankDetailsCurrentAccount);
        $this->assertSame(null,                                          $view->customerJuristicPersonPhone);
        $this->assertSame(null,                                          $view->customerJuristicPersonPhoneAdditional);
        $this->assertSame(null,                                          $view->customerJuristicPersonFax);
        $this->assertSame(null,                                          $view->customerJuristicPersonGeneralDirector);
        $this->assertSame(null,                                          $view->customerJuristicPersonEmail);
        $this->assertSame(null,                                          $view->customerJuristicPersonWebsite);
        $this->assertSame('NP008',                                       $view->burialPlaceOwnerId);
        $this->assertSame('Беляев Мечеслав Федорович',                   $view->burialPlaceOwnerFullName);
        $this->assertSame(null,                                          $view->burialPlaceOwnerPhone);
        $this->assertSame(null,                                          $view->burialPlaceOwnerPhoneAdditional);
        $this->assertSame('mecheslav.belyaev@gmail.com',                 $view->burialPlaceOwnerEmail);
        $this->assertSame(null,                                          $view->burialPlaceOwnerAddress);
        $this->assertSame(null,                                          $view->burialPlaceOwnerBornAt);
        $this->assertSame(null,                                          $view->burialPlaceOwnerPlaceOfBirth);
        $this->assertSame('2345',                                        $view->burialPlaceOwnerPassportSeries);
        $this->assertSame('162354',                                      $view->burialPlaceOwnerPassportNumber);
        $this->assertSame('1981-10-20',                                  $view->burialPlaceOwnerPassportIssuedAt);
        $this->assertSame('Отделом МВД Ленинского района г. Пензы',      $view->burialPlaceOwnerPassportIssuedBy);
        $this->assertSame(null,                                          $view->burialPlaceOwnerPassportDivisionCode);
        $this->assertSame('FC002',                                       $view->funeralCompanyId);
        $this->assertSame(null,                                          $view->burialChainId);
        $this->assertSame('GS002',                                       $view->burialPlaceId);
        $this->assertSame(GraveSite::CLASS_SHORTCUT,                     $view->burialPlaceType);
        $this->assertSame('CB002',                                       $view->burialPlaceGraveSiteCemeteryBlockId);
        $this->assertSame('общий А',                                     $view->burialPlaceGraveSiteCemeteryBlockName);
        $this->assertSame(3,                                             $view->burialPlaceGraveSiteRowInBlock);
        $this->assertSame(4,                                             $view->burialPlaceGraveSitePositionInRow);
        $this->assertSame(null,                                          $view->burialPlaceGraveSiteSize);
        $this->assertSame('54.950357',                                   $view->burialPlaceGraveSiteGeoPositionLatitude);
        $this->assertSame('82.7972252',                                  $view->burialPlaceGraveSiteGeoPositionLongitude);
        $this->assertSame('0.5',                                         $view->burialPlaceGraveSiteGeoPositionError);
        $this->assertSame(null,                                          $view->burialPlaceColumbariumNicheColumbariumId);
        $this->assertSame(null,                                          $view->burialPlaceColumbariumNicheColumbariumName);
        $this->assertSame(null,                                          $view->burialPlaceColumbariumNicheRowInColumbarium);
        $this->assertSame(null,                                          $view->burialPlaceColumbariumNicheNumber);
        $this->assertSame(null,                                          $view->burialPlaceColumbariumNicheGeoPositionLatitude);
        $this->assertSame(null,                                          $view->burialPlaceColumbariumNicheGeoPositionLongitude);
        $this->assertSame(null,                                          $view->burialPlaceColumbariumNicheGeoPositionError);
        $this->assertSame(null,                                          $view->burialPlaceMemorialTreeNumber);
        $this->assertSame(null,                                          $view->burialPlaceMemorialTreeGeoPositionLatitude);
        $this->assertSame(null,                                          $view->burialPlaceMemorialTreeGeoPositionLongitude);
        $this->assertSame(null,                                          $view->burialPlaceMemorialTreeGeoPositionError);
        $this->assertSame(null,                                          $view->burialContainerType);
        $this->assertSame(null,                                          $view->burialContainerCoffinSize);
        $this->assertSame(null,                                          $view->burialContainerCoffinShape);
        $this->assertSame(null,                                          $view->burialContainerCoffinIsNonStandard);
        $this->assertSame('2010-01-28 12:55:00',                         $view->buriedAt);
        $this->assertValidDateTimeValue($view->createdAt);
        $this->assertValidDateTimeValue($view->updatedAt);
    }

    private function testItReturnsBurialViewForB006(): void
    {
        $view = $this->burialFetcher->getViewById('B006');
        $this->assertInstanceOf(BurialView::class, $view);
        $this->assertSame('B006',                                        $view->id);
        $this->assertSame('11006',                                       $view->code);
        $this->assertSame(BurialType::COFFIN_IN_GRAVE_SITE,              $view->type);
        $this->assertSame('D006',                                        $view->deceasedId);
        $this->assertSame('NP006',                                       $view->deceasedNaturalPersonId);
        $this->assertSame('Гришина Устинья Ярославовна',                 $view->deceasedNaturalPersonFullName);
        $this->assertSame(null,                                          $view->deceasedNaturalPersonBornAt);
        $this->assertSame('2021-12-03',                                  $view->deceasedDiedAt);
        $this->assertSame(null,                                          $view->deceasedAge);
        $this->assertSame(null,                                          $view->deceasedDeathCertificateId);
        $this->assertSame(null,                                          $view->deceasedCauseOfDeathId);
        $this->assertSame('NP007',                                       $view->customerId);
        $this->assertSame(NaturalPerson::CLASS_SHORTCUT,                 $view->customerType);
        $this->assertSame('Громов Никифор Рудольфович',                  $view->customerNaturalPersonFullName);
        $this->assertSame(null,                                          $view->customerNaturalPersonPhone);
        $this->assertSame(null,                                          $view->customerNaturalPersonPhoneAdditional);
        $this->assertSame(null,                                          $view->customerNaturalPersonEmail);
        $this->assertSame('Новосибирск, ул. Н.-Данченко, д. 18, кв. 17', $view->customerNaturalPersonAddress);
        $this->assertSame('1915-09-24',                                  $view->customerNaturalPersonBornAt);
        $this->assertSame(null,                                          $view->customerNaturalPersonPlaceOfBirth);
        $this->assertSame(null,                                          $view->customerNaturalPersonPassportSeries);
        $this->assertSame(null,                                          $view->customerNaturalPersonPassportNumber);
        $this->assertSame(null,                                          $view->customerNaturalPersonPassportIssuedAt);
        $this->assertSame(null,                                          $view->customerNaturalPersonPassportIssuedBy);
        $this->assertSame(null,                                          $view->customerNaturalPersonPassportDivisionCode);
        $this->assertSame(null,                                          $view->customerSoleProprietorName);
        $this->assertSame(null,                                          $view->customerSoleProprietorInn);
        $this->assertSame(null,                                          $view->customerSoleProprietorOgrnip);
        $this->assertSame(null,                                          $view->customerSoleProprietorOkpo);
        $this->assertSame(null,                                          $view->customerSoleProprietorOkved);
        $this->assertSame(null,                                          $view->customerSoleProprietorRegistrationAddress);
        $this->assertSame(null,                                          $view->customerSoleProprietorActualLocationAddress);
        $this->assertSame(null,                                          $view->customerSoleProprietorBankDetailsBankName);
        $this->assertSame(null,                                          $view->customerSoleProprietorBankDetailsBik);
        $this->assertSame(null,                                          $view->customerSoleProprietorBankDetailsCorrespondentAccount);
        $this->assertSame(null,                                          $view->customerSoleProprietorBankDetailsCurrentAccount);
        $this->assertSame(null,                                          $view->customerSoleProprietorPhone);
        $this->assertSame(null,                                          $view->customerSoleProprietorPhoneAdditional);
        $this->assertSame(null,                                          $view->customerSoleProprietorFax);
        $this->assertSame(null,                                          $view->customerSoleProprietorEmail);
        $this->assertSame(null,                                          $view->customerSoleProprietorWebsite);
        $this->assertSame(null,                                          $view->customerJuristicPersonName);
        $this->assertSame(null,                                          $view->customerJuristicPersonInn);
        $this->assertSame(null,                                          $view->customerJuristicPersonKpp);
        $this->assertSame(null,                                          $view->customerJuristicPersonOgrn);
        $this->assertSame(null,                                          $view->customerJuristicPersonOkpo);
        $this->assertSame(null,                                          $view->customerJuristicPersonOkved);
        $this->assertSame(null,                                          $view->customerJuristicPersonLegalAddress);
        $this->assertSame(null,                                          $view->customerJuristicPersonPostalAddress);
        $this->assertSame(null,                                          $view->customerJuristicPersonBankDetailsBankName);
        $this->assertSame(null,                                          $view->customerJuristicPersonBankDetailsBik);
        $this->assertSame(null,                                          $view->customerJuristicPersonBankDetailsCorrespondentAccount);
        $this->assertSame(null,                                          $view->customerJuristicPersonBankDetailsCurrentAccount);
        $this->assertSame(null,                                          $view->customerJuristicPersonPhone);
        $this->assertSame(null,                                          $view->customerJuristicPersonPhoneAdditional);
        $this->assertSame(null,                                          $view->customerJuristicPersonFax);
        $this->assertSame(null,                                          $view->customerJuristicPersonGeneralDirector);
        $this->assertSame(null,                                          $view->customerJuristicPersonEmail);
        $this->assertSame(null,                                          $view->customerJuristicPersonWebsite);
        $this->assertSame(null,                                          $view->burialPlaceOwnerId);
        $this->assertSame(null,                                          $view->burialPlaceOwnerFullName);
        $this->assertSame(null,                                          $view->burialPlaceOwnerPhone);
        $this->assertSame(null,                                          $view->burialPlaceOwnerPhoneAdditional);
        $this->assertSame(null,                                          $view->burialPlaceOwnerEmail);
        $this->assertSame(null,                                          $view->burialPlaceOwnerAddress);
        $this->assertSame(null,                                          $view->burialPlaceOwnerBornAt);
        $this->assertSame(null,                                          $view->burialPlaceOwnerPlaceOfBirth);
        $this->assertSame(null,                                          $view->burialPlaceOwnerPassportSeries);
        $this->assertSame(null,                                          $view->burialPlaceOwnerPassportNumber);
        $this->assertSame(null,                                          $view->burialPlaceOwnerPassportIssuedAt);
        $this->assertSame(null,                                          $view->burialPlaceOwnerPassportIssuedBy);
        $this->assertSame(null,                                          $view->burialPlaceOwnerPassportDivisionCode);
        $this->assertSame('FC003',                                       $view->funeralCompanyId);
        $this->assertSame(null,                                          $view->burialChainId);
        $this->assertSame(null,                                          $view->burialPlaceId);
        $this->assertSame(null,                                          $view->burialPlaceType);
        $this->assertSame(null,                                          $view->burialPlaceGraveSiteCemeteryBlockId);
        $this->assertSame(null,                                          $view->burialPlaceGraveSiteCemeteryBlockName);
        $this->assertSame(null,                                          $view->burialPlaceGraveSiteRowInBlock);
        $this->assertSame(null,                                          $view->burialPlaceGraveSitePositionInRow);
        $this->assertSame(null,                                          $view->burialPlaceGraveSiteSize);
        $this->assertSame(null,                                          $view->burialPlaceGraveSiteGeoPositionLatitude);
        $this->assertSame(null,                                          $view->burialPlaceGraveSiteGeoPositionLongitude);
        $this->assertSame(null,                                          $view->burialPlaceGraveSiteGeoPositionError);
        $this->assertSame(null,                                          $view->burialPlaceColumbariumNicheColumbariumId);
        $this->assertSame(null,                                          $view->burialPlaceColumbariumNicheColumbariumName);
        $this->assertSame(null,                                          $view->burialPlaceColumbariumNicheRowInColumbarium);
        $this->assertSame(null,                                          $view->burialPlaceColumbariumNicheNumber);
        $this->assertSame(null,                                          $view->burialPlaceColumbariumNicheGeoPositionLatitude);
        $this->assertSame(null,                                          $view->burialPlaceColumbariumNicheGeoPositionLongitude);
        $this->assertSame(null,                                          $view->burialPlaceColumbariumNicheGeoPositionError);
        $this->assertSame(null,                                          $view->burialPlaceMemorialTreeNumber);
        $this->assertSame(null,                                          $view->burialPlaceMemorialTreeGeoPositionLatitude);
        $this->assertSame(null,                                          $view->burialPlaceMemorialTreeGeoPositionLongitude);
        $this->assertSame(null,                                          $view->burialPlaceMemorialTreeGeoPositionError);
        $this->assertSame(null,                                          $view->burialContainerType);
        $this->assertSame(null,                                          $view->burialContainerCoffinSize);
        $this->assertSame(null,                                          $view->burialContainerCoffinShape);
        $this->assertSame(null,                                          $view->burialContainerCoffinIsNonStandard);
        $this->assertSame(null,                                          $view->buriedAt);
        $this->assertValidDateTimeValue($view->createdAt);
        $this->assertValidDateTimeValue($view->updatedAt);
    }

    private function testItReturnsBurialViewForB007(): void
    {
        $view = $this->burialFetcher->getViewById('B007');
        $this->assertInstanceOf(BurialView::class, $view);
        $this->assertSame('B007',                                        $view->id);
        $this->assertSame('01',                                          $view->code);
        $this->assertSame(BurialType::COFFIN_IN_GRAVE_SITE,              $view->type);
        $this->assertSame('D007',                                        $view->deceasedId);
        $this->assertSame('NP009',                                       $view->deceasedNaturalPersonId);
        $this->assertSame('Никонов Родион Митрофанович',                 $view->deceasedNaturalPersonFullName);
        $this->assertSame(null,                                          $view->deceasedNaturalPersonBornAt);
        $this->assertSame('1980-05-26',                                  $view->deceasedDiedAt);
        $this->assertSame(null,                                          $view->deceasedAge);
        $this->assertSame(null,                                          $view->deceasedDeathCertificateId);
        $this->assertSame(null,                                          $view->deceasedCauseOfDeathId);
        $this->assertSame(null,                                          $view->customerId);
        $this->assertSame(null,                                          $view->customerType);
        $this->assertSame(null ,                                         $view->customerNaturalPersonFullName);
        $this->assertSame(null,                                          $view->customerNaturalPersonPhone);
        $this->assertSame(null,                                          $view->customerNaturalPersonPhoneAdditional);
        $this->assertSame(null,                                          $view->customerNaturalPersonEmail);
        $this->assertSame(null,                                          $view->customerNaturalPersonAddress);
        $this->assertSame(null,                                          $view->customerNaturalPersonBornAt);
        $this->assertSame(null,                                          $view->customerNaturalPersonPlaceOfBirth);
        $this->assertSame(null,                                          $view->customerNaturalPersonPassportSeries);
        $this->assertSame(null,                                          $view->customerNaturalPersonPassportNumber);
        $this->assertSame(null,                                          $view->customerNaturalPersonPassportIssuedAt);
        $this->assertSame(null,                                          $view->customerNaturalPersonPassportIssuedBy);
        $this->assertSame(null,                                          $view->customerNaturalPersonPassportDivisionCode);
        $this->assertSame(null,                                          $view->customerSoleProprietorName);
        $this->assertSame(null,                                          $view->customerSoleProprietorInn);
        $this->assertSame(null,                                          $view->customerSoleProprietorOgrnip);
        $this->assertSame(null,                                          $view->customerSoleProprietorOkpo);
        $this->assertSame(null,                                          $view->customerSoleProprietorOkved);
        $this->assertSame(null,                                          $view->customerSoleProprietorRegistrationAddress);
        $this->assertSame(null,                                          $view->customerSoleProprietorActualLocationAddress);
        $this->assertSame(null,                                          $view->customerSoleProprietorBankDetailsBankName);
        $this->assertSame(null,                                          $view->customerSoleProprietorBankDetailsBik);
        $this->assertSame(null,                                          $view->customerSoleProprietorBankDetailsCorrespondentAccount);
        $this->assertSame(null,                                          $view->customerSoleProprietorBankDetailsCurrentAccount);
        $this->assertSame(null,                                          $view->customerSoleProprietorPhone);
        $this->assertSame(null,                                          $view->customerSoleProprietorPhoneAdditional);
        $this->assertSame(null,                                          $view->customerSoleProprietorFax);
        $this->assertSame(null,                                          $view->customerSoleProprietorEmail);
        $this->assertSame(null,                                          $view->customerSoleProprietorWebsite);
        $this->assertSame(null,                                          $view->customerJuristicPersonName);
        $this->assertSame(null,                                          $view->customerJuristicPersonInn);
        $this->assertSame(null,                                          $view->customerJuristicPersonKpp);
        $this->assertSame(null,                                          $view->customerJuristicPersonOgrn);
        $this->assertSame(null,                                          $view->customerJuristicPersonOkpo);
        $this->assertSame(null,                                          $view->customerJuristicPersonOkved);
        $this->assertSame(null,                                          $view->customerJuristicPersonLegalAddress);
        $this->assertSame(null,                                          $view->customerJuristicPersonPostalAddress);
        $this->assertSame(null,                                          $view->customerJuristicPersonBankDetailsBankName);
        $this->assertSame(null,                                          $view->customerJuristicPersonBankDetailsBik);
        $this->assertSame(null,                                          $view->customerJuristicPersonBankDetailsCorrespondentAccount);
        $this->assertSame(null,                                          $view->customerJuristicPersonBankDetailsCurrentAccount);
        $this->assertSame(null,                                          $view->customerJuristicPersonPhone);
        $this->assertSame(null,                                          $view->customerJuristicPersonPhoneAdditional);
        $this->assertSame(null,                                          $view->customerJuristicPersonFax);
        $this->assertSame(null,                                          $view->customerJuristicPersonGeneralDirector);
        $this->assertSame(null,                                          $view->customerJuristicPersonEmail);
        $this->assertSame(null,                                          $view->customerJuristicPersonWebsite);
        $this->assertSame(null,                                          $view->burialPlaceOwnerId);
        $this->assertSame(null,                                          $view->burialPlaceOwnerFullName);
        $this->assertSame(null,                                          $view->burialPlaceOwnerPhone);
        $this->assertSame(null,                                          $view->burialPlaceOwnerPhoneAdditional);
        $this->assertSame(null,                                          $view->burialPlaceOwnerEmail);
        $this->assertSame(null,                                          $view->burialPlaceOwnerAddress);
        $this->assertSame(null,                                          $view->burialPlaceOwnerBornAt);
        $this->assertSame(null,                                          $view->burialPlaceOwnerPlaceOfBirth);
        $this->assertSame(null,                                          $view->burialPlaceOwnerPassportSeries);
        $this->assertSame(null,                                          $view->burialPlaceOwnerPassportNumber);
        $this->assertSame(null,                                          $view->burialPlaceOwnerPassportIssuedAt);
        $this->assertSame(null,                                          $view->burialPlaceOwnerPassportIssuedBy);
        $this->assertSame(null,                                          $view->burialPlaceOwnerPassportDivisionCode);
        $this->assertSame(null,                                          $view->funeralCompanyId);
        $this->assertSame(null,                                          $view->burialChainId);
        $this->assertSame('GS005',                                       $view->burialPlaceId);
        $this->assertSame(GraveSite::CLASS_SHORTCUT,                     $view->burialPlaceType);
        $this->assertSame('CB004',                                       $view->burialPlaceGraveSiteCemeteryBlockId);
        $this->assertSame('мусульманский',                               $view->burialPlaceGraveSiteCemeteryBlockName);
        $this->assertSame(3,                                             $view->burialPlaceGraveSiteRowInBlock);
        $this->assertSame(null,                                          $view->burialPlaceGraveSitePositionInRow);
        $this->assertSame(null,                                          $view->burialPlaceGraveSiteSize);
        $this->assertSame(null,                                          $view->burialPlaceGraveSiteGeoPositionLatitude);
        $this->assertSame(null,                                          $view->burialPlaceGraveSiteGeoPositionLongitude);
        $this->assertSame(null,                                          $view->burialPlaceGraveSiteGeoPositionError);
        $this->assertSame(null,                                          $view->burialPlaceColumbariumNicheColumbariumId);
        $this->assertSame(null,                                          $view->burialPlaceColumbariumNicheColumbariumName);
        $this->assertSame(null,                                          $view->burialPlaceColumbariumNicheRowInColumbarium);
        $this->assertSame(null,                                          $view->burialPlaceColumbariumNicheNumber);
        $this->assertSame(null,                                          $view->burialPlaceColumbariumNicheGeoPositionLatitude);
        $this->assertSame(null,                                          $view->burialPlaceColumbariumNicheGeoPositionLongitude);
        $this->assertSame(null,                                          $view->burialPlaceColumbariumNicheGeoPositionError);
        $this->assertSame(null,                                          $view->burialPlaceMemorialTreeNumber);
        $this->assertSame(null,                                          $view->burialPlaceMemorialTreeGeoPositionLatitude);
        $this->assertSame(null,                                          $view->burialPlaceMemorialTreeGeoPositionLongitude);
        $this->assertSame(null,                                          $view->burialPlaceMemorialTreeGeoPositionError);
        $this->assertSame(null,                                          $view->burialContainerType);
        $this->assertSame(null,                                          $view->burialContainerCoffinSize);
        $this->assertSame(null,                                          $view->burialContainerCoffinShape);
        $this->assertSame(null,                                          $view->burialContainerCoffinIsNonStandard);
        $this->assertSame(null,                                          $view->buriedAt);
        $this->assertValidDateTimeValue($view->createdAt);
        $this->assertValidDateTimeValue($view->updatedAt);
    }

    private function expectExceptionForNotFoundBurialById(string $burialId): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(\sprintf('Захоронение с ID "%s" не найдено.', $burialId));
    }
}
