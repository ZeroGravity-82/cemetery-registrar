<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Application\Burial\Doctrine\Dbal;

use Cemetery\Registrar\Application\Burial\BurialFetcher;
use Cemetery\Registrar\Application\Burial\BurialView;
use Cemetery\Registrar\Application\Burial\BurialViewListItem;
use Cemetery\Registrar\Domain\Burial\BurialCollection;
use Cemetery\Registrar\Domain\BurialPlace\ColumbariumNiche\ColumbariumCollection;
use Cemetery\Registrar\Domain\BurialPlace\ColumbariumNiche\ColumbariumNiche;
use Cemetery\Registrar\Domain\BurialPlace\ColumbariumNiche\ColumbariumNicheCollection;
use Cemetery\Registrar\Domain\BurialPlace\GraveSite\CemeteryBlockCollection;
use Cemetery\Registrar\Domain\BurialPlace\GraveSite\GraveSiteCollection;
use Cemetery\Registrar\Domain\BurialPlace\MemorialTree\MemorialTreeCollection;
use Cemetery\Registrar\Domain\Deceased\DeceasedCollection;
use Cemetery\Registrar\Domain\NaturalPerson\NaturalPersonCollection;
use Cemetery\Registrar\Domain\NaturalPerson\NaturalPersonId;
use Cemetery\Registrar\Domain\Organization\JuristicPerson\JuristicPerson;
use Cemetery\Registrar\Domain\Organization\JuristicPerson\JuristicPersonCollection;
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
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\Burial\BurialPlaceIdType;
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
        $this->fetcher = new DoctrineDbalBurialFetcher();
    }

    public function testItHasValidPageSizeConstant(): void
    {
        $this->assertSame(self::DEFAULT_PAGE_SIZE, BurialFetcher::DEFAULT_PAGE_SIZE);
    }

    public function testItReturnsBurialViewById(): void
    {
//        $knownBurialId = 'B001';
//        $burialView    = $this->fetcher->getById($knownBurialId);
//        $this->assertInstanceOf(BurialView::class, $burialView);
//        $this->assertSame($knownBurialId, $burialView->id);
//        $this->assertSame('000000001', $burialView->code);
//        $this->assertSame('D001', $burialView->deceasedId);
//        $this->assertSame('URN_IN_COLUMBARIUM_NICHE', $burialView->type);
//        $this->assertSame('NP005', $burialView->customerId);
//        $this->assertSame('NaturalPersonId', $burialView->customerType);
//        $this->assertSame('южный колумбарий, ряд 2, ниша 002', $burialView->burialPlaceId);
//        $this->assertSame(ColumbariumNiche::CLASS_SHORTCUT, $burialView->burialPlaceType);
//        $this->assertSame(null, $burialView->burialPlaceOwnerId);
//        $this->assertSame('', $burialView->funeralCompanyId);
//        $this->assertSame('', $burialView->funeralCompanyType);
//        $this->assertNull($burialView->customerPhone);
    }

    public function testItFailsToReturnBurialViewByUnknownId(): void
    {
        $unknownBurialId = 'unknown_id';
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(\sprintf('Захоронение с ID "%s" не найдено.', $unknownBurialId));
        $this->fetcher->getById($unknownBurialId);
    }

    public function testItReturnsBurialViewListItemsByPage(): void
    {
        $customPageSize = 4;

        $itemsForFirstPage = $this->fetcher->findAll(1, null, $customPageSize);
        $this->assertIsArray($itemsForFirstPage);
        $this->assertCount(4, $itemsForFirstPage);
        $this->assertContainsOnlyInstancesOf(BurialViewListItem::class, $itemsForFirstPage);
        $this->assertSame('B001',                                        $itemsForFirstPage[0]->id);
        $this->assertSame('000000001',                                   $itemsForFirstPage[0]->code);
        $this->assertSame('Егоров Абрам Даниилович',                     $itemsForFirstPage[0]->deceasedFullName);
        $this->assertSame('2021-12-01',                                  $itemsForFirstPage[0]->deceasedDiedAt);
        $this->assertSame(null,                                          $itemsForFirstPage[0]->deceasedBornAt);
        $this->assertSame('2022-01-15 13:10:00',                         $itemsForFirstPage[0]->deceasedBuriedAt);
        $this->assertSame(null,                                          $itemsForFirstPage[0]->deceasedAge);
        $this->assertSame('южный колумбарий, ряд 2, ниша 002',           $itemsForFirstPage[0]->burialPlace);
        $this->assertSame('Жданова Инга Григорьевна',                    $itemsForFirstPage[0]->customerName);
        $this->assertSame('Новосибирск, ул. Ленина, д. 1',               $itemsForFirstPage[0]->customerAddress);
        $this->assertSame('+7-913-111-22-33',                            $itemsForFirstPage[0]->customerPhone);
        $this->assertSame('B002',                                        $itemsForFirstPage[1]->id);
        $this->assertSame('000000002',                                   $itemsForFirstPage[1]->code);
        $this->assertSame('Устинов Арсений Максович',                    $itemsForFirstPage[1]->deceasedFullName);
        $this->assertSame('2001-02-11',                                  $itemsForFirstPage[1]->deceasedDiedAt);
        $this->assertSame('1918-12-30',                                  $itemsForFirstPage[1]->deceasedBornAt);
        $this->assertSame(null,                                          $itemsForFirstPage[1]->deceasedBuriedAt);
        $this->assertSame(82,                                            $itemsForFirstPage[1]->deceasedAge);
        $this->assertSame('общий квартал Б, ряд 1',                      $itemsForFirstPage[1]->burialPlace);
        $this->assertSame('Жданова Инга Григорьевна',                    $itemsForFirstPage[1]->customerName);
        $this->assertSame('Новосибирск, ул. Ленина, д. 1',               $itemsForFirstPage[1]->customerAddress);
        $this->assertSame('+7-913-111-22-33',                            $itemsForFirstPage[1]->customerPhone);
        $this->assertSame('B003',                                        $itemsForFirstPage[2]->id);
        $this->assertSame('000000003',                                   $itemsForFirstPage[2]->code);
        $this->assertSame('Шилов Александр Михаилович',                  $itemsForFirstPage[2]->deceasedFullName);
        $this->assertSame('2011-05-13',                                  $itemsForFirstPage[2]->deceasedDiedAt);
        $this->assertSame('1969-05-20',                                  $itemsForFirstPage[2]->deceasedBornAt);
        $this->assertSame(null,                                          $itemsForFirstPage[2]->deceasedBuriedAt);
        $this->assertSame(null,                                          $itemsForFirstPage[2]->deceasedAge);
        $this->assertSame('памятное дерево 002',                         $itemsForFirstPage[2]->burialPlace);
        $this->assertSame('Гришина Устинья Ярославовна',                 $itemsForFirstPage[2]->customerName);
        $this->assertSame(null,                                          $itemsForFirstPage[2]->customerAddress);
        $this->assertSame(null,                                          $itemsForFirstPage[2]->customerPhone);
        $this->assertSame('B004',                                        $itemsForFirstPage[3]->id);
        $this->assertSame('000000004',                                   $itemsForFirstPage[3]->code);
        $this->assertSame('Жданова Инга Григорьевна',                    $itemsForFirstPage[3]->deceasedFullName);
        $this->assertSame('2022-03-10',                                  $itemsForFirstPage[3]->deceasedDiedAt);
        $this->assertSame('1979-02-12',                                  $itemsForFirstPage[3]->deceasedBornAt);
        $this->assertSame(null,                                          $itemsForFirstPage[3]->deceasedBuriedAt);
        $this->assertSame(null,                                          $itemsForFirstPage[3]->deceasedAge);
        $this->assertSame(null,                                          $itemsForFirstPage[3]->burialPlace);
        $this->assertSame('ООО "Рога и копыта"',                         $itemsForFirstPage[3]->customerName);
        $this->assertSame('г. Кемерово, пр. Строителей, д. 5, офис 102', $itemsForFirstPage[3]->customerAddress);
        $this->assertSame(null,                                          $itemsForFirstPage[3]->customerPhone);

        $itemsForSecondPage = $this->fetcher->findAll(2, null, $customPageSize);
        $this->assertIsArray($itemsForSecondPage);
        $this->assertCount(2, $itemsForSecondPage);
        $this->assertContainsOnlyInstancesOf(BurialViewListItem::class, $itemsForSecondPage);
        $this->assertSame('B005',                                        $itemsForFirstPage[0]->id);
        $this->assertSame('000000005',                                   $itemsForFirstPage[0]->code);
        $this->assertSame('Соколов Герман Маркович',                     $itemsForFirstPage[0]->deceasedFullName);
        $this->assertSame('2010-01-26',                                  $itemsForFirstPage[0]->deceasedDiedAt);
        $this->assertSame(null,                                          $itemsForFirstPage[0]->deceasedBornAt);
        $this->assertSame(null,                                          $itemsForFirstPage[0]->deceasedBuriedAt);
        $this->assertSame(null,                                          $itemsForFirstPage[0]->deceasedAge);
        $this->assertSame(null,                                          $itemsForFirstPage[0]->burialPlace);
        $this->assertSame('ИП Иванов Иван Иванович',                     $itemsForFirstPage[0]->customerName);
        $this->assertSame(null,                                          $itemsForFirstPage[0]->customerAddress);
        $this->assertSame(null,                                          $itemsForFirstPage[0]->customerPhone);
        $this->assertSame('B006',                                        $itemsForFirstPage[1]->id);
        $this->assertSame('000000006',                                   $itemsForFirstPage[1]->code);
        $this->assertSame('Гришина Устинья Ярославовна',                 $itemsForFirstPage[1]->deceasedFullName);
        $this->assertSame('2021-12-03',                                  $itemsForFirstPage[1]->deceasedDiedAt);
        $this->assertSame(null,                                          $itemsForFirstPage[1]->deceasedBornAt);
        $this->assertSame(null,                                          $itemsForFirstPage[1]->deceasedBuriedAt);
        $this->assertSame(null,                                          $itemsForFirstPage[1]->deceasedAge);
        $this->assertSame(null,                                          $itemsForFirstPage[1]->burialPlace);
        $this->assertSame('Громов Никифор Рудольфович',                  $itemsForFirstPage[1]->customerName);
        $this->assertSame(null,                                          $itemsForFirstPage[1]->customerAddress);
        $this->assertSame(null,                                          $itemsForFirstPage[1]->customerPhone);

        $itemsForThirdPage = $this->fetcher->findAll(3, null, $customPageSize);
        $this->assertIsArray($itemsForThirdPage);
        $this->assertEmpty($itemsForThirdPage);

        $itemsForDefaultPageSize = $this->fetcher->findAll(1);
        $this->assertIsArray($itemsForDefaultPageSize);
        $this->assertCount(6, $itemsForDefaultPageSize);
    }

    public function testItReturnsBurialViewListItemsByTerm(): void
    {

    }

    public function testItReturnsBurialViewListItemsByPageAndTerm(): void
    {

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
}
