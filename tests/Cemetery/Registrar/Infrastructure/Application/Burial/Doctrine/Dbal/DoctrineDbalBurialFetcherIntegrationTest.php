<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Application\Burial\Doctrine\Dbal;

use Cemetery\Registrar\Application\Burial\BurialFetcher;
use Cemetery\Registrar\Application\Burial\BurialFormView;
use Cemetery\Registrar\Application\Burial\BurialViewList;
use Cemetery\Registrar\Application\Burial\BurialViewListItem;
use Cemetery\Registrar\Domain\Burial\BurialCollection;
use Cemetery\Registrar\Domain\Burial\BurialType;
use Cemetery\Registrar\Domain\BurialContainer\Coffin;
use Cemetery\Registrar\Domain\BurialContainer\CoffinShape;
use Cemetery\Registrar\Domain\BurialContainer\Urn;
use Cemetery\Registrar\Domain\BurialPlace\ColumbariumNiche\ColumbariumCollection;
use Cemetery\Registrar\Domain\BurialPlace\ColumbariumNiche\ColumbariumNiche;
use Cemetery\Registrar\Domain\BurialPlace\ColumbariumNiche\ColumbariumNicheCollection;
use Cemetery\Registrar\Domain\BurialPlace\GraveSite\CemeteryBlockCollection;
use Cemetery\Registrar\Domain\BurialPlace\GraveSite\GraveSite;
use Cemetery\Registrar\Domain\BurialPlace\GraveSite\GraveSiteCollection;
use Cemetery\Registrar\Domain\BurialPlace\MemorialTree\MemorialTree;
use Cemetery\Registrar\Domain\BurialPlace\MemorialTree\MemorialTreeCollection;
use Cemetery\Registrar\Domain\Deceased\DeceasedCollection;
use Cemetery\Registrar\Domain\NaturalPerson\NaturalPerson;
use Cemetery\Registrar\Domain\NaturalPerson\NaturalPersonCollection;
use Cemetery\Registrar\Domain\Organization\JuristicPerson\JuristicPerson;
use Cemetery\Registrar\Domain\Organization\JuristicPerson\JuristicPersonCollection;
use Cemetery\Registrar\Domain\Organization\SoleProprietor\SoleProprietor;
use Cemetery\Registrar\Domain\Organization\SoleProprietor\SoleProprietorCollection;
use Cemetery\Registrar\Infrastructure\Application\Burial\Doctrine\Dbal\DoctrineDbalBurialFetcher;
use Cemetery\Registrar\Infrastructure\Domain\Burial\Doctrine\Orm\DoctrineOrmBurialRepository;
use Cemetery\Registrar\Infrastructure\Domain\BurialPlace\ColumbariumNiche\Doctrine\Orm\DoctrineOrmColumbariumNicheRepository;
use Cemetery\Registrar\Infrastructure\Domain\BurialPlace\ColumbariumNiche\Doctrine\Orm\DoctrineOrmColumbariumRepository;
use Cemetery\Registrar\Infrastructure\Domain\BurialPlace\GraveSite\Doctrine\Orm\DoctrineOrmCemeteryBlockRepository;
use Cemetery\Registrar\Infrastructure\Domain\BurialPlace\GraveSite\Doctrine\Orm\DoctrineOrmGraveSiteRepository;
use Cemetery\Registrar\Infrastructure\Domain\BurialPlace\MemorialTree\Doctrine\Orm\DoctrineOrmMemorialTreeRepository;
use Cemetery\Registrar\Infrastructure\Domain\Deceased\Doctrine\Orm\DoctrineOrmDeceasedRepository;
use Cemetery\Registrar\Infrastructure\Domain\NaturalPerson\Doctrine\Orm\DoctrineOrmNaturalPersonRepository;
use Cemetery\Registrar\Infrastructure\Domain\Organization\JuristicPerson\Doctrine\Orm\DoctrineOrmJuristicPersonRepository;
use Cemetery\Registrar\Infrastructure\Domain\Organization\SoleProprietor\Doctrine\Orm\DoctrineOrmSoleProprietorRepository;
use Cemetery\Tests\Registrar\Domain\Burial\BurialProvider;
use Cemetery\Tests\Registrar\Domain\BurialPlace\ColumbariumNiche\ColumbariumNicheProvider;
use Cemetery\Tests\Registrar\Domain\BurialPlace\ColumbariumNiche\ColumbariumProvider;
use Cemetery\Tests\Registrar\Domain\BurialPlace\GraveSite\CemeteryBlockProvider;
use Cemetery\Tests\Registrar\Domain\BurialPlace\GraveSite\GraveSiteProvider;
use Cemetery\Tests\Registrar\Domain\BurialPlace\MemorialTree\MemorialTreeProvider;
use Cemetery\Tests\Registrar\Domain\Deceased\DeceasedProvider;
use Cemetery\Tests\Registrar\Domain\NaturalPerson\NaturalPersonProvider;
use Cemetery\Tests\Registrar\Domain\Organization\JuristicPerson\JuristicPersonProvider;
use Cemetery\Tests\Registrar\Domain\Organization\SoleProprietor\SoleProprietorProvider;
use Cemetery\Tests\Registrar\Infrastructure\Application\FetcherIntegrationTest;

