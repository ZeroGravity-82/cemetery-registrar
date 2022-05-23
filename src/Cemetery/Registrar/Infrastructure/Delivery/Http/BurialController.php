<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Delivery\Http;

use Cemetery\Registrar\Application\Burial\BurialFetcher;
use Cemetery\Registrar\Application\Burial\CreateBurial\CreateBurialRequest;
use Cemetery\Registrar\Application\Burial\CreateBurial\CreateBurialService;
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
     * @param BurialFetcher       $burialFetcher
     * @param CreateBurialService $createBurialService
     */
    public function __construct(
        private readonly BurialFetcher       $burialFetcher,
        private readonly CreateBurialService $createBurialService,
    ) {}

    #[Route('/burial', name: 'burial_index', methods: 'GET')]
    public function index(): Response
    {
        $burialViewList = $this->burialFetcher->findAll(1);

        return $this->render('burial/index.html.twig', [
            'burialViewList' => $burialViewList,
        ]);
    }

    #[Route('/burial/new', name: 'burial_new', methods: 'POST')]
    public function new(Request $request): Response
    {
        $createBurialRequest = new CreateBurialRequest(
            $request->request->get('type') ?? null,
            $request->request->get('deceasedNaturalPersonId') ?? null,
            $request->request->get('deceasedNaturalPersonFullName') ?? null,
            $request->request->get('deceasedNaturalPersonBornAt') ?? null,
            $request->request->get('deceasedDiedAt') ?? null,
            $request->request->get('deceasedAge') !== null ? (int) $request->request->get('deceasedAge') : null,
            $request->request->get('deceasedDeathCertificateId') ?? null,
            $request->request->get('deceasedCauseOfDeath') ?? null,
            $request->request->get('customerId') ?? null,
            $request->request->get('customerType') ?? null,
            $request->request->get('customerNaturalPersonFullName') ?? null,
            $request->request->get('customerNaturalPersonPhone') ?? null,
            $request->request->get('customerNaturalPersonPhoneAdditional') ?? null,
            $request->request->get('customerNaturalPersonEmail') ?? null,
            $request->request->get('customerNaturalPersonAddress') ?? null,
            $request->request->get('customerNaturalPersonBornAt') ?? null,
            $request->request->get('customerNaturalPersonPlaceOfBirth') ?? null,
            $request->request->get('customerNaturalPersonPassportSeries') ?? null,
            $request->request->get('customerNaturalPersonPassportNumber') ?? null,
            $request->request->get('customerNaturalPersonPassportIssuedAt') ?? null,
            $request->request->get('customerNaturalPersonPassportIssuedBy') ?? null,
            $request->request->get('customerNaturalPersonPassportDivisionCode') ?? null,
            $request->request->get('customerSoleProprietorName') ?? null,
            $request->request->get('customerSoleProprietorInn') ?? null,
            $request->request->get('customerSoleProprietorOgrnip') ?? null,
            $request->request->get('customerSoleProprietorOkpo') ?? null,
            $request->request->get('customerSoleProprietorOkved') ?? null,
            $request->request->get('customerSoleProprietorRegistrationAddress') ?? null,
            $request->request->get('customerSoleProprietorActualLocationAddress') ?? null,
            $request->request->get('customerSoleProprietorBankDetailsBankName') ?? null,
            $request->request->get('customerSoleProprietorBankDetailsBik') ?? null,
            $request->request->get('customerSoleProprietorBankDetailsCorrespondentAccount') ?? null,
            $request->request->get('customerSoleProprietorBankDetailsCurrentAccount') ?? null,
            $request->request->get('customerSoleProprietorPhone') ?? null,
            $request->request->get('customerSoleProprietorPhoneAdditional') ?? null,
            $request->request->get('customerSoleProprietorFax') ?? null,
            $request->request->get('customerSoleProprietorEmail') ?? null,
            $request->request->get('customerSoleProprietorWebsite') ?? null,
            $request->request->get('customerJuristicPersonName') ?? null,
            $request->request->get('customerJuristicPersonInn') ?? null,
            $request->request->get('customerJuristicPersonKpp') ?? null,
            $request->request->get('customerJuristicPersonOgrn') ?? null,
            $request->request->get('customerJuristicPersonOkpo') ?? null,
            $request->request->get('customerJuristicPersonOkved') ?? null,
            $request->request->get('customerJuristicPersonLegalAddress') ?? null,
            $request->request->get('customerJuristicPersonPostalAddress') ?? null,
            $request->request->get('customerJuristicPersonBankDetailsBankName') ?? null,
            $request->request->get('customerJuristicPersonBankDetailsBik') ?? null,
            $request->request->get('customerJuristicPersonBankDetailsCorrespondentAccount') ?? null,
            $request->request->get('customerJuristicPersonBankDetailsCurrentAccount') ?? null,
            $request->request->get('customerJuristicPersonPhone') ?? null,
            $request->request->get('customerJuristicPersonPhoneAdditional') ?? null,
            $request->request->get('customerJuristicPersonFax') ?? null,
            $request->request->get('customerJuristicPersonGeneralDirector') ?? null,
            $request->request->get('customerJuristicPersonEmail') ?? null,
            $request->request->get('customerJuristicPersonWebsite') ?? null,
            $request->request->get('burialPlaceOwnerId') ?? null,
            $request->request->get('burialPlaceOwnerFullName') ?? null,
            $request->request->get('burialPlaceOwnerPhone') ?? null,
            $request->request->get('burialPlaceOwnerPhoneAdditional') ?? null,
            $request->request->get('burialPlaceOwnerEmail') ?? null,
            $request->request->get('burialPlaceOwnerAddress') ?? null,
            $request->request->get('burialPlaceOwnerBornAt') ?? null,
            $request->request->get('burialPlaceOwnerPlaceOfBirth') ?? null,
            $request->request->get('burialPlaceOwnerPassportSeries') ?? null,
            $request->request->get('burialPlaceOwnerPassportNumber') ?? null,
            $request->request->get('burialPlaceOwnerPassportIssuedAt') ?? null,
            $request->request->get('burialPlaceOwnerPassportIssuedBy') ?? null,
            $request->request->get('burialPlaceOwnerPassportDivisionCode') ?? null,
            $request->request->get('funeralCompanyId') ?? null,
            $request->request->get('funeralCompanyType') ?? null,
            $request->request->get('funeralCompanySoleProprietorName') ?? null,
            $request->request->get('funeralCompanySoleProprietorInn') ?? null,
            $request->request->get('funeralCompanySoleProprietorOgrnip') ?? null,
            $request->request->get('funeralCompanySoleProprietorOkpo') ?? null,
            $request->request->get('funeralCompanySoleProprietorOkved') ?? null,
            $request->request->get('funeralCompanySoleProprietorRegistrationAddress') ?? null,
            $request->request->get('funeralCompanySoleProprietorActualLocationAddress') ?? null,
            $request->request->get('funeralCompanySoleProprietorBankDetailsBankName') ?? null,
            $request->request->get('funeralCompanySoleProprietorBankDetailsBik') ?? null,
            $request->request->get('funeralCompanySoleProprietorBankDetailsCorrespondentAccount') ?? null,
            $request->request->get('funeralCompanySoleProprietorBankDetailsCurrentAccount') ?? null,
            $request->request->get('funeralCompanySoleProprietorPhone') ?? null,
            $request->request->get('funeralCompanySoleProprietorPhoneAdditional') ?? null,
            $request->request->get('funeralCompanySoleProprietorFax') ?? null,
            $request->request->get('funeralCompanySoleProprietorEmail') ?? null,
            $request->request->get('funeralCompanySoleProprietorWebsite') ?? null,
            $request->request->get('funeralCompanyJuristicPersonName') ?? null,
            $request->request->get('funeralCompanyJuristicPersonInn') ?? null,
            $request->request->get('funeralCompanyJuristicPersonKpp') ?? null,
            $request->request->get('funeralCompanyJuristicPersonOgrn') ?? null,
            $request->request->get('funeralCompanyJuristicPersonOkpo') ?? null,
            $request->request->get('funeralCompanyJuristicPersonOkved') ?? null,
            $request->request->get('funeralCompanyJuristicPersonLegalAddress') ?? null,
            $request->request->get('funeralCompanyJuristicPersonPostalAddress') ?? null,
            $request->request->get('funeralCompanyJuristicPersonBankDetailsBankName') ?? null,
            $request->request->get('funeralCompanyJuristicPersonBankDetailsBik') ?? null,
            $request->request->get('funeralCompanyJuristicPersonBankDetailsCorrespondentAccount') ?? null,
            $request->request->get('funeralCompanyJuristicPersonBankDetailsCurrentAccount') ?? null,
            $request->request->get('funeralCompanyJuristicPersonPhone') ?? null,
            $request->request->get('funeralCompanyJuristicPersonPhoneAdditional') ?? null,
            $request->request->get('funeralCompanyJuristicPersonFax') ?? null,
            $request->request->get('funeralCompanyJuristicPersonGeneralDirector') ?? null,
            $request->request->get('funeralCompanyJuristicPersonEmail') ?? null,
            $request->request->get('funeralCompanyJuristicPersonWebsite') ?? null,
            $request->request->get('burialChainId') ?? null,
            $request->request->get('burialPlaceId') ?? null,
            $request->request->get('burialPlaceType') ?? null,
            $request->request->get('burialPlaceGraveSiteCemeteryBlockId') ?? null,
            $request->request->get('burialPlaceGraveSiteRowInBlock') !== null ? (int) $request->request->get('burialPlaceGraveSiteRowInBlock') : null,
            $request->request->get('burialPlaceGraveSitePositionInRow') !== null ? (int) $request->request->get('burialPlaceGraveSitePositionInRow') : null,
            $request->request->get('burialPlaceGraveSiteSize') ?? null,
            $request->request->get('burialPlaceColumbariumNicheColumbariumId') ?? null,
            $request->request->get('burialPlaceColumbariumNicheRowInColumbarium') !== null ? (int) $request->request->get('burialPlaceColumbariumNicheRowInColumbarium') : null,
            $request->request->get('burialPlaceColumbariumNicheNicheNumber') ?? null,
            $request->request->get('burialPlaceMemorialTreeNumber') ?? null,
            $request->request->get('burialPlaceGeoPosition') ?? null,
//            $request->request->get('burialPlaceGeoPositionLatitude') ?? null,
//            $request->request->get('burialPlaceGeoPositionLongitude') ?? null,
//            $request->request->get('burialPlaceGeoPositionError') ?? null,
            $request->request->get('burialContainerType') ?? null,
            $request->request->get('burialContainerCoffinSize') !== null ? (int) $request->request->get('burialContainerCoffinSize') : null,
            $request->request->get('burialContainerCoffinShape') ?? null,
            $request->request->get('burialPlaceGraveSitePositionInRow') !== null,
            $request->request->get('buriedAt') ?? null,
        );
        dump($createBurialRequest);

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
        $createBurialRequest = new CreateBurialRequest(
            $request->request->get('type') ?? null,
            $request->request->get('deceasedNaturalPersonId') ?? null,
            $request->request->get('deceasedNaturalPersonFullName') ?? null,
            $request->request->get('deceasedNaturalPersonBornAt') ?? null,
            $request->request->get('deceasedDiedAt') ?? null,
            $request->request->get('deceasedAge') !== null ? (int) $request->request->get('deceasedAge') : null,
            $request->request->get('deceasedDeathCertificateId') ?? null,
            $request->request->get('deceasedCauseOfDeath') ?? null,
            $request->request->get('customerId') ?? null,
            $request->request->get('customerType') ?? null,
            $request->request->get('customerNaturalPersonFullName') ?? null,
            $request->request->get('customerNaturalPersonPhone') ?? null,
            $request->request->get('customerNaturalPersonPhoneAdditional') ?? null,
            $request->request->get('customerNaturalPersonEmail') ?? null,
            $request->request->get('customerNaturalPersonAddress') ?? null,
            $request->request->get('customerNaturalPersonBornAt') ?? null,
            $request->request->get('customerNaturalPersonPlaceOfBirth') ?? null,
            $request->request->get('customerNaturalPersonPassportSeries') ?? null,
            $request->request->get('customerNaturalPersonPassportNumber') ?? null,
            $request->request->get('customerNaturalPersonPassportIssuedAt') ?? null,
            $request->request->get('customerNaturalPersonPassportIssuedBy') ?? null,
            $request->request->get('customerNaturalPersonPassportDivisionCode') ?? null,
            $request->request->get('customerSoleProprietorName') ?? null,
            $request->request->get('customerSoleProprietorInn') ?? null,
            $request->request->get('customerSoleProprietorOgrnip') ?? null,
            $request->request->get('customerSoleProprietorOkpo') ?? null,
            $request->request->get('customerSoleProprietorOkved') ?? null,
            $request->request->get('customerSoleProprietorRegistrationAddress') ?? null,
            $request->request->get('customerSoleProprietorActualLocationAddress') ?? null,
            $request->request->get('customerSoleProprietorBankDetailsBankName') ?? null,
            $request->request->get('customerSoleProprietorBankDetailsBik') ?? null,
            $request->request->get('customerSoleProprietorBankDetailsCorrespondentAccount') ?? null,
            $request->request->get('customerSoleProprietorBankDetailsCurrentAccount') ?? null,
            $request->request->get('customerSoleProprietorPhone') ?? null,
            $request->request->get('customerSoleProprietorPhoneAdditional') ?? null,
            $request->request->get('customerSoleProprietorFax') ?? null,
            $request->request->get('customerSoleProprietorEmail') ?? null,
            $request->request->get('customerSoleProprietorWebsite') ?? null,
            $request->request->get('customerJuristicPersonName') ?? null,
            $request->request->get('customerJuristicPersonInn') ?? null,
            $request->request->get('customerJuristicPersonKpp') ?? null,
            $request->request->get('customerJuristicPersonOgrn') ?? null,
            $request->request->get('customerJuristicPersonOkpo') ?? null,
            $request->request->get('customerJuristicPersonOkved') ?? null,
            $request->request->get('customerJuristicPersonLegalAddress') ?? null,
            $request->request->get('customerJuristicPersonPostalAddress') ?? null,
            $request->request->get('customerJuristicPersonBankDetailsBankName') ?? null,
            $request->request->get('customerJuristicPersonBankDetailsBik') ?? null,
            $request->request->get('customerJuristicPersonBankDetailsCorrespondentAccount') ?? null,
            $request->request->get('customerJuristicPersonBankDetailsCurrentAccount') ?? null,
            $request->request->get('customerJuristicPersonPhone') ?? null,
            $request->request->get('customerJuristicPersonPhoneAdditional') ?? null,
            $request->request->get('customerJuristicPersonFax') ?? null,
            $request->request->get('customerJuristicPersonGeneralDirector') ?? null,
            $request->request->get('customerJuristicPersonEmail') ?? null,
            $request->request->get('customerJuristicPersonWebsite') ?? null,
            $request->request->get('burialPlaceOwnerId') ?? null,
            $request->request->get('burialPlaceOwnerFullName') ?? null,
            $request->request->get('burialPlaceOwnerPhone') ?? null,
            $request->request->get('burialPlaceOwnerPhoneAdditional') ?? null,
            $request->request->get('burialPlaceOwnerEmail') ?? null,
            $request->request->get('burialPlaceOwnerAddress') ?? null,
            $request->request->get('burialPlaceOwnerBornAt') ?? null,
            $request->request->get('burialPlaceOwnerPlaceOfBirth') ?? null,
            $request->request->get('burialPlaceOwnerPassportSeries') ?? null,
            $request->request->get('burialPlaceOwnerPassportNumber') ?? null,
            $request->request->get('burialPlaceOwnerPassportIssuedAt') ?? null,
            $request->request->get('burialPlaceOwnerPassportIssuedBy') ?? null,
            $request->request->get('burialPlaceOwnerPassportDivisionCode') ?? null,
            $request->request->get('funeralCompanyId') ?? null,
            $request->request->get('funeralCompanyType') ?? null,
            $request->request->get('funeralCompanySoleProprietorName') ?? null,
            $request->request->get('funeralCompanySoleProprietorInn') ?? null,
            $request->request->get('funeralCompanySoleProprietorOgrnip') ?? null,
            $request->request->get('funeralCompanySoleProprietorOkpo') ?? null,
            $request->request->get('funeralCompanySoleProprietorOkved') ?? null,
            $request->request->get('funeralCompanySoleProprietorRegistrationAddress') ?? null,
            $request->request->get('funeralCompanySoleProprietorActualLocationAddress') ?? null,
            $request->request->get('funeralCompanySoleProprietorBankDetailsBankName') ?? null,
            $request->request->get('funeralCompanySoleProprietorBankDetailsBik') ?? null,
            $request->request->get('funeralCompanySoleProprietorBankDetailsCorrespondentAccount') ?? null,
            $request->request->get('funeralCompanySoleProprietorBankDetailsCurrentAccount') ?? null,
            $request->request->get('funeralCompanySoleProprietorPhone') ?? null,
            $request->request->get('funeralCompanySoleProprietorPhoneAdditional') ?? null,
            $request->request->get('funeralCompanySoleProprietorFax') ?? null,
            $request->request->get('funeralCompanySoleProprietorEmail') ?? null,
            $request->request->get('funeralCompanySoleProprietorWebsite') ?? null,
            $request->request->get('funeralCompanyJuristicPersonName') ?? null,
            $request->request->get('funeralCompanyJuristicPersonInn') ?? null,
            $request->request->get('funeralCompanyJuristicPersonKpp') ?? null,
            $request->request->get('funeralCompanyJuristicPersonOgrn') ?? null,
            $request->request->get('funeralCompanyJuristicPersonOkpo') ?? null,
            $request->request->get('funeralCompanyJuristicPersonOkved') ?? null,
            $request->request->get('funeralCompanyJuristicPersonLegalAddress') ?? null,
            $request->request->get('funeralCompanyJuristicPersonPostalAddress') ?? null,
            $request->request->get('funeralCompanyJuristicPersonBankDetailsBankName') ?? null,
            $request->request->get('funeralCompanyJuristicPersonBankDetailsBik') ?? null,
            $request->request->get('funeralCompanyJuristicPersonBankDetailsCorrespondentAccount') ?? null,
            $request->request->get('funeralCompanyJuristicPersonBankDetailsCurrentAccount') ?? null,
            $request->request->get('funeralCompanyJuristicPersonPhone') ?? null,
            $request->request->get('funeralCompanyJuristicPersonPhoneAdditional') ?? null,
            $request->request->get('funeralCompanyJuristicPersonFax') ?? null,
            $request->request->get('funeralCompanyJuristicPersonGeneralDirector') ?? null,
            $request->request->get('funeralCompanyJuristicPersonEmail') ?? null,
            $request->request->get('funeralCompanyJuristicPersonWebsite') ?? null,
            $request->request->get('burialChainId') ?? null,
            $request->request->get('burialPlaceId') ?? null,
            $request->request->get('burialPlaceType') ?? null,
            $request->request->get('burialPlaceGraveSiteCemeteryBlockId') ?? null,
            $request->request->get('burialPlaceGraveSiteRowInBlock') !== null ? (int) $request->request->get('burialPlaceGraveSiteRowInBlock') : null,
            $request->request->get('burialPlaceGraveSitePositionInRow') !== null ? (int) $request->request->get('burialPlaceGraveSitePositionInRow') : null,
            $request->request->get('burialPlaceGraveSiteSize') ?? null,
            $request->request->get('burialPlaceColumbariumNicheColumbariumId') ?? null,
            $request->request->get('burialPlaceColumbariumNicheRowInColumbarium') !== null ? (int) $request->request->get('burialPlaceColumbariumNicheRowInColumbarium') : null,
            $request->request->get('burialPlaceColumbariumNicheNicheNumber') ?? null,
            $request->request->get('burialPlaceMemorialTreeNumber') ?? null,
            $request->request->get('burialPlaceGeoPosition') ?? null,
//            $request->request->get('burialPlaceGeoPositionLatitude') ?? null,
//            $request->request->get('burialPlaceGeoPositionLongitude') ?? null,
//            $request->request->get('burialPlaceGeoPositionError') ?? null,
            $request->request->get('burialContainerType') ?? null,
            $request->request->get('burialContainerCoffinSize') !== null ? (int) $request->request->get('burialContainerCoffinSize') : null,
            $request->request->get('burialContainerCoffinShape') ?? null,
            $request->request->get('burialPlaceGraveSitePositionInRow') !== null,
            $request->request->get('buriedAt') ?? null,
        );
        dump($createBurialRequest);

        $this->createBurialService->execute($createBurialRequest);

        return $this->redirectToRoute('burial_index');
    }

}
