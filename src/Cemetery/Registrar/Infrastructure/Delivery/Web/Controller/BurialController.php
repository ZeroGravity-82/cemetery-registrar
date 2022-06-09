<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Delivery\Web\Controller;

use Cemetery\Registrar\Application\Burial\BurialFetcher;
use Cemetery\Registrar\Application\Burial\CreateBurial\CreateBurialRequest;
use Cemetery\Registrar\Application\Burial\CreateBurial\CreateBurialService;
use Cemetery\Registrar\Application\FuneralCompany\FuneralCompanyFetcher;
use Cemetery\Registrar\Domain\GeoPosition\Coordinates;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class BurialController extends AbstractController
{
    /**
     * @param BurialFetcher         $burialFetcher
     * @param FuneralCompanyFetcher $funeralCompanyFetcher
     * @param CreateBurialService   $createBurialService
     */
    public function __construct(
        private readonly BurialFetcher         $burialFetcher,
        private readonly FuneralCompanyFetcher $funeralCompanyFetcher,
        private readonly CreateBurialService   $createBurialService,
    ) {}

    #[Route('/burial', name: 'burial_index', methods: 'GET')]
    public function index(): Response
    {
        $burialViewList         = $this->burialFetcher->findAll(1);
        $burialTotalCount       = $this->burialFetcher->getTotalCount();
        $funeralCompanyViewList = $this->funeralCompanyFetcher->findAll(1, null, PHP_INT_MAX);

        return $this->render('burial/index.html.twig', [
            'burialViewList'         => $burialViewList,
            'burialTotalCount'       => $burialTotalCount,
            'funeralCompanyViewList' => $funeralCompanyViewList,
        ]);
    }

    #[Route('/burial/new', name: 'burial_new', methods: 'POST')]
    public function new(Request $request): Response
    {
        $createBurialRequest = new CreateBurialRequest(...$this->getRequestArgs($request));
        $this->createBurialService->execute($createBurialRequest);

        return $this->redirectToRoute('burial_index');
    }
    
    #[Route('/burial/edit-get/{id}', name: 'burial_edit_get', methods: 'GET')]
    public function editGet(string $id): JsonResponse
    {
        $burialFormView = $this->burialFetcher->getFormViewById($id);

        return $this->json($burialFormView);
    }

    #[Route('/burial/edit-post/{id}', name: 'burial_edit_post', methods: 'POST')]
    public function editPost(Request $request): Response
    {
        // TODO
    }

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
            $request->request->get('burialPlaceOwnerId'),
            $request->request->get('burialPlaceOwnerFullName'),
            $request->request->get('burialPlaceOwnerPhone'),
            $request->request->get('burialPlaceOwnerPhoneAdditional'),
            $request->request->get('burialPlaceOwnerEmail'),
            $request->request->get('burialPlaceOwnerAddress'),
            $request->request->get('burialPlaceOwnerBornAt'),
            $request->request->get('burialPlaceOwnerPlaceOfBirth'),
            $request->request->get('burialPlaceOwnerPassportSeries'),
            $request->request->get('burialPlaceOwnerPassportNumber'),
            $request->request->get('burialPlaceOwnerPassportIssuedAt'),
            $request->request->get('burialPlaceOwnerPassportIssuedBy'),
            $request->request->get('burialPlaceOwnerPassportDivisionCode'),
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
