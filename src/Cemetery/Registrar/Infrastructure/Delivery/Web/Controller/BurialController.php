<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Delivery\Web\Controller;

use Cemetery\Registrar\Application\Command\Burial\RegisterNewBurial\RegisterNewBurialRequest;
use Cemetery\Registrar\Application\Command\Burial\RegisterNewBurial\RegisterNewBurialService;
use Cemetery\Registrar\Application\Query\Burial\CountBurialTotal\CountBurialTotalRequest;
use Cemetery\Registrar\Application\Query\Burial\CountBurialTotal\CountBurialTotalService;
use Cemetery\Registrar\Application\Query\Burial\ListBurials\ListBurialsRequest;
use Cemetery\Registrar\Application\Query\Burial\ListBurials\ListBurialsService;
use Cemetery\Registrar\Application\Query\CauseOfDeath\ListCausesOfDeath\ListCausesOfDeathRequest;
use Cemetery\Registrar\Application\Query\CauseOfDeath\ListCausesOfDeath\ListCausesOfDeathService;
use Cemetery\Registrar\Application\Query\FuneralCompany\ListFuneralCompanies\ListFuneralCompaniesRequest;
use Cemetery\Registrar\Application\Query\FuneralCompany\ListFuneralCompanies\ListFuneralCompaniesService;
use Cemetery\Registrar\Domain\Model\GeoPosition\Coordinates;
use Cemetery\Registrar\Domain\View\Burial\BurialFetcher;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
#[Route('/burial')]
class BurialController extends AbstractController
{
    /**
     * @param BurialFetcher               $burialFetcher
     * @param CountBurialTotalService     $countBurialTotalService
     * @param ListBurialsService          $listBurialsService
     * @param ListFuneralCompaniesService $listFuneralCompaniesService
     * @param ListCausesOfDeathService    $listCausesOfDeathService
     * @param RegisterNewBurialService    $registerNewBurialService
     */
    public function __construct(
        private readonly BurialFetcher               $burialFetcher,
        private readonly CountBurialTotalService     $countBurialTotalService,
        private readonly ListBurialsService          $listBurialsService,
        private readonly ListFuneralCompaniesService $listFuneralCompaniesService,
        private readonly ListCausesOfDeathService    $listCausesOfDeathService,
        private readonly RegisterNewBurialService    $registerNewBurialService,
    ) {}

    #[Route('/', name: 'burial_list', methods: Request::METHOD_GET)]
    public function list(): Response
    {
        $burialTotalCount = $this->countBurialTotalService
            ->execute(new CountBurialTotalRequest())
            ->totalCount;
        $burialList = $this->listBurialsService
            ->execute(new ListBurialsRequest())
            ->list;
        $funeralCompanyList = $this->listFuneralCompaniesService
            ->execute(new ListFuneralCompaniesRequest())
            ->list;
        $causeOfDeathList = $this->listCausesOfDeathService
            ->execute(new ListCausesOfDeathRequest())
            ->list;

        return $this->render('burial/list.html.twig', [
            'burialTotalCount'   => $burialTotalCount,
            'burialList'         => $burialList,
            'funeralCompanyList' => $funeralCompanyList,
            'causeOfDeathList'   => $causeOfDeathList,
        ]);
    }

    #[Route('/new', name: 'burial_new', methods: [Request::METHOD_GET, Request::METHOD_POST])]
    public function new(Request $request): Response
    {
        if ($request->isMethod(Request::METHOD_GET)) {
//            $causeOfDeathViewList   = $this->causeOfDeathFetcher->findAll();
//            $funeralCompanyViewList = $this->funeralCompanyFetcher->findAll();
            $response               = $this->render('burial/new.html.twig', [
                'causeOfDeathViewList'   => [],
                'funeralCompanyViewList' => [],
            ]);
        }
        if ($request->isMethod(Request::METHOD_POST)) {
            $registerNewBurialRequest  = new RegisterNewBurialRequest(...$this->getRequestArgs($request));
            $burialId             = $this->registerNewBurialService->execute($registerNewBurialRequest)->burialId;

            $response = $this->redirectToRoute('burial_list', [
                'burialId' => $burialId,
            ]);
        }

        return $response;
    }
    
