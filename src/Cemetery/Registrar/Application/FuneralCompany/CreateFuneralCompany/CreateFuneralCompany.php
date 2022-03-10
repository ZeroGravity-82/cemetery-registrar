<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\FuneralCompany\CreateFuneralCompany;

use Cemetery\Registrar\Domain\FuneralCompany\FuneralCompanyFactory;
use Cemetery\Registrar\Domain\FuneralCompany\FuneralCompanyRepositoryInterface;
use Cemetery\Registrar\Domain\Organization\JuristicPerson\JuristicPersonFactory;
use Cemetery\Registrar\Domain\Organization\JuristicPerson\JuristicPersonId;
use Cemetery\Registrar\Domain\Organization\JuristicPerson\JuristicPersonRepositoryInterface;
use Cemetery\Registrar\Domain\Organization\OrganizationId;
use Cemetery\Registrar\Domain\Organization\OrganizationType;
use Cemetery\Registrar\Domain\Organization\SoleProprietor\SoleProprietorFactory;
use Cemetery\Registrar\Domain\Organization\SoleProprietor\SoleProprietorId;
use Cemetery\Registrar\Domain\Organization\SoleProprietor\SoleProprietorRepositoryInterface;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class CreateFuneralCompany
{
    /**
     * @param SoleProprietorRepositoryInterface $soleProprietorRepository
     * @param SoleProprietorFactory             $soleProprietorFactory
     * @param JuristicPersonRepositoryInterface $juristicPersonRepository
     * @param JuristicPersonFactory             $juristicPersonFactory
     * @param FuneralCompanyRepositoryInterface $funeralCompanyRepo
     * @param FuneralCompanyFactory             $funeralCompanyFactory
     */
    public function __construct(
        private SoleProprietorRepositoryInterface $soleProprietorRepository,
        private SoleProprietorFactory             $soleProprietorFactory,
        private JuristicPersonRepositoryInterface $juristicPersonRepository,
        private JuristicPersonFactory             $juristicPersonFactory,
        private FuneralCompanyRepositoryInterface $funeralCompanyRepo,
        private FuneralCompanyFactory             $funeralCompanyFactory,
    ) {}

    /**
     * @param CreateFuneralCompanyRequest $request
     *
     * @return CreateFuneralCompanyResponse
     */
    public function execute(CreateFuneralCompanyRequest $request): CreateFuneralCompanyResponse
    {
        $this->assertValidFuneralCompanyData($request);
        if ($request->funeralCompanyOrganizationId) {
            $organizationType = new OrganizationType($request->funeralCompanyOrganizationType);
            $organizationId   = new OrganizationId($request->funeralCompanyOrganizationId, $organizationType);
            $this->assertOrganizationExists($organizationId);
        } else {
            $organizationId = $this->createOrganization($request);
        }
        $funeralCompany = $this->funeralCompanyFactory->create($organizationId, $request->funeralCompanyNote);
        $this->funeralCompanyRepo->save($funeralCompany);

        return new CreateFuneralCompanyResponse((string) $funeralCompany->getId());
    }

    /**
     * @param CreateFuneralCompanyRequest $request
     *
     * @throws \RuntimeException when the organization type is not provided
     */
    private function assertValidFuneralCompanyData(CreateFuneralCompanyRequest $request): void
    {
        if (!$request->funeralCompanyOrganizationType) {
            throw new \RuntimeException('Тип организации не задан.');
        }
    }

    /**
     * @param OrganizationId $organizationId
     *
     * @throws \RuntimeException when the organization does not exist
     */
    private function assertOrganizationExists(OrganizationId $organizationId): void
    {
        switch (true) {
            case $organizationId->getType()->isSoleProprietor():
                $soleProprietorId = new SoleProprietorId($organizationId->getValue());
                $organization     = $this->soleProprietorRepository->findById($soleProprietorId);
                break;
            case $organizationId->getType()->isJuristicPerson():
                $juristicPersonId = new JuristicPersonId($organizationId->getValue());
                $organization     = $this->juristicPersonRepository->findById($juristicPersonId);
                break;
        }
        if (!isset($organization)) {
            throw new \LogicException(\sprintf(
                'Организация с типом "%s" и ID "%s" не найдена.',
                $organizationId->getType(),
                $organizationId->getValue()
            ));
        }
    }

    /**
     * @param CreateFuneralCompanyRequest $request
     *
     * @return OrganizationId
     */
    private function createOrganization(CreateFuneralCompanyRequest $request): OrganizationId
    {
        $organizationType = new OrganizationType($request->funeralCompanyOrganizationType);
        switch (true) {
            case $organizationType->isSoleProprietor():
                $soleProprietor = $this->soleProprietorFactory->create(
                    $request->funeralCompanySoleProprietorName,
                    $request->funeralCompanySoleProprietorInn,
                    $request->funeralCompanySoleProprietorOgrnip,
                    $request->funeralCompanySoleProprietorOkpo,
                    $request->funeralCompanySoleProprietorOkved,
                    $request->funeralCompanySoleProprietorRegistrationAddress,
                    $request->funeralCompanySoleProprietorActualLocationAddress,
                    $request->funeralCompanySoleProprietorBankName,
                    $request->funeralCompanySoleProprietorBik,
                    $request->funeralCompanySoleProprietorCorrespondentAccount,
                    $request->funeralCompanySoleProprietorCurrentAccount,
                    $request->funeralCompanySoleProprietorPhone,
                    $request->funeralCompanySoleProprietorPhoneAdditional,
                    $request->funeralCompanySoleProprietorFax,
                    $request->funeralCompanySoleProprietorEmail,
                    $request->funeralCompanySoleProprietorWebsite,
                );
                $organizationId = new OrganizationId((string) $soleProprietor->getId(), $organizationType);
                $this->soleProprietorRepository->save($soleProprietor);
                break;
            case $organizationType->isJuristicPerson():
                $juristicPerson = $this->juristicPersonFactory->create(
                    $request->funeralCompanyJuristicPersonName,
                    $request->funeralCompanyJuristicPersonInn,
                    $request->funeralCompanyJuristicPersonKpp,
                    $request->funeralCompanyJuristicPersonOgrn,
                    $request->funeralCompanyJuristicPersonOkpo,
                    $request->funeralCompanyJuristicPersonOkved,
                    $request->funeralCompanyJuristicPersonLegalAddress,
                    $request->funeralCompanyJuristicPersonPostalAddress,
                    $request->funeralCompanyJuristicPersonBankName,
                    $request->funeralCompanyJuristicPersonBik,
                    $request->funeralCompanyJuristicPersonCorrespondentAccount,
                    $request->funeralCompanyJuristicPersonCurrentAccount,
                    $request->funeralCompanyJuristicPersonPhone,
                    $request->funeralCompanyJuristicPersonPhoneAdditional,
                    $request->funeralCompanyJuristicPersonFax,
                    $request->funeralCompanyJuristicPersonGeneralDirector,
                    $request->funeralCompanyJuristicPersonEmail,
                    $request->funeralCompanyJuristicPersonWebsite,
                );
                $organizationId = new OrganizationId((string) $juristicPerson->getId(), $organizationType);
                $this->juristicPersonRepository->save($juristicPerson);
                break;
        }

        if (!isset($organizationId)) {
            throw new \LogicException(\sprintf('Организация с типом "%s" не была создана.', $organizationType));
        }

        return $organizationId;
    }
}
