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
        $burialView = $this->burialFetcher->getViewById('B001');
        $this->assertInstanceOf(BurialView::class, $burialView);
        $this->assertSame('B001',                                      $burialView->id);
        $this->assertSame('11',                                        $burialView->code);
        $this->assertSame(BurialType::URN_IN_COLUMBARIUM_NICHE,        $burialView->type);
        $this->assertSame('D001',                                      $burialView->deceasedId);
        $this->assertSame('NP001',                                     $burialView->deceasedNaturalPersonId);
        $this->assertSame('Егоров Абрам Даниилович',                   $burialView->deceasedNaturalPersonFullName);
        $this->assertSame(null,                                        $burialView->deceasedNaturalPersonBornAt);
        $this->assertSame('2021-12-01',                                $burialView->deceasedDiedAt);
        $this->assertSame(null,                                        $burialView->deceasedAge);
        $this->assertSame(null,                                        $burialView->deceasedDeathCertificateId);
        $this->assertSame(null,                                        $burialView->deceasedCauseOfDeathId);
        $this->assertSame('NP005',                                     $burialView->customerId);
        $this->assertSame(NaturalPerson::CLASS_SHORTCUT,               $burialView->customerType);
        $this->assertSame('Жданова Инга Григорьевна',                  $burialView->customerNaturalPersonFullName);
        $this->assertSame('+7-913-771-22-33',                          $burialView->customerNaturalPersonPhone);
        $this->assertSame(null,                                        $burialView->customerNaturalPersonPhoneAdditional);
        $this->assertSame(null,                                        $burialView->customerNaturalPersonEmail);
        $this->assertSame('Новосибирск, ул. Ленина, д. 1',             $burialView->customerNaturalPersonAddress);
        $this->assertSame('1979-02-12',                                $burialView->customerNaturalPersonBornAt);
        $this->assertSame(null,                                        $burialView->customerNaturalPersonPlaceOfBirth);
        $this->assertSame('1234',                                      $burialView->customerNaturalPersonPassportSeries);
        $this->assertSame('567890',                                    $burialView->customerNaturalPersonPassportNumber);
        $this->assertSame('2002-10-28',                                $burialView->customerNaturalPersonPassportIssuedAt);
        $this->assertSame('УВД Кировского района города Новосибирска', $burialView->customerNaturalPersonPassportIssuedBy);
        $this->assertSame('540-001',                                   $burialView->customerNaturalPersonPassportDivisionCode);
        $this->assertSame(null,                                        $burialView->customerSoleProprietorName);
        $this->assertSame(null,                                        $burialView->customerSoleProprietorInn);
        $this->assertSame(null,                                        $burialView->customerSoleProprietorOgrnip);
        $this->assertSame(null,                                        $burialView->customerSoleProprietorOkpo);
        $this->assertSame(null,                                        $burialView->customerSoleProprietorOkved);
        $this->assertSame(null,                                        $burialView->customerSoleProprietorRegistrationAddress);
        $this->assertSame(null,                                        $burialView->customerSoleProprietorActualLocationAddress);
        $this->assertSame(null,                                        $burialView->customerSoleProprietorBankDetailsBankName);
        $this->assertSame(null,                                        $burialView->customerSoleProprietorBankDetailsBik);
        $this->assertSame(null,                                        $burialView->customerSoleProprietorBankDetailsCorrespondentAccount);
        $this->assertSame(null,                                        $burialView->customerSoleProprietorBankDetailsCurrentAccount);
        $this->assertSame(null,                                        $burialView->customerSoleProprietorPhone);
        $this->assertSame(null,                                        $burialView->customerSoleProprietorPhoneAdditional);
        $this->assertSame(null,                                        $burialView->customerSoleProprietorFax);
        $this->assertSame(null,                                        $burialView->customerSoleProprietorEmail);
        $this->assertSame(null,                                        $burialView->customerSoleProprietorWebsite);
        $this->assertSame(null,                                        $burialView->customerJuristicPersonName);
        $this->assertSame(null,                                        $burialView->customerJuristicPersonInn);
        $this->assertSame(null,                                        $burialView->customerJuristicPersonKpp);
        $this->assertSame(null,                                        $burialView->customerJuristicPersonOgrn);
        $this->assertSame(null,                                        $burialView->customerJuristicPersonOkpo);
        $this->assertSame(null,                                        $burialView->customerJuristicPersonOkved);
        $this->assertSame(null,                                        $burialView->customerJuristicPersonLegalAddress);
        $this->assertSame(null,                                        $burialView->customerJuristicPersonPostalAddress);
        $this->assertSame(null,                                        $burialView->customerJuristicPersonBankDetailsBankName);
        $this->assertSame(null,                                        $burialView->customerJuristicPersonBankDetailsBik);
        $this->assertSame(null,                                        $burialView->customerJuristicPersonBankDetailsCorrespondentAccount);
        $this->assertSame(null,                                        $burialView->customerJuristicPersonBankDetailsCurrentAccount);
        $this->assertSame(null,                                        $burialView->customerJuristicPersonPhone);
        $this->assertSame(null,                                        $burialView->customerJuristicPersonPhoneAdditional);
        $this->assertSame(null,                                        $burialView->customerJuristicPersonFax);
        $this->assertSame(null,                                        $burialView->customerJuristicPersonGeneralDirector);
        $this->assertSame(null,                                        $burialView->customerJuristicPersonEmail);
        $this->assertSame(null,                                        $burialView->customerJuristicPersonWebsite);
        $this->assertSame(null,                                        $burialView->burialPlaceOwnerId);
        $this->assertSame(null,                                        $burialView->burialPlaceOwnerFullName);
        $this->assertSame(null,                                        $burialView->burialPlaceOwnerPhone);
        $this->assertSame(null,                                        $burialView->burialPlaceOwnerPhoneAdditional);
        $this->assertSame(null,                                        $burialView->burialPlaceOwnerEmail);
        $this->assertSame(null,                                        $burialView->burialPlaceOwnerAddress);
        $this->assertSame(null,                                        $burialView->burialPlaceOwnerBornAt);
        $this->assertSame(null,                                        $burialView->burialPlaceOwnerPlaceOfBirth);
        $this->assertSame(null,                                        $burialView->burialPlaceOwnerPassportSeries);
        $this->assertSame(null,                                        $burialView->burialPlaceOwnerPassportNumber);
        $this->assertSame(null,                                        $burialView->burialPlaceOwnerPassportIssuedAt);
        $this->assertSame(null,                                        $burialView->burialPlaceOwnerPassportIssuedBy);
        $this->assertSame(null,                                        $burialView->burialPlaceOwnerPassportDivisionCode);
        $this->assertSame(null,                                        $burialView->funeralCompanyId);
        $this->assertSame(null,                                        $burialView->burialChainId);
        $this->assertSame('CN002',                                     $burialView->burialPlaceId);
        $this->assertSame(ColumbariumNiche::CLASS_SHORTCUT,            $burialView->burialPlaceType);
        $this->assertSame(null,                                        $burialView->burialPlaceGraveSiteCemeteryBlockId);
        $this->assertSame(null,                                        $burialView->burialPlaceGraveSiteCemeteryBlockName);
        $this->assertSame(null,                                        $burialView->burialPlaceGraveSiteRowInBlock);
        $this->assertSame(null,                                        $burialView->burialPlaceGraveSitePositionInRow);
        $this->assertSame(null,                                        $burialView->burialPlaceGraveSiteSize);
        $this->assertSame(null,                                        $burialView->burialPlaceGraveSiteGeoPositionLatitude);
        $this->assertSame(null,                                        $burialView->burialPlaceGraveSiteGeoPositionLongitude);
        $this->assertSame(null,                                        $burialView->burialPlaceGraveSiteGeoPositionError);
        $this->assertSame('C002',                                      $burialView->burialPlaceColumbariumNicheColumbariumId);
        $this->assertSame('южный',                                     $burialView->burialPlaceColumbariumNicheColumbariumName);
        $this->assertSame(2,                                           $burialView->burialPlaceColumbariumNicheRowInColumbarium);
        $this->assertSame('002',                                       $burialView->burialPlaceColumbariumNicheNumber);
        $this->assertSame('54.95035712',                               $burialView->burialPlaceColumbariumNicheGeoPositionLatitude);
        $this->assertSame('82.79252',                                  $burialView->burialPlaceColumbariumNicheGeoPositionLongitude);
        $this->assertSame('0.5',                                       $burialView->burialPlaceColumbariumNicheGeoPositionError);
        $this->assertSame(null,                                        $burialView->burialPlaceMemorialTreeNumber);
        $this->assertSame(null,                                        $burialView->burialPlaceMemorialTreeGeoPositionLatitude);
        $this->assertSame(null,                                        $burialView->burialPlaceMemorialTreeGeoPositionLongitude);
        $this->assertSame(null,                                        $burialView->burialPlaceMemorialTreeGeoPositionError);
        $this->assertSame(Urn::CLASS_SHORTCUT,                         $burialView->burialContainerType);
        $this->assertSame(null,                                        $burialView->burialContainerCoffinSize);
        $this->assertSame(null,                                        $burialView->burialContainerCoffinShape);
        $this->assertSame(null,                                        $burialView->burialContainerCoffinIsNonStandard);
        $this->assertSame('2021-12-03 13:10:00',                       $burialView->buriedAt);
        $this->assertValidDateTimeValue($burialView->createdAt);
        $this->assertValidDateTimeValue($burialView->updatedAt);
    }

    private function testItReturnsBurialViewForB002(): void
    {
        $burialView = $this->burialFetcher->getViewById('B002');
        $this->assertInstanceOf(BurialView::class, $burialView);
        $this->assertSame('B002',                                      $burialView->id);
        $this->assertSame('11002',                                     $burialView->code);
        $this->assertSame(BurialType::COFFIN_IN_GRAVE_SITE,            $burialView->type);
        $this->assertSame('D002',                                      $burialView->deceasedId);
        $this->assertSame('NP002',                                     $burialView->deceasedNaturalPersonId);
        $this->assertSame('Устинов Арсений Максович',                  $burialView->deceasedNaturalPersonFullName);
        $this->assertSame('1918-12-30',                                $burialView->deceasedNaturalPersonBornAt);
        $this->assertSame('2001-02-12',                                $burialView->deceasedDiedAt);
        $this->assertSame(82,                                          $burialView->deceasedAge);
        $this->assertSame('DC001',                                     $burialView->deceasedDeathCertificateId);
        $this->assertSame('CD008',                                     $burialView->deceasedCauseOfDeathId);
        $this->assertSame('NP005',                                     $burialView->customerId);
        $this->assertSame(NaturalPerson::CLASS_SHORTCUT,               $burialView->customerType);
        $this->assertSame('Жданова Инга Григорьевна',                  $burialView->customerNaturalPersonFullName);
        $this->assertSame('+7-913-771-22-33',                          $burialView->customerNaturalPersonPhone);
        $this->assertSame(null,                                        $burialView->customerNaturalPersonPhoneAdditional);
        $this->assertSame(null,                                        $burialView->customerNaturalPersonEmail);
        $this->assertSame('Новосибирск, ул. Ленина, д. 1',             $burialView->customerNaturalPersonAddress);
        $this->assertSame('1979-02-12',                                $burialView->customerNaturalPersonBornAt);
        $this->assertSame(null,                                        $burialView->customerNaturalPersonPlaceOfBirth);
        $this->assertSame('1234',                                      $burialView->customerNaturalPersonPassportSeries);
        $this->assertSame('567890',                                    $burialView->customerNaturalPersonPassportNumber);
        $this->assertSame('2002-10-28',                                $burialView->customerNaturalPersonPassportIssuedAt);
        $this->assertSame('УВД Кировского района города Новосибирска', $burialView->customerNaturalPersonPassportIssuedBy);
        $this->assertSame('540-001',                                   $burialView->customerNaturalPersonPassportDivisionCode);
        $this->assertSame(null,                                        $burialView->customerSoleProprietorName);
        $this->assertSame(null,                                        $burialView->customerSoleProprietorInn);
        $this->assertSame(null,                                        $burialView->customerSoleProprietorOgrnip);
        $this->assertSame(null,                                        $burialView->customerSoleProprietorOkpo);
        $this->assertSame(null,                                        $burialView->customerSoleProprietorOkved);
        $this->assertSame(null,                                        $burialView->customerSoleProprietorRegistrationAddress);
        $this->assertSame(null,                                        $burialView->customerSoleProprietorActualLocationAddress);
        $this->assertSame(null,                                        $burialView->customerSoleProprietorBankDetailsBankName);
        $this->assertSame(null,                                        $burialView->customerSoleProprietorBankDetailsBik);
        $this->assertSame(null,                                        $burialView->customerSoleProprietorBankDetailsCorrespondentAccount);
        $this->assertSame(null,                                        $burialView->customerSoleProprietorBankDetailsCurrentAccount);
        $this->assertSame(null,                                        $burialView->customerSoleProprietorPhone);
        $this->assertSame(null,                                        $burialView->customerSoleProprietorPhoneAdditional);
        $this->assertSame(null,                                        $burialView->customerSoleProprietorFax);
        $this->assertSame(null,                                        $burialView->customerSoleProprietorEmail);
        $this->assertSame(null,                                        $burialView->customerSoleProprietorWebsite);
        $this->assertSame(null,                                        $burialView->customerJuristicPersonName);
        $this->assertSame(null,                                        $burialView->customerJuristicPersonInn);
        $this->assertSame(null,                                        $burialView->customerJuristicPersonKpp);
        $this->assertSame(null,                                        $burialView->customerJuristicPersonOgrn);
        $this->assertSame(null,                                        $burialView->customerJuristicPersonOkpo);
        $this->assertSame(null,                                        $burialView->customerJuristicPersonOkved);
        $this->assertSame(null,                                        $burialView->customerJuristicPersonLegalAddress);
        $this->assertSame(null,                                        $burialView->customerJuristicPersonPostalAddress);
        $this->assertSame(null,                                        $burialView->customerJuristicPersonBankDetailsBankName);
        $this->assertSame(null,                                        $burialView->customerJuristicPersonBankDetailsBik);
        $this->assertSame(null,                                        $burialView->customerJuristicPersonBankDetailsCorrespondentAccount);
        $this->assertSame(null,                                        $burialView->customerJuristicPersonBankDetailsCurrentAccount);
        $this->assertSame(null,                                        $burialView->customerJuristicPersonPhone);
        $this->assertSame(null,                                        $burialView->customerJuristicPersonPhoneAdditional);
        $this->assertSame(null,                                        $burialView->customerJuristicPersonFax);
        $this->assertSame(null,                                        $burialView->customerJuristicPersonGeneralDirector);
        $this->assertSame(null,                                        $burialView->customerJuristicPersonEmail);
        $this->assertSame(null,                                        $burialView->customerJuristicPersonWebsite);
        $this->assertSame('NP006',                                     $burialView->burialPlaceOwnerId);
        $this->assertSame('Гришина Устинья Ярославовна',               $burialView->burialPlaceOwnerFullName);
        $this->assertSame(null,                                        $burialView->burialPlaceOwnerPhone);
        $this->assertSame(null,                                        $burialView->burialPlaceOwnerPhoneAdditional);
        $this->assertSame(null,                                        $burialView->burialPlaceOwnerEmail);
        $this->assertSame(null,                                        $burialView->burialPlaceOwnerAddress);
        $this->assertSame(null,                                        $burialView->burialPlaceOwnerBornAt);
        $this->assertSame(null,                                        $burialView->burialPlaceOwnerPlaceOfBirth);
        $this->assertSame(null,                                        $burialView->burialPlaceOwnerPassportSeries);
        $this->assertSame(null,                                        $burialView->burialPlaceOwnerPassportNumber);
        $this->assertSame(null,                                        $burialView->burialPlaceOwnerPassportIssuedAt);
        $this->assertSame(null,                                        $burialView->burialPlaceOwnerPassportIssuedBy);
        $this->assertSame(null,                                        $burialView->burialPlaceOwnerPassportDivisionCode);
        $this->assertSame(null,                                        $burialView->funeralCompanyId);
        $this->assertSame(null,                                        $burialView->burialChainId);
        $this->assertSame('GS003',                                     $burialView->burialPlaceId);
        $this->assertSame(GraveSite::CLASS_SHORTCUT,                   $burialView->burialPlaceType);
        $this->assertSame('CB003',                                     $burialView->burialPlaceGraveSiteCemeteryBlockId);
        $this->assertSame('общий Б',                                   $burialView->burialPlaceGraveSiteCemeteryBlockName);
        $this->assertSame(7,                                           $burialView->burialPlaceGraveSiteRowInBlock);
        $this->assertSame(null,                                        $burialView->burialPlaceGraveSitePositionInRow);
        $this->assertSame('2.5',                                       $burialView->burialPlaceGraveSiteSize);
        $this->assertSame('50.950357',                                 $burialView->burialPlaceGraveSiteGeoPositionLatitude);
        $this->assertSame('80.7972252',                                $burialView->burialPlaceGraveSiteGeoPositionLongitude);
        $this->assertSame(null,                                        $burialView->burialPlaceGraveSiteGeoPositionError);
        $this->assertSame(null,                                        $burialView->burialPlaceColumbariumNicheColumbariumId);
        $this->assertSame(null,                                        $burialView->burialPlaceColumbariumNicheColumbariumName);
        $this->assertSame(null,                                        $burialView->burialPlaceColumbariumNicheRowInColumbarium);
        $this->assertSame(null,                                        $burialView->burialPlaceColumbariumNicheNumber);
        $this->assertSame(null,                                        $burialView->burialPlaceColumbariumNicheGeoPositionLatitude);
        $this->assertSame(null,                                        $burialView->burialPlaceColumbariumNicheGeoPositionLongitude);
        $this->assertSame(null,                                        $burialView->burialPlaceColumbariumNicheGeoPositionError);
        $this->assertSame(null,                                        $burialView->burialPlaceMemorialTreeNumber);
        $this->assertSame(null,                                        $burialView->burialPlaceMemorialTreeGeoPositionLatitude);
        $this->assertSame(null,                                        $burialView->burialPlaceMemorialTreeGeoPositionLongitude);
        $this->assertSame(null,                                        $burialView->burialPlaceMemorialTreeGeoPositionError);
        $this->assertSame(Coffin::CLASS_SHORTCUT,                      $burialView->burialContainerType);
        $this->assertSame(180,                                         $burialView->burialContainerCoffinSize);
        $this->assertSame(CoffinShape::TRAPEZOID,                      $burialView->burialContainerCoffinShape);
        $this->assertSame(false,                                       $burialView->burialContainerCoffinIsNonStandard);
        $this->assertSame(null,                                        $burialView->buriedAt);
        $this->assertValidDateTimeValue($burialView->createdAt);
        $this->assertValidDateTimeValue($burialView->updatedAt);
    }

    private function testItReturnsBurialViewForB003(): void
    {
        $burialView = $this->burialFetcher->getViewById('B003');
        $this->assertInstanceOf(BurialView::class, $burialView);
        $this->assertSame('B003',                                        $burialView->id);
        $this->assertSame('11003',                                       $burialView->code);
        $this->assertSame(BurialType::ASHES_UNDER_MEMORIAL_TREE,         $burialView->type);
        $this->assertSame('D003',                                        $burialView->deceasedId);
        $this->assertSame('NP003',                                       $burialView->deceasedNaturalPersonId);
        $this->assertSame('Шилов Александр Михаилович',                  $burialView->deceasedNaturalPersonFullName);
        $this->assertSame('1969-05-20',                                  $burialView->deceasedNaturalPersonBornAt);
        $this->assertSame('2012-05-13',                                  $burialView->deceasedDiedAt);
        $this->assertSame(null,                                          $burialView->deceasedAge);
        $this->assertSame('DC002',                                       $burialView->deceasedDeathCertificateId);
        $this->assertSame('CD004',                                       $burialView->deceasedCauseOfDeathId);
        $this->assertSame('NP006',                                       $burialView->customerId);
        $this->assertSame(NaturalPerson::CLASS_SHORTCUT,                 $burialView->customerType);
        $this->assertSame('Гришина Устинья Ярославовна',                 $burialView->customerNaturalPersonFullName);
        $this->assertSame(null,                                          $burialView->customerNaturalPersonPhone);
        $this->assertSame(null,                                          $burialView->customerNaturalPersonPhoneAdditional);
        $this->assertSame(null,                                          $burialView->customerNaturalPersonEmail);
        $this->assertSame(null,                                          $burialView->customerNaturalPersonAddress);
        $this->assertSame(null,                                          $burialView->customerNaturalPersonBornAt);
        $this->assertSame(null,                                          $burialView->customerNaturalPersonPlaceOfBirth);
        $this->assertSame(null,                                          $burialView->customerNaturalPersonPassportSeries);
        $this->assertSame(null,                                          $burialView->customerNaturalPersonPassportNumber);
        $this->assertSame(null,                                          $burialView->customerNaturalPersonPassportIssuedAt);
        $this->assertSame(null,                                          $burialView->customerNaturalPersonPassportIssuedBy);
        $this->assertSame(null,                                          $burialView->customerNaturalPersonPassportDivisionCode);
        $this->assertSame(null,                                          $burialView->customerSoleProprietorName);
        $this->assertSame(null,                                          $burialView->customerSoleProprietorInn);
        $this->assertSame(null,                                          $burialView->customerSoleProprietorOgrnip);
        $this->assertSame(null,                                          $burialView->customerSoleProprietorOkpo);
        $this->assertSame(null,                                          $burialView->customerSoleProprietorOkved);
        $this->assertSame(null,                                          $burialView->customerSoleProprietorRegistrationAddress);
        $this->assertSame(null,                                          $burialView->customerSoleProprietorActualLocationAddress);
        $this->assertSame(null,                                          $burialView->customerSoleProprietorBankDetailsBankName);
        $this->assertSame(null,                                          $burialView->customerSoleProprietorBankDetailsBik);
        $this->assertSame(null,                                          $burialView->customerSoleProprietorBankDetailsCorrespondentAccount);
        $this->assertSame(null,                                          $burialView->customerSoleProprietorBankDetailsCurrentAccount);
        $this->assertSame(null,                                          $burialView->customerSoleProprietorPhone);
        $this->assertSame(null,                                          $burialView->customerSoleProprietorPhoneAdditional);
        $this->assertSame(null,                                          $burialView->customerSoleProprietorFax);
        $this->assertSame(null,                                          $burialView->customerSoleProprietorEmail);
        $this->assertSame(null,                                          $burialView->customerSoleProprietorWebsite);
        $this->assertSame(null,                                          $burialView->customerJuristicPersonName);
        $this->assertSame(null,                                          $burialView->customerJuristicPersonInn);
        $this->assertSame(null,                                          $burialView->customerJuristicPersonKpp);
        $this->assertSame(null,                                          $burialView->customerJuristicPersonOgrn);
        $this->assertSame(null,                                          $burialView->customerJuristicPersonOkpo);
        $this->assertSame(null,                                          $burialView->customerJuristicPersonOkved);
        $this->assertSame(null,                                          $burialView->customerJuristicPersonLegalAddress);
        $this->assertSame(null,                                          $burialView->customerJuristicPersonPostalAddress);
        $this->assertSame(null,                                          $burialView->customerJuristicPersonBankDetailsBankName);
        $this->assertSame(null,                                          $burialView->customerJuristicPersonBankDetailsBik);
        $this->assertSame(null,                                          $burialView->customerJuristicPersonBankDetailsCorrespondentAccount);
        $this->assertSame(null,                                          $burialView->customerJuristicPersonBankDetailsCurrentAccount);
        $this->assertSame(null,                                          $burialView->customerJuristicPersonPhone);
        $this->assertSame(null,                                          $burialView->customerJuristicPersonPhoneAdditional);
        $this->assertSame(null,                                          $burialView->customerJuristicPersonFax);
        $this->assertSame(null,                                          $burialView->customerJuristicPersonGeneralDirector);
        $this->assertSame(null,                                          $burialView->customerJuristicPersonEmail);
        $this->assertSame(null,                                          $burialView->customerJuristicPersonWebsite);
        $this->assertSame('NP006',                                       $burialView->burialPlaceOwnerId);
        $this->assertSame('Гришина Устинья Ярославовна',                 $burialView->burialPlaceOwnerFullName);
        $this->assertSame(null,                                          $burialView->burialPlaceOwnerPhone);
        $this->assertSame(null,                                          $burialView->burialPlaceOwnerPhoneAdditional);
        $this->assertSame(null,                                          $burialView->burialPlaceOwnerEmail);
        $this->assertSame(null,                                          $burialView->burialPlaceOwnerAddress);
        $this->assertSame(null,                                          $burialView->burialPlaceOwnerBornAt);
        $this->assertSame(null,                                          $burialView->burialPlaceOwnerPlaceOfBirth);
        $this->assertSame(null,                                          $burialView->burialPlaceOwnerPassportSeries);
        $this->assertSame(null,                                          $burialView->burialPlaceOwnerPassportNumber);
        $this->assertSame(null,                                          $burialView->burialPlaceOwnerPassportIssuedAt);
        $this->assertSame(null,                                          $burialView->burialPlaceOwnerPassportIssuedBy);
        $this->assertSame(null,                                          $burialView->burialPlaceOwnerPassportDivisionCode);
        $this->assertSame('FC001',                                       $burialView->funeralCompanyId);
        $this->assertSame(null,                                          $burialView->burialChainId);
        $this->assertSame('MT002',                                       $burialView->burialPlaceId);
        $this->assertSame(MemorialTree::CLASS_SHORTCUT,                  $burialView->burialPlaceType);
        $this->assertSame(null,                                          $burialView->burialPlaceGraveSiteCemeteryBlockId);
        $this->assertSame(null,                                          $burialView->burialPlaceGraveSiteCemeteryBlockName);
        $this->assertSame(null,                                          $burialView->burialPlaceGraveSiteRowInBlock);
        $this->assertSame(null,                                          $burialView->burialPlaceGraveSitePositionInRow);
        $this->assertSame(null,                                          $burialView->burialPlaceGraveSiteSize);
        $this->assertSame(null,                                          $burialView->burialPlaceGraveSiteGeoPositionLatitude);
        $this->assertSame(null,                                          $burialView->burialPlaceGraveSiteGeoPositionLongitude);
        $this->assertSame(null,                                          $burialView->burialPlaceGraveSiteGeoPositionError);
        $this->assertSame(null,                                          $burialView->burialPlaceColumbariumNicheColumbariumId);
        $this->assertSame(null,                                          $burialView->burialPlaceColumbariumNicheColumbariumName);
        $this->assertSame(null,                                          $burialView->burialPlaceColumbariumNicheRowInColumbarium);
        $this->assertSame(null,                                          $burialView->burialPlaceColumbariumNicheNumber);
        $this->assertSame(null,                                          $burialView->burialPlaceColumbariumNicheGeoPositionLatitude);
        $this->assertSame(null,                                          $burialView->burialPlaceColumbariumNicheGeoPositionLongitude);
        $this->assertSame(null,                                          $burialView->burialPlaceColumbariumNicheGeoPositionError);
        $this->assertSame('002',                                         $burialView->burialPlaceMemorialTreeNumber);
        $this->assertSame('54.950457',                                   $burialView->burialPlaceMemorialTreeGeoPositionLatitude);
        $this->assertSame('82.7972252',                                  $burialView->burialPlaceMemorialTreeGeoPositionLongitude);
        $this->assertSame('0.5',                                         $burialView->burialPlaceMemorialTreeGeoPositionError);
        $this->assertSame(null,                                          $burialView->burialContainerType);
        $this->assertSame(null,                                          $burialView->burialContainerCoffinSize);
        $this->assertSame(null,                                          $burialView->burialContainerCoffinShape);
        $this->assertSame(null,                                          $burialView->burialContainerCoffinIsNonStandard);
        $this->assertSame(null,                                          $burialView->buriedAt);
        $this->assertValidDateTimeValue($burialView->createdAt);
        $this->assertValidDateTimeValue($burialView->updatedAt);
    }

    private function testItReturnsBurialViewForB004(): void
    {
        $burialView = $this->burialFetcher->getViewById('B004');
        $this->assertInstanceOf(BurialView::class, $burialView);
        $this->assertSame('B004',                                        $burialView->id);
        $this->assertSame('234117890',                                   $burialView->code);
        $this->assertSame(BurialType::COFFIN_IN_GRAVE_SITE,              $burialView->type);
        $this->assertSame('D004',                                        $burialView->deceasedId);
        $this->assertSame('NP005',                                       $burialView->deceasedNaturalPersonId);
        $this->assertSame('Жданова Инга Григорьевна',                    $burialView->deceasedNaturalPersonFullName);
        $this->assertSame('1979-02-12',                                  $burialView->deceasedNaturalPersonBornAt);
        $this->assertSame('2022-03-10',                                  $burialView->deceasedDiedAt);
        $this->assertSame(null,                                          $burialView->deceasedAge);
        $this->assertSame(null,                                          $burialView->deceasedDeathCertificateId);
        $this->assertSame(null,                                          $burialView->deceasedCauseOfDeathId);
        $this->assertSame('JP004',                                       $burialView->customerId);
        $this->assertSame(JuristicPerson::CLASS_SHORTCUT,                $burialView->customerType);
        $this->assertSame(null,                                          $burialView->customerNaturalPersonFullName);
        $this->assertSame(null,                                          $burialView->customerNaturalPersonPhone);
        $this->assertSame(null,                                          $burialView->customerNaturalPersonPhoneAdditional);
        $this->assertSame(null,                                          $burialView->customerNaturalPersonEmail);
        $this->assertSame(null,                                          $burialView->customerNaturalPersonAddress);
        $this->assertSame(null,                                          $burialView->customerNaturalPersonBornAt);
        $this->assertSame(null,                                          $burialView->customerNaturalPersonPlaceOfBirth);
        $this->assertSame(null,                                          $burialView->customerNaturalPersonPassportSeries);
        $this->assertSame(null,                                          $burialView->customerNaturalPersonPassportNumber);
        $this->assertSame(null,                                          $burialView->customerNaturalPersonPassportIssuedAt);
        $this->assertSame(null,                                          $burialView->customerNaturalPersonPassportIssuedBy);
        $this->assertSame(null,                                          $burialView->customerNaturalPersonPassportDivisionCode);
        $this->assertSame(null,                                          $burialView->customerSoleProprietorName);
        $this->assertSame(null,                                          $burialView->customerSoleProprietorInn);
        $this->assertSame(null,                                          $burialView->customerSoleProprietorOgrnip);
        $this->assertSame(null,                                          $burialView->customerSoleProprietorOkpo);
        $this->assertSame(null,                                          $burialView->customerSoleProprietorOkved);
        $this->assertSame(null,                                          $burialView->customerSoleProprietorRegistrationAddress);
        $this->assertSame(null,                                          $burialView->customerSoleProprietorActualLocationAddress);
        $this->assertSame(null,                                          $burialView->customerSoleProprietorBankDetailsBankName);
        $this->assertSame(null,                                          $burialView->customerSoleProprietorBankDetailsBik);
        $this->assertSame(null,                                          $burialView->customerSoleProprietorBankDetailsCorrespondentAccount);
        $this->assertSame(null,                                          $burialView->customerSoleProprietorBankDetailsCurrentAccount);
        $this->assertSame(null,                                          $burialView->customerSoleProprietorPhone);
        $this->assertSame(null,                                          $burialView->customerSoleProprietorPhoneAdditional);
        $this->assertSame(null,                                          $burialView->customerSoleProprietorFax);
        $this->assertSame(null,                                          $burialView->customerSoleProprietorEmail);
        $this->assertSame(null,                                          $burialView->customerSoleProprietorWebsite);
        $this->assertSame('МУП "Новосибирский метрополитен"',            $burialView->customerJuristicPersonName);
        $this->assertSame(null,                                          $burialView->customerJuristicPersonInn);
        $this->assertSame(null,                                          $burialView->customerJuristicPersonKpp);
        $this->assertSame(null,                                          $burialView->customerJuristicPersonOgrn);
        $this->assertSame(null,                                          $burialView->customerJuristicPersonOkpo);
        $this->assertSame(null,                                          $burialView->customerJuristicPersonOkved);
        $this->assertSame(null,                                          $burialView->customerJuristicPersonLegalAddress);
        $this->assertSame(null,                                          $burialView->customerJuristicPersonPostalAddress);
        $this->assertSame(null,                                          $burialView->customerJuristicPersonBankDetailsBankName);
        $this->assertSame(null,                                          $burialView->customerJuristicPersonBankDetailsBik);
        $this->assertSame(null,                                          $burialView->customerJuristicPersonBankDetailsCorrespondentAccount);
        $this->assertSame(null,                                          $burialView->customerJuristicPersonBankDetailsCurrentAccount);
        $this->assertSame(null,                                          $burialView->customerJuristicPersonPhone);
        $this->assertSame(null,                                          $burialView->customerJuristicPersonPhoneAdditional);
        $this->assertSame(null,                                          $burialView->customerJuristicPersonFax);
        $this->assertSame(null,                                          $burialView->customerJuristicPersonGeneralDirector);
        $this->assertSame(null,                                          $burialView->customerJuristicPersonEmail);
        $this->assertSame(null,                                          $burialView->customerJuristicPersonWebsite);
        $this->assertSame(null,                                          $burialView->burialPlaceOwnerId);
        $this->assertSame(null,                                          $burialView->burialPlaceOwnerFullName);
        $this->assertSame(null,                                          $burialView->burialPlaceOwnerPhone);
        $this->assertSame(null,                                          $burialView->burialPlaceOwnerPhoneAdditional);
        $this->assertSame(null,                                          $burialView->burialPlaceOwnerEmail);
        $this->assertSame(null,                                          $burialView->burialPlaceOwnerAddress);
        $this->assertSame(null,                                          $burialView->burialPlaceOwnerBornAt);
        $this->assertSame(null,                                          $burialView->burialPlaceOwnerPlaceOfBirth);
        $this->assertSame(null,                                          $burialView->burialPlaceOwnerPassportSeries);
        $this->assertSame(null,                                          $burialView->burialPlaceOwnerPassportNumber);
        $this->assertSame(null,                                          $burialView->burialPlaceOwnerPassportIssuedAt);
        $this->assertSame(null,                                          $burialView->burialPlaceOwnerPassportIssuedBy);
        $this->assertSame(null,                                          $burialView->burialPlaceOwnerPassportDivisionCode);
        $this->assertSame('FC001',                                       $burialView->funeralCompanyId);
        $this->assertSame(null,                                          $burialView->burialChainId);
        $this->assertSame('GS001',                                       $burialView->burialPlaceId);
        $this->assertSame(GraveSite::CLASS_SHORTCUT,                     $burialView->burialPlaceType);
        $this->assertSame('CB001',                                       $burialView->burialPlaceGraveSiteCemeteryBlockId);
        $this->assertSame('воинский',                                    $burialView->burialPlaceGraveSiteCemeteryBlockName);
        $this->assertSame(1,                                             $burialView->burialPlaceGraveSiteRowInBlock);
        $this->assertSame(null,                                          $burialView->burialPlaceGraveSitePositionInRow);
        $this->assertSame(null,                                          $burialView->burialPlaceGraveSiteSize);
        $this->assertSame(null,                                          $burialView->burialPlaceGraveSiteGeoPositionLatitude);
        $this->assertSame(null,                                          $burialView->burialPlaceGraveSiteGeoPositionLongitude);
        $this->assertSame(null,                                          $burialView->burialPlaceGraveSiteGeoPositionError);
        $this->assertSame(null,                                          $burialView->burialPlaceColumbariumNicheColumbariumId);
        $this->assertSame(null,                                          $burialView->burialPlaceColumbariumNicheColumbariumName);
        $this->assertSame(null,                                          $burialView->burialPlaceColumbariumNicheRowInColumbarium);
        $this->assertSame(null,                                          $burialView->burialPlaceColumbariumNicheNumber);
        $this->assertSame(null,                                          $burialView->burialPlaceColumbariumNicheGeoPositionLatitude);
        $this->assertSame(null,                                          $burialView->burialPlaceColumbariumNicheGeoPositionLongitude);
        $this->assertSame(null,                                          $burialView->burialPlaceColumbariumNicheGeoPositionError);
        $this->assertSame(null,                                          $burialView->burialPlaceMemorialTreeNumber);
        $this->assertSame(null,                                          $burialView->burialPlaceMemorialTreeGeoPositionLatitude);
        $this->assertSame(null,                                          $burialView->burialPlaceMemorialTreeGeoPositionLongitude);
        $this->assertSame(null,                                          $burialView->burialPlaceMemorialTreeGeoPositionError);
        $this->assertSame(null,                                          $burialView->burialContainerType);
        $this->assertSame(null,                                          $burialView->burialContainerCoffinSize);
        $this->assertSame(null,                                          $burialView->burialContainerCoffinShape);
        $this->assertSame(null,                                          $burialView->burialContainerCoffinIsNonStandard);
        $this->assertSame(null,                                          $burialView->buriedAt);
        $this->assertValidDateTimeValue($burialView->createdAt);
        $this->assertValidDateTimeValue($burialView->updatedAt);
    }

    private function testItReturnsBurialViewForB005(): void
    {
        $burialView = $this->burialFetcher->getViewById('B005');
        $this->assertInstanceOf(BurialView::class, $burialView);
        $this->assertSame('B005',                                        $burialView->id);
        $this->assertSame('11005',                                       $burialView->code);
        $this->assertSame(BurialType::COFFIN_IN_GRAVE_SITE,              $burialView->type);
        $this->assertSame('D005',                                        $burialView->deceasedId);
        $this->assertSame('NP004',                                       $burialView->deceasedNaturalPersonId);
        $this->assertSame('Соколов Герман Маркович',                     $burialView->deceasedNaturalPersonFullName);
        $this->assertSame(null,                                          $burialView->deceasedNaturalPersonBornAt);
        $this->assertSame('2010-01-26',                                  $burialView->deceasedDiedAt);
        $this->assertSame(null,                                          $burialView->deceasedAge);
        $this->assertSame(null,                                          $burialView->deceasedDeathCertificateId);
        $this->assertSame(null,                                          $burialView->deceasedCauseOfDeathId);
        $this->assertSame('SP003',                                       $burialView->customerId);
        $this->assertSame(SoleProprietor::CLASS_SHORTCUT,                $burialView->customerType);
        $this->assertSame(null,                                          $burialView->customerNaturalPersonFullName);
        $this->assertSame(null,                                          $burialView->customerNaturalPersonPhone);
        $this->assertSame(null,                                          $burialView->customerNaturalPersonPhoneAdditional);
        $this->assertSame(null,                                          $burialView->customerNaturalPersonEmail);
        $this->assertSame(null,                                          $burialView->customerNaturalPersonAddress);
        $this->assertSame(null,                                          $burialView->customerNaturalPersonBornAt);
        $this->assertSame(null,                                          $burialView->customerNaturalPersonPlaceOfBirth);
        $this->assertSame(null,                                          $burialView->customerNaturalPersonPassportSeries);
        $this->assertSame(null,                                          $burialView->customerNaturalPersonPassportNumber);
        $this->assertSame(null,                                          $burialView->customerNaturalPersonPassportIssuedAt);
        $this->assertSame(null,                                          $burialView->customerNaturalPersonPassportIssuedBy);
        $this->assertSame(null,                                          $burialView->customerNaturalPersonPassportDivisionCode);
        $this->assertSame('ИП Сидоров Сидр Сидорович',                   $burialView->customerSoleProprietorName);
        $this->assertSame('391600743661',                                $burialView->customerSoleProprietorInn);
        $this->assertSame(null,                                          $burialView->customerSoleProprietorOgrnip);
        $this->assertSame(null,                                          $burialView->customerSoleProprietorOkpo);
        $this->assertSame(null,                                          $burialView->customerSoleProprietorOkved);
        $this->assertSame(null,                                          $burialView->customerSoleProprietorRegistrationAddress);
        $this->assertSame('с. Каменка, д. 14',                           $burialView->customerSoleProprietorActualLocationAddress);
        $this->assertSame(null,                                          $burialView->customerSoleProprietorBankDetailsBankName);
        $this->assertSame(null,                                          $burialView->customerSoleProprietorBankDetailsBik);
        $this->assertSame(null,                                          $burialView->customerSoleProprietorBankDetailsCorrespondentAccount);
        $this->assertSame(null,                                          $burialView->customerSoleProprietorBankDetailsCurrentAccount);
        $this->assertSame('8(383)147-22-33',                             $burialView->customerSoleProprietorPhone);
        $this->assertSame(null,                                          $burialView->customerSoleProprietorPhoneAdditional);
        $this->assertSame(null,                                          $burialView->customerSoleProprietorFax);
        $this->assertSame(null,                                          $burialView->customerSoleProprietorEmail);
        $this->assertSame(null,                                          $burialView->customerSoleProprietorWebsite);
        $this->assertSame(null,                                          $burialView->customerJuristicPersonName);
        $this->assertSame(null,                                          $burialView->customerJuristicPersonInn);
        $this->assertSame(null,                                          $burialView->customerJuristicPersonKpp);
        $this->assertSame(null,                                          $burialView->customerJuristicPersonOgrn);
        $this->assertSame(null,                                          $burialView->customerJuristicPersonOkpo);
        $this->assertSame(null,                                          $burialView->customerJuristicPersonOkved);
        $this->assertSame(null,                                          $burialView->customerJuristicPersonLegalAddress);
        $this->assertSame(null,                                          $burialView->customerJuristicPersonPostalAddress);
        $this->assertSame(null,                                          $burialView->customerJuristicPersonBankDetailsBankName);
        $this->assertSame(null,                                          $burialView->customerJuristicPersonBankDetailsBik);
        $this->assertSame(null,                                          $burialView->customerJuristicPersonBankDetailsCorrespondentAccount);
        $this->assertSame(null,                                          $burialView->customerJuristicPersonBankDetailsCurrentAccount);
        $this->assertSame(null,                                          $burialView->customerJuristicPersonPhone);
        $this->assertSame(null,                                          $burialView->customerJuristicPersonPhoneAdditional);
        $this->assertSame(null,                                          $burialView->customerJuristicPersonFax);
        $this->assertSame(null,                                          $burialView->customerJuristicPersonGeneralDirector);
        $this->assertSame(null,                                          $burialView->customerJuristicPersonEmail);
        $this->assertSame(null,                                          $burialView->customerJuristicPersonWebsite);
        $this->assertSame('NP008',                                       $burialView->burialPlaceOwnerId);
        $this->assertSame('Беляев Мечеслав Федорович',                   $burialView->burialPlaceOwnerFullName);
        $this->assertSame(null,                                          $burialView->burialPlaceOwnerPhone);
        $this->assertSame(null,                                          $burialView->burialPlaceOwnerPhoneAdditional);
        $this->assertSame('mecheslav.belyaev@gmail.com',                 $burialView->burialPlaceOwnerEmail);
        $this->assertSame(null,                                          $burialView->burialPlaceOwnerAddress);
        $this->assertSame(null,                                          $burialView->burialPlaceOwnerBornAt);
        $this->assertSame(null,                                          $burialView->burialPlaceOwnerPlaceOfBirth);
        $this->assertSame('2345',                                        $burialView->burialPlaceOwnerPassportSeries);
        $this->assertSame('162354',                                      $burialView->burialPlaceOwnerPassportNumber);
        $this->assertSame('1981-10-20',                                  $burialView->burialPlaceOwnerPassportIssuedAt);
        $this->assertSame('Отделом МВД Ленинского района г. Пензы',      $burialView->burialPlaceOwnerPassportIssuedBy);
        $this->assertSame(null,                                          $burialView->burialPlaceOwnerPassportDivisionCode);
        $this->assertSame('FC002',                                       $burialView->funeralCompanyId);
        $this->assertSame(null,                                          $burialView->burialChainId);
        $this->assertSame('GS002',                                       $burialView->burialPlaceId);
        $this->assertSame(GraveSite::CLASS_SHORTCUT,                     $burialView->burialPlaceType);
        $this->assertSame('CB002',                                       $burialView->burialPlaceGraveSiteCemeteryBlockId);
        $this->assertSame('общий А',                                     $burialView->burialPlaceGraveSiteCemeteryBlockName);
        $this->assertSame(3,                                             $burialView->burialPlaceGraveSiteRowInBlock);
        $this->assertSame(4,                                             $burialView->burialPlaceGraveSitePositionInRow);
        $this->assertSame(null,                                          $burialView->burialPlaceGraveSiteSize);
        $this->assertSame('54.950357',                                   $burialView->burialPlaceGraveSiteGeoPositionLatitude);
        $this->assertSame('82.7972252',                                  $burialView->burialPlaceGraveSiteGeoPositionLongitude);
        $this->assertSame('0.5',                                         $burialView->burialPlaceGraveSiteGeoPositionError);
        $this->assertSame(null,                                          $burialView->burialPlaceColumbariumNicheColumbariumId);
        $this->assertSame(null,                                          $burialView->burialPlaceColumbariumNicheColumbariumName);
        $this->assertSame(null,                                          $burialView->burialPlaceColumbariumNicheRowInColumbarium);
        $this->assertSame(null,                                          $burialView->burialPlaceColumbariumNicheNumber);
        $this->assertSame(null,                                          $burialView->burialPlaceColumbariumNicheGeoPositionLatitude);
        $this->assertSame(null,                                          $burialView->burialPlaceColumbariumNicheGeoPositionLongitude);
        $this->assertSame(null,                                          $burialView->burialPlaceColumbariumNicheGeoPositionError);
        $this->assertSame(null,                                          $burialView->burialPlaceMemorialTreeNumber);
        $this->assertSame(null,                                          $burialView->burialPlaceMemorialTreeGeoPositionLatitude);
        $this->assertSame(null,                                          $burialView->burialPlaceMemorialTreeGeoPositionLongitude);
        $this->assertSame(null,                                          $burialView->burialPlaceMemorialTreeGeoPositionError);
        $this->assertSame(null,                                          $burialView->burialContainerType);
        $this->assertSame(null,                                          $burialView->burialContainerCoffinSize);
        $this->assertSame(null,                                          $burialView->burialContainerCoffinShape);
        $this->assertSame(null,                                          $burialView->burialContainerCoffinIsNonStandard);
        $this->assertSame('2010-01-28 12:55:00',                         $burialView->buriedAt);
        $this->assertValidDateTimeValue($burialView->createdAt);
        $this->assertValidDateTimeValue($burialView->updatedAt);
    }

    private function testItReturnsBurialViewForB006(): void
    {
        $burialView = $this->burialFetcher->getViewById('B006');
        $this->assertInstanceOf(BurialView::class, $burialView);
        $this->assertSame('B006',                                        $burialView->id);
        $this->assertSame('11006',                                       $burialView->code);
        $this->assertSame(BurialType::COFFIN_IN_GRAVE_SITE,              $burialView->type);
        $this->assertSame('D006',                                        $burialView->deceasedId);
        $this->assertSame('NP006',                                       $burialView->deceasedNaturalPersonId);
        $this->assertSame('Гришина Устинья Ярославовна',                 $burialView->deceasedNaturalPersonFullName);
        $this->assertSame(null,                                          $burialView->deceasedNaturalPersonBornAt);
        $this->assertSame('2021-12-03',                                  $burialView->deceasedDiedAt);
        $this->assertSame(null,                                          $burialView->deceasedAge);
        $this->assertSame(null,                                          $burialView->deceasedDeathCertificateId);
        $this->assertSame(null,                                          $burialView->deceasedCauseOfDeathId);
        $this->assertSame('NP007',                                       $burialView->customerId);
        $this->assertSame(NaturalPerson::CLASS_SHORTCUT,                 $burialView->customerType);
        $this->assertSame('Громов Никифор Рудольфович',                  $burialView->customerNaturalPersonFullName);
        $this->assertSame(null,                                          $burialView->customerNaturalPersonPhone);
        $this->assertSame(null,                                          $burialView->customerNaturalPersonPhoneAdditional);
        $this->assertSame(null,                                          $burialView->customerNaturalPersonEmail);
        $this->assertSame('Новосибирск, ул. Н.-Данченко, д. 18, кв. 17', $burialView->customerNaturalPersonAddress);
        $this->assertSame('1915-09-24',                                  $burialView->customerNaturalPersonBornAt);
        $this->assertSame(null,                                          $burialView->customerNaturalPersonPlaceOfBirth);
        $this->assertSame(null,                                          $burialView->customerNaturalPersonPassportSeries);
        $this->assertSame(null,                                          $burialView->customerNaturalPersonPassportNumber);
        $this->assertSame(null,                                          $burialView->customerNaturalPersonPassportIssuedAt);
        $this->assertSame(null,                                          $burialView->customerNaturalPersonPassportIssuedBy);
        $this->assertSame(null,                                          $burialView->customerNaturalPersonPassportDivisionCode);
        $this->assertSame(null,                                          $burialView->customerSoleProprietorName);
        $this->assertSame(null,                                          $burialView->customerSoleProprietorInn);
        $this->assertSame(null,                                          $burialView->customerSoleProprietorOgrnip);
        $this->assertSame(null,                                          $burialView->customerSoleProprietorOkpo);
        $this->assertSame(null,                                          $burialView->customerSoleProprietorOkved);
        $this->assertSame(null,                                          $burialView->customerSoleProprietorRegistrationAddress);
        $this->assertSame(null,                                          $burialView->customerSoleProprietorActualLocationAddress);
        $this->assertSame(null,                                          $burialView->customerSoleProprietorBankDetailsBankName);
        $this->assertSame(null,                                          $burialView->customerSoleProprietorBankDetailsBik);
        $this->assertSame(null,                                          $burialView->customerSoleProprietorBankDetailsCorrespondentAccount);
        $this->assertSame(null,                                          $burialView->customerSoleProprietorBankDetailsCurrentAccount);
        $this->assertSame(null,                                          $burialView->customerSoleProprietorPhone);
        $this->assertSame(null,                                          $burialView->customerSoleProprietorPhoneAdditional);
        $this->assertSame(null,                                          $burialView->customerSoleProprietorFax);
        $this->assertSame(null,                                          $burialView->customerSoleProprietorEmail);
        $this->assertSame(null,                                          $burialView->customerSoleProprietorWebsite);
        $this->assertSame(null,                                          $burialView->customerJuristicPersonName);
        $this->assertSame(null,                                          $burialView->customerJuristicPersonInn);
        $this->assertSame(null,                                          $burialView->customerJuristicPersonKpp);
        $this->assertSame(null,                                          $burialView->customerJuristicPersonOgrn);
        $this->assertSame(null,                                          $burialView->customerJuristicPersonOkpo);
        $this->assertSame(null,                                          $burialView->customerJuristicPersonOkved);
        $this->assertSame(null,                                          $burialView->customerJuristicPersonLegalAddress);
        $this->assertSame(null,                                          $burialView->customerJuristicPersonPostalAddress);
        $this->assertSame(null,                                          $burialView->customerJuristicPersonBankDetailsBankName);
        $this->assertSame(null,                                          $burialView->customerJuristicPersonBankDetailsBik);
        $this->assertSame(null,                                          $burialView->customerJuristicPersonBankDetailsCorrespondentAccount);
        $this->assertSame(null,                                          $burialView->customerJuristicPersonBankDetailsCurrentAccount);
        $this->assertSame(null,                                          $burialView->customerJuristicPersonPhone);
        $this->assertSame(null,                                          $burialView->customerJuristicPersonPhoneAdditional);
        $this->assertSame(null,                                          $burialView->customerJuristicPersonFax);
        $this->assertSame(null,                                          $burialView->customerJuristicPersonGeneralDirector);
        $this->assertSame(null,                                          $burialView->customerJuristicPersonEmail);
        $this->assertSame(null,                                          $burialView->customerJuristicPersonWebsite);
        $this->assertSame(null,                                          $burialView->burialPlaceOwnerId);
        $this->assertSame(null,                                          $burialView->burialPlaceOwnerFullName);
        $this->assertSame(null,                                          $burialView->burialPlaceOwnerPhone);
        $this->assertSame(null,                                          $burialView->burialPlaceOwnerPhoneAdditional);
        $this->assertSame(null,                                          $burialView->burialPlaceOwnerEmail);
        $this->assertSame(null,                                          $burialView->burialPlaceOwnerAddress);
        $this->assertSame(null,                                          $burialView->burialPlaceOwnerBornAt);
        $this->assertSame(null,                                          $burialView->burialPlaceOwnerPlaceOfBirth);
        $this->assertSame(null,                                          $burialView->burialPlaceOwnerPassportSeries);
        $this->assertSame(null,                                          $burialView->burialPlaceOwnerPassportNumber);
        $this->assertSame(null,                                          $burialView->burialPlaceOwnerPassportIssuedAt);
        $this->assertSame(null,                                          $burialView->burialPlaceOwnerPassportIssuedBy);
        $this->assertSame(null,                                          $burialView->burialPlaceOwnerPassportDivisionCode);
        $this->assertSame('FC003',                                       $burialView->funeralCompanyId);
        $this->assertSame(null,                                          $burialView->burialChainId);
        $this->assertSame(null,                                          $burialView->burialPlaceId);
        $this->assertSame(null,                                          $burialView->burialPlaceType);
        $this->assertSame(null,                                          $burialView->burialPlaceGraveSiteCemeteryBlockId);
        $this->assertSame(null,                                          $burialView->burialPlaceGraveSiteCemeteryBlockName);
        $this->assertSame(null,                                          $burialView->burialPlaceGraveSiteRowInBlock);
        $this->assertSame(null,                                          $burialView->burialPlaceGraveSitePositionInRow);
        $this->assertSame(null,                                          $burialView->burialPlaceGraveSiteSize);
        $this->assertSame(null,                                          $burialView->burialPlaceGraveSiteGeoPositionLatitude);
        $this->assertSame(null,                                          $burialView->burialPlaceGraveSiteGeoPositionLongitude);
        $this->assertSame(null,                                          $burialView->burialPlaceGraveSiteGeoPositionError);
        $this->assertSame(null,                                          $burialView->burialPlaceColumbariumNicheColumbariumId);
        $this->assertSame(null,                                          $burialView->burialPlaceColumbariumNicheColumbariumName);
        $this->assertSame(null,                                          $burialView->burialPlaceColumbariumNicheRowInColumbarium);
        $this->assertSame(null,                                          $burialView->burialPlaceColumbariumNicheNumber);
        $this->assertSame(null,                                          $burialView->burialPlaceColumbariumNicheGeoPositionLatitude);
        $this->assertSame(null,                                          $burialView->burialPlaceColumbariumNicheGeoPositionLongitude);
        $this->assertSame(null,                                          $burialView->burialPlaceColumbariumNicheGeoPositionError);
        $this->assertSame(null,                                          $burialView->burialPlaceMemorialTreeNumber);
        $this->assertSame(null,                                          $burialView->burialPlaceMemorialTreeGeoPositionLatitude);
        $this->assertSame(null,                                          $burialView->burialPlaceMemorialTreeGeoPositionLongitude);
        $this->assertSame(null,                                          $burialView->burialPlaceMemorialTreeGeoPositionError);
        $this->assertSame(null,                                          $burialView->burialContainerType);
        $this->assertSame(null,                                          $burialView->burialContainerCoffinSize);
        $this->assertSame(null,                                          $burialView->burialContainerCoffinShape);
        $this->assertSame(null,                                          $burialView->burialContainerCoffinIsNonStandard);
        $this->assertSame(null,                                          $burialView->buriedAt);
        $this->assertValidDateTimeValue($burialView->createdAt);
        $this->assertValidDateTimeValue($burialView->updatedAt);
    }

    private function testItReturnsBurialViewForB007(): void
    {
        $burialView = $this->burialFetcher->getViewById('B007');
        $this->assertInstanceOf(BurialView::class, $burialView);
        $this->assertSame('B007',                                        $burialView->id);
        $this->assertSame('01',                                          $burialView->code);
        $this->assertSame(BurialType::COFFIN_IN_GRAVE_SITE,              $burialView->type);
        $this->assertSame('D007',                                        $burialView->deceasedId);
        $this->assertSame('NP009',                                       $burialView->deceasedNaturalPersonId);
        $this->assertSame('Никонов Родион Митрофанович',                 $burialView->deceasedNaturalPersonFullName);
        $this->assertSame(null,                                          $burialView->deceasedNaturalPersonBornAt);
        $this->assertSame('1980-05-26',                                  $burialView->deceasedDiedAt);
        $this->assertSame(null,                                          $burialView->deceasedAge);
        $this->assertSame(null,                                          $burialView->deceasedDeathCertificateId);
        $this->assertSame(null,                                          $burialView->deceasedCauseOfDeathId);
        $this->assertSame(null,                                          $burialView->customerId);
        $this->assertSame(null,                                          $burialView->customerType);
        $this->assertSame(null ,                                         $burialView->customerNaturalPersonFullName);
        $this->assertSame(null,                                          $burialView->customerNaturalPersonPhone);
        $this->assertSame(null,                                          $burialView->customerNaturalPersonPhoneAdditional);
        $this->assertSame(null,                                          $burialView->customerNaturalPersonEmail);
        $this->assertSame(null,                                          $burialView->customerNaturalPersonAddress);
        $this->assertSame(null,                                          $burialView->customerNaturalPersonBornAt);
        $this->assertSame(null,                                          $burialView->customerNaturalPersonPlaceOfBirth);
        $this->assertSame(null,                                          $burialView->customerNaturalPersonPassportSeries);
        $this->assertSame(null,                                          $burialView->customerNaturalPersonPassportNumber);
        $this->assertSame(null,                                          $burialView->customerNaturalPersonPassportIssuedAt);
        $this->assertSame(null,                                          $burialView->customerNaturalPersonPassportIssuedBy);
        $this->assertSame(null,                                          $burialView->customerNaturalPersonPassportDivisionCode);
        $this->assertSame(null,                                          $burialView->customerSoleProprietorName);
        $this->assertSame(null,                                          $burialView->customerSoleProprietorInn);
        $this->assertSame(null,                                          $burialView->customerSoleProprietorOgrnip);
        $this->assertSame(null,                                          $burialView->customerSoleProprietorOkpo);
        $this->assertSame(null,                                          $burialView->customerSoleProprietorOkved);
        $this->assertSame(null,                                          $burialView->customerSoleProprietorRegistrationAddress);
        $this->assertSame(null,                                          $burialView->customerSoleProprietorActualLocationAddress);
        $this->assertSame(null,                                          $burialView->customerSoleProprietorBankDetailsBankName);
        $this->assertSame(null,                                          $burialView->customerSoleProprietorBankDetailsBik);
        $this->assertSame(null,                                          $burialView->customerSoleProprietorBankDetailsCorrespondentAccount);
        $this->assertSame(null,                                          $burialView->customerSoleProprietorBankDetailsCurrentAccount);
        $this->assertSame(null,                                          $burialView->customerSoleProprietorPhone);
        $this->assertSame(null,                                          $burialView->customerSoleProprietorPhoneAdditional);
        $this->assertSame(null,                                          $burialView->customerSoleProprietorFax);
        $this->assertSame(null,                                          $burialView->customerSoleProprietorEmail);
        $this->assertSame(null,                                          $burialView->customerSoleProprietorWebsite);
        $this->assertSame(null,                                          $burialView->customerJuristicPersonName);
        $this->assertSame(null,                                          $burialView->customerJuristicPersonInn);
        $this->assertSame(null,                                          $burialView->customerJuristicPersonKpp);
        $this->assertSame(null,                                          $burialView->customerJuristicPersonOgrn);
        $this->assertSame(null,                                          $burialView->customerJuristicPersonOkpo);
        $this->assertSame(null,                                          $burialView->customerJuristicPersonOkved);
        $this->assertSame(null,                                          $burialView->customerJuristicPersonLegalAddress);
        $this->assertSame(null,                                          $burialView->customerJuristicPersonPostalAddress);
        $this->assertSame(null,                                          $burialView->customerJuristicPersonBankDetailsBankName);
        $this->assertSame(null,                                          $burialView->customerJuristicPersonBankDetailsBik);
        $this->assertSame(null,                                          $burialView->customerJuristicPersonBankDetailsCorrespondentAccount);
        $this->assertSame(null,                                          $burialView->customerJuristicPersonBankDetailsCurrentAccount);
        $this->assertSame(null,                                          $burialView->customerJuristicPersonPhone);
        $this->assertSame(null,                                          $burialView->customerJuristicPersonPhoneAdditional);
        $this->assertSame(null,                                          $burialView->customerJuristicPersonFax);
        $this->assertSame(null,                                          $burialView->customerJuristicPersonGeneralDirector);
        $this->assertSame(null,                                          $burialView->customerJuristicPersonEmail);
        $this->assertSame(null,                                          $burialView->customerJuristicPersonWebsite);
        $this->assertSame(null,                                          $burialView->burialPlaceOwnerId);
        $this->assertSame(null,                                          $burialView->burialPlaceOwnerFullName);
        $this->assertSame(null,                                          $burialView->burialPlaceOwnerPhone);
        $this->assertSame(null,                                          $burialView->burialPlaceOwnerPhoneAdditional);
        $this->assertSame(null,                                          $burialView->burialPlaceOwnerEmail);
        $this->assertSame(null,                                          $burialView->burialPlaceOwnerAddress);
        $this->assertSame(null,                                          $burialView->burialPlaceOwnerBornAt);
        $this->assertSame(null,                                          $burialView->burialPlaceOwnerPlaceOfBirth);
        $this->assertSame(null,                                          $burialView->burialPlaceOwnerPassportSeries);
        $this->assertSame(null,                                          $burialView->burialPlaceOwnerPassportNumber);
        $this->assertSame(null,                                          $burialView->burialPlaceOwnerPassportIssuedAt);
        $this->assertSame(null,                                          $burialView->burialPlaceOwnerPassportIssuedBy);
        $this->assertSame(null,                                          $burialView->burialPlaceOwnerPassportDivisionCode);
        $this->assertSame(null,                                          $burialView->funeralCompanyId);
        $this->assertSame(null,                                          $burialView->burialChainId);
        $this->assertSame('GS005',                                       $burialView->burialPlaceId);
        $this->assertSame(GraveSite::CLASS_SHORTCUT,                     $burialView->burialPlaceType);
        $this->assertSame('CB004',                                       $burialView->burialPlaceGraveSiteCemeteryBlockId);
        $this->assertSame('мусульманский',                               $burialView->burialPlaceGraveSiteCemeteryBlockName);
        $this->assertSame(3,                                             $burialView->burialPlaceGraveSiteRowInBlock);
        $this->assertSame(null,                                          $burialView->burialPlaceGraveSitePositionInRow);
        $this->assertSame(null,                                          $burialView->burialPlaceGraveSiteSize);
        $this->assertSame(null,                                          $burialView->burialPlaceGraveSiteGeoPositionLatitude);
        $this->assertSame(null,                                          $burialView->burialPlaceGraveSiteGeoPositionLongitude);
        $this->assertSame(null,                                          $burialView->burialPlaceGraveSiteGeoPositionError);
        $this->assertSame(null,                                          $burialView->burialPlaceColumbariumNicheColumbariumId);
        $this->assertSame(null,                                          $burialView->burialPlaceColumbariumNicheColumbariumName);
        $this->assertSame(null,                                          $burialView->burialPlaceColumbariumNicheRowInColumbarium);
        $this->assertSame(null,                                          $burialView->burialPlaceColumbariumNicheNumber);
        $this->assertSame(null,                                          $burialView->burialPlaceColumbariumNicheGeoPositionLatitude);
        $this->assertSame(null,                                          $burialView->burialPlaceColumbariumNicheGeoPositionLongitude);
        $this->assertSame(null,                                          $burialView->burialPlaceColumbariumNicheGeoPositionError);
        $this->assertSame(null,                                          $burialView->burialPlaceMemorialTreeNumber);
        $this->assertSame(null,                                          $burialView->burialPlaceMemorialTreeGeoPositionLatitude);
        $this->assertSame(null,                                          $burialView->burialPlaceMemorialTreeGeoPositionLongitude);
        $this->assertSame(null,                                          $burialView->burialPlaceMemorialTreeGeoPositionError);
        $this->assertSame(null,                                          $burialView->burialContainerType);
        $this->assertSame(null,                                          $burialView->burialContainerCoffinSize);
        $this->assertSame(null,                                          $burialView->burialContainerCoffinShape);
        $this->assertSame(null,                                          $burialView->burialContainerCoffinIsNonStandard);
        $this->assertSame(null,                                          $burialView->buriedAt);
        $this->assertValidDateTimeValue($burialView->createdAt);
        $this->assertValidDateTimeValue($burialView->updatedAt);
    }

    private function expectExceptionForNotFoundBurialById(string $burialId): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(\sprintf('Захоронение с ID "%s" не найдено.', $burialId));
    }
}