    #[Route('/edit/{id}', name: 'burial_edit', methods: [Request::METHOD_GET, Request::METHOD_POST])]
    public function edit(Request $request, string $id): JsonResponse
    {
        if ($request->isMethod(Request::METHOD_GET)) {
            $burialView = $this->burialFetcher->getViewById($id);
            $response = $this->json($burialView);
        }
        if ($request->isMethod(Request::METHOD_POST)) {
            // TODO
        }

        return $response;
    }

    /**
     * @param Request $request
     *
     * @return array
     */
    private function getRequestArgs(Request $request): array
    {
        return [
            $request->request->get('type'),
            $request->request->get('deceasedNaturalPersonId'),
            $request->request->get('deceasedNaturalPersonFullName'),
            $request->request->get('deceasedNaturalPersonBornAt'),
            $request->request->get('deceasedDiedAt'),
            $request->request->get('deceasedAge') !== null ?
                (int) $request->request->get('deceasedAge')
                : null,
            $request->request->get('deceasedDeathCertificateId'),
            $request->request->get('deceasedCauseOfDeath'),
            $request->request->get('customerId'),
            $request->request->get('customerType'),
            $request->request->get('customerNaturalPersonFullName'),
            $request->request->get('customerNaturalPersonPhone'),
            $request->request->get('customerNaturalPersonPhoneAdditional'),
            $request->request->get('customerNaturalPersonEmail'),
            $request->request->get('customerNaturalPersonAddress'),
            $request->request->get('customerNaturalPersonBornAt'),
            $request->request->get('customerNaturalPersonPlaceOfBirth'),
            $request->request->get('customerNaturalPersonPassportSeries'),
            $request->request->get('customerNaturalPersonPassportNumber'),
            $request->request->get('customerNaturalPersonPassportIssuedAt'),
            $request->request->get('customerNaturalPersonPassportIssuedBy'),
            $request->request->get('customerNaturalPersonPassportDivisionCode'),
            $request->request->get('customerSoleProprietorName'),
            $request->request->get('customerSoleProprietorInn'),
            $request->request->get('customerSoleProprietorOgrnip'),
            $request->request->get('customerSoleProprietorOkpo'),
            $request->request->get('customerSoleProprietorOkved'),
            $request->request->get('customerSoleProprietorRegistrationAddress'),
            $request->request->get('customerSoleProprietorActualLocationAddress'),
            $request->request->get('customerSoleProprietorBankDetailsBankName'),
            $request->request->get('customerSoleProprietorBankDetailsBik'),
            $request->request->get('customerSoleProprietorBankDetailsCorrespondentAccount'),
            $request->request->get('customerSoleProprietorBankDetailsCurrentAccount'),
            $request->request->get('customerSoleProprietorPhone'),
            $request->request->get('customerSoleProprietorPhoneAdditional'),
            $request->request->get('customerSoleProprietorFax'),
            $request->request->get('customerSoleProprietorEmail'),
            $request->request->get('customerSoleProprietorWebsite'),
            $request->request->get('customerJuristicPersonName'),
            $request->request->get('customerJuristicPersonInn'),
            $request->request->get('customerJuristicPersonKpp'),
            $request->request->get('customerJuristicPersonOgrn'),
            $request->request->get('customerJuristicPersonOkpo'),
            $request->request->get('customerJuristicPersonOkved'),
            $request->request->get('customerJuristicPersonLegalAddress'),
            $request->request->get('customerJuristicPersonPostalAddress'),
            $request->request->get('customerJuristicPersonBankDetailsBankName'),
            $request->request->get('customerJuristicPersonBankDetailsBik'),
            $request->request->get('customerJuristicPersonBankDetailsCorrespondentAccount'),
            $request->request->get('customerJuristicPersonBankDetailsCurrentAccount'),
            $request->request->get('customerJuristicPersonPhone'),
            $request->request->get('customerJuristicPersonPhoneAdditional'),
            $request->request->get('customerJuristicPersonFax'),
            $request->request->get('customerJuristicPersonGeneralDirector'),
            $request->request->get('customerJuristicPersonEmail'),
            $request->request->get('customerJuristicPersonWebsite'),
            $request->request->get('personInChargeId'),
            $request->request->get('personInChargeFullName'),
            $request->request->get('personInChargePhone'),
            $request->request->get('personInChargePhoneAdditional'),
            $request->request->get('personInChargeEmail'),
            $request->request->get('personInChargeAddress'),
            $request->request->get('personInChargeBornAt'),
            $request->request->get('personInChargePlaceOfBirth'),
            $request->request->get('personInChargePassportSeries'),
            $request->request->get('personInChargePassportNumber'),
            $request->request->get('personInChargePassportIssuedAt'),
            $request->request->get('personInChargePassportIssuedBy'),
            $request->request->get('personInChargePassportDivisionCode'),
            $request->request->get('funeralCompanyId'),
            $request->request->get('funeralCompanyName'),
            $request->request->get('burialChainId'),
            $request->request->get('burialPlaceId'),
            $request->request->get('burialPlaceType'),
            $request->request->get('burialPlaceGraveSiteCemeteryBlockId'),
            $request->request->get('burialPlaceGraveSiteRowInBlock') !== null
                ? (int) $request->request->get('burialPlaceGraveSiteRowInBlock')
                : null,
            $request->request->get('burialPlaceGraveSitePositionInRow') !== null
                ? (int) $request->request->get('burialPlaceGraveSitePositionInRow')
                : null,
            $request->request->get('burialPlaceGraveSiteSize'),
            $request->request->get('burialPlaceColumbariumNicheColumbariumId'),
            $request->request->get('burialPlaceColumbariumNicheRowInColumbarium') !== null
                ? (int) $request->request->get('burialPlaceColumbariumNicheRowInColumbarium')
                : null,
            $request->request->get('burialPlaceColumbariumNicheNicheNumber'),
            $request->request->get('burialPlaceMemorialTreeNumber'),
            $this->extractLatitudeFromGeoPosition($request->request->get('burialPlaceGeoPosition')),
            $this->extractLongitudeFromGeoPosition($request->request->get('burialPlaceGeoPosition')),
            null,                               // HTML form does not have the ability to specify a geo position error
            $request->request->get('burialContainerType'),
            $request->request->get('burialContainerCoffinSize') !== null
                ? (int) $request->request->get('burialContainerCoffinSize')
                : null,
            $request->request->get('burialContainerCoffinShape'),
            $request->request->get('burialContainerCoffinIsNonStandard') !== null
                ? $request->request->filter('burialContainerCoffinIsNonStandard', null, \FILTER_VALIDATE_BOOL)
                : null,
            $request->request->get('buriedAt'),
        ];
    }

