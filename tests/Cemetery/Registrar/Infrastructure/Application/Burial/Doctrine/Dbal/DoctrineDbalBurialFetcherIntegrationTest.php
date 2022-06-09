<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Application\Burial\Doctrine\Dbal;

use Cemetery\Registrar\Application\Burial\BurialFetcher;
use Cemetery\Registrar\Application\Burial\BurialFormView;
use Cemetery\Registrar\Application\Burial\BurialViewList;
use Cemetery\Registrar\Application\Burial\BurialViewListItem;
use Cemetery\Registrar\Domain\Burial\BurialId;
use Cemetery\Registrar\Domain\Burial\BurialRepository;
use Cemetery\Registrar\Domain\Burial\BurialType;
use Cemetery\Registrar\Domain\BurialContainer\Coffin;
use Cemetery\Registrar\Domain\BurialContainer\CoffinShape;
use Cemetery\Registrar\Domain\BurialContainer\Urn;
use Cemetery\Registrar\Domain\BurialPlace\ColumbariumNiche\ColumbariumNiche;
use Cemetery\Registrar\Domain\BurialPlace\GraveSite\GraveSite;
use Cemetery\Registrar\Domain\BurialPlace\MemorialTree\MemorialTree;
use Cemetery\Registrar\Domain\NaturalPerson\NaturalPerson;
use Cemetery\Registrar\Domain\Organization\JuristicPerson\JuristicPerson;
use Cemetery\Registrar\Domain\Organization\SoleProprietor\SoleProprietor;
use Cemetery\Registrar\Infrastructure\Application\Burial\Doctrine\Dbal\DoctrineDbalBurialFetcher;
use Cemetery\Registrar\Infrastructure\Domain\Burial\Doctrine\Orm\DoctrineOrmBurialRepository;
use Cemetery\Tests\Registrar\Infrastructure\Application\FetcherIntegrationTest;
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

    public function testItReturnsBurialFormViewById(): void
    {
        $this->testItReturnsBurialFormViewForB001();
        $this->testItReturnsBurialFormViewForB002();
        $this->testItReturnsBurialFormViewForB003();
        $this->testItReturnsBurialFormViewForB004();
        $this->testItReturnsBurialFormViewForB005();
        $this->testItReturnsBurialFormViewForB006();
        $this->testItReturnsBurialFormViewForB007();
    }

    public function testItFailsToReturnBurialFormViewByUnknownId(): void
    {
        $this->expectExceptionForNotFoundBurialById('unknown_id');
        $this->burialFetcher->getFormViewById('unknown_id');
    }

    public function testItFailsToReturnBurialFormViewForRemovedBurial(): void
    {
        // Prepare database table for testing
        $burialToRemove = $this->burialRepo->findById(new BurialId('B004'));
        $this->burialRepo->remove($burialToRemove);
        $removedBurialId = $burialToRemove->id()->value();

        // Testing itself
        $this->expectExceptionForNotFoundBurialById($removedBurialId);
        $this->burialFetcher->getFormViewById($removedBurialId);
    }

    public function testItReturnsBurialViewListItemsByPage(): void
    {
        $customPageSize = 4;

        // First page
        $listForFirstPage = $this->burialFetcher->findAll(1, null, $customPageSize);
        $this->assertInstanceOf(BurialViewList::class, $listForFirstPage);
        $this->assertCount(4,              $listForFirstPage->listItems);
        $this->assertSame(1,               $listForFirstPage->page);
        $this->assertSame($customPageSize, $listForFirstPage->pageSize);
        $this->assertSame(null,            $listForFirstPage->term);
        $this->assertSame(7,               $listForFirstPage->totalCount);
        $this->assertSame(2,               $listForFirstPage->totalPages);
        $this->assertIsArray($listForFirstPage->listItems);
        $this->assertContainsOnlyInstancesOf(BurialViewListItem::class, $listForFirstPage->listItems);
        $this->assertItemEqualsB007($listForFirstPage->listItems[0]);  // This item has minimum code value
        $this->assertItemEqualsB001($listForFirstPage->listItems[1]);
        $this->assertItemEqualsB002($listForFirstPage->listItems[2]);
        $this->assertItemEqualsB003($listForFirstPage->listItems[3]);

        // Second page
        $listForSecondPage = $this->burialFetcher->findAll(2, null, $customPageSize);
        $this->assertInstanceOf(BurialViewList::class, $listForSecondPage);
        $this->assertCount(3,              $listForSecondPage->listItems);
        $this->assertSame(2,               $listForSecondPage->page);
        $this->assertSame($customPageSize, $listForSecondPage->pageSize);
        $this->assertSame(null,            $listForSecondPage->term);
        $this->assertSame(7,               $listForSecondPage->totalCount);
        $this->assertSame(2,               $listForSecondPage->totalPages);
        $this->assertIsArray($listForSecondPage->listItems);
        $this->assertContainsOnlyInstancesOf(BurialViewListItem::class, $listForSecondPage->listItems);
        $this->assertItemEqualsB005($listForSecondPage->listItems[0]);
        $this->assertItemEqualsB006($listForSecondPage->listItems[1]);
        $this->assertItemEqualsB004($listForSecondPage->listItems[2]);  // This item has maximum code value

        // Third page
        $listForThirdPage = $this->burialFetcher->findAll(3, null, $customPageSize);
        $this->assertInstanceOf(BurialViewList::class, $listForThirdPage);
        $this->assertCount(0,              $listForThirdPage->listItems);
        $this->assertSame(3,               $listForThirdPage->page);
        $this->assertSame($customPageSize, $listForThirdPage->pageSize);
        $this->assertSame(null,            $listForThirdPage->term);
        $this->assertSame(7,               $listForThirdPage->totalCount);
        $this->assertSame(2,               $listForThirdPage->totalPages);

        // All at once
        $listForDefaultPageSize = $this->burialFetcher->findAll(1);
        $this->assertInstanceOf(BurialViewList::class, $listForDefaultPageSize);
        $this->assertCount(7,                      $listForDefaultPageSize->listItems);
        $this->assertSame(1,                       $listForDefaultPageSize->page);
        $this->assertSame(self::DEFAULT_PAGE_SIZE, $listForDefaultPageSize->pageSize);
        $this->assertSame(null,                    $listForDefaultPageSize->term);
        $this->assertSame(7,                       $listForDefaultPageSize->totalCount);
        $this->assertSame(1,                       $listForDefaultPageSize->totalPages);
        $this->assertIsArray($listForDefaultPageSize->listItems);
        $this->assertContainsOnlyInstancesOf(BurialViewListItem::class, $listForDefaultPageSize->listItems);
    }

    public function testItReturnsBurialViewListItemsByPageAndTerm(): void
    {
        $customPageSize = 4;

        $list = $this->burialFetcher->findAll(1, 'Ждан', $customPageSize);
        $this->assertInstanceOf(BurialViewList::class, $list);
        $this->assertCount(3,              $list->listItems);
        $this->assertSame(1,               $list->page);
        $this->assertSame($customPageSize, $list->pageSize);
        $this->assertSame('Ждан',          $list->term);
        $this->assertSame(3,               $list->totalCount);
        $this->assertSame(1,               $list->totalPages);
        $this->assertIsArray($list->listItems);
        $this->assertContainsOnlyInstancesOf(BurialViewListItem::class, $list->listItems);

        $list = $this->burialFetcher->findAll(1, 'Новос', $customPageSize);
        $this->assertInstanceOf(BurialViewList::class, $list);
        $this->assertCount(4,              $list->listItems);
        $this->assertSame(1,               $list->page);
        $this->assertSame($customPageSize, $list->pageSize);
        $this->assertSame('Новос',          $list->term);
        $this->assertSame(4,               $list->totalCount);
        $this->assertSame(1,               $list->totalPages);
        $this->assertIsArray($list->listItems);
        $this->assertContainsOnlyInstancesOf(BurialViewListItem::class, $list->listItems);

        $list = $this->burialFetcher->findAll(1, '11', $customPageSize);
        $this->assertInstanceOf(BurialViewList::class, $list);
        $this->assertCount(4,              $list->listItems);
        $this->assertSame(1,               $list->page);
        $this->assertSame($customPageSize, $list->pageSize);
        $this->assertSame('11',            $list->term);
        $this->assertSame(6,               $list->totalCount);
        $this->assertSame(2,               $list->totalPages);
        $this->assertIsArray($list->listItems);
        $this->assertContainsOnlyInstancesOf(BurialViewListItem::class, $list->listItems);
        $list = $this->burialFetcher->findAll(2, '11', $customPageSize);
        $this->assertInstanceOf(BurialViewList::class, $list);
        $this->assertCount(2,              $list->listItems);
        $this->assertSame(2,               $list->page);
        $this->assertSame($customPageSize, $list->pageSize);
        $this->assertSame('11',            $list->term);
        $this->assertSame(6,               $list->totalCount);
        $this->assertSame(2,               $list->totalPages);
        $this->assertIsArray($list->listItems);
        $this->assertContainsOnlyInstancesOf(BurialViewListItem::class, $list->listItems);
    }

    public function testItReturnsBurialTotalCount(): void
    {
        $this->assertSame(7, $this->burialFetcher->getTotalCount());
    }

    public function testItDoesNotCountRemovedBurialsWhenCalculatingTotalCount(): void
    {
        // Prepare database table for testing
        $burialToRemove = $this->burialRepo->findById(new BurialId('B004'));
        $this->burialRepo->remove($burialToRemove);

        // Testing itself
        $this->assertSame(6, $this->burialFetcher->getTotalCount());
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

    private function assertItemEqualsB001(BurialViewListItem $item): void
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

    private function assertItemEqualsB002(BurialViewListItem $item): void
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

    private function assertItemEqualsB003(BurialViewListItem $item): void
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

    private function assertItemEqualsB004(BurialViewListItem $item): void
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

    private function assertItemEqualsB005(BurialViewListItem $item): void
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

    private function assertItemEqualsB006(BurialViewListItem $item): void
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

    private function assertItemEqualsB007(BurialViewListItem $item): void
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

    private function testItReturnsBurialFormViewForB001(): void
    {
        $burialFormView = $this->burialFetcher->getFormViewById('B001');
        $this->assertInstanceOf(BurialFormView::class, $burialFormView);
        $this->assertSame('B001',                                      $burialFormView->id);
        $this->assertSame('11',                                        $burialFormView->code);
        $this->assertSame(BurialType::URN_IN_COLUMBARIUM_NICHE,        $burialFormView->type);
        $this->assertSame('D001',                                      $burialFormView->deceasedId);
        $this->assertSame('NP001',                                     $burialFormView->deceasedNaturalPersonId);
        $this->assertSame('Егоров Абрам Даниилович',                   $burialFormView->deceasedNaturalPersonFullName);
        $this->assertSame(null,                                        $burialFormView->deceasedNaturalPersonBornAt);
        $this->assertSame('2021-12-01',                                $burialFormView->deceasedDiedAt);
        $this->assertSame(null,                                        $burialFormView->deceasedAge);
        $this->assertSame(null,                                        $burialFormView->deceasedDeathCertificateId);
        $this->assertSame(null,                                        $burialFormView->deceasedCauseOfDeath);
        $this->assertSame('NP005',                                     $burialFormView->customerId);
        $this->assertSame(NaturalPerson::CLASS_SHORTCUT,               $burialFormView->customerType);
        $this->assertSame('Жданова Инга Григорьевна',                  $burialFormView->customerNaturalPersonFullName);
        $this->assertSame('+7-913-771-22-33',                          $burialFormView->customerNaturalPersonPhone);
        $this->assertSame(null,                                        $burialFormView->customerNaturalPersonPhoneAdditional);
        $this->assertSame(null,                                        $burialFormView->customerNaturalPersonEmail);
        $this->assertSame('Новосибирск, ул. Ленина, д. 1',             $burialFormView->customerNaturalPersonAddress);
        $this->assertSame('1979-02-12',                                $burialFormView->customerNaturalPersonBornAt);
        $this->assertSame(null,                                        $burialFormView->customerNaturalPersonPlaceOfBirth);
        $this->assertSame('1234',                                      $burialFormView->customerNaturalPersonPassportSeries);
        $this->assertSame('567890',                                    $burialFormView->customerNaturalPersonPassportNumber);
        $this->assertSame('2002-10-28',                                $burialFormView->customerNaturalPersonPassportIssuedAt);
        $this->assertSame('УВД Кировского района города Новосибирска', $burialFormView->customerNaturalPersonPassportIssuedBy);
        $this->assertSame('540-001',                                   $burialFormView->customerNaturalPersonPassportDivisionCode);
        $this->assertSame(null,                                        $burialFormView->customerSoleProprietorName);
        $this->assertSame(null,                                        $burialFormView->customerSoleProprietorInn);
        $this->assertSame(null,                                        $burialFormView->customerSoleProprietorOgrnip);
        $this->assertSame(null,                                        $burialFormView->customerSoleProprietorOkpo);
        $this->assertSame(null,                                        $burialFormView->customerSoleProprietorOkved);
        $this->assertSame(null,                                        $burialFormView->customerSoleProprietorRegistrationAddress);
        $this->assertSame(null,                                        $burialFormView->customerSoleProprietorActualLocationAddress);
        $this->assertSame(null,                                        $burialFormView->customerSoleProprietorBankDetailsBankName);
        $this->assertSame(null,                                        $burialFormView->customerSoleProprietorBankDetailsBik);
        $this->assertSame(null,                                        $burialFormView->customerSoleProprietorBankDetailsCorrespondentAccount);
        $this->assertSame(null,                                        $burialFormView->customerSoleProprietorBankDetailsCurrentAccount);
        $this->assertSame(null,                                        $burialFormView->customerSoleProprietorPhone);
        $this->assertSame(null,                                        $burialFormView->customerSoleProprietorPhoneAdditional);
        $this->assertSame(null,                                        $burialFormView->customerSoleProprietorFax);
        $this->assertSame(null,                                        $burialFormView->customerSoleProprietorEmail);
        $this->assertSame(null,                                        $burialFormView->customerSoleProprietorWebsite);
        $this->assertSame(null,                                        $burialFormView->customerJuristicPersonName);
        $this->assertSame(null,                                        $burialFormView->customerJuristicPersonInn);
        $this->assertSame(null,                                        $burialFormView->customerJuristicPersonKpp);
        $this->assertSame(null,                                        $burialFormView->customerJuristicPersonOgrn);
        $this->assertSame(null,                                        $burialFormView->customerJuristicPersonOkpo);
        $this->assertSame(null,                                        $burialFormView->customerJuristicPersonOkved);
        $this->assertSame(null,                                        $burialFormView->customerJuristicPersonLegalAddress);
        $this->assertSame(null,                                        $burialFormView->customerJuristicPersonPostalAddress);
        $this->assertSame(null,                                        $burialFormView->customerJuristicPersonBankDetailsBankName);
        $this->assertSame(null,                                        $burialFormView->customerJuristicPersonBankDetailsBik);
        $this->assertSame(null,                                        $burialFormView->customerJuristicPersonBankDetailsCorrespondentAccount);
        $this->assertSame(null,                                        $burialFormView->customerJuristicPersonBankDetailsCurrentAccount);
        $this->assertSame(null,                                        $burialFormView->customerJuristicPersonPhone);
        $this->assertSame(null,                                        $burialFormView->customerJuristicPersonPhoneAdditional);
        $this->assertSame(null,                                        $burialFormView->customerJuristicPersonFax);
        $this->assertSame(null,                                        $burialFormView->customerJuristicPersonGeneralDirector);
        $this->assertSame(null,                                        $burialFormView->customerJuristicPersonEmail);
        $this->assertSame(null,                                        $burialFormView->customerJuristicPersonWebsite);
        $this->assertSame(null,                                        $burialFormView->burialPlaceOwnerId);
        $this->assertSame(null,                                        $burialFormView->burialPlaceOwnerFullName);
        $this->assertSame(null,                                        $burialFormView->burialPlaceOwnerPhone);
        $this->assertSame(null,                                        $burialFormView->burialPlaceOwnerPhoneAdditional);
        $this->assertSame(null,                                        $burialFormView->burialPlaceOwnerEmail);
        $this->assertSame(null,                                        $burialFormView->burialPlaceOwnerAddress);
        $this->assertSame(null,                                        $burialFormView->burialPlaceOwnerBornAt);
        $this->assertSame(null,                                        $burialFormView->burialPlaceOwnerPlaceOfBirth);
        $this->assertSame(null,                                        $burialFormView->burialPlaceOwnerPassportSeries);
        $this->assertSame(null,                                        $burialFormView->burialPlaceOwnerPassportNumber);
        $this->assertSame(null,                                        $burialFormView->burialPlaceOwnerPassportIssuedAt);
        $this->assertSame(null,                                        $burialFormView->burialPlaceOwnerPassportIssuedBy);
        $this->assertSame(null,                                        $burialFormView->burialPlaceOwnerPassportDivisionCode);
        $this->assertSame(null,                                        $burialFormView->funeralCompanyId);
        $this->assertSame(null,                                        $burialFormView->burialChainId);
        $this->assertSame('CN002',                                     $burialFormView->burialPlaceId);
        $this->assertSame(ColumbariumNiche::CLASS_SHORTCUT,            $burialFormView->burialPlaceType);
        $this->assertSame(null,                                        $burialFormView->burialPlaceGraveSiteCemeteryBlockId);
        $this->assertSame(null,                                        $burialFormView->burialPlaceGraveSiteCemeteryBlockName);
        $this->assertSame(null,                                        $burialFormView->burialPlaceGraveSiteRowInBlock);
        $this->assertSame(null,                                        $burialFormView->burialPlaceGraveSitePositionInRow);
        $this->assertSame(null,                                        $burialFormView->burialPlaceGraveSiteSize);
        $this->assertSame(null,                                        $burialFormView->burialPlaceGraveSiteGeoPositionLatitude);
        $this->assertSame(null,                                        $burialFormView->burialPlaceGraveSiteGeoPositionLongitude);
        $this->assertSame(null,                                        $burialFormView->burialPlaceGraveSiteGeoPositionError);
        $this->assertSame('C002',                                      $burialFormView->burialPlaceColumbariumNicheColumbariumId);
        $this->assertSame('южный',                                     $burialFormView->burialPlaceColumbariumNicheColumbariumName);
        $this->assertSame(2,                                           $burialFormView->burialPlaceColumbariumNicheRowInColumbarium);
        $this->assertSame('002',                                       $burialFormView->burialPlaceColumbariumNicheNumber);
        $this->assertSame('54.95035712',                               $burialFormView->burialPlaceColumbariumNicheGeoPositionLatitude);
        $this->assertSame('82.79252',                                  $burialFormView->burialPlaceColumbariumNicheGeoPositionLongitude);
        $this->assertSame('0.5',                                       $burialFormView->burialPlaceColumbariumNicheGeoPositionError);
        $this->assertSame(null,                                        $burialFormView->burialPlaceMemorialTreeNumber);
        $this->assertSame(null,                                        $burialFormView->burialPlaceMemorialTreeGeoPositionLatitude);
        $this->assertSame(null,                                        $burialFormView->burialPlaceMemorialTreeGeoPositionLongitude);
        $this->assertSame(null,                                        $burialFormView->burialPlaceMemorialTreeGeoPositionError);
        $this->assertSame(Urn::CLASS_SHORTCUT,                         $burialFormView->burialContainerType);
        $this->assertSame(null,                                        $burialFormView->burialContainerCoffinSize);
        $this->assertSame(null,                                        $burialFormView->burialContainerCoffinShape);
        $this->assertSame(null,                                        $burialFormView->burialContainerCoffinIsNonStandard);
        $this->assertSame('2021-12-03 13:10:00',                       $burialFormView->buriedAt);
        $this->assertValidUpdateAtValue($burialFormView->updatedAt);
    }

    private function testItReturnsBurialFormViewForB002(): void
    {
        $burialFormView = $this->burialFetcher->getFormViewById('B002');
        $this->assertInstanceOf(BurialFormView::class, $burialFormView);
        $this->assertSame('B002',                                      $burialFormView->id);
        $this->assertSame('11002',                                     $burialFormView->code);
        $this->assertSame(BurialType::COFFIN_IN_GRAVE_SITE,            $burialFormView->type);
        $this->assertSame('D002',                                      $burialFormView->deceasedId);
        $this->assertSame('NP002',                                     $burialFormView->deceasedNaturalPersonId);
        $this->assertSame('Устинов Арсений Максович',                  $burialFormView->deceasedNaturalPersonFullName);
        $this->assertSame('1918-12-30',                                $burialFormView->deceasedNaturalPersonBornAt);
        $this->assertSame('2001-02-12',                                $burialFormView->deceasedDiedAt);
        $this->assertSame(82,                                          $burialFormView->deceasedAge);
        $this->assertSame('DC001',                                     $burialFormView->deceasedDeathCertificateId);
        $this->assertSame('Болезнь сердечно-легочная хроническая',     $burialFormView->deceasedCauseOfDeath);
        $this->assertSame('NP005',                                     $burialFormView->customerId);
        $this->assertSame(NaturalPerson::CLASS_SHORTCUT,               $burialFormView->customerType);
        $this->assertSame('Жданова Инга Григорьевна',                  $burialFormView->customerNaturalPersonFullName);
        $this->assertSame('+7-913-771-22-33',                          $burialFormView->customerNaturalPersonPhone);
        $this->assertSame(null,                                        $burialFormView->customerNaturalPersonPhoneAdditional);
        $this->assertSame(null,                                        $burialFormView->customerNaturalPersonEmail);
        $this->assertSame('Новосибирск, ул. Ленина, д. 1',             $burialFormView->customerNaturalPersonAddress);
        $this->assertSame('1979-02-12',                                $burialFormView->customerNaturalPersonBornAt);
        $this->assertSame(null,                                        $burialFormView->customerNaturalPersonPlaceOfBirth);
        $this->assertSame('1234',                                      $burialFormView->customerNaturalPersonPassportSeries);
        $this->assertSame('567890',                                    $burialFormView->customerNaturalPersonPassportNumber);
        $this->assertSame('2002-10-28',                                $burialFormView->customerNaturalPersonPassportIssuedAt);
        $this->assertSame('УВД Кировского района города Новосибирска', $burialFormView->customerNaturalPersonPassportIssuedBy);
        $this->assertSame('540-001',                                   $burialFormView->customerNaturalPersonPassportDivisionCode);
        $this->assertSame(null,                                        $burialFormView->customerSoleProprietorName);
        $this->assertSame(null,                                        $burialFormView->customerSoleProprietorInn);
        $this->assertSame(null,                                        $burialFormView->customerSoleProprietorOgrnip);
        $this->assertSame(null,                                        $burialFormView->customerSoleProprietorOkpo);
        $this->assertSame(null,                                        $burialFormView->customerSoleProprietorOkved);
        $this->assertSame(null,                                        $burialFormView->customerSoleProprietorRegistrationAddress);
        $this->assertSame(null,                                        $burialFormView->customerSoleProprietorActualLocationAddress);
        $this->assertSame(null,                                        $burialFormView->customerSoleProprietorBankDetailsBankName);
        $this->assertSame(null,                                        $burialFormView->customerSoleProprietorBankDetailsBik);
        $this->assertSame(null,                                        $burialFormView->customerSoleProprietorBankDetailsCorrespondentAccount);
        $this->assertSame(null,                                        $burialFormView->customerSoleProprietorBankDetailsCurrentAccount);
        $this->assertSame(null,                                        $burialFormView->customerSoleProprietorPhone);
        $this->assertSame(null,                                        $burialFormView->customerSoleProprietorPhoneAdditional);
        $this->assertSame(null,                                        $burialFormView->customerSoleProprietorFax);
        $this->assertSame(null,                                        $burialFormView->customerSoleProprietorEmail);
        $this->assertSame(null,                                        $burialFormView->customerSoleProprietorWebsite);
        $this->assertSame(null,                                        $burialFormView->customerJuristicPersonName);
        $this->assertSame(null,                                        $burialFormView->customerJuristicPersonInn);
        $this->assertSame(null,                                        $burialFormView->customerJuristicPersonKpp);
        $this->assertSame(null,                                        $burialFormView->customerJuristicPersonOgrn);
        $this->assertSame(null,                                        $burialFormView->customerJuristicPersonOkpo);
        $this->assertSame(null,                                        $burialFormView->customerJuristicPersonOkved);
        $this->assertSame(null,                                        $burialFormView->customerJuristicPersonLegalAddress);
        $this->assertSame(null,                                        $burialFormView->customerJuristicPersonPostalAddress);
        $this->assertSame(null,                                        $burialFormView->customerJuristicPersonBankDetailsBankName);
        $this->assertSame(null,                                        $burialFormView->customerJuristicPersonBankDetailsBik);
        $this->assertSame(null,                                        $burialFormView->customerJuristicPersonBankDetailsCorrespondentAccount);
        $this->assertSame(null,                                        $burialFormView->customerJuristicPersonBankDetailsCurrentAccount);
        $this->assertSame(null,                                        $burialFormView->customerJuristicPersonPhone);
        $this->assertSame(null,                                        $burialFormView->customerJuristicPersonPhoneAdditional);
        $this->assertSame(null,                                        $burialFormView->customerJuristicPersonFax);
        $this->assertSame(null,                                        $burialFormView->customerJuristicPersonGeneralDirector);
        $this->assertSame(null,                                        $burialFormView->customerJuristicPersonEmail);
        $this->assertSame(null,                                        $burialFormView->customerJuristicPersonWebsite);
        $this->assertSame('NP006',                                     $burialFormView->burialPlaceOwnerId);
        $this->assertSame('Гришина Устинья Ярославовна',               $burialFormView->burialPlaceOwnerFullName);
        $this->assertSame(null,                                        $burialFormView->burialPlaceOwnerPhone);
        $this->assertSame(null,                                        $burialFormView->burialPlaceOwnerPhoneAdditional);
        $this->assertSame(null,                                        $burialFormView->burialPlaceOwnerEmail);
        $this->assertSame(null,                                        $burialFormView->burialPlaceOwnerAddress);
        $this->assertSame(null,                                        $burialFormView->burialPlaceOwnerBornAt);
        $this->assertSame(null,                                        $burialFormView->burialPlaceOwnerPlaceOfBirth);
        $this->assertSame(null,                                        $burialFormView->burialPlaceOwnerPassportSeries);
        $this->assertSame(null,                                        $burialFormView->burialPlaceOwnerPassportNumber);
        $this->assertSame(null,                                        $burialFormView->burialPlaceOwnerPassportIssuedAt);
        $this->assertSame(null,                                        $burialFormView->burialPlaceOwnerPassportIssuedBy);
        $this->assertSame(null,                                        $burialFormView->burialPlaceOwnerPassportDivisionCode);
        $this->assertSame(null,                                        $burialFormView->funeralCompanyId);
        $this->assertSame(null,                                        $burialFormView->burialChainId);
        $this->assertSame('GS003',                                     $burialFormView->burialPlaceId);
        $this->assertSame(GraveSite::CLASS_SHORTCUT,                   $burialFormView->burialPlaceType);
        $this->assertSame('CB003',                                     $burialFormView->burialPlaceGraveSiteCemeteryBlockId);
        $this->assertSame('общий Б',                                   $burialFormView->burialPlaceGraveSiteCemeteryBlockName);
        $this->assertSame(7,                                           $burialFormView->burialPlaceGraveSiteRowInBlock);
        $this->assertSame(null,                                        $burialFormView->burialPlaceGraveSitePositionInRow);
        $this->assertSame('2.5',                                       $burialFormView->burialPlaceGraveSiteSize);
        $this->assertSame('50.950357',                                 $burialFormView->burialPlaceGraveSiteGeoPositionLatitude);
        $this->assertSame('80.7972252',                                $burialFormView->burialPlaceGraveSiteGeoPositionLongitude);
        $this->assertSame(null,                                        $burialFormView->burialPlaceGraveSiteGeoPositionError);
        $this->assertSame(null,                                        $burialFormView->burialPlaceColumbariumNicheColumbariumId);
        $this->assertSame(null,                                        $burialFormView->burialPlaceColumbariumNicheColumbariumName);
        $this->assertSame(null,                                        $burialFormView->burialPlaceColumbariumNicheRowInColumbarium);
        $this->assertSame(null,                                        $burialFormView->burialPlaceColumbariumNicheNumber);
        $this->assertSame(null,                                        $burialFormView->burialPlaceColumbariumNicheGeoPositionLatitude);
        $this->assertSame(null,                                        $burialFormView->burialPlaceColumbariumNicheGeoPositionLongitude);
        $this->assertSame(null,                                        $burialFormView->burialPlaceColumbariumNicheGeoPositionError);
        $this->assertSame(null,                                        $burialFormView->burialPlaceMemorialTreeNumber);
        $this->assertSame(null,                                        $burialFormView->burialPlaceMemorialTreeGeoPositionLatitude);
        $this->assertSame(null,                                        $burialFormView->burialPlaceMemorialTreeGeoPositionLongitude);
        $this->assertSame(null,                                        $burialFormView->burialPlaceMemorialTreeGeoPositionError);
        $this->assertSame(Coffin::CLASS_SHORTCUT,                      $burialFormView->burialContainerType);
        $this->assertSame(180,                                         $burialFormView->burialContainerCoffinSize);
        $this->assertSame(CoffinShape::TRAPEZOID,                      $burialFormView->burialContainerCoffinShape);
        $this->assertSame(false,                                       $burialFormView->burialContainerCoffinIsNonStandard);
        $this->assertSame(null,                                        $burialFormView->buriedAt);
        $this->assertValidUpdateAtValue($burialFormView->updatedAt);
    }

    private function testItReturnsBurialFormViewForB003(): void
    {
        $burialFormView = $this->burialFetcher->getFormViewById('B003');
        $this->assertInstanceOf(BurialFormView::class, $burialFormView);
        $this->assertSame('B003',                                        $burialFormView->id);
        $this->assertSame('11003',                                       $burialFormView->code);
        $this->assertSame(BurialType::ASHES_UNDER_MEMORIAL_TREE,         $burialFormView->type);
        $this->assertSame('D003',                                        $burialFormView->deceasedId);
        $this->assertSame('NP003',                                       $burialFormView->deceasedNaturalPersonId);
        $this->assertSame('Шилов Александр Михаилович',                  $burialFormView->deceasedNaturalPersonFullName);
        $this->assertSame('1969-05-20',                                  $burialFormView->deceasedNaturalPersonBornAt);
        $this->assertSame('2012-05-13',                                  $burialFormView->deceasedDiedAt);
        $this->assertSame(null,                                          $burialFormView->deceasedAge);
        $this->assertSame('DC002',                                       $burialFormView->deceasedDeathCertificateId);
        $this->assertSame('Онкология',                                   $burialFormView->deceasedCauseOfDeath);
        $this->assertSame('NP006',                                       $burialFormView->customerId);
        $this->assertSame(NaturalPerson::CLASS_SHORTCUT,                 $burialFormView->customerType);
        $this->assertSame('Гришина Устинья Ярославовна',                 $burialFormView->customerNaturalPersonFullName);
        $this->assertSame(null,                                          $burialFormView->customerNaturalPersonPhone);
        $this->assertSame(null,                                          $burialFormView->customerNaturalPersonPhoneAdditional);
        $this->assertSame(null,                                          $burialFormView->customerNaturalPersonEmail);
        $this->assertSame(null,                                          $burialFormView->customerNaturalPersonAddress);
        $this->assertSame(null,                                          $burialFormView->customerNaturalPersonBornAt);
        $this->assertSame(null,                                          $burialFormView->customerNaturalPersonPlaceOfBirth);
        $this->assertSame(null,                                          $burialFormView->customerNaturalPersonPassportSeries);
        $this->assertSame(null,                                          $burialFormView->customerNaturalPersonPassportNumber);
        $this->assertSame(null,                                          $burialFormView->customerNaturalPersonPassportIssuedAt);
        $this->assertSame(null,                                          $burialFormView->customerNaturalPersonPassportIssuedBy);
        $this->assertSame(null,                                          $burialFormView->customerNaturalPersonPassportDivisionCode);
        $this->assertSame(null,                                          $burialFormView->customerSoleProprietorName);
        $this->assertSame(null,                                          $burialFormView->customerSoleProprietorInn);
        $this->assertSame(null,                                          $burialFormView->customerSoleProprietorOgrnip);
        $this->assertSame(null,                                          $burialFormView->customerSoleProprietorOkpo);
        $this->assertSame(null,                                          $burialFormView->customerSoleProprietorOkved);
        $this->assertSame(null,                                          $burialFormView->customerSoleProprietorRegistrationAddress);
        $this->assertSame(null,                                          $burialFormView->customerSoleProprietorActualLocationAddress);
        $this->assertSame(null,                                          $burialFormView->customerSoleProprietorBankDetailsBankName);
        $this->assertSame(null,                                          $burialFormView->customerSoleProprietorBankDetailsBik);
        $this->assertSame(null,                                          $burialFormView->customerSoleProprietorBankDetailsCorrespondentAccount);
        $this->assertSame(null,                                          $burialFormView->customerSoleProprietorBankDetailsCurrentAccount);
        $this->assertSame(null,                                          $burialFormView->customerSoleProprietorPhone);
        $this->assertSame(null,                                          $burialFormView->customerSoleProprietorPhoneAdditional);
        $this->assertSame(null,                                          $burialFormView->customerSoleProprietorFax);
        $this->assertSame(null,                                          $burialFormView->customerSoleProprietorEmail);
        $this->assertSame(null,                                          $burialFormView->customerSoleProprietorWebsite);
        $this->assertSame(null,                                          $burialFormView->customerJuristicPersonName);
        $this->assertSame(null,                                          $burialFormView->customerJuristicPersonInn);
        $this->assertSame(null,                                          $burialFormView->customerJuristicPersonKpp);
        $this->assertSame(null,                                          $burialFormView->customerJuristicPersonOgrn);
        $this->assertSame(null,                                          $burialFormView->customerJuristicPersonOkpo);
        $this->assertSame(null,                                          $burialFormView->customerJuristicPersonOkved);
        $this->assertSame(null,                                          $burialFormView->customerJuristicPersonLegalAddress);
        $this->assertSame(null,                                          $burialFormView->customerJuristicPersonPostalAddress);
        $this->assertSame(null,                                          $burialFormView->customerJuristicPersonBankDetailsBankName);
        $this->assertSame(null,                                          $burialFormView->customerJuristicPersonBankDetailsBik);
        $this->assertSame(null,                                          $burialFormView->customerJuristicPersonBankDetailsCorrespondentAccount);
        $this->assertSame(null,                                          $burialFormView->customerJuristicPersonBankDetailsCurrentAccount);
        $this->assertSame(null,                                          $burialFormView->customerJuristicPersonPhone);
        $this->assertSame(null,                                          $burialFormView->customerJuristicPersonPhoneAdditional);
        $this->assertSame(null,                                          $burialFormView->customerJuristicPersonFax);
        $this->assertSame(null,                                          $burialFormView->customerJuristicPersonGeneralDirector);
        $this->assertSame(null,                                          $burialFormView->customerJuristicPersonEmail);
        $this->assertSame(null,                                          $burialFormView->customerJuristicPersonWebsite);
        $this->assertSame('NP006',                                       $burialFormView->burialPlaceOwnerId);
        $this->assertSame('Гришина Устинья Ярославовна',                 $burialFormView->burialPlaceOwnerFullName);
        $this->assertSame(null,                                          $burialFormView->burialPlaceOwnerPhone);
        $this->assertSame(null,                                          $burialFormView->burialPlaceOwnerPhoneAdditional);
        $this->assertSame(null,                                          $burialFormView->burialPlaceOwnerEmail);
        $this->assertSame(null,                                          $burialFormView->burialPlaceOwnerAddress);
        $this->assertSame(null,                                          $burialFormView->burialPlaceOwnerBornAt);
        $this->assertSame(null,                                          $burialFormView->burialPlaceOwnerPlaceOfBirth);
        $this->assertSame(null,                                          $burialFormView->burialPlaceOwnerPassportSeries);
        $this->assertSame(null,                                          $burialFormView->burialPlaceOwnerPassportNumber);
        $this->assertSame(null,                                          $burialFormView->burialPlaceOwnerPassportIssuedAt);
        $this->assertSame(null,                                          $burialFormView->burialPlaceOwnerPassportIssuedBy);
        $this->assertSame(null,                                          $burialFormView->burialPlaceOwnerPassportDivisionCode);
        $this->assertSame('FC001',                                       $burialFormView->funeralCompanyId);
        $this->assertSame(null,                                          $burialFormView->burialChainId);
        $this->assertSame('MT002',                                       $burialFormView->burialPlaceId);
        $this->assertSame(MemorialTree::CLASS_SHORTCUT,                  $burialFormView->burialPlaceType);
        $this->assertSame(null,                                          $burialFormView->burialPlaceGraveSiteCemeteryBlockId);
        $this->assertSame(null,                                          $burialFormView->burialPlaceGraveSiteCemeteryBlockName);
        $this->assertSame(null,                                          $burialFormView->burialPlaceGraveSiteRowInBlock);
        $this->assertSame(null,                                          $burialFormView->burialPlaceGraveSitePositionInRow);
        $this->assertSame(null,                                          $burialFormView->burialPlaceGraveSiteSize);
        $this->assertSame(null,                                          $burialFormView->burialPlaceGraveSiteGeoPositionLatitude);
        $this->assertSame(null,                                          $burialFormView->burialPlaceGraveSiteGeoPositionLongitude);
        $this->assertSame(null,                                          $burialFormView->burialPlaceGraveSiteGeoPositionError);
        $this->assertSame(null,                                          $burialFormView->burialPlaceColumbariumNicheColumbariumId);
        $this->assertSame(null,                                          $burialFormView->burialPlaceColumbariumNicheColumbariumName);
        $this->assertSame(null,                                          $burialFormView->burialPlaceColumbariumNicheRowInColumbarium);
        $this->assertSame(null,                                          $burialFormView->burialPlaceColumbariumNicheNumber);
        $this->assertSame(null,                                          $burialFormView->burialPlaceColumbariumNicheGeoPositionLatitude);
        $this->assertSame(null,                                          $burialFormView->burialPlaceColumbariumNicheGeoPositionLongitude);
        $this->assertSame(null,                                          $burialFormView->burialPlaceColumbariumNicheGeoPositionError);
        $this->assertSame('002',                                         $burialFormView->burialPlaceMemorialTreeNumber);
        $this->assertSame('54.950457',                                   $burialFormView->burialPlaceMemorialTreeGeoPositionLatitude);
        $this->assertSame('82.7972252',                                  $burialFormView->burialPlaceMemorialTreeGeoPositionLongitude);
        $this->assertSame('0.5',                                         $burialFormView->burialPlaceMemorialTreeGeoPositionError);
        $this->assertSame(null,                                          $burialFormView->burialContainerType);
        $this->assertSame(null,                                          $burialFormView->burialContainerCoffinSize);
        $this->assertSame(null,                                          $burialFormView->burialContainerCoffinShape);
        $this->assertSame(null,                                          $burialFormView->burialContainerCoffinIsNonStandard);
        $this->assertSame(null,                                          $burialFormView->buriedAt);
        $this->assertValidUpdateAtValue($burialFormView->updatedAt);
    }

    private function testItReturnsBurialFormViewForB004(): void
    {
        $burialFormView = $this->burialFetcher->getFormViewById('B004');
        $this->assertInstanceOf(BurialFormView::class, $burialFormView);
        $this->assertSame('B004',                                        $burialFormView->id);
        $this->assertSame('234117890',                                   $burialFormView->code);
        $this->assertSame(BurialType::COFFIN_IN_GRAVE_SITE,              $burialFormView->type);
        $this->assertSame('D004',                                        $burialFormView->deceasedId);
        $this->assertSame('NP005',                                       $burialFormView->deceasedNaturalPersonId);
        $this->assertSame('Жданова Инга Григорьевна',                    $burialFormView->deceasedNaturalPersonFullName);
        $this->assertSame('1979-02-12',                                  $burialFormView->deceasedNaturalPersonBornAt);
        $this->assertSame('2022-03-10',                                  $burialFormView->deceasedDiedAt);
        $this->assertSame(null,                                          $burialFormView->deceasedAge);
        $this->assertSame(null,                                          $burialFormView->deceasedDeathCertificateId);
        $this->assertSame(null,                                          $burialFormView->deceasedCauseOfDeath);
        $this->assertSame('JP004',                                       $burialFormView->customerId);
        $this->assertSame(JuristicPerson::CLASS_SHORTCUT,                $burialFormView->customerType);
        $this->assertSame(null,                                          $burialFormView->customerNaturalPersonFullName);
        $this->assertSame(null,                                          $burialFormView->customerNaturalPersonPhone);
        $this->assertSame(null,                                          $burialFormView->customerNaturalPersonPhoneAdditional);
        $this->assertSame(null,                                          $burialFormView->customerNaturalPersonEmail);
        $this->assertSame(null,                                          $burialFormView->customerNaturalPersonAddress);
        $this->assertSame(null,                                          $burialFormView->customerNaturalPersonBornAt);
        $this->assertSame(null,                                          $burialFormView->customerNaturalPersonPlaceOfBirth);
        $this->assertSame(null,                                          $burialFormView->customerNaturalPersonPassportSeries);
        $this->assertSame(null,                                          $burialFormView->customerNaturalPersonPassportNumber);
        $this->assertSame(null,                                          $burialFormView->customerNaturalPersonPassportIssuedAt);
        $this->assertSame(null,                                          $burialFormView->customerNaturalPersonPassportIssuedBy);
        $this->assertSame(null,                                          $burialFormView->customerNaturalPersonPassportDivisionCode);
        $this->assertSame(null,                                          $burialFormView->customerSoleProprietorName);
        $this->assertSame(null,                                          $burialFormView->customerSoleProprietorInn);
        $this->assertSame(null,                                          $burialFormView->customerSoleProprietorOgrnip);
        $this->assertSame(null,                                          $burialFormView->customerSoleProprietorOkpo);
        $this->assertSame(null,                                          $burialFormView->customerSoleProprietorOkved);
        $this->assertSame(null,                                          $burialFormView->customerSoleProprietorRegistrationAddress);
        $this->assertSame(null,                                          $burialFormView->customerSoleProprietorActualLocationAddress);
        $this->assertSame(null,                                          $burialFormView->customerSoleProprietorBankDetailsBankName);
        $this->assertSame(null,                                          $burialFormView->customerSoleProprietorBankDetailsBik);
        $this->assertSame(null,                                          $burialFormView->customerSoleProprietorBankDetailsCorrespondentAccount);
        $this->assertSame(null,                                          $burialFormView->customerSoleProprietorBankDetailsCurrentAccount);
        $this->assertSame(null,                                          $burialFormView->customerSoleProprietorPhone);
        $this->assertSame(null,                                          $burialFormView->customerSoleProprietorPhoneAdditional);
        $this->assertSame(null,                                          $burialFormView->customerSoleProprietorFax);
        $this->assertSame(null,                                          $burialFormView->customerSoleProprietorEmail);
        $this->assertSame(null,                                          $burialFormView->customerSoleProprietorWebsite);
        $this->assertSame('МУП "Новосибирский метрополитен"',            $burialFormView->customerJuristicPersonName);
        $this->assertSame(null,                                          $burialFormView->customerJuristicPersonInn);
        $this->assertSame(null,                                          $burialFormView->customerJuristicPersonKpp);
        $this->assertSame(null,                                          $burialFormView->customerJuristicPersonOgrn);
        $this->assertSame(null,                                          $burialFormView->customerJuristicPersonOkpo);
        $this->assertSame(null,                                          $burialFormView->customerJuristicPersonOkved);
        $this->assertSame(null,                                          $burialFormView->customerJuristicPersonLegalAddress);
        $this->assertSame(null,                                          $burialFormView->customerJuristicPersonPostalAddress);
        $this->assertSame(null,                                          $burialFormView->customerJuristicPersonBankDetailsBankName);
        $this->assertSame(null,                                          $burialFormView->customerJuristicPersonBankDetailsBik);
        $this->assertSame(null,                                          $burialFormView->customerJuristicPersonBankDetailsCorrespondentAccount);
        $this->assertSame(null,                                          $burialFormView->customerJuristicPersonBankDetailsCurrentAccount);
        $this->assertSame(null,                                          $burialFormView->customerJuristicPersonPhone);
        $this->assertSame(null,                                          $burialFormView->customerJuristicPersonPhoneAdditional);
        $this->assertSame(null,                                          $burialFormView->customerJuristicPersonFax);
        $this->assertSame(null,                                          $burialFormView->customerJuristicPersonGeneralDirector);
        $this->assertSame(null,                                          $burialFormView->customerJuristicPersonEmail);
        $this->assertSame(null,                                          $burialFormView->customerJuristicPersonWebsite);
        $this->assertSame(null,                                          $burialFormView->burialPlaceOwnerId);
        $this->assertSame(null,                                          $burialFormView->burialPlaceOwnerFullName);
        $this->assertSame(null,                                          $burialFormView->burialPlaceOwnerPhone);
        $this->assertSame(null,                                          $burialFormView->burialPlaceOwnerPhoneAdditional);
        $this->assertSame(null,                                          $burialFormView->burialPlaceOwnerEmail);
        $this->assertSame(null,                                          $burialFormView->burialPlaceOwnerAddress);
        $this->assertSame(null,                                          $burialFormView->burialPlaceOwnerBornAt);
        $this->assertSame(null,                                          $burialFormView->burialPlaceOwnerPlaceOfBirth);
        $this->assertSame(null,                                          $burialFormView->burialPlaceOwnerPassportSeries);
        $this->assertSame(null,                                          $burialFormView->burialPlaceOwnerPassportNumber);
        $this->assertSame(null,                                          $burialFormView->burialPlaceOwnerPassportIssuedAt);
        $this->assertSame(null,                                          $burialFormView->burialPlaceOwnerPassportIssuedBy);
        $this->assertSame(null,                                          $burialFormView->burialPlaceOwnerPassportDivisionCode);
        $this->assertSame('FC001',                                       $burialFormView->funeralCompanyId);
        $this->assertSame(null,                                          $burialFormView->burialChainId);
        $this->assertSame('GS001',                                       $burialFormView->burialPlaceId);
        $this->assertSame(GraveSite::CLASS_SHORTCUT,                     $burialFormView->burialPlaceType);
        $this->assertSame('CB001',                                       $burialFormView->burialPlaceGraveSiteCemeteryBlockId);
        $this->assertSame('воинский',                                    $burialFormView->burialPlaceGraveSiteCemeteryBlockName);
        $this->assertSame(1,                                             $burialFormView->burialPlaceGraveSiteRowInBlock);
        $this->assertSame(null,                                          $burialFormView->burialPlaceGraveSitePositionInRow);
        $this->assertSame(null,                                          $burialFormView->burialPlaceGraveSiteSize);
        $this->assertSame(null,                                          $burialFormView->burialPlaceGraveSiteGeoPositionLatitude);
        $this->assertSame(null,                                          $burialFormView->burialPlaceGraveSiteGeoPositionLongitude);
        $this->assertSame(null,                                          $burialFormView->burialPlaceGraveSiteGeoPositionError);
        $this->assertSame(null,                                          $burialFormView->burialPlaceColumbariumNicheColumbariumId);
        $this->assertSame(null,                                          $burialFormView->burialPlaceColumbariumNicheColumbariumName);
        $this->assertSame(null,                                          $burialFormView->burialPlaceColumbariumNicheRowInColumbarium);
        $this->assertSame(null,                                          $burialFormView->burialPlaceColumbariumNicheNumber);
        $this->assertSame(null,                                          $burialFormView->burialPlaceColumbariumNicheGeoPositionLatitude);
        $this->assertSame(null,                                          $burialFormView->burialPlaceColumbariumNicheGeoPositionLongitude);
        $this->assertSame(null,                                          $burialFormView->burialPlaceColumbariumNicheGeoPositionError);
        $this->assertSame(null,                                          $burialFormView->burialPlaceMemorialTreeNumber);
        $this->assertSame(null,                                          $burialFormView->burialPlaceMemorialTreeGeoPositionLatitude);
        $this->assertSame(null,                                          $burialFormView->burialPlaceMemorialTreeGeoPositionLongitude);
        $this->assertSame(null,                                          $burialFormView->burialPlaceMemorialTreeGeoPositionError);
        $this->assertSame(null,                                          $burialFormView->burialContainerType);
        $this->assertSame(null,                                          $burialFormView->burialContainerCoffinSize);
        $this->assertSame(null,                                          $burialFormView->burialContainerCoffinShape);
        $this->assertSame(null,                                          $burialFormView->burialContainerCoffinIsNonStandard);
        $this->assertSame(null,                                          $burialFormView->buriedAt);
        $this->assertValidUpdateAtValue($burialFormView->updatedAt);
    }

    private function testItReturnsBurialFormViewForB005(): void
    {
        $burialFormView = $this->burialFetcher->getFormViewById('B005');
        $this->assertInstanceOf(BurialFormView::class, $burialFormView);
        $this->assertSame('B005',                                        $burialFormView->id);
        $this->assertSame('11005',                                       $burialFormView->code);
        $this->assertSame(BurialType::COFFIN_IN_GRAVE_SITE,              $burialFormView->type);
        $this->assertSame('D005',                                        $burialFormView->deceasedId);
        $this->assertSame('NP004',                                       $burialFormView->deceasedNaturalPersonId);
        $this->assertSame('Соколов Герман Маркович',                     $burialFormView->deceasedNaturalPersonFullName);
        $this->assertSame(null,                                          $burialFormView->deceasedNaturalPersonBornAt);
        $this->assertSame('2010-01-26',                                  $burialFormView->deceasedDiedAt);
        $this->assertSame(null,                                          $burialFormView->deceasedAge);
        $this->assertSame(null,                                          $burialFormView->deceasedDeathCertificateId);
        $this->assertSame(null,                                          $burialFormView->deceasedCauseOfDeath);
        $this->assertSame('SP003',                                       $burialFormView->customerId);
        $this->assertSame(SoleProprietor::CLASS_SHORTCUT,                $burialFormView->customerType);
        $this->assertSame(null,                                          $burialFormView->customerNaturalPersonFullName);
        $this->assertSame(null,                                          $burialFormView->customerNaturalPersonPhone);
        $this->assertSame(null,                                          $burialFormView->customerNaturalPersonPhoneAdditional);
        $this->assertSame(null,                                          $burialFormView->customerNaturalPersonEmail);
        $this->assertSame(null,                                          $burialFormView->customerNaturalPersonAddress);
        $this->assertSame(null,                                          $burialFormView->customerNaturalPersonBornAt);
        $this->assertSame(null,                                          $burialFormView->customerNaturalPersonPlaceOfBirth);
        $this->assertSame(null,                                          $burialFormView->customerNaturalPersonPassportSeries);
        $this->assertSame(null,                                          $burialFormView->customerNaturalPersonPassportNumber);
        $this->assertSame(null,                                          $burialFormView->customerNaturalPersonPassportIssuedAt);
        $this->assertSame(null,                                          $burialFormView->customerNaturalPersonPassportIssuedBy);
        $this->assertSame(null,                                          $burialFormView->customerNaturalPersonPassportDivisionCode);
        $this->assertSame('ИП Сидоров Сидр Сидорович',                   $burialFormView->customerSoleProprietorName);
        $this->assertSame('391600743661',                                $burialFormView->customerSoleProprietorInn);
        $this->assertSame(null,                                          $burialFormView->customerSoleProprietorOgrnip);
        $this->assertSame(null,                                          $burialFormView->customerSoleProprietorOkpo);
        $this->assertSame(null,                                          $burialFormView->customerSoleProprietorOkved);
        $this->assertSame(null,                                          $burialFormView->customerSoleProprietorRegistrationAddress);
        $this->assertSame('с. Каменка, д. 14',                           $burialFormView->customerSoleProprietorActualLocationAddress);
        $this->assertSame(null,                                          $burialFormView->customerSoleProprietorBankDetailsBankName);
        $this->assertSame(null,                                          $burialFormView->customerSoleProprietorBankDetailsBik);
        $this->assertSame(null,                                          $burialFormView->customerSoleProprietorBankDetailsCorrespondentAccount);
        $this->assertSame(null,                                          $burialFormView->customerSoleProprietorBankDetailsCurrentAccount);
        $this->assertSame('8(383)147-22-33',                             $burialFormView->customerSoleProprietorPhone);
        $this->assertSame(null,                                          $burialFormView->customerSoleProprietorPhoneAdditional);
        $this->assertSame(null,                                          $burialFormView->customerSoleProprietorFax);
        $this->assertSame(null,                                          $burialFormView->customerSoleProprietorEmail);
        $this->assertSame(null,                                          $burialFormView->customerSoleProprietorWebsite);
        $this->assertSame(null,                                          $burialFormView->customerJuristicPersonName);
        $this->assertSame(null,                                          $burialFormView->customerJuristicPersonInn);
        $this->assertSame(null,                                          $burialFormView->customerJuristicPersonKpp);
        $this->assertSame(null,                                          $burialFormView->customerJuristicPersonOgrn);
        $this->assertSame(null,                                          $burialFormView->customerJuristicPersonOkpo);
        $this->assertSame(null,                                          $burialFormView->customerJuristicPersonOkved);
        $this->assertSame(null,                                          $burialFormView->customerJuristicPersonLegalAddress);
        $this->assertSame(null,                                          $burialFormView->customerJuristicPersonPostalAddress);
        $this->assertSame(null,                                          $burialFormView->customerJuristicPersonBankDetailsBankName);
        $this->assertSame(null,                                          $burialFormView->customerJuristicPersonBankDetailsBik);
        $this->assertSame(null,                                          $burialFormView->customerJuristicPersonBankDetailsCorrespondentAccount);
        $this->assertSame(null,                                          $burialFormView->customerJuristicPersonBankDetailsCurrentAccount);
        $this->assertSame(null,                                          $burialFormView->customerJuristicPersonPhone);
        $this->assertSame(null,                                          $burialFormView->customerJuristicPersonPhoneAdditional);
        $this->assertSame(null,                                          $burialFormView->customerJuristicPersonFax);
        $this->assertSame(null,                                          $burialFormView->customerJuristicPersonGeneralDirector);
        $this->assertSame(null,                                          $burialFormView->customerJuristicPersonEmail);
        $this->assertSame(null,                                          $burialFormView->customerJuristicPersonWebsite);
        $this->assertSame('NP008',                                       $burialFormView->burialPlaceOwnerId);
        $this->assertSame('Беляев Мечеслав Федорович',                   $burialFormView->burialPlaceOwnerFullName);
        $this->assertSame(null,                                          $burialFormView->burialPlaceOwnerPhone);
        $this->assertSame(null,                                          $burialFormView->burialPlaceOwnerPhoneAdditional);
        $this->assertSame('mecheslav.belyaev@gmail.com',                 $burialFormView->burialPlaceOwnerEmail);
        $this->assertSame(null,                                          $burialFormView->burialPlaceOwnerAddress);
        $this->assertSame(null,                                          $burialFormView->burialPlaceOwnerBornAt);
        $this->assertSame(null,                                          $burialFormView->burialPlaceOwnerPlaceOfBirth);
        $this->assertSame('2345',                                        $burialFormView->burialPlaceOwnerPassportSeries);
        $this->assertSame('162354',                                      $burialFormView->burialPlaceOwnerPassportNumber);
        $this->assertSame('1981-10-20',                                  $burialFormView->burialPlaceOwnerPassportIssuedAt);
        $this->assertSame('Отделом МВД Ленинского района г. Пензы',      $burialFormView->burialPlaceOwnerPassportIssuedBy);
        $this->assertSame(null,                                          $burialFormView->burialPlaceOwnerPassportDivisionCode);
        $this->assertSame('FC002',                                       $burialFormView->funeralCompanyId);
        $this->assertSame(null,                                          $burialFormView->burialChainId);
        $this->assertSame('GS002',                                       $burialFormView->burialPlaceId);
        $this->assertSame(GraveSite::CLASS_SHORTCUT,                     $burialFormView->burialPlaceType);
        $this->assertSame('CB002',                                       $burialFormView->burialPlaceGraveSiteCemeteryBlockId);
        $this->assertSame('общий А',                                     $burialFormView->burialPlaceGraveSiteCemeteryBlockName);
        $this->assertSame(3,                                             $burialFormView->burialPlaceGraveSiteRowInBlock);
        $this->assertSame(4,                                             $burialFormView->burialPlaceGraveSitePositionInRow);
        $this->assertSame(null,                                          $burialFormView->burialPlaceGraveSiteSize);
        $this->assertSame('54.950357',                                   $burialFormView->burialPlaceGraveSiteGeoPositionLatitude);
        $this->assertSame('82.7972252',                                  $burialFormView->burialPlaceGraveSiteGeoPositionLongitude);
        $this->assertSame('0.5',                                         $burialFormView->burialPlaceGraveSiteGeoPositionError);
        $this->assertSame(null,                                          $burialFormView->burialPlaceColumbariumNicheColumbariumId);
        $this->assertSame(null,                                          $burialFormView->burialPlaceColumbariumNicheColumbariumName);
        $this->assertSame(null,                                          $burialFormView->burialPlaceColumbariumNicheRowInColumbarium);
        $this->assertSame(null,                                          $burialFormView->burialPlaceColumbariumNicheNumber);
        $this->assertSame(null,                                          $burialFormView->burialPlaceColumbariumNicheGeoPositionLatitude);
        $this->assertSame(null,                                          $burialFormView->burialPlaceColumbariumNicheGeoPositionLongitude);
        $this->assertSame(null,                                          $burialFormView->burialPlaceColumbariumNicheGeoPositionError);
        $this->assertSame(null,                                          $burialFormView->burialPlaceMemorialTreeNumber);
        $this->assertSame(null,                                          $burialFormView->burialPlaceMemorialTreeGeoPositionLatitude);
        $this->assertSame(null,                                          $burialFormView->burialPlaceMemorialTreeGeoPositionLongitude);
        $this->assertSame(null,                                          $burialFormView->burialPlaceMemorialTreeGeoPositionError);
        $this->assertSame(null,                                          $burialFormView->burialContainerType);
        $this->assertSame(null,                                          $burialFormView->burialContainerCoffinSize);
        $this->assertSame(null,                                          $burialFormView->burialContainerCoffinShape);
        $this->assertSame(null,                                          $burialFormView->burialContainerCoffinIsNonStandard);
        $this->assertSame('2010-01-28 12:55:00',                         $burialFormView->buriedAt);
        $this->assertValidUpdateAtValue($burialFormView->updatedAt);
    }

    private function testItReturnsBurialFormViewForB006(): void
    {
        $burialFormView = $this->burialFetcher->getFormViewById('B006');
        $this->assertInstanceOf(BurialFormView::class, $burialFormView);
        $this->assertSame('B006',                                        $burialFormView->id);
        $this->assertSame('11006',                                       $burialFormView->code);
        $this->assertSame(BurialType::COFFIN_IN_GRAVE_SITE,              $burialFormView->type);
        $this->assertSame('D006',                                        $burialFormView->deceasedId);
        $this->assertSame('NP006',                                       $burialFormView->deceasedNaturalPersonId);
        $this->assertSame('Гришина Устинья Ярославовна',                 $burialFormView->deceasedNaturalPersonFullName);
        $this->assertSame(null,                                          $burialFormView->deceasedNaturalPersonBornAt);
        $this->assertSame('2021-12-03',                                  $burialFormView->deceasedDiedAt);
        $this->assertSame(null,                                          $burialFormView->deceasedAge);
        $this->assertSame(null,                                          $burialFormView->deceasedDeathCertificateId);
        $this->assertSame(null,                                          $burialFormView->deceasedCauseOfDeath);
        $this->assertSame('NP007',                                       $burialFormView->customerId);
        $this->assertSame(NaturalPerson::CLASS_SHORTCUT,                 $burialFormView->customerType);
        $this->assertSame('Громов Никифор Рудольфович',                  $burialFormView->customerNaturalPersonFullName);
        $this->assertSame(null,                                          $burialFormView->customerNaturalPersonPhone);
        $this->assertSame(null,                                          $burialFormView->customerNaturalPersonPhoneAdditional);
        $this->assertSame(null,                                          $burialFormView->customerNaturalPersonEmail);
        $this->assertSame('Новосибирск, ул. Н.-Данченко, д. 18, кв. 17', $burialFormView->customerNaturalPersonAddress);
        $this->assertSame('1915-09-24',                                  $burialFormView->customerNaturalPersonBornAt);
        $this->assertSame(null,                                          $burialFormView->customerNaturalPersonPlaceOfBirth);
        $this->assertSame(null,                                          $burialFormView->customerNaturalPersonPassportSeries);
        $this->assertSame(null,                                          $burialFormView->customerNaturalPersonPassportNumber);
        $this->assertSame(null,                                          $burialFormView->customerNaturalPersonPassportIssuedAt);
        $this->assertSame(null,                                          $burialFormView->customerNaturalPersonPassportIssuedBy);
        $this->assertSame(null,                                          $burialFormView->customerNaturalPersonPassportDivisionCode);
        $this->assertSame(null,                                          $burialFormView->customerSoleProprietorName);
        $this->assertSame(null,                                          $burialFormView->customerSoleProprietorInn);
        $this->assertSame(null,                                          $burialFormView->customerSoleProprietorOgrnip);
        $this->assertSame(null,                                          $burialFormView->customerSoleProprietorOkpo);
        $this->assertSame(null,                                          $burialFormView->customerSoleProprietorOkved);
        $this->assertSame(null,                                          $burialFormView->customerSoleProprietorRegistrationAddress);
        $this->assertSame(null,                                          $burialFormView->customerSoleProprietorActualLocationAddress);
        $this->assertSame(null,                                          $burialFormView->customerSoleProprietorBankDetailsBankName);
        $this->assertSame(null,                                          $burialFormView->customerSoleProprietorBankDetailsBik);
        $this->assertSame(null,                                          $burialFormView->customerSoleProprietorBankDetailsCorrespondentAccount);
        $this->assertSame(null,                                          $burialFormView->customerSoleProprietorBankDetailsCurrentAccount);
        $this->assertSame(null,                                          $burialFormView->customerSoleProprietorPhone);
        $this->assertSame(null,                                          $burialFormView->customerSoleProprietorPhoneAdditional);
        $this->assertSame(null,                                          $burialFormView->customerSoleProprietorFax);
        $this->assertSame(null,                                          $burialFormView->customerSoleProprietorEmail);
        $this->assertSame(null,                                          $burialFormView->customerSoleProprietorWebsite);
        $this->assertSame(null,                                          $burialFormView->customerJuristicPersonName);
        $this->assertSame(null,                                          $burialFormView->customerJuristicPersonInn);
        $this->assertSame(null,                                          $burialFormView->customerJuristicPersonKpp);
        $this->assertSame(null,                                          $burialFormView->customerJuristicPersonOgrn);
        $this->assertSame(null,                                          $burialFormView->customerJuristicPersonOkpo);
        $this->assertSame(null,                                          $burialFormView->customerJuristicPersonOkved);
        $this->assertSame(null,                                          $burialFormView->customerJuristicPersonLegalAddress);
        $this->assertSame(null,                                          $burialFormView->customerJuristicPersonPostalAddress);
        $this->assertSame(null,                                          $burialFormView->customerJuristicPersonBankDetailsBankName);
        $this->assertSame(null,                                          $burialFormView->customerJuristicPersonBankDetailsBik);
        $this->assertSame(null,                                          $burialFormView->customerJuristicPersonBankDetailsCorrespondentAccount);
        $this->assertSame(null,                                          $burialFormView->customerJuristicPersonBankDetailsCurrentAccount);
        $this->assertSame(null,                                          $burialFormView->customerJuristicPersonPhone);
        $this->assertSame(null,                                          $burialFormView->customerJuristicPersonPhoneAdditional);
        $this->assertSame(null,                                          $burialFormView->customerJuristicPersonFax);
        $this->assertSame(null,                                          $burialFormView->customerJuristicPersonGeneralDirector);
        $this->assertSame(null,                                          $burialFormView->customerJuristicPersonEmail);
        $this->assertSame(null,                                          $burialFormView->customerJuristicPersonWebsite);
        $this->assertSame(null,                                          $burialFormView->burialPlaceOwnerId);
        $this->assertSame(null,                                          $burialFormView->burialPlaceOwnerFullName);
        $this->assertSame(null,                                          $burialFormView->burialPlaceOwnerPhone);
        $this->assertSame(null,                                          $burialFormView->burialPlaceOwnerPhoneAdditional);
        $this->assertSame(null,                                          $burialFormView->burialPlaceOwnerEmail);
        $this->assertSame(null,                                          $burialFormView->burialPlaceOwnerAddress);
        $this->assertSame(null,                                          $burialFormView->burialPlaceOwnerBornAt);
        $this->assertSame(null,                                          $burialFormView->burialPlaceOwnerPlaceOfBirth);
        $this->assertSame(null,                                          $burialFormView->burialPlaceOwnerPassportSeries);
        $this->assertSame(null,                                          $burialFormView->burialPlaceOwnerPassportNumber);
        $this->assertSame(null,                                          $burialFormView->burialPlaceOwnerPassportIssuedAt);
        $this->assertSame(null,                                          $burialFormView->burialPlaceOwnerPassportIssuedBy);
        $this->assertSame(null,                                          $burialFormView->burialPlaceOwnerPassportDivisionCode);
        $this->assertSame('FC003',                                       $burialFormView->funeralCompanyId);
        $this->assertSame(null,                                          $burialFormView->burialChainId);
        $this->assertSame(null,                                          $burialFormView->burialPlaceId);
        $this->assertSame(null,                                          $burialFormView->burialPlaceType);
        $this->assertSame(null,                                          $burialFormView->burialPlaceGraveSiteCemeteryBlockId);
        $this->assertSame(null,                                          $burialFormView->burialPlaceGraveSiteCemeteryBlockName);
        $this->assertSame(null,                                          $burialFormView->burialPlaceGraveSiteRowInBlock);
        $this->assertSame(null,                                          $burialFormView->burialPlaceGraveSitePositionInRow);
        $this->assertSame(null,                                          $burialFormView->burialPlaceGraveSiteSize);
        $this->assertSame(null,                                          $burialFormView->burialPlaceGraveSiteGeoPositionLatitude);
        $this->assertSame(null,                                          $burialFormView->burialPlaceGraveSiteGeoPositionLongitude);
        $this->assertSame(null,                                          $burialFormView->burialPlaceGraveSiteGeoPositionError);
        $this->assertSame(null,                                          $burialFormView->burialPlaceColumbariumNicheColumbariumId);
        $this->assertSame(null,                                          $burialFormView->burialPlaceColumbariumNicheColumbariumName);
        $this->assertSame(null,                                          $burialFormView->burialPlaceColumbariumNicheRowInColumbarium);
        $this->assertSame(null,                                          $burialFormView->burialPlaceColumbariumNicheNumber);
        $this->assertSame(null,                                          $burialFormView->burialPlaceColumbariumNicheGeoPositionLatitude);
        $this->assertSame(null,                                          $burialFormView->burialPlaceColumbariumNicheGeoPositionLongitude);
        $this->assertSame(null,                                          $burialFormView->burialPlaceColumbariumNicheGeoPositionError);
        $this->assertSame(null,                                          $burialFormView->burialPlaceMemorialTreeNumber);
        $this->assertSame(null,                                          $burialFormView->burialPlaceMemorialTreeGeoPositionLatitude);
        $this->assertSame(null,                                          $burialFormView->burialPlaceMemorialTreeGeoPositionLongitude);
        $this->assertSame(null,                                          $burialFormView->burialPlaceMemorialTreeGeoPositionError);
        $this->assertSame(null,                                          $burialFormView->burialContainerType);
        $this->assertSame(null,                                          $burialFormView->burialContainerCoffinSize);
        $this->assertSame(null,                                          $burialFormView->burialContainerCoffinShape);
        $this->assertSame(null,                                          $burialFormView->burialContainerCoffinIsNonStandard);
        $this->assertSame(null,                                          $burialFormView->buriedAt);
        $this->assertValidUpdateAtValue($burialFormView->updatedAt);
    }

    private function testItReturnsBurialFormViewForB007(): void
    {
        $burialFormView = $this->burialFetcher->getFormViewById('B007');
        $this->assertInstanceOf(BurialFormView::class, $burialFormView);
        $this->assertSame('B007',                                        $burialFormView->id);
        $this->assertSame('01',                                          $burialFormView->code);
        $this->assertSame(BurialType::COFFIN_IN_GRAVE_SITE,              $burialFormView->type);
        $this->assertSame('D007',                                        $burialFormView->deceasedId);
        $this->assertSame('NP009',                                       $burialFormView->deceasedNaturalPersonId);
        $this->assertSame('Никонов Родион Митрофанович',                 $burialFormView->deceasedNaturalPersonFullName);
        $this->assertSame(null,                                          $burialFormView->deceasedNaturalPersonBornAt);
        $this->assertSame('1980-05-26',                                  $burialFormView->deceasedDiedAt);
        $this->assertSame(null,                                          $burialFormView->deceasedAge);
        $this->assertSame(null,                                          $burialFormView->deceasedDeathCertificateId);
        $this->assertSame(null,                                          $burialFormView->deceasedCauseOfDeath);
        $this->assertSame(null,                                          $burialFormView->customerId);
        $this->assertSame(null,                                          $burialFormView->customerType);
        $this->assertSame(null ,                                         $burialFormView->customerNaturalPersonFullName);
        $this->assertSame(null,                                          $burialFormView->customerNaturalPersonPhone);
        $this->assertSame(null,                                          $burialFormView->customerNaturalPersonPhoneAdditional);
        $this->assertSame(null,                                          $burialFormView->customerNaturalPersonEmail);
        $this->assertSame(null,                                          $burialFormView->customerNaturalPersonAddress);
        $this->assertSame(null,                                          $burialFormView->customerNaturalPersonBornAt);
        $this->assertSame(null,                                          $burialFormView->customerNaturalPersonPlaceOfBirth);
        $this->assertSame(null,                                          $burialFormView->customerNaturalPersonPassportSeries);
        $this->assertSame(null,                                          $burialFormView->customerNaturalPersonPassportNumber);
        $this->assertSame(null,                                          $burialFormView->customerNaturalPersonPassportIssuedAt);
        $this->assertSame(null,                                          $burialFormView->customerNaturalPersonPassportIssuedBy);
        $this->assertSame(null,                                          $burialFormView->customerNaturalPersonPassportDivisionCode);
        $this->assertSame(null,                                          $burialFormView->customerSoleProprietorName);
        $this->assertSame(null,                                          $burialFormView->customerSoleProprietorInn);
        $this->assertSame(null,                                          $burialFormView->customerSoleProprietorOgrnip);
        $this->assertSame(null,                                          $burialFormView->customerSoleProprietorOkpo);
        $this->assertSame(null,                                          $burialFormView->customerSoleProprietorOkved);
        $this->assertSame(null,                                          $burialFormView->customerSoleProprietorRegistrationAddress);
        $this->assertSame(null,                                          $burialFormView->customerSoleProprietorActualLocationAddress);
        $this->assertSame(null,                                          $burialFormView->customerSoleProprietorBankDetailsBankName);
        $this->assertSame(null,                                          $burialFormView->customerSoleProprietorBankDetailsBik);
        $this->assertSame(null,                                          $burialFormView->customerSoleProprietorBankDetailsCorrespondentAccount);
        $this->assertSame(null,                                          $burialFormView->customerSoleProprietorBankDetailsCurrentAccount);
        $this->assertSame(null,                                          $burialFormView->customerSoleProprietorPhone);
        $this->assertSame(null,                                          $burialFormView->customerSoleProprietorPhoneAdditional);
        $this->assertSame(null,                                          $burialFormView->customerSoleProprietorFax);
        $this->assertSame(null,                                          $burialFormView->customerSoleProprietorEmail);
        $this->assertSame(null,                                          $burialFormView->customerSoleProprietorWebsite);
        $this->assertSame(null,                                          $burialFormView->customerJuristicPersonName);
        $this->assertSame(null,                                          $burialFormView->customerJuristicPersonInn);
        $this->assertSame(null,                                          $burialFormView->customerJuristicPersonKpp);
        $this->assertSame(null,                                          $burialFormView->customerJuristicPersonOgrn);
        $this->assertSame(null,                                          $burialFormView->customerJuristicPersonOkpo);
        $this->assertSame(null,                                          $burialFormView->customerJuristicPersonOkved);
        $this->assertSame(null,                                          $burialFormView->customerJuristicPersonLegalAddress);
        $this->assertSame(null,                                          $burialFormView->customerJuristicPersonPostalAddress);
        $this->assertSame(null,                                          $burialFormView->customerJuristicPersonBankDetailsBankName);
        $this->assertSame(null,                                          $burialFormView->customerJuristicPersonBankDetailsBik);
        $this->assertSame(null,                                          $burialFormView->customerJuristicPersonBankDetailsCorrespondentAccount);
        $this->assertSame(null,                                          $burialFormView->customerJuristicPersonBankDetailsCurrentAccount);
        $this->assertSame(null,                                          $burialFormView->customerJuristicPersonPhone);
        $this->assertSame(null,                                          $burialFormView->customerJuristicPersonPhoneAdditional);
        $this->assertSame(null,                                          $burialFormView->customerJuristicPersonFax);
        $this->assertSame(null,                                          $burialFormView->customerJuristicPersonGeneralDirector);
        $this->assertSame(null,                                          $burialFormView->customerJuristicPersonEmail);
        $this->assertSame(null,                                          $burialFormView->customerJuristicPersonWebsite);
        $this->assertSame(null,                                          $burialFormView->burialPlaceOwnerId);
        $this->assertSame(null,                                          $burialFormView->burialPlaceOwnerFullName);
        $this->assertSame(null,                                          $burialFormView->burialPlaceOwnerPhone);
        $this->assertSame(null,                                          $burialFormView->burialPlaceOwnerPhoneAdditional);
        $this->assertSame(null,                                          $burialFormView->burialPlaceOwnerEmail);
        $this->assertSame(null,                                          $burialFormView->burialPlaceOwnerAddress);
        $this->assertSame(null,                                          $burialFormView->burialPlaceOwnerBornAt);
        $this->assertSame(null,                                          $burialFormView->burialPlaceOwnerPlaceOfBirth);
        $this->assertSame(null,                                          $burialFormView->burialPlaceOwnerPassportSeries);
        $this->assertSame(null,                                          $burialFormView->burialPlaceOwnerPassportNumber);
        $this->assertSame(null,                                          $burialFormView->burialPlaceOwnerPassportIssuedAt);
        $this->assertSame(null,                                          $burialFormView->burialPlaceOwnerPassportIssuedBy);
        $this->assertSame(null,                                          $burialFormView->burialPlaceOwnerPassportDivisionCode);
        $this->assertSame(null,                                          $burialFormView->funeralCompanyId);
        $this->assertSame(null,                                          $burialFormView->burialChainId);
        $this->assertSame('GS005',                                       $burialFormView->burialPlaceId);
        $this->assertSame(GraveSite::CLASS_SHORTCUT,                     $burialFormView->burialPlaceType);
        $this->assertSame('CB004',                                       $burialFormView->burialPlaceGraveSiteCemeteryBlockId);
        $this->assertSame('мусульманский',                               $burialFormView->burialPlaceGraveSiteCemeteryBlockName);
        $this->assertSame(3,                                             $burialFormView->burialPlaceGraveSiteRowInBlock);
        $this->assertSame(null,                                          $burialFormView->burialPlaceGraveSitePositionInRow);
        $this->assertSame(null,                                          $burialFormView->burialPlaceGraveSiteSize);
        $this->assertSame(null,                                          $burialFormView->burialPlaceGraveSiteGeoPositionLatitude);
        $this->assertSame(null,                                          $burialFormView->burialPlaceGraveSiteGeoPositionLongitude);
        $this->assertSame(null,                                          $burialFormView->burialPlaceGraveSiteGeoPositionError);
        $this->assertSame(null,                                          $burialFormView->burialPlaceColumbariumNicheColumbariumId);
        $this->assertSame(null,                                          $burialFormView->burialPlaceColumbariumNicheColumbariumName);
        $this->assertSame(null,                                          $burialFormView->burialPlaceColumbariumNicheRowInColumbarium);
        $this->assertSame(null,                                          $burialFormView->burialPlaceColumbariumNicheNumber);
        $this->assertSame(null,                                          $burialFormView->burialPlaceColumbariumNicheGeoPositionLatitude);
        $this->assertSame(null,                                          $burialFormView->burialPlaceColumbariumNicheGeoPositionLongitude);
        $this->assertSame(null,                                          $burialFormView->burialPlaceColumbariumNicheGeoPositionError);
        $this->assertSame(null,                                          $burialFormView->burialPlaceMemorialTreeNumber);
        $this->assertSame(null,                                          $burialFormView->burialPlaceMemorialTreeGeoPositionLatitude);
        $this->assertSame(null,                                          $burialFormView->burialPlaceMemorialTreeGeoPositionLongitude);
        $this->assertSame(null,                                          $burialFormView->burialPlaceMemorialTreeGeoPositionError);
        $this->assertSame(null,                                          $burialFormView->burialContainerType);
        $this->assertSame(null,                                          $burialFormView->burialContainerCoffinSize);
        $this->assertSame(null,                                          $burialFormView->burialContainerCoffinShape);
        $this->assertSame(null,                                          $burialFormView->burialContainerCoffinIsNonStandard);
        $this->assertSame(null,                                          $burialFormView->buriedAt);
        $this->assertValidUpdateAtValue($burialFormView->updatedAt);
    }

    private function assertValidUpdateAtValue(string $updatedAt): void
    {
        $this->assertTrue(
            new \DateTimeImmutable() >= \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $updatedAt)
        );
    }

    private function expectExceptionForNotFoundBurialById(string $burialId): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(\sprintf('Захоронение с ID "%s" не найдено.', $burialId));
    }
}
