<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Fetcher;

use Cemetery\Registrar\Domain\Model\Burial\BurialContainer\Coffin;
use Cemetery\Registrar\Domain\Model\Burial\BurialContainer\CoffinShape;
use Cemetery\Registrar\Domain\Model\Burial\BurialContainer\Urn;
use Cemetery\Registrar\Domain\Model\Burial\BurialId;
use Cemetery\Registrar\Domain\Model\Burial\BurialRepository;
use Cemetery\Registrar\Domain\Model\Burial\BurialType;
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

    public function testItReturnsBurialListByPage(): void
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
        $this->assertListItemEqualsB007($listForFirstPage->listItems[0]);  // This item has minimum code value
        $this->assertListItemEqualsB001($listForFirstPage->listItems[1]);
        $this->assertListItemEqualsB002($listForFirstPage->listItems[2]);
        $this->assertListItemEqualsB003($listForFirstPage->listItems[3]);

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
        $this->assertListItemEqualsB005($listForSecondPage->listItems[0]);
        $this->assertListItemEqualsB006($listForSecondPage->listItems[1]);
        $this->assertListItemEqualsB004($listForSecondPage->listItems[2]);  // This item has maximum code value

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

        // Default page size
        $listForDefaultPageSize = $this->burialFetcher->findAll(1);
        $this->assertInstanceOf(BurialList::class, $listForDefaultPageSize);
        $this->assertIsArray($listForDefaultPageSize->listItems);
        $this->assertContainsOnlyInstancesOf(BurialListItem::class, $listForDefaultPageSize->listItems);
        $this->assertCount(7,                      $listForDefaultPageSize->listItems);
        $this->assertSame(1,                       $listForDefaultPageSize->page);
        $this->assertSame(self::DEFAULT_PAGE_SIZE, $listForDefaultPageSize->pageSize);
        $this->assertSame(null,                    $listForDefaultPageSize->term);
        $this->assertSame(7,                       $listForDefaultPageSize->totalCount);
        $this->assertSame(1,                       $listForDefaultPageSize->totalPages);
    }

    public function testItReturnsBurialListByPageAndTerm(): void
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
        $this->assertSame(7,               $list->totalCount);
        $this->assertSame(2,               $list->totalPages);
        $list = $this->burialFetcher->findAll(2, '11', $customPageSize);
        $this->assertInstanceOf(BurialList::class, $list);
        $this->assertIsArray($list->listItems);
        $this->assertContainsOnlyInstancesOf(BurialListItem::class, $list->listItems);
        $this->assertCount(3,              $list->listItems);
        $this->assertSame(2,               $list->page);
        $this->assertSame($customPageSize, $list->pageSize);
        $this->assertSame('11',            $list->term);
        $this->assertSame(7,               $list->totalCount);
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

    private function assertListItemEqualsB001(BurialListItem $listItem): void
    {
        $this->assertSame('B001',                              $listItem->id);
        $this->assertSame('11',                                $listItem->code);
        $this->assertSame('Егоров Абрам Даниилович',           $listItem->deceasedNaturalPersonFullName);
        $this->assertSame(null,                                $listItem->deceasedNaturalPersonBornAt);
        $this->assertSame('2021-12-01',                        $listItem->deceasedDiedAt);
        $this->assertSame(null,                                $listItem->deceasedAge);
        $this->assertSame('2021-12-03 13:10:00',               $listItem->buriedAt);
        $this->assertSame(ColumbariumNiche::CLASS_SHORTCUT,    $listItem->burialPlaceType);
        $this->assertSame(null,                                $listItem->burialPlaceGraveSiteCemeteryBlockName);
        $this->assertSame(null,                                $listItem->burialPlaceGraveSiteRowInBlock);
        $this->assertSame(null,                                $listItem->burialPlaceGraveSitePositionInRow);
        $this->assertSame('южный',                             $listItem->burialPlaceColumbariumNicheColumbariumName);
        $this->assertSame(2,                                   $listItem->burialPlaceColumbariumNicheRowInColumbarium);
        $this->assertSame('002',                               $listItem->burialPlaceColumbariumNicheNumber);
        $this->assertSame(null,                                $listItem->burialPlaceMemorialTreeNumber);
        $this->assertSame(NaturalPerson::CLASS_SHORTCUT,       $listItem->customerType);
        $this->assertSame('Жданова Инга Григорьевна',          $listItem->customerNaturalPersonFullName);
        $this->assertSame('Новосибирск, ул. Ленина, д. 1',     $listItem->customerNaturalPersonAddress);
        $this->assertSame('+7-913-771-22-33',                  $listItem->customerNaturalPersonPhone);
        $this->assertSame(null,                                $listItem->customerSoleProprietorName);
        $this->assertSame(null,                                $listItem->customerSoleProprietorRegistrationAddress);
        $this->assertSame(null,                                $listItem->customerSoleProprietorActualLocationAddress);
        $this->assertSame(null,                                $listItem->customerSoleProprietorPhone);
        $this->assertSame(null,                                $listItem->customerJuristicPersonName);
        $this->assertSame(null,                                $listItem->customerJuristicPersonLegalAddress);
        $this->assertSame(null,                                $listItem->customerJuristicPersonPostalAddress);
        $this->assertSame(null,                                $listItem->customerJuristicPersonPhone);
    }

    private function assertListItemEqualsB002(BurialListItem $listItem): void
    {
        $this->assertSame('B002',                          $listItem->id);
        $this->assertSame('11002',                         $listItem->code);
        $this->assertSame('Устинов Арсений Максович',      $listItem->deceasedNaturalPersonFullName);
        $this->assertSame('1918-12-30',                    $listItem->deceasedNaturalPersonBornAt);
        $this->assertSame('2001-02-12',                    $listItem->deceasedDiedAt);
        $this->assertSame(82,                              $listItem->deceasedAge);
        $this->assertSame(null,                            $listItem->buriedAt);
        $this->assertSame(GraveSite::CLASS_SHORTCUT,       $listItem->burialPlaceType);
        $this->assertSame('общий Б',                       $listItem->burialPlaceGraveSiteCemeteryBlockName);
        $this->assertSame(7,                               $listItem->burialPlaceGraveSiteRowInBlock);
        $this->assertSame(null,                            $listItem->burialPlaceGraveSitePositionInRow);
        $this->assertSame(null,                            $listItem->burialPlaceColumbariumNicheColumbariumName);
        $this->assertSame(null,                            $listItem->burialPlaceColumbariumNicheRowInColumbarium);
        $this->assertSame(null,                            $listItem->burialPlaceColumbariumNicheNumber);
        $this->assertSame(null,                            $listItem->burialPlaceMemorialTreeNumber);
        $this->assertSame(NaturalPerson::CLASS_SHORTCUT,   $listItem->customerType);
        $this->assertSame('Жданова Инга Григорьевна',      $listItem->customerNaturalPersonFullName);
        $this->assertSame('Новосибирск, ул. Ленина, д. 1', $listItem->customerNaturalPersonAddress);
        $this->assertSame('+7-913-771-22-33',              $listItem->customerNaturalPersonPhone);
        $this->assertSame(null,                            $listItem->customerSoleProprietorName);
        $this->assertSame(null,                            $listItem->customerSoleProprietorRegistrationAddress);
        $this->assertSame(null,                            $listItem->customerSoleProprietorActualLocationAddress);
        $this->assertSame(null,                            $listItem->customerSoleProprietorPhone);
        $this->assertSame(null,                            $listItem->customerJuristicPersonName);
        $this->assertSame(null,                            $listItem->customerJuristicPersonLegalAddress);
        $this->assertSame(null,                            $listItem->customerJuristicPersonPostalAddress);
        $this->assertSame(null,                            $listItem->customerJuristicPersonPhone);
    }

    private function assertListItemEqualsB003(BurialListItem $listItem): void
    {
        $this->assertSame('B003',                        $listItem->id);
        $this->assertSame('11003',                       $listItem->code);
        $this->assertSame('Шилов Александр Михаилович',  $listItem->deceasedNaturalPersonFullName);
        $this->assertSame('1969-05-20',                  $listItem->deceasedNaturalPersonBornAt);
        $this->assertSame('2012-05-13',                  $listItem->deceasedDiedAt);
        $this->assertSame(null,                          $listItem->deceasedAge);
        $this->assertSame(null,                          $listItem->buriedAt);
        $this->assertSame(MemorialTree::CLASS_SHORTCUT,  $listItem->burialPlaceType);
        $this->assertSame(null,                          $listItem->burialPlaceGraveSiteCemeteryBlockName);
        $this->assertSame(null,                          $listItem->burialPlaceGraveSiteRowInBlock);
        $this->assertSame(null,                          $listItem->burialPlaceGraveSitePositionInRow);
        $this->assertSame(null,                          $listItem->burialPlaceColumbariumNicheColumbariumName);
        $this->assertSame(null,                          $listItem->burialPlaceColumbariumNicheRowInColumbarium);
        $this->assertSame(null,                          $listItem->burialPlaceColumbariumNicheNumber);
        $this->assertSame('002',                         $listItem->burialPlaceMemorialTreeNumber);
        $this->assertSame(NaturalPerson::CLASS_SHORTCUT, $listItem->customerType);
        $this->assertSame('Гришина Устинья Ярославовна', $listItem->customerNaturalPersonFullName);
        $this->assertSame(null,                          $listItem->customerNaturalPersonAddress);
        $this->assertSame(null,                          $listItem->customerNaturalPersonPhone);
        $this->assertSame(null,                          $listItem->customerSoleProprietorName);
        $this->assertSame(null,                          $listItem->customerSoleProprietorRegistrationAddress);
        $this->assertSame(null,                          $listItem->customerSoleProprietorActualLocationAddress);
        $this->assertSame(null,                          $listItem->customerSoleProprietorPhone);
        $this->assertSame(null,                          $listItem->customerJuristicPersonName);
        $this->assertSame(null,                          $listItem->customerJuristicPersonLegalAddress);
        $this->assertSame(null,                          $listItem->customerJuristicPersonPostalAddress);
        $this->assertSame(null,                          $listItem->customerJuristicPersonPhone);
    }

    private function assertListItemEqualsB004(BurialListItem $listItem): void
    {
        $this->assertSame('B004',                                        $listItem->id);
        $this->assertSame('234117890',                                   $listItem->code);
        $this->assertSame('Жданова Инга Григорьевна',                    $listItem->deceasedNaturalPersonFullName);
        $this->assertSame('1979-02-12',                                  $listItem->deceasedNaturalPersonBornAt);
        $this->assertSame('2022-03-10',                                  $listItem->deceasedDiedAt);
        $this->assertSame(null,                                          $listItem->deceasedAge);
        $this->assertSame(null,                                          $listItem->buriedAt);
        $this->assertSame(GraveSite::CLASS_SHORTCUT,                     $listItem->burialPlaceType);
        $this->assertSame('воинский',                                    $listItem->burialPlaceGraveSiteCemeteryBlockName);
        $this->assertSame(1,                                             $listItem->burialPlaceGraveSiteRowInBlock);
        $this->assertSame(null,                                          $listItem->burialPlaceGraveSitePositionInRow);
        $this->assertSame(null,                                          $listItem->burialPlaceColumbariumNicheColumbariumName);
        $this->assertSame(null,                                          $listItem->burialPlaceColumbariumNicheRowInColumbarium);
        $this->assertSame(null,                                          $listItem->burialPlaceColumbariumNicheNumber);
        $this->assertSame(null,                                          $listItem->burialPlaceMemorialTreeNumber);
        $this->assertSame(JuristicPerson::CLASS_SHORTCUT,                $listItem->customerType);
        $this->assertSame(null,                                          $listItem->customerNaturalPersonFullName);
        $this->assertSame(null,                                          $listItem->customerNaturalPersonAddress);
        $this->assertSame(null,                                          $listItem->customerNaturalPersonPhone);
        $this->assertSame(null,                                          $listItem->customerSoleProprietorName);
        $this->assertSame(null,                                          $listItem->customerSoleProprietorRegistrationAddress);
        $this->assertSame(null,                                          $listItem->customerSoleProprietorActualLocationAddress);
        $this->assertSame(null,                                          $listItem->customerSoleProprietorPhone);
        $this->assertSame('МУП "Новосибирский метрополитен"',            $listItem->customerJuristicPersonName);
        $this->assertSame(null,                                          $listItem->customerJuristicPersonLegalAddress);
        $this->assertSame(null,                                          $listItem->customerJuristicPersonPostalAddress);
        $this->assertSame(null,                                          $listItem->customerJuristicPersonPhone);
    }

    private function assertListItemEqualsB005(BurialListItem $listItem): void
    {
        $this->assertSame('B005',                         $listItem->id);
        $this->assertSame('11005',                        $listItem->code);
        $this->assertSame('Соколов Герман Маркович',      $listItem->deceasedNaturalPersonFullName);
        $this->assertSame(null,                           $listItem->deceasedNaturalPersonBornAt);
        $this->assertSame('2010-01-26',                   $listItem->deceasedDiedAt);
        $this->assertSame(null,                           $listItem->deceasedAge);
        $this->assertSame('2010-01-28 12:55:00',          $listItem->buriedAt);
        $this->assertSame(GraveSite::CLASS_SHORTCUT,      $listItem->burialPlaceType);
        $this->assertSame('общий А',                      $listItem->burialPlaceGraveSiteCemeteryBlockName);
        $this->assertSame(3,                              $listItem->burialPlaceGraveSiteRowInBlock);
        $this->assertSame(4,                              $listItem->burialPlaceGraveSitePositionInRow);
        $this->assertSame(null,                           $listItem->burialPlaceColumbariumNicheColumbariumName);
        $this->assertSame(null,                           $listItem->burialPlaceColumbariumNicheRowInColumbarium);
        $this->assertSame(null,                           $listItem->burialPlaceColumbariumNicheNumber);
        $this->assertSame(null,                           $listItem->burialPlaceMemorialTreeNumber);
        $this->assertSame(SoleProprietor::CLASS_SHORTCUT, $listItem->customerType);
        $this->assertSame(null,                           $listItem->customerNaturalPersonFullName);
        $this->assertSame(null,                           $listItem->customerNaturalPersonAddress);
        $this->assertSame(null,                           $listItem->customerNaturalPersonPhone);
        $this->assertSame('ИП Сидоров Сидр Сидорович',    $listItem->customerSoleProprietorName);
        $this->assertSame(null,                           $listItem->customerSoleProprietorRegistrationAddress);
        $this->assertSame('с. Каменка, д. 14',            $listItem->customerSoleProprietorActualLocationAddress);
        $this->assertSame('8(383)147-22-33',              $listItem->customerSoleProprietorPhone);
        $this->assertSame(null,                           $listItem->customerJuristicPersonName);
        $this->assertSame(null,                           $listItem->customerJuristicPersonLegalAddress);
        $this->assertSame(null,                           $listItem->customerJuristicPersonPostalAddress);
        $this->assertSame(null,                           $listItem->customerJuristicPersonPhone);
    }

    private function assertListItemEqualsB006(BurialListItem $listItem): void
    {
        $this->assertSame('B006',                                        $listItem->id);
        $this->assertSame('11006',                                       $listItem->code);
        $this->assertSame('Гришина Устинья Ярославовна',                 $listItem->deceasedNaturalPersonFullName);
        $this->assertSame(null,                                          $listItem->deceasedNaturalPersonBornAt);
        $this->assertSame('2021-12-03',                                  $listItem->deceasedDiedAt);
        $this->assertSame(null,                                          $listItem->deceasedAge);
        $this->assertSame(null,                                          $listItem->buriedAt);
        $this->assertSame(null,                                          $listItem->burialPlaceType);
        $this->assertSame(null,                                          $listItem->burialPlaceGraveSiteCemeteryBlockName);
        $this->assertSame(null,                                          $listItem->burialPlaceGraveSiteRowInBlock);
        $this->assertSame(null,                                          $listItem->burialPlaceGraveSitePositionInRow);
        $this->assertSame(null,                                          $listItem->burialPlaceColumbariumNicheColumbariumName);
        $this->assertSame(null,                                          $listItem->burialPlaceColumbariumNicheRowInColumbarium);
        $this->assertSame(null,                                          $listItem->burialPlaceColumbariumNicheNumber);
        $this->assertSame(null,                                          $listItem->burialPlaceMemorialTreeNumber);
        $this->assertSame(NaturalPerson::CLASS_SHORTCUT,                 $listItem->customerType);
        $this->assertSame('Громов Никифор Рудольфович',                  $listItem->customerNaturalPersonFullName);
        $this->assertSame('Новосибирск, ул. Н.-Данченко, д. 18, кв. 17', $listItem->customerNaturalPersonAddress);
        $this->assertSame(null,                                          $listItem->customerNaturalPersonPhone);
        $this->assertSame(null,                                          $listItem->customerSoleProprietorName);
        $this->assertSame(null,                                          $listItem->customerSoleProprietorRegistrationAddress);
        $this->assertSame(null,                                          $listItem->customerSoleProprietorActualLocationAddress);
        $this->assertSame(null,                                          $listItem->customerSoleProprietorPhone);
        $this->assertSame(null,                                          $listItem->customerJuristicPersonName);
        $this->assertSame(null,                                          $listItem->customerJuristicPersonLegalAddress);
        $this->assertSame(null,                                          $listItem->customerJuristicPersonPostalAddress);
        $this->assertSame(null,                                          $listItem->customerJuristicPersonPhone);
    }

    private function assertListItemEqualsB007(BurialListItem $listItem): void
    {
        $this->assertSame('B007',                              $listItem->id);
        $this->assertSame('01',                                $listItem->code);
        $this->assertSame('Никонов Родион Митрофанович',       $listItem->deceasedNaturalPersonFullName);
        $this->assertSame(null,                                $listItem->deceasedNaturalPersonBornAt);
        $this->assertSame('1980-05-26',                        $listItem->deceasedDiedAt);
        $this->assertSame(null,                                $listItem->deceasedAge);
        $this->assertSame(null,                                $listItem->buriedAt);
        $this->assertSame(GraveSite::CLASS_SHORTCUT,           $listItem->burialPlaceType);
        $this->assertSame('мусульманский',                     $listItem->burialPlaceGraveSiteCemeteryBlockName);
        $this->assertSame(3,                                   $listItem->burialPlaceGraveSiteRowInBlock);
        $this->assertSame(11,                                  $listItem->burialPlaceGraveSitePositionInRow);
        $this->assertSame(null,                                $listItem->burialPlaceColumbariumNicheColumbariumName);
        $this->assertSame(null,                                $listItem->burialPlaceColumbariumNicheRowInColumbarium);
        $this->assertSame(null,                                $listItem->burialPlaceColumbariumNicheNumber);
        $this->assertSame(null,                                $listItem->burialPlaceMemorialTreeNumber);
        $this->assertSame(null,                                $listItem->customerType);
        $this->assertSame(null,                                $listItem->customerNaturalPersonFullName);
        $this->assertSame(null,                                $listItem->customerNaturalPersonAddress);
        $this->assertSame(null,                                $listItem->customerNaturalPersonPhone);
        $this->assertSame(null,                                $listItem->customerSoleProprietorName);
        $this->assertSame(null,                                $listItem->customerSoleProprietorRegistrationAddress);
        $this->assertSame(null,                                $listItem->customerSoleProprietorActualLocationAddress);
        $this->assertSame(null,                                $listItem->customerSoleProprietorPhone);
        $this->assertSame(null,                                $listItem->customerJuristicPersonName);
        $this->assertSame(null,                                $listItem->customerJuristicPersonLegalAddress);
        $this->assertSame(null,                                $listItem->customerJuristicPersonPostalAddress);
        $this->assertSame(null,                                $listItem->customerJuristicPersonPhone);
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
        $this->assertSame(null,                                        $view->personInChargeId);
        $this->assertSame(null,                                        $view->personInChargeFullName);
        $this->assertSame(null,                                        $view->personInChargePhone);
        $this->assertSame(null,                                        $view->personInChargePhoneAdditional);
        $this->assertSame(null,                                        $view->personInChargeEmail);
        $this->assertSame(null,                                        $view->personInChargeAddress);
        $this->assertSame(null,                                        $view->personInChargeBornAt);
        $this->assertSame(null,                                        $view->personInChargePlaceOfBirth);
        $this->assertSame(null,                                        $view->personInChargePassportSeries);
        $this->assertSame(null,                                        $view->personInChargePassportNumber);
        $this->assertSame(null,                                        $view->personInChargePassportIssuedAt);
        $this->assertSame(null,                                        $view->personInChargePassportIssuedBy);
        $this->assertSame(null,                                        $view->personInChargePassportDivisionCode);
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
        $this->assertSame('NP006',                                     $view->personInChargeId);
        $this->assertSame('Гришина Устинья Ярославовна',               $view->personInChargeFullName);
        $this->assertSame(null,                                        $view->personInChargePhone);
        $this->assertSame(null,                                        $view->personInChargePhoneAdditional);
        $this->assertSame(null,                                        $view->personInChargeEmail);
        $this->assertSame(null,                                        $view->personInChargeAddress);
        $this->assertSame(null,                                        $view->personInChargeBornAt);
        $this->assertSame(null,                                        $view->personInChargePlaceOfBirth);
        $this->assertSame(null,                                        $view->personInChargePassportSeries);
        $this->assertSame(null,                                        $view->personInChargePassportNumber);
        $this->assertSame(null,                                        $view->personInChargePassportIssuedAt);
        $this->assertSame(null,                                        $view->personInChargePassportIssuedBy);
        $this->assertSame(null,                                        $view->personInChargePassportDivisionCode);
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
        $this->assertSame('NP006',                                       $view->personInChargeId);
        $this->assertSame('Гришина Устинья Ярославовна',                 $view->personInChargeFullName);
        $this->assertSame(null,                                          $view->personInChargePhone);
        $this->assertSame(null,                                          $view->personInChargePhoneAdditional);
        $this->assertSame(null,                                          $view->personInChargeEmail);
        $this->assertSame(null,                                          $view->personInChargeAddress);
        $this->assertSame(null,                                          $view->personInChargeBornAt);
        $this->assertSame(null,                                          $view->personInChargePlaceOfBirth);
        $this->assertSame(null,                                          $view->personInChargePassportSeries);
        $this->assertSame(null,                                          $view->personInChargePassportNumber);
        $this->assertSame(null,                                          $view->personInChargePassportIssuedAt);
        $this->assertSame(null,                                          $view->personInChargePassportIssuedBy);
        $this->assertSame(null,                                          $view->personInChargePassportDivisionCode);
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
        $this->assertSame(null,                                          $view->personInChargeId);
        $this->assertSame(null,                                          $view->personInChargeFullName);
        $this->assertSame(null,                                          $view->personInChargePhone);
        $this->assertSame(null,                                          $view->personInChargePhoneAdditional);
        $this->assertSame(null,                                          $view->personInChargeEmail);
        $this->assertSame(null,                                          $view->personInChargeAddress);
        $this->assertSame(null,                                          $view->personInChargeBornAt);
        $this->assertSame(null,                                          $view->personInChargePlaceOfBirth);
        $this->assertSame(null,                                          $view->personInChargePassportSeries);
        $this->assertSame(null,                                          $view->personInChargePassportNumber);
        $this->assertSame(null,                                          $view->personInChargePassportIssuedAt);
        $this->assertSame(null,                                          $view->personInChargePassportIssuedBy);
        $this->assertSame(null,                                          $view->personInChargePassportDivisionCode);
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
        $this->assertSame('NP008',                                       $view->personInChargeId);
        $this->assertSame('Беляев Мечеслав Федорович',                   $view->personInChargeFullName);
        $this->assertSame(null,                                          $view->personInChargePhone);
        $this->assertSame(null,                                          $view->personInChargePhoneAdditional);
        $this->assertSame('mecheslav.belyaev@gmail.com',                 $view->personInChargeEmail);
        $this->assertSame(null,                                          $view->personInChargeAddress);
        $this->assertSame(null,                                          $view->personInChargeBornAt);
        $this->assertSame(null,                                          $view->personInChargePlaceOfBirth);
        $this->assertSame('2345',                                        $view->personInChargePassportSeries);
        $this->assertSame('162354',                                      $view->personInChargePassportNumber);
        $this->assertSame('1981-10-20',                                  $view->personInChargePassportIssuedAt);
        $this->assertSame('Отделом МВД Ленинского района г. Пензы',      $view->personInChargePassportIssuedBy);
        $this->assertSame(null,                                          $view->personInChargePassportDivisionCode);
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
        $this->assertSame(null,                                          $view->personInChargeId);
        $this->assertSame(null,                                          $view->personInChargeFullName);
        $this->assertSame(null,                                          $view->personInChargePhone);
        $this->assertSame(null,                                          $view->personInChargePhoneAdditional);
        $this->assertSame(null,                                          $view->personInChargeEmail);
        $this->assertSame(null,                                          $view->personInChargeAddress);
        $this->assertSame(null,                                          $view->personInChargeBornAt);
        $this->assertSame(null,                                          $view->personInChargePlaceOfBirth);
        $this->assertSame(null,                                          $view->personInChargePassportSeries);
        $this->assertSame(null,                                          $view->personInChargePassportNumber);
        $this->assertSame(null,                                          $view->personInChargePassportIssuedAt);
        $this->assertSame(null,                                          $view->personInChargePassportIssuedBy);
        $this->assertSame(null,                                          $view->personInChargePassportDivisionCode);
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
        $this->assertSame(null,                                          $view->personInChargeId);
        $this->assertSame(null,                                          $view->personInChargeFullName);
        $this->assertSame(null,                                          $view->personInChargePhone);
        $this->assertSame(null,                                          $view->personInChargePhoneAdditional);
        $this->assertSame(null,                                          $view->personInChargeEmail);
        $this->assertSame(null,                                          $view->personInChargeAddress);
        $this->assertSame(null,                                          $view->personInChargeBornAt);
        $this->assertSame(null,                                          $view->personInChargePlaceOfBirth);
        $this->assertSame(null,                                          $view->personInChargePassportSeries);
        $this->assertSame(null,                                          $view->personInChargePassportNumber);
        $this->assertSame(null,                                          $view->personInChargePassportIssuedAt);
        $this->assertSame(null,                                          $view->personInChargePassportIssuedBy);
        $this->assertSame(null,                                          $view->personInChargePassportDivisionCode);
        $this->assertSame(null,                                          $view->funeralCompanyId);
        $this->assertSame(null,                                          $view->burialChainId);
        $this->assertSame('GS005',                                       $view->burialPlaceId);
        $this->assertSame(GraveSite::CLASS_SHORTCUT,                     $view->burialPlaceType);
        $this->assertSame('CB004',                                       $view->burialPlaceGraveSiteCemeteryBlockId);
        $this->assertSame('мусульманский',                               $view->burialPlaceGraveSiteCemeteryBlockName);
        $this->assertSame(3,                                             $view->burialPlaceGraveSiteRowInBlock);
        $this->assertSame(11,                                            $view->burialPlaceGraveSitePositionInRow);
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