    /**
     * @param string|null $geoPosition
     *
     * @return string|null
     */
    private function extractLatitudeFromGeoPosition(?string $geoPosition): ?string
    {
        $latitude = null;

        if ($geoPosition !== null) {
            $this->assertValidGeoPositionFormat($geoPosition);
            [$latitude,] = \explode(',', $geoPosition);
            $latitude    = \trim($latitude);
        }

        return $latitude;
    }

    /**
     * @param string|null $geoPosition
     *
     * @return string|null
     */
    private function extractLongitudeFromGeoPosition(?string $geoPosition): ?string
    {
        $longitude = null;

        if ($geoPosition !== null) {
            $this->assertValidGeoPositionFormat($geoPosition);
            [,$longitude] = \explode(',', $geoPosition);
            $longitude    = \trim($longitude);
        }

        return $longitude;
    }

    /**
     * @param string $geoPosition
     *
     * @throws \RuntimeException when the geo position string has invalid format
     */
    private function assertValidGeoPositionFormat(string $geoPosition): void
    {
        $hasComma = \str_contains($geoPosition, ',');
        if ($hasComma) {
            [$latitudeRaw, $longitudeRaw] = \explode(',', $geoPosition);
            $latitude                     = \trim($latitudeRaw);
            $longitude                    = \trim($longitudeRaw);
            $coordinateValuePattern       = Coordinates::VALUE_PATTERN;
            $isLatitudeHasValidFormat     = \preg_match($coordinateValuePattern, $latitude)  === 1;
            $isLongitudeHasValidFormat    = \preg_match($coordinateValuePattern, $longitude) === 1;
        }
        if (!$hasComma || !$isLatitudeHasValidFormat || !$isLongitudeHasValidFormat) {
            throw new \RuntimeException(\sprintf('Неверный формат геопозиции "%s".', $geoPosition));
        }
    }
}