/**
 * @group database
 *
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class DoctrineDbalBurialFetcherIntegrationTest extends FetcherIntegrationTest
{
    private const DEFAULT_PAGE_SIZE = 20;

    private BurialFetcher $fetcher;

    public function setUp(): void
    {
        parent::setUp();

        $this->fillDatabase();
        $this->fetcher = new DoctrineDbalBurialFetcher($this->connection);
    }

    public function testItHasValidPageSizeConstant(): void
    {
        $this->assertSame(self::DEFAULT_PAGE_SIZE, BurialFetcher::DEFAULT_PAGE_SIZE);
    }

    public function testItReturnsBurialFormViewById(): void
    {
        $this->testItReturnsBurialViewFormForB001();
        $this->testItReturnsBurialViewFormForB002();
        $this->testItReturnsBurialViewFormForB003();
        $this->testItReturnsBurialViewFormForB004();
        $this->testItReturnsBurialViewFormForB005();
        $this->testItReturnsBurialViewFormForB006();
    }

    public function testItFailsToReturnBurialFormViewByUnknownId(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Захоронение с ID "unknown_id" не найдено.');
        $this->fetcher->getFormViewById('unknown_id');
    }

    public function testItReturnsBurialViewListItemsByPage(): void
    {
        $customPageSize = 4;

        // First page
        $burialViewListForFirstPage = $this->fetcher->findAll(1, null, $customPageSize);
        $this->assertInstanceOf(BurialViewList::class, $burialViewListForFirstPage);
        $this->assertSame(4, $burialViewListForFirstPage->totalCount);
        $this->assertCount(4, $burialViewListForFirstPage->burialViewListItems);
        $this->assertContainsOnlyInstancesOf(BurialViewListItem::class, $burialViewListForFirstPage->burialViewListItems);
        $this->assertItemForFirstPageEqualsB001($burialViewListForFirstPage->burialViewListItems[0]);
        $this->assertItemForFirstPageEqualsB002($burialViewListForFirstPage->burialViewListItems[1]);
        $this->assertItemForFirstPageEqualsB003($burialViewListForFirstPage->burialViewListItems[2]);
        $this->assertItemForFirstPageEqualsB004($burialViewListForFirstPage->burialViewListItems[3]);

        // Second page
        $burialViewListForSecondPage = $this->fetcher->findAll(2, null, $customPageSize);
        $this->assertInstanceOf(BurialViewList::class, $burialViewListForSecondPage);
        $this->assertSame(2, $burialViewListForSecondPage->totalCount);
        $this->assertCount(2, $burialViewListForSecondPage->burialViewListItems);
        $this->assertContainsOnlyInstancesOf(BurialViewListItem::class, $burialViewListForSecondPage->burialViewListItems);
        $this->assertItemForSecondPageEqualsB005($burialViewListForSecondPage->burialViewListItems[0]);
        $this->assertItemForSecondPageEqualsB006($burialViewListForSecondPage->burialViewListItems[1]);

        // Third page
        $burialViewListForThirdPage = $this->fetcher->findAll(3, null, $customPageSize);
        $this->assertInstanceOf(BurialViewList::class, $burialViewListForThirdPage);
        $this->assertSame(0, $burialViewListForThirdPage->totalCount);
        $this->assertEmpty($burialViewListForThirdPage->burialViewListItems);

        // All at once
        $burialViewListForDefaultPageSize = $this->fetcher->findAll(1);
        $this->assertInstanceOf(BurialViewList::class, $burialViewListForDefaultPageSize);
        $this->assertSame(6, $burialViewListForDefaultPageSize->totalCount);
        $this->assertCount(6, $burialViewListForDefaultPageSize->burialViewListItems);
        $this->assertContainsOnlyInstancesOf(BurialViewListItem::class, $burialViewListForDefaultPageSize->burialViewListItems);
    }

    public function testItReturnsBurialViewListItemsByPageAndTerm(): void
    {
        $customPageSize = 4;

        $burialViewList = $this->fetcher->findAll(1, 'Ждан', $customPageSize);
        $this->assertInstanceOf(BurialViewList::class, $burialViewList);
        $this->assertSame(3, $burialViewList->totalCount);
        $this->assertIsArray($burialViewList->burialViewListItems);
        $this->assertCount(3, $burialViewList->burialViewListItems);
        $this->assertContainsOnlyInstancesOf(BurialViewListItem::class, $burialViewList->burialViewListItems);

        $burialViewList = $this->fetcher->findAll(1, 'Новос', $customPageSize);
        $this->assertInstanceOf(BurialViewList::class, $burialViewList);
        $this->assertSame(3, $burialViewList->totalCount);
        $this->assertIsArray($burialViewList->burialViewListItems);
        $this->assertCount(3, $burialViewList->burialViewListItems);
        $this->assertContainsOnlyInstancesOf(BurialViewListItem::class, $burialViewList->burialViewListItems);

        $burialViewList = $this->fetcher->findAll(1, '00000000', $customPageSize);
        $this->assertInstanceOf(BurialViewList::class, $burialViewList);
        $this->assertSame(4, $burialViewList->totalCount);
        $this->assertIsArray($burialViewList->burialViewListItems);
        $this->assertCount(4, $burialViewList->burialViewListItems);
        $this->assertContainsOnlyInstancesOf(BurialViewListItem::class, $burialViewList->burialViewListItems);
        $burialViewList = $this->fetcher->findAll(2, '00000000', $customPageSize);
        $this->assertInstanceOf(BurialViewList::class, $burialViewList);
        $this->assertSame(2, $burialViewList->totalCount);
        $this->assertIsArray($burialViewList->burialViewListItems);
        $this->assertCount(2, $burialViewList->burialViewListItems);
        $this->assertContainsOnlyInstancesOf(BurialViewListItem::class, $burialViewList->burialViewListItems);
    }

    private function fillDatabase(): void
    {
        $this->fillBurialTable();
        $this->fillDeceasedTable();
        $this->fillNaturalPersonTable();
        $this->fillSoleProprietorTable();
        $this->fillJuristicPersonTable();
        $this->fillColumbariumTable();
        $this->fillColumbariumNicheTable();
        $this->fillCemeteryBlockTable();
        $this->fillGraveSiteTable();
        $this->fillMemorialTreeTable();
    }

    private function fillBurialTable(): void
    {
        (new DoctrineOrmBurialRepository($this->entityManager))
            ->saveAll(new BurialCollection([
                BurialProvider::getBurialA(),
                BurialProvider::getBurialB(),
                BurialProvider::getBurialC(),
                BurialProvider::getBurialD(),
                BurialProvider::getBurialE(),
                BurialProvider::getBurialF(),
            ]));
    }

    private function fillDeceasedTable(): void
    {
        (new DoctrineOrmDeceasedRepository($this->entityManager))
            ->saveAll(new DeceasedCollection([
                DeceasedProvider::getDeceasedA(),
                DeceasedProvider::getDeceasedB(),
                DeceasedProvider::getDeceasedC(),
                DeceasedProvider::getDeceasedD(),
                DeceasedProvider::getDeceasedE(),
                DeceasedProvider::getDeceasedF(),
            ]));
    }

    private function fillNaturalPersonTable(): void
    {
        (new DoctrineOrmNaturalPersonRepository($this->entityManager))
            ->saveAll(new NaturalPersonCollection([
                NaturalPersonProvider::getNaturalPersonA(),
                NaturalPersonProvider::getNaturalPersonB(),
                NaturalPersonProvider::getNaturalPersonC(),
                NaturalPersonProvider::getNaturalPersonD(),
                NaturalPersonProvider::getNaturalPersonE(),
                NaturalPersonProvider::getNaturalPersonF(),
                NaturalPersonProvider::getNaturalPersonG(),
                NaturalPersonProvider::getNaturalPersonH(),
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

    private function fillColumbariumTable(): void
    {
        (new DoctrineOrmColumbariumRepository($this->entityManager))
            ->saveAll(new ColumbariumCollection([
                ColumbariumProvider::getColumbariumA(),
                ColumbariumProvider::getColumbariumB(),
                ColumbariumProvider::getColumbariumC(),
                ColumbariumProvider::getColumbariumD(),
            ]));
    }

    private function fillColumbariumNicheTable(): void
    {
        (new DoctrineOrmColumbariumNicheRepository($this->entityManager))
            ->saveAll(new ColumbariumNicheCollection([
                ColumbariumNicheProvider::getColumbariumNicheA(),
                ColumbariumNicheProvider::getColumbariumNicheB(),
                ColumbariumNicheProvider::getColumbariumNicheC(),
                ColumbariumNicheProvider::getColumbariumNicheD(),
            ]));
    }

    private function fillCemeteryBlockTable(): void
    {
        (new DoctrineOrmCemeteryBlockRepository($this->entityManager))
            ->saveAll(new CemeteryBlockCollection([
                CemeteryBlockProvider::getCemeteryBlockA(),
                CemeteryBlockProvider::getCemeteryBlockB(),
                CemeteryBlockProvider::getCemeteryBlockC(),
                CemeteryBlockProvider::getCemeteryBlockD(),
            ]));
    }

    private function fillGraveSiteTable(): void
    {
        (new DoctrineOrmGraveSiteRepository($this->entityManager))
            ->saveAll(new GraveSiteCollection([
                GraveSiteProvider::getGraveSiteA(),
                GraveSiteProvider::getGraveSiteB(),
                GraveSiteProvider::getGraveSiteC(),
                GraveSiteProvider::getGraveSiteD(),
            ]));
    }

    private function fillMemorialTreeTable(): void
    {
        (new DoctrineOrmMemorialTreeRepository($this->entityManager))
            ->saveAll(new MemorialTreeCollection([
                MemorialTreeProvider::getMemorialTreeA(),
                MemorialTreeProvider::getMemorialTreeB(),
                MemorialTreeProvider::getMemorialTreeC(),
                MemorialTreeProvider::getMemorialTreeD(),
            ]));
    }

    private function assertItemForFirstPageEqualsB001(BurialViewListItem $item): void
    {
        $this->assertSame('B001',                              $item->id);
        $this->assertSame('000000001',                         $item->code);
        $this->assertSame('Егоров Абрам Даниилович',           $item->deceasedNaturalPersonFullName);
        $this->assertSame(null,                                $item->deceasedNaturalPersonBornAt);
        $this->assertSame('2021-12-01',                        $item->deceasedDiedAt);
        $this->assertSame(null,                                $item->deceasedAge);
        $this->assertSame('2021-12-03 13:10:00',               $item->buriedAt);
        $this->assertSame(ColumbariumNiche::CLASS_SHORTCUT,    $item->burialPlaceType);
        $this->assertSame(null,                                $item->burialPlaceGraveSiteCemeteryBlockName);
        $this->assertSame(null,                                $item->burialPlaceGraveSiteRowInBlock);
        $this->assertSame(null,                                $item->burialPlaceGraveSitePositionInRow);
        $this->assertSame('южный',                  $item->burialPlaceColumbariumNicheColumbariumName);
        $this->assertSame(2,                                   $item->burialPlaceColumbariumNicheRowInColumbarium);
        $this->assertSame('002',                               $item->burialPlaceColumbariumNicheNumber);
        $this->assertSame(null,                                $item->burialPlaceMemorialTreeNumber);
        $this->assertSame(NaturalPerson::CLASS_SHORTCUT,       $item->customerType);
        $this->assertSame('Жданова Инга Григорьевна',          $item->customerNaturalPersonFullName);
        $this->assertSame('Новосибирск, ул. Ленина, д. 1',     $item->customerNaturalPersonAddress);
        $this->assertSame('+7-913-111-22-33',                  $item->customerNaturalPersonPhone);
        $this->assertSame(null,                                $item->customerSoleProprietorName);
        $this->assertSame(null,                                $item->customerSoleProprietorRegistrationAddress);
        $this->assertSame(null,                                $item->customerSoleProprietorActualLocationAddress);
        $this->assertSame(null,                                $item->customerSoleProprietorPhone);
        $this->assertSame(null,                                $item->customerJuristicPersonName);
        $this->assertSame(null,                                $item->customerJuristicPersonLegalAddress);
        $this->assertSame(null,                                $item->customerJuristicPersonPostalAddress);
        $this->assertSame(null,                                $item->customerJuristicPersonPhone);
    }

    private function assertItemForFirstPageEqualsB002(BurialViewListItem $item): void
    {
        $this->assertSame('B002',                          $item->id);
        $this->assertSame('000000002',                     $item->code);
        $this->assertSame('Устинов Арсений Максович',      $item->deceasedNaturalPersonFullName);
        $this->assertSame('1918-12-30',                    $item->deceasedNaturalPersonBornAt);
        $this->assertSame('2001-02-11',                    $item->deceasedDiedAt);
        $this->assertSame(82,                              $item->deceasedAge);
        $this->assertSame(null,                            $item->buriedAt);
        $this->assertSame(GraveSite::CLASS_SHORTCUT,       $item->burialPlaceType);
        $this->assertSame('общий Б',               $item->burialPlaceGraveSiteCemeteryBlockName);
        $this->assertSame(1,                               $item->burialPlaceGraveSiteRowInBlock);
        $this->assertSame(null,                            $item->burialPlaceGraveSitePositionInRow);
        $this->assertSame(null,                            $item->burialPlaceColumbariumNicheColumbariumName);
        $this->assertSame(null,                            $item->burialPlaceColumbariumNicheRowInColumbarium);
        $this->assertSame(null,                            $item->burialPlaceColumbariumNicheNumber);
        $this->assertSame(null,                            $item->burialPlaceMemorialTreeNumber);
        $this->assertSame(NaturalPerson::CLASS_SHORTCUT,   $item->customerType);
        $this->assertSame('Жданова Инга Григорьевна',      $item->customerNaturalPersonFullName);
        $this->assertSame('Новосибирск, ул. Ленина, д. 1', $item->customerNaturalPersonAddress);
        $this->assertSame('+7-913-111-22-33',              $item->customerNaturalPersonPhone);
        $this->assertSame(null,                            $item->customerSoleProprietorName);
        $this->assertSame(null,                            $item->customerSoleProprietorRegistrationAddress);
        $this->assertSame(null,                            $item->customerSoleProprietorActualLocationAddress);
        $this->assertSame(null,                            $item->customerSoleProprietorPhone);
        $this->assertSame(null,                            $item->customerJuristicPersonName);
        $this->assertSame(null,                            $item->customerJuristicPersonLegalAddress);
        $this->assertSame(null,                            $item->customerJuristicPersonPostalAddress);
        $this->assertSame(null,                            $item->customerJuristicPersonPhone);
    }

    private function assertItemForFirstPageEqualsB003(BurialViewListItem $item): void
    {
        $this->assertSame('B003',                        $item->id);
        $this->assertSame('000000003',                   $item->code);
        $this->assertSame('Шилов Александр Михаилович',  $item->deceasedNaturalPersonFullName);
        $this->assertSame('1969-05-20',                  $item->deceasedNaturalPersonBornAt);
        $this->assertSame('2011-05-13',                  $item->deceasedDiedAt);
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

    private function assertItemForFirstPageEqualsB004(BurialViewListItem $item): void
    {
        $this->assertSame('B004',                                        $item->id);
        $this->assertSame('000000004',                                   $item->code);
        $this->assertSame('Жданова Инга Григорьевна',                    $item->deceasedNaturalPersonFullName);
        $this->assertSame('1979-02-12',                                  $item->deceasedNaturalPersonBornAt);
        $this->assertSame('2022-03-10',                                  $item->deceasedDiedAt);
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

    private function assertItemForSecondPageEqualsB005(BurialViewListItem $item): void
    {
        $this->assertSame('B005',                         $item->id);
        $this->assertSame('000000005',                    $item->code);
        $this->assertSame('Соколов Герман Маркович',      $item->deceasedNaturalPersonFullName);
        $this->assertSame(null,                           $item->deceasedNaturalPersonBornAt);
        $this->assertSame('2010-01-26',                   $item->deceasedDiedAt);
        $this->assertSame(null,                           $item->deceasedAge);
        $this->assertSame('2010-01-28 12:55:00',          $item->buriedAt);
        $this->assertSame(null,                           $item->burialPlaceType);
        $this->assertSame(null,                           $item->burialPlaceGraveSiteCemeteryBlockName);
        $this->assertSame(null,                           $item->burialPlaceGraveSiteRowInBlock);
        $this->assertSame(null,                           $item->burialPlaceGraveSitePositionInRow);
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
        $this->assertSame(null,                           $item->customerSoleProprietorActualLocationAddress);
        $this->assertSame(null,                           $item->customerSoleProprietorPhone);
        $this->assertSame(null,                           $item->customerJuristicPersonName);
        $this->assertSame(null,                           $item->customerJuristicPersonLegalAddress);
        $this->assertSame(null,                           $item->customerJuristicPersonPostalAddress);
        $this->assertSame(null,                           $item->customerJuristicPersonPhone);
    }

    private function assertItemForSecondPageEqualsB006(BurialViewListItem $item): void
    {
        $this->assertSame('B006',                        $item->id);
        $this->assertSame('000000006',                   $item->code);
        $this->assertSame('Гришина Устинья Ярославовна', $item->deceasedNaturalPersonFullName);
        $this->assertSame(null,                          $item->deceasedNaturalPersonBornAt);
        $this->assertSame('2021-12-03',                  $item->deceasedDiedAt);
        $this->assertSame(null,                          $item->deceasedAge);
        $this->assertSame(null,                          $item->buriedAt);
        $this->assertSame(null,                          $item->burialPlaceType);
        $this->assertSame(null,                          $item->burialPlaceGraveSiteCemeteryBlockName);
        $this->assertSame(null,                          $item->burialPlaceGraveSiteRowInBlock);
        $this->assertSame(null,                          $item->burialPlaceGraveSitePositionInRow);
        $this->assertSame(null,                          $item->burialPlaceColumbariumNicheColumbariumName);
        $this->assertSame(null,                          $item->burialPlaceColumbariumNicheRowInColumbarium);
        $this->assertSame(null,                          $item->burialPlaceColumbariumNicheNumber);
        $this->assertSame(null,                          $item->burialPlaceMemorialTreeNumber);
        $this->assertSame(NaturalPerson::CLASS_SHORTCUT, $item->customerType);
        $this->assertSame('Громов Никифор Рудольфович',  $item->customerNaturalPersonFullName);
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

    private function testItReturnsBurialViewFormForB001(): void
    {
        $burialFormView = $this->fetcher->getFormViewById('B001');
        $this->assertInstanceOf(BurialFormView::class, $burialFormView);
        $this->assertSame('B001',                                      $burialFormView->id);
        $this->assertSame('000000001',                                 $burialFormView->code);
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
        $this->assertSame('+7-913-111-22-33',                          $burialFormView->customerNaturalPersonPhone);
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
        $this->assertSame(null,                                        $burialFormView->funeralCompanyType);
        $this->assertSame(null,                                        $burialFormView->funeralCompanySoleProprietorName);
        $this->assertSame(null,                                        $burialFormView->funeralCompanySoleProprietorInn);
        $this->assertSame(null,                                        $burialFormView->funeralCompanySoleProprietorOgrnip);
        $this->assertSame(null,                                        $burialFormView->funeralCompanySoleProprietorOkpo);
        $this->assertSame(null,                                        $burialFormView->funeralCompanySoleProprietorOkved);
        $this->assertSame(null,                                        $burialFormView->funeralCompanySoleProprietorRegistrationAddress);
        $this->assertSame(null,                                        $burialFormView->funeralCompanySoleProprietorActualLocationAddress);
        $this->assertSame(null,                                        $burialFormView->funeralCompanySoleProprietorBankDetailsBankName);
        $this->assertSame(null,                                        $burialFormView->funeralCompanySoleProprietorBankDetailsBik);
        $this->assertSame(null,                                        $burialFormView->funeralCompanySoleProprietorBankDetailsCorrespondentAccount);
        $this->assertSame(null,                                        $burialFormView->funeralCompanySoleProprietorBankDetailsCurrentAccount);
        $this->assertSame(null,                                        $burialFormView->funeralCompanySoleProprietorPhone);
        $this->assertSame(null,                                        $burialFormView->funeralCompanySoleProprietorPhoneAdditional);
        $this->assertSame(null,                                        $burialFormView->funeralCompanySoleProprietorFax);
        $this->assertSame(null,                                        $burialFormView->funeralCompanySoleProprietorEmail);
        $this->assertSame(null,                                        $burialFormView->funeralCompanySoleProprietorWebsite);
        $this->assertSame(null,                                        $burialFormView->funeralCompanyJuristicPersonName);
        $this->assertSame(null,                                        $burialFormView->funeralCompanyJuristicPersonInn);
        $this->assertSame(null,                                        $burialFormView->funeralCompanyJuristicPersonKpp);
        $this->assertSame(null,                                        $burialFormView->funeralCompanyJuristicPersonOgrn);
        $this->assertSame(null,                                        $burialFormView->funeralCompanyJuristicPersonOkpo);
        $this->assertSame(null,                                        $burialFormView->funeralCompanyJuristicPersonOkved);
        $this->assertSame(null,                                        $burialFormView->funeralCompanyJuristicPersonLegalAddress);
        $this->assertSame(null,                                        $burialFormView->funeralCompanyJuristicPersonPostalAddress);
        $this->assertSame(null,                                        $burialFormView->funeralCompanyJuristicPersonBankDetailsBankName);
        $this->assertSame(null,                                        $burialFormView->funeralCompanyJuristicPersonBankDetailsBik);
        $this->assertSame(null,                                        $burialFormView->funeralCompanyJuristicPersonBankDetailsCorrespondentAccount);
        $this->assertSame(null,                                        $burialFormView->funeralCompanyJuristicPersonBankDetailsCurrentAccount);
        $this->assertSame(null,                                        $burialFormView->funeralCompanyJuristicPersonPhone);
        $this->assertSame(null,                                        $burialFormView->funeralCompanyJuristicPersonPhoneAdditional);
        $this->assertSame(null,                                        $burialFormView->funeralCompanyJuristicPersonFax);
        $this->assertSame(null,                                        $burialFormView->funeralCompanyJuristicPersonGeneralDirector);
        $this->assertSame(null,                                        $burialFormView->funeralCompanyJuristicPersonEmail);
        $this->assertSame(null,                                        $burialFormView->funeralCompanyJuristicPersonWebsite);
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
        $this->assertSame('южный',                          $burialFormView->burialPlaceColumbariumNicheColumbariumName);
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

    private function testItReturnsBurialViewFormForB002(): void
    {
        $burialFormView = $this->fetcher->getFormViewById('B002');
        $this->assertInstanceOf(BurialFormView::class, $burialFormView);
        $this->assertSame('B002',                                      $burialFormView->id);
        $this->assertSame('000000002',                                 $burialFormView->code);
        $this->assertSame(BurialType::COFFIN_IN_GRAVE_SITE,            $burialFormView->type);
        $this->assertSame('D002',                                      $burialFormView->deceasedId);
        $this->assertSame('NP002',                                     $burialFormView->deceasedNaturalPersonId);
        $this->assertSame('Устинов Арсений Максович',                  $burialFormView->deceasedNaturalPersonFullName);
        $this->assertSame('1918-12-30',                                $burialFormView->deceasedNaturalPersonBornAt);
        $this->assertSame('2001-02-11',                                $burialFormView->deceasedDiedAt);
        $this->assertSame(82,                                          $burialFormView->deceasedAge);
        $this->assertSame('DC001',                                     $burialFormView->deceasedDeathCertificateId);
        $this->assertSame('Болезнь сердечно-легочная хроническая',     $burialFormView->deceasedCauseOfDeath);
        $this->assertSame('NP005',                                     $burialFormView->customerId);
        $this->assertSame(NaturalPerson::CLASS_SHORTCUT,               $burialFormView->customerType);
        $this->assertSame('Жданова Инга Григорьевна',                  $burialFormView->customerNaturalPersonFullName);
        $this->assertSame('+7-913-111-22-33',                          $burialFormView->customerNaturalPersonPhone);
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
        $this->assertSame(null,                                        $burialFormView->funeralCompanyType);
        $this->assertSame(null,                                        $burialFormView->funeralCompanySoleProprietorName);
        $this->assertSame(null,                                        $burialFormView->funeralCompanySoleProprietorInn);
        $this->assertSame(null,                                        $burialFormView->funeralCompanySoleProprietorOgrnip);
        $this->assertSame(null,                                        $burialFormView->funeralCompanySoleProprietorOkpo);
        $this->assertSame(null,                                        $burialFormView->funeralCompanySoleProprietorOkved);
        $this->assertSame(null,                                        $burialFormView->funeralCompanySoleProprietorRegistrationAddress);
        $this->assertSame(null,                                        $burialFormView->funeralCompanySoleProprietorActualLocationAddress);
        $this->assertSame(null,                                        $burialFormView->funeralCompanySoleProprietorBankDetailsBankName);
        $this->assertSame(null,                                        $burialFormView->funeralCompanySoleProprietorBankDetailsBik);
        $this->assertSame(null,                                        $burialFormView->funeralCompanySoleProprietorBankDetailsCorrespondentAccount);
        $this->assertSame(null,                                        $burialFormView->funeralCompanySoleProprietorBankDetailsCurrentAccount);
        $this->assertSame(null,                                        $burialFormView->funeralCompanySoleProprietorPhone);
        $this->assertSame(null,                                        $burialFormView->funeralCompanySoleProprietorPhoneAdditional);
        $this->assertSame(null,                                        $burialFormView->funeralCompanySoleProprietorFax);
        $this->assertSame(null,                                        $burialFormView->funeralCompanySoleProprietorEmail);
        $this->assertSame(null,                                        $burialFormView->funeralCompanySoleProprietorWebsite);
        $this->assertSame(null,                                        $burialFormView->funeralCompanyJuristicPersonName);
        $this->assertSame(null,                                        $burialFormView->funeralCompanyJuristicPersonInn);
        $this->assertSame(null,                                        $burialFormView->funeralCompanyJuristicPersonKpp);
        $this->assertSame(null,                                        $burialFormView->funeralCompanyJuristicPersonOgrn);
        $this->assertSame(null,                                        $burialFormView->funeralCompanyJuristicPersonOkpo);
        $this->assertSame(null,                                        $burialFormView->funeralCompanyJuristicPersonOkved);
        $this->assertSame(null,                                        $burialFormView->funeralCompanyJuristicPersonLegalAddress);
        $this->assertSame(null,                                        $burialFormView->funeralCompanyJuristicPersonPostalAddress);
        $this->assertSame(null,                                        $burialFormView->funeralCompanyJuristicPersonBankDetailsBankName);
        $this->assertSame(null,                                        $burialFormView->funeralCompanyJuristicPersonBankDetailsBik);
        $this->assertSame(null,                                        $burialFormView->funeralCompanyJuristicPersonBankDetailsCorrespondentAccount);
        $this->assertSame(null,                                        $burialFormView->funeralCompanyJuristicPersonBankDetailsCurrentAccount);
        $this->assertSame(null,                                        $burialFormView->funeralCompanyJuristicPersonPhone);
        $this->assertSame(null,                                        $burialFormView->funeralCompanyJuristicPersonPhoneAdditional);
        $this->assertSame(null,                                        $burialFormView->funeralCompanyJuristicPersonFax);
        $this->assertSame(null,                                        $burialFormView->funeralCompanyJuristicPersonGeneralDirector);
        $this->assertSame(null,                                        $burialFormView->funeralCompanyJuristicPersonEmail);
        $this->assertSame(null,                                        $burialFormView->funeralCompanyJuristicPersonWebsite);
        $this->assertSame(null,                                        $burialFormView->burialChainId);
        $this->assertSame('GS003',                                     $burialFormView->burialPlaceId);
        $this->assertSame(GraveSite::CLASS_SHORTCUT,                   $burialFormView->burialPlaceType);
        $this->assertSame('CB003',                                     $burialFormView->burialPlaceGraveSiteCemeteryBlockId);
        $this->assertSame('общий Б',                           $burialFormView->burialPlaceGraveSiteCemeteryBlockName);
        $this->assertSame(1,                                           $burialFormView->burialPlaceGraveSiteRowInBlock);
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

    private function testItReturnsBurialViewFormForB003(): void
    {
        $burialFormView = $this->fetcher->getFormViewById('B003');
        $this->assertInstanceOf(BurialFormView::class, $burialFormView);
        $this->assertSame('B003',                                        $burialFormView->id);
        $this->assertSame('000000003',                                   $burialFormView->code);
        $this->assertSame(BurialType::ASHES_UNDER_MEMORIAL_TREE,         $burialFormView->type);
        $this->assertSame('D003',                                        $burialFormView->deceasedId);
        $this->assertSame('NP003',                                       $burialFormView->deceasedNaturalPersonId);
        $this->assertSame('Шилов Александр Михаилович',                  $burialFormView->deceasedNaturalPersonFullName);
        $this->assertSame('1969-05-20',                                  $burialFormView->deceasedNaturalPersonBornAt);
        $this->assertSame('2011-05-13',                                  $burialFormView->deceasedDiedAt);
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
        $this->assertSame('JP001',                                       $burialFormView->funeralCompanyId);
        $this->assertSame(JuristicPerson::CLASS_SHORTCUT,                $burialFormView->funeralCompanyType);
        $this->assertSame(null,                                          $burialFormView->funeralCompanySoleProprietorName);
        $this->assertSame(null,                                          $burialFormView->funeralCompanySoleProprietorInn);
        $this->assertSame(null,                                          $burialFormView->funeralCompanySoleProprietorOgrnip);
        $this->assertSame(null,                                          $burialFormView->funeralCompanySoleProprietorOkpo);
        $this->assertSame(null,                                          $burialFormView->funeralCompanySoleProprietorOkved);
        $this->assertSame(null,                                          $burialFormView->funeralCompanySoleProprietorRegistrationAddress);
        $this->assertSame(null,                                          $burialFormView->funeralCompanySoleProprietorActualLocationAddress);
        $this->assertSame(null,                                          $burialFormView->funeralCompanySoleProprietorBankDetailsBankName);
        $this->assertSame(null,                                          $burialFormView->funeralCompanySoleProprietorBankDetailsBik);
        $this->assertSame(null,                                          $burialFormView->funeralCompanySoleProprietorBankDetailsCorrespondentAccount);
        $this->assertSame(null,                                          $burialFormView->funeralCompanySoleProprietorBankDetailsCurrentAccount);
        $this->assertSame(null,                                          $burialFormView->funeralCompanySoleProprietorPhone);
        $this->assertSame(null,                                          $burialFormView->funeralCompanySoleProprietorPhoneAdditional);
        $this->assertSame(null,                                          $burialFormView->funeralCompanySoleProprietorFax);
        $this->assertSame(null,                                          $burialFormView->funeralCompanySoleProprietorEmail);
        $this->assertSame(null,                                          $burialFormView->funeralCompanySoleProprietorWebsite);
        $this->assertSame('ООО "Рога и копыта"',                         $burialFormView->funeralCompanyJuristicPersonName);
        $this->assertSame(null,                                          $burialFormView->funeralCompanyJuristicPersonInn);
        $this->assertSame(null,                                          $burialFormView->funeralCompanyJuristicPersonKpp);
        $this->assertSame(null,                                          $burialFormView->funeralCompanyJuristicPersonOgrn);
        $this->assertSame(null,                                          $burialFormView->funeralCompanyJuristicPersonOkpo);
        $this->assertSame(null,                                          $burialFormView->funeralCompanyJuristicPersonOkved);
        $this->assertSame(null,                                          $burialFormView->funeralCompanyJuristicPersonLegalAddress);
        $this->assertSame('г. Кемерово, пр. Строителей, д. 5, офис 102', $burialFormView->funeralCompanyJuristicPersonPostalAddress);
        $this->assertSame(null,                                          $burialFormView->funeralCompanyJuristicPersonBankDetailsBankName);
        $this->assertSame(null,                                          $burialFormView->funeralCompanyJuristicPersonBankDetailsBik);
        $this->assertSame(null,                                          $burialFormView->funeralCompanyJuristicPersonBankDetailsCorrespondentAccount);
        $this->assertSame(null,                                          $burialFormView->funeralCompanyJuristicPersonBankDetailsCurrentAccount);
        $this->assertSame(null,                                          $burialFormView->funeralCompanyJuristicPersonPhone);
        $this->assertSame(null,                                          $burialFormView->funeralCompanyJuristicPersonPhoneAdditional);
        $this->assertSame(null,                                          $burialFormView->funeralCompanyJuristicPersonFax);
        $this->assertSame(null,                                          $burialFormView->funeralCompanyJuristicPersonGeneralDirector);
        $this->assertSame(null,                                          $burialFormView->funeralCompanyJuristicPersonEmail);
        $this->assertSame(null,                                          $burialFormView->funeralCompanyJuristicPersonWebsite);
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

    private function testItReturnsBurialViewFormForB004(): void
    {
        $burialFormView = $this->fetcher->getFormViewById('B004');
        $this->assertInstanceOf(BurialFormView::class, $burialFormView);
        $this->assertSame('B004',                                        $burialFormView->id);
        $this->assertSame('000000004',                                   $burialFormView->code);
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
        $this->assertSame('JP001',                                       $burialFormView->funeralCompanyId);
        $this->assertSame(JuristicPerson::CLASS_SHORTCUT,                $burialFormView->funeralCompanyType);
        $this->assertSame(null,                                          $burialFormView->funeralCompanySoleProprietorName);
        $this->assertSame(null,                                          $burialFormView->funeralCompanySoleProprietorInn);
        $this->assertSame(null,                                          $burialFormView->funeralCompanySoleProprietorOgrnip);
        $this->assertSame(null,                                          $burialFormView->funeralCompanySoleProprietorOkpo);
        $this->assertSame(null,                                          $burialFormView->funeralCompanySoleProprietorOkved);
        $this->assertSame(null,                                          $burialFormView->funeralCompanySoleProprietorRegistrationAddress);
        $this->assertSame(null,                                          $burialFormView->funeralCompanySoleProprietorActualLocationAddress);
        $this->assertSame(null,                                          $burialFormView->funeralCompanySoleProprietorBankDetailsBankName);
        $this->assertSame(null,                                          $burialFormView->funeralCompanySoleProprietorBankDetailsBik);
        $this->assertSame(null,                                          $burialFormView->funeralCompanySoleProprietorBankDetailsCorrespondentAccount);
        $this->assertSame(null,                                          $burialFormView->funeralCompanySoleProprietorBankDetailsCurrentAccount);
        $this->assertSame(null,                                          $burialFormView->funeralCompanySoleProprietorPhone);
        $this->assertSame(null,                                          $burialFormView->funeralCompanySoleProprietorPhoneAdditional);
        $this->assertSame(null,                                          $burialFormView->funeralCompanySoleProprietorFax);
        $this->assertSame(null,                                          $burialFormView->funeralCompanySoleProprietorEmail);
        $this->assertSame(null,                                          $burialFormView->funeralCompanySoleProprietorWebsite);
        $this->assertSame('ООО "Рога и копыта"',                         $burialFormView->funeralCompanyJuristicPersonName);
        $this->assertSame(null,                                          $burialFormView->funeralCompanyJuristicPersonInn);
        $this->assertSame(null,                                          $burialFormView->funeralCompanyJuristicPersonKpp);
        $this->assertSame(null,                                          $burialFormView->funeralCompanyJuristicPersonOgrn);
        $this->assertSame(null,                                          $burialFormView->funeralCompanyJuristicPersonOkpo);
        $this->assertSame(null,                                          $burialFormView->funeralCompanyJuristicPersonOkved);
        $this->assertSame(null,                                          $burialFormView->funeralCompanyJuristicPersonLegalAddress);
        $this->assertSame('г. Кемерово, пр. Строителей, д. 5, офис 102', $burialFormView->funeralCompanyJuristicPersonPostalAddress);
        $this->assertSame(null,                                          $burialFormView->funeralCompanyJuristicPersonBankDetailsBankName);
        $this->assertSame(null,                                          $burialFormView->funeralCompanyJuristicPersonBankDetailsBik);
        $this->assertSame(null,                                          $burialFormView->funeralCompanyJuristicPersonBankDetailsCorrespondentAccount);
        $this->assertSame(null,                                          $burialFormView->funeralCompanyJuristicPersonBankDetailsCurrentAccount);
        $this->assertSame(null,                                          $burialFormView->funeralCompanyJuristicPersonPhone);
        $this->assertSame(null,                                          $burialFormView->funeralCompanyJuristicPersonPhoneAdditional);
        $this->assertSame(null,                                          $burialFormView->funeralCompanyJuristicPersonFax);
        $this->assertSame(null,                                          $burialFormView->funeralCompanyJuristicPersonGeneralDirector);
        $this->assertSame(null,                                          $burialFormView->funeralCompanyJuristicPersonEmail);
        $this->assertSame(null,                                          $burialFormView->funeralCompanyJuristicPersonWebsite);
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

    private function testItReturnsBurialViewFormForB005(): void
    {
        $burialFormView = $this->fetcher->getFormViewById('B005');
        $this->assertInstanceOf(BurialFormView::class, $burialFormView);
        $this->assertSame('B005',                                        $burialFormView->id);
        $this->assertSame('000000005',                                   $burialFormView->code);
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
        $this->assertSame('1981-11-20',                                  $burialFormView->burialPlaceOwnerPassportIssuedAt);
        $this->assertSame('Отделом МВД Ленинского района г. Пензы',      $burialFormView->burialPlaceOwnerPassportIssuedBy);
        $this->assertSame(null,                                          $burialFormView->burialPlaceOwnerPassportDivisionCode);
        $this->assertSame('JP002',                                       $burialFormView->funeralCompanyId);
        $this->assertSame(JuristicPerson::CLASS_SHORTCUT,                $burialFormView->funeralCompanyType);
        $this->assertSame(null,                                          $burialFormView->funeralCompanySoleProprietorName);
        $this->assertSame(null,                                          $burialFormView->funeralCompanySoleProprietorInn);
        $this->assertSame(null,                                          $burialFormView->funeralCompanySoleProprietorOgrnip);
        $this->assertSame(null,                                          $burialFormView->funeralCompanySoleProprietorOkpo);
        $this->assertSame(null,                                          $burialFormView->funeralCompanySoleProprietorOkved);
        $this->assertSame(null,                                          $burialFormView->funeralCompanySoleProprietorRegistrationAddress);
        $this->assertSame(null,                                          $burialFormView->funeralCompanySoleProprietorActualLocationAddress);
        $this->assertSame(null,                                          $burialFormView->funeralCompanySoleProprietorBankDetailsBankName);
        $this->assertSame(null,                                          $burialFormView->funeralCompanySoleProprietorBankDetailsBik);
        $this->assertSame(null,                                          $burialFormView->funeralCompanySoleProprietorBankDetailsCorrespondentAccount);
        $this->assertSame(null,                                          $burialFormView->funeralCompanySoleProprietorBankDetailsCurrentAccount);
        $this->assertSame(null,                                          $burialFormView->funeralCompanySoleProprietorPhone);
        $this->assertSame(null,                                          $burialFormView->funeralCompanySoleProprietorPhoneAdditional);
        $this->assertSame(null,                                          $burialFormView->funeralCompanySoleProprietorFax);
        $this->assertSame(null,                                          $burialFormView->funeralCompanySoleProprietorEmail);
        $this->assertSame(null,                                          $burialFormView->funeralCompanySoleProprietorWebsite);
        $this->assertSame('ООО Ромашка',                                 $burialFormView->funeralCompanyJuristicPersonName);
        $this->assertSame('5404447629',                                  $burialFormView->funeralCompanyJuristicPersonInn);
        $this->assertSame(null,                                          $burialFormView->funeralCompanyJuristicPersonKpp);
        $this->assertSame(null,                                          $burialFormView->funeralCompanyJuristicPersonOgrn);
        $this->assertSame(null,                                          $burialFormView->funeralCompanyJuristicPersonOkpo);
        $this->assertSame(null,                                          $burialFormView->funeralCompanyJuristicPersonOkved);
        $this->assertSame(null,                                          $burialFormView->funeralCompanyJuristicPersonLegalAddress);
        $this->assertSame(null,                                          $burialFormView->funeralCompanyJuristicPersonPostalAddress);
        $this->assertSame('АО "АЛЬФА-БАНК"',                             $burialFormView->funeralCompanyJuristicPersonBankDetailsBankName);
        $this->assertSame('044525593',                                   $burialFormView->funeralCompanyJuristicPersonBankDetailsBik);
        $this->assertSame('30101810200000000593',                        $burialFormView->funeralCompanyJuristicPersonBankDetailsCorrespondentAccount);
        $this->assertSame('40701810401400000014',                        $burialFormView->funeralCompanyJuristicPersonBankDetailsCurrentAccount);
        $this->assertSame(null,                                          $burialFormView->funeralCompanyJuristicPersonPhone);
        $this->assertSame(null,                                          $burialFormView->funeralCompanyJuristicPersonPhoneAdditional);
        $this->assertSame(null,                                          $burialFormView->funeralCompanyJuristicPersonFax);
        $this->assertSame(null,                                          $burialFormView->funeralCompanyJuristicPersonGeneralDirector);
        $this->assertSame(null,                                          $burialFormView->funeralCompanyJuristicPersonEmail);
        $this->assertSame(null,                                          $burialFormView->funeralCompanyJuristicPersonWebsite);
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
        $this->assertSame('2010-01-28 12:55:00',                         $burialFormView->buriedAt);
        $this->assertValidUpdateAtValue($burialFormView->updatedAt);
    }

    private function testItReturnsBurialViewFormForB006(): void
    {
        $burialFormView = $this->fetcher->getFormViewById('B006');
        $this->assertInstanceOf(BurialFormView::class, $burialFormView);
        $this->assertSame('B006',                                        $burialFormView->id);
        $this->assertSame('000000006',                                   $burialFormView->code);
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
        $this->assertSame(null,                                          $burialFormView->customerNaturalPersonAddress);
        $this->assertSame('1915-11-24',                                  $burialFormView->customerNaturalPersonBornAt);
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
        $this->assertSame('SP002',                                       $burialFormView->funeralCompanyId);
        $this->assertSame(SoleProprietor::CLASS_SHORTCUT,                $burialFormView->funeralCompanyType);
        $this->assertSame('ИП Петров Пётр Петрович',                     $burialFormView->funeralCompanySoleProprietorName);
        $this->assertSame('772208786091',                                $burialFormView->funeralCompanySoleProprietorInn);
        $this->assertSame(null,                                          $burialFormView->funeralCompanySoleProprietorOgrnip);
        $this->assertSame(null,                                          $burialFormView->funeralCompanySoleProprietorOkpo);
        $this->assertSame(null,                                          $burialFormView->funeralCompanySoleProprietorOkved);
        $this->assertSame(null,                                          $burialFormView->funeralCompanySoleProprietorRegistrationAddress);
        $this->assertSame(null,                                          $burialFormView->funeralCompanySoleProprietorActualLocationAddress);
        $this->assertSame('АО "АЛЬФА-БАНК"',                             $burialFormView->funeralCompanySoleProprietorBankDetailsBankName);
        $this->assertSame('044525593',                                   $burialFormView->funeralCompanySoleProprietorBankDetailsBik);
        $this->assertSame('30101810200000000593',                        $burialFormView->funeralCompanySoleProprietorBankDetailsCorrespondentAccount);
        $this->assertSame('40701810401400000014',                        $burialFormView->funeralCompanySoleProprietorBankDetailsCurrentAccount);
        $this->assertSame('8(383)111-22-33',                             $burialFormView->funeralCompanySoleProprietorPhone);
        $this->assertSame('8(383)111-22-44',                             $burialFormView->funeralCompanySoleProprietorPhoneAdditional);
        $this->assertSame('8(383)111-22-55',                             $burialFormView->funeralCompanySoleProprietorFax);
        $this->assertSame('info@funeral54.ru',                           $burialFormView->funeralCompanySoleProprietorEmail);
        $this->assertSame('funeral54.ru',                                $burialFormView->funeralCompanySoleProprietorWebsite);
        $this->assertSame(null,                                          $burialFormView->funeralCompanyJuristicPersonName);
        $this->assertSame(null,                                          $burialFormView->funeralCompanyJuristicPersonInn);
        $this->assertSame(null,                                          $burialFormView->funeralCompanyJuristicPersonKpp);
        $this->assertSame(null,                                          $burialFormView->funeralCompanyJuristicPersonOgrn);
        $this->assertSame(null,                                          $burialFormView->funeralCompanyJuristicPersonOkpo);
        $this->assertSame(null,                                          $burialFormView->funeralCompanyJuristicPersonOkved);
        $this->assertSame(null,                                          $burialFormView->funeralCompanyJuristicPersonLegalAddress);
        $this->assertSame(null,                                          $burialFormView->funeralCompanyJuristicPersonPostalAddress);
        $this->assertSame(null,                                          $burialFormView->funeralCompanyJuristicPersonBankDetailsBankName);
        $this->assertSame(null,                                          $burialFormView->funeralCompanyJuristicPersonBankDetailsBik);
        $this->assertSame(null,                                          $burialFormView->funeralCompanyJuristicPersonBankDetailsCorrespondentAccount);
        $this->assertSame(null,                                          $burialFormView->funeralCompanyJuristicPersonBankDetailsCurrentAccount);
        $this->assertSame(null,                                          $burialFormView->funeralCompanyJuristicPersonPhone);
        $this->assertSame(null,                                          $burialFormView->funeralCompanyJuristicPersonPhoneAdditional);
        $this->assertSame(null,                                          $burialFormView->funeralCompanyJuristicPersonFax);
        $this->assertSame(null,                                          $burialFormView->funeralCompanyJuristicPersonGeneralDirector);
        $this->assertSame(null,                                          $burialFormView->funeralCompanyJuristicPersonEmail);
        $this->assertSame(null,                                          $burialFormView->funeralCompanyJuristicPersonWebsite);
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

    private function assertValidUpdateAtValue(string $updatedAt): void
    {
        $this->assertTrue(
            new \DateTimeImmutable() >= \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $updatedAt)
        );
    }
}
