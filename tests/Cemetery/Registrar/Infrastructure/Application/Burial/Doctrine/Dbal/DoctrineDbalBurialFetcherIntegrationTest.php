<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Application\Burial\Doctrine\Dbal;

use Cemetery\Registrar\Application\Burial\BurialFetcher;
use Cemetery\Registrar\Application\Burial\BurialView;
use Cemetery\Registrar\Domain\Burial\BurialCollection;
use Cemetery\Registrar\Domain\BurialPlace\ColumbariumNiche\ColumbariumCollection;
use Cemetery\Registrar\Domain\BurialPlace\ColumbariumNiche\ColumbariumNicheCollection;
use Cemetery\Registrar\Domain\BurialPlace\GraveSite\CemeteryBlockCollection;
use Cemetery\Registrar\Domain\BurialPlace\GraveSite\GraveSiteCollection;
use Cemetery\Registrar\Domain\BurialPlace\MemorialTree\MemorialTreeCollection;
use Cemetery\Registrar\Domain\Deceased\DeceasedCollection;
use Cemetery\Registrar\Domain\NaturalPerson\NaturalPersonCollection;
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
    private BurialFetcher $fetcher;

    public function setUp(): void
    {
        parent::setUp();

        $this->fillDatabase();
        $this->fetcher = new DoctrineDbalBurialFetcher();
    }

    public function testItReturnsBurialViewById(): void
    {
        $burialView = $this->fetcher->getById('B001');
        $this->assertInstanceOf(BurialView::class, $burialView);
        $this->assertSame('B001', $burialView->id);
        $this->assertSame('000000001', $burialView->code);
        $this->assertSame('URN_IN_COLUMBARIUM_NICHE', $burialView->type);
        $this->assertSame('Егоров Абрам Даниилович', $burialView->deceasedFullName);
        $this->assertSame('2021-12-01', $burialView->deceasedDiedAt);
        $this->assertNull($burialView->deceasedBornAt);
        $this->assertSame('2022-01-15 13:10:00', $burialView->deceasedBuriedAt);
        $this->assertNull($burialView->deceasedAge);
        $this->assertSame('южный колумбарий, ряд 2, ниша 002', $burialView->burialPlace);
        $this->assertSame('Жданова Инга Григорьевна', $burialView->customerName);
        $this->assertSame('Новосибирск, ул. Ленина, д. 1', $burialView->customerAddress);
        $this->assertNull($burialView->customerPhone);
    }

    public function testItFailsToReturnBurialViewByUnknownId(): void
    {

    }

    public function testItReturnsAllBurialViews(): void
    {

    }

    public function testItReturnsBurialViewsByPage(): void
    {

    }

    public function testItReturnsBurialViewsByTerm(): void
    {

    }

    public function testItReturnsBurialViewsByPageAndTerm(): void
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
