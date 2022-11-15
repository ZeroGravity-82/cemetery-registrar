<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Delivery\Web\Controller;

use Cemetery\Registrar\Application\ApplicationRequestBus;
use Cemetery\Registrar\Application\Burial\Command\RegisterNewBurial\RegisterNewBurialRequest;
use Cemetery\Registrar\Application\Burial\Query\ListBurials\ListBurialsRequest;
use Cemetery\Registrar\Application\CauseOfDeath\Query\ListAllCausesOfDeath\ListAllCausesOfDeathRequest;
use Cemetery\Registrar\Domain\Model\GeoPosition\Coordinates;
use Cemetery\Registrar\Domain\View\Burial\BurialFetcherInterface;
use Symfony\Component\HttpFoundation\Request as HttpRequest;
use Symfony\Component\HttpFoundation\Response as HttpResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class BurialController extends AbstractController
{
    public function __construct(
        private ApplicationRequestBus  $appRequestBus,
        private BurialFetcherInterface $burialFetcher,
    ) {}

    #[Route('/burial', name: 'burial_list', methods: HttpRequest::METHOD_GET)]
    public function list(): HttpResponse
    {
        $queryRequest       = new ListBurialsRequest();
        $queryResponse      = $this->appRequestBus->execute($queryRequest);
        $list               = $queryResponse->data->list;
        $totalCount         = $queryResponse->data->totalCount;
        $funeralCompanyList = $queryResponse->data->funeralCompanyList;
        $cemeteryBlockList  = $queryResponse->data->cemeteryBlockList;
        $coffinShapeList    = $queryResponse->data->coffinShapeList;

        $queryRequest       = new ListAllCausesOfDeathRequest();
        $queryResponse      = $this->appRequestBus->execute($queryRequest);
        $causeOfDeathList   = $queryResponse->data->list;

        return $this->render('burial/list_burial.html.twig', [
            'list'               => $list,
            'totalCount'         => $totalCount,
            'funeralCompanyList' => $funeralCompanyList,
            'causeOfDeathList'   => $causeOfDeathList,
            'cemeteryBlockList'  => $cemeteryBlockList,
            'coffinShapeList'    => $coffinShapeList,
        ]);
    }

    #[Route('/burial/new', name: 'burial_new', methods: [HttpRequest::METHOD_GET, HttpRequest::METHOD_POST])]
    public function new(HttpRequest $request): HttpResponse
    {
        if ($request->isMethod(HttpRequest::METHOD_GET)) {
//            $causeOfDeathViewList   = $this->causeOfDeathFetcher->findAll();
//            $funeralCompanyViewList = $this->funeralCompanyFetcher->findAll();
            $response               = $this->render('burial/new.html.twig', [
                'causeOfDeathViewList'   => [],
                'funeralCompanyViewList' => [],
            ]);
        }
        if ($request->isMethod(HttpRequest::METHOD_POST)) {
            $registerNewBurialRequest = new RegisterNewBurialRequest(...$this->getRequestArgs($request));
            $burialId                 = $this->appRequestBus->execute($registerNewBurialRequest)->id;

            $response = $this->redirectToRoute('burial_list', [
                'burialId' => $burialId,
            ]);
        }

        return $response;
    }

    #[Route('/burial/edit/{id}', name: 'burial_edit', methods: [HttpRequest::METHOD_GET, HttpRequest::METHOD_POST])]
    public function edit(HttpRequest $request, string $id): HttpResponse
    {
        if ($request->isMethod(HttpRequest::METHOD_GET)) {
            $burialView = $this->burialFetcher->findViewById($id);
            $response = $this->json($burialView);
        }
        if ($request->isMethod(HttpRequest::METHOD_POST)) {
            // TODO
        }

        return $response;
    }

    private function getRequestArgs(HttpRequest $request): array
    {
        return [
            $this->getInputString($request, 'type'),
            $this->getInputString($request, 'deceasedNaturalPersonId'),
            $this->getInputString($request, 'deceasedNaturalPersonFullName'),
            $this->getInputString($request, 'deceasedNaturalPersonBornAt'),
            $this->getInputString($request, 'deceasedDiedAt'),
            $this->getInputInt($request,    'deceasedAge'),
            $this->getInputString($request, 'deceasedDeathCertificateId'),
            $this->getInputString($request, 'deceasedCauseOfDeathId'),
            $this->getInputString($request, 'customerId'),
            $this->getInputString($request, 'customerType'),
            $this->getInputString($request, 'customerNaturalPersonFullName'),
            $this->getInputString($request, 'customerNaturalPersonPhone'),
            $this->getInputString($request, 'customerNaturalPersonPhoneAdditional'),
            $this->getInputString($request, 'customerNaturalPersonEmail'),
            $this->getInputString($request, 'customerNaturalPersonAddress'),
            $this->getInputString($request, 'customerNaturalPersonBornAt'),
            $this->getInputString($request, 'customerNaturalPersonPlaceOfBirth'),
            $this->getInputString($request, 'customerNaturalPersonPassportSeries'),
            $this->getInputString($request, 'customerNaturalPersonPassportNumber'),
            $this->getInputString($request, 'customerNaturalPersonPassportIssuedAt'),
            $this->getInputString($request, 'customerNaturalPersonPassportIssuedBy'),
            $this->getInputString($request, 'customerNaturalPersonPassportDivisionCode'),
            $this->getInputString($request, 'customerSoleProprietorName'),
            $this->getInputString($request, 'customerSoleProprietorInn'),
            $this->getInputString($request, 'customerSoleProprietorOgrnip'),
            $this->getInputString($request, 'customerSoleProprietorOkpo'),
            $this->getInputString($request, 'customerSoleProprietorOkved'),
            $this->getInputString($request, 'customerSoleProprietorRegistrationAddress'),
            $this->getInputString($request, 'customerSoleProprietorActualLocationAddress'),
            $this->getInputString($request, 'customerSoleProprietorBankDetailsBankName'),
            $this->getInputString($request, 'customerSoleProprietorBankDetailsBik'),
            $this->getInputString($request, 'customerSoleProprietorBankDetailsCorrespondentAccount'),
            $this->getInputString($request, 'customerSoleProprietorBankDetailsCurrentAccount'),
            $this->getInputString($request, 'customerSoleProprietorPhone'),
            $this->getInputString($request, 'customerSoleProprietorPhoneAdditional'),
            $this->getInputString($request, 'customerSoleProprietorFax'),
            $this->getInputString($request, 'customerSoleProprietorEmail'),
            $this->getInputString($request, 'customerSoleProprietorWebsite'),
            $this->getInputString($request, 'customerJuristicPersonName'),
            $this->getInputString($request, 'customerJuristicPersonInn'),
            $this->getInputString($request, 'customerJuristicPersonKpp'),
            $this->getInputString($request, 'customerJuristicPersonOgrn'),
            $this->getInputString($request, 'customerJuristicPersonOkpo'),
            $this->getInputString($request, 'customerJuristicPersonOkved'),
            $this->getInputString($request, 'customerJuristicPersonLegalAddress'),
            $this->getInputString($request, 'customerJuristicPersonPostalAddress'),
            $this->getInputString($request, 'customerJuristicPersonBankDetailsBankName'),
            $this->getInputString($request, 'customerJuristicPersonBankDetailsBik'),
            $this->getInputString($request, 'customerJuristicPersonBankDetailsCorrespondentAccount'),
            $this->getInputString($request, 'customerJuristicPersonBankDetailsCurrentAccount'),
            $this->getInputString($request, 'customerJuristicPersonPhone'),
            $this->getInputString($request, 'customerJuristicPersonPhoneAdditional'),
            $this->getInputString($request, 'customerJuristicPersonFax'),
            $this->getInputString($request, 'customerJuristicPersonGeneralDirector'),
            $this->getInputString($request, 'customerJuristicPersonEmail'),
            $this->getInputString($request, 'customerJuristicPersonWebsite'),
            $this->getInputString($request, 'personInChargeId'),
            $this->getInputString($request, 'personInChargeFullName'),
            $this->getInputString($request, 'personInChargePhone'),
            $this->getInputString($request, 'personInChargePhoneAdditional'),
            $this->getInputString($request, 'personInChargeEmail'),
            $this->getInputString($request, 'personInChargeAddress'),
            $this->getInputString($request, 'personInChargeBornAt'),
            $this->getInputString($request, 'personInChargePlaceOfBirth'),
            $this->getInputString($request, 'personInChargePassportSeries'),
            $this->getInputString($request, 'personInChargePassportNumber'),
            $this->getInputString($request, 'personInChargePassportIssuedAt'),
            $this->getInputString($request, 'personInChargePassportIssuedBy'),
            $this->getInputString($request, 'personInChargePassportDivisionCode'),
            $this->getInputString($request, 'funeralCompanyId'),
            $this->getInputString($request, 'burialChainId'),
            $this->getInputString($request, 'burialPlaceId'),
            $this->getInputString($request, 'burialPlaceType'),
            $this->getInputString($request, 'burialPlaceGraveSiteCemeteryBlockId'),
            $this->getInputInt($request,    'burialPlaceGraveSiteRowInBlock'),
            $this->getInputInt($request,    'burialPlaceGraveSitePositionInRow'),
            $this->getInputString($request, 'burialPlaceGraveSiteSize'),
            $this->getInputString($request, 'burialPlaceColumbariumNicheColumbariumId'),
            $this->getInputInt($request,    'burialPlaceColumbariumNicheRowInColumbarium'),
            $this->getInputString($request, 'burialPlaceColumbariumNicheNicheNumber'),
            $this->getInputString($request, 'burialPlaceMemorialTreeNumber'),
            $this->extractLatitudeFromGeoPosition($this->getInputString($request, 'burialPlaceGeoPosition')),
            $this->extractLongitudeFromGeoPosition($this->getInputString($request, 'burialPlaceGeoPosition')),
            null,                            // HTML form does not have the ability to specify a geo position error
            $this->getInputString($request, 'burialContainerType'),
            $this->getInputInt($request,    'burialContainerCoffinSize'),
            $this->getInputString($request, 'burialContainerCoffinShape'),
            $this->getInputBool($request,   'burialContainerCoffinIsNonStandard'),
            $this->getInputString($request, 'buriedAt'),
        ];
    }

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
     * @throws \RuntimeException when the geo position string has invalid format
     */
    private function assertValidGeoPositionFormat(string $geoPosition): void
    {
        $hasComma = \str_contains($geoPosition, ',');
        if ($hasComma) {
            [$latitudeRaw, $longitudeRaw] = \explode(',', $geoPosition);
            $latitude                     = \trim($latitudeRaw);
            $longitude                    = \trim($longitudeRaw);
            $coordinateFormat             = Coordinates::FORMAT;
            $isLatitudeHasValidFormat     = \preg_match($coordinateFormat, $latitude)  === 1;
            $isLongitudeHasValidFormat    = \preg_match($coordinateFormat, $longitude) === 1;
        }
        if (!$hasComma || !$isLatitudeHasValidFormat || !$isLongitudeHasValidFormat) {
            throw new \RuntimeException(\sprintf('Неверный формат геопозиции "%s".', $geoPosition));
        }
    }
}
