<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Application\Burial\Doctrine\Dbal;

use Cemetery\Registrar\Application\Burial\BurialFetcher;
use Cemetery\Registrar\Application\Burial\BurialFormView;
use Cemetery\Registrar\Application\Burial\BurialViewListItem;
use Cemetery\Registrar\Domain\Burial\BurialCollection;
use Cemetery\Registrar\Domain\Burial\BurialType;
use Cemetery\Registrar\Domain\BurialContainer\Urn;
use Cemetery\Registrar\Domain\BurialPlace\ColumbariumNiche\ColumbariumCollection;
use Cemetery\Registrar\Domain\BurialPlace\ColumbariumNiche\ColumbariumNiche;
use Cemetery\Registrar\Domain\BurialPlace\ColumbariumNiche\ColumbariumNicheCollection;
use Cemetery\Registrar\Domain\BurialPlace\GraveSite\CemeteryBlockCollection;
use Cemetery\Registrar\Domain\BurialPlace\GraveSite\GraveSiteCollection;
use Cemetery\Registrar\Domain\BurialPlace\MemorialTree\MemorialTreeCollection;
use Cemetery\Registrar\Domain\Deceased\DeceasedCollection;
use Cemetery\Registrar\Domain\NaturalPerson\NaturalPerson;
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

    public function testItReturnsBurialFormViewById(): void
    {
        $burialFormView = $this->fetcher->getById('B001');
        $this->assertInstanceOf(BurialFormView::class, $burialFormView);
        $this->assertSame('B001',                                      $burialFormView->id);
        $this->assertSame('000000001',                                 $burialFormView->code);
        $this->assertSame(BurialType::URN_IN_COLUMBARIUM_NICHE,        $burialFormView->type);
        $this->assertSame('D001',                                      $burialFormView->deceasedId);
        $this->assertSame('NP001',                                     $burialFormView->deceasedNaturalPersonId);
        $this->assertSame('Егоров Абрам Даниилович',                   $burialFormView->deceasedNaturalPersonFullName);
        $this->assertSame(null,                                        $burialFormView->deceasedNaturalPersonBornAt);
        $this->assertSame('2021-12-01',                                $burialFormView->deceasedDiedAt);
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
        $this->assertSame(null,                                        $burialFormView->customerSoleProprietorBankDetails);
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
        $this->assertSame(null,                                        $burialFormView->customerJuristicPersonBankDetails);
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
        $this->assertSame(null,                                        $burialFormView->funeralCompanySoleProprietorBankName);
        $this->assertSame(null,                                        $burialFormView->funeralCompanySoleProprietorBik);
        $this->assertSame(null,                                        $burialFormView->funeralCompanySoleProprietorCorrespondentAccount);
        $this->assertSame(null,                                        $burialFormView->funeralCompanySoleProprietorCurrentAccount);
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
        $this->assertSame(null,                                        $burialFormView->funeralCompanyJuristicPersonBankName);
        $this->assertSame(null,                                        $burialFormView->funeralCompanyJuristicPersonBik);
        $this->assertSame(null,                                        $burialFormView->funeralCompanyJuristicPersonCorrespondentAccount);
        $this->assertSame(null,                                        $burialFormView->funeralCompanyJuristicPersonCurrentAccount);
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
        $this->assertSame('',                                          $burialFormView->burialPlaceGraveSiteSize);
        $this->assertSame('C002',                                      $burialFormView->burialPlaceColumbariumNicheColumbariumId);
        $this->assertSame('южный колумбарий',                          $burialFormView->burialPlaceColumbariumNicheColumbariumName);
        $this->assertSame(2,                                           $burialFormView->burialPlaceColumbariumNicheRowInColumbarium);
        $this->assertSame('002',                                       $burialFormView->burialPlaceColumbariumNicheNicheNumber);
        $this->assertSame(null,                                        $burialFormView->burialPlaceMemorialTreeNumber);
        $this->assertSame('54.95035712',                               $burialFormView->burialPlaceGeoPositionLatitude);
        $this->assertSame('82.79252',                                  $burialFormView->burialPlaceGeoPositionLongitude);
        $this->assertSame('0.5',                                       $burialFormView->burialPlaceGeoPositionError);
        $this->assertSame(Urn::CLASS_SHORTCUT,                         $burialFormView->burialContainerType);
        $this->assertSame(null,                                        $burialFormView->burialContainerCoffinSize);
        $this->assertSame(null,                                        $burialFormView->burialContainerCoffinShape);
        $this->assertSame(null,                                        $burialFormView->burialContainerCoffinIsNonStandard);
        $this->assertSame('2022-01-15 13:10:00',                       $burialFormView->buriedAt);
    }

    public function testItFailsToReturnBurialViewByUnknownId(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Захоронение с ID "unknown_id" не найдено.');
        $this->fetcher->getById('unknown_id');
    }

    public function testItReturnsBurialViewListItemsByPage(): void
    {
        $customPageSize = 4;

        // First page
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

        // Second page
        $itemsForSecondPage = $this->fetcher->findAll(2, null, $customPageSize);
        $this->assertIsArray($itemsForSecondPage);
        $this->assertCount(2, $itemsForSecondPage);
        $this->assertContainsOnlyInstancesOf(BurialViewListItem::class, $itemsForSecondPage);
        $this->assertSame('B005',                        $itemsForFirstPage[0]->id);
        $this->assertSame('000000005',                   $itemsForFirstPage[0]->code);
        $this->assertSame('Соколов Герман Маркович',     $itemsForFirstPage[0]->deceasedFullName);
        $this->assertSame('2010-01-26',                  $itemsForFirstPage[0]->deceasedDiedAt);
        $this->assertSame(null,                          $itemsForFirstPage[0]->deceasedBornAt);
        $this->assertSame(null,                          $itemsForFirstPage[0]->deceasedBuriedAt);
        $this->assertSame(null,                          $itemsForFirstPage[0]->deceasedAge);
        $this->assertSame(null,                          $itemsForFirstPage[0]->burialPlace);
        $this->assertSame('ИП Иванов Иван Иванович',     $itemsForFirstPage[0]->customerName);
        $this->assertSame(null,                          $itemsForFirstPage[0]->customerAddress);
        $this->assertSame(null,                          $itemsForFirstPage[0]->customerPhone);

        $this->assertSame('B006',                        $itemsForFirstPage[1]->id);
        $this->assertSame('000000006',                   $itemsForFirstPage[1]->code);
        $this->assertSame('Гришина Устинья Ярославовна', $itemsForFirstPage[1]->deceasedFullName);
        $this->assertSame('2021-12-03',                  $itemsForFirstPage[1]->deceasedDiedAt);
        $this->assertSame(null,                          $itemsForFirstPage[1]->deceasedBornAt);
        $this->assertSame(null,                          $itemsForFirstPage[1]->deceasedBuriedAt);
        $this->assertSame(null,                          $itemsForFirstPage[1]->deceasedAge);
        $this->assertSame(null,                          $itemsForFirstPage[1]->burialPlace);
        $this->assertSame('Громов Никифор Рудольфович',  $itemsForFirstPage[1]->customerName);
        $this->assertSame(null,                          $itemsForFirstPage[1]->customerAddress);
        $this->assertSame(null,                          $itemsForFirstPage[1]->customerPhone);

        // Third page
        $itemsForThirdPage = $this->fetcher->findAll(3, null, $customPageSize);
        $this->assertIsArray($itemsForThirdPage);
        $this->assertEmpty($itemsForThirdPage);

        // All at once
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
