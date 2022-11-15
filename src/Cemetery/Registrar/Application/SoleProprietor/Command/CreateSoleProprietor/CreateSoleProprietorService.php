<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\SoleProprietor\Command\CreateSoleProprietor;

use Cemetery\Registrar\Application\ApplicationRequest;
use Cemetery\Registrar\Application\ApplicationSuccessResponse;
use Cemetery\Registrar\Application\SoleProprietor\Command\SoleProprietorService;
use Cemetery\Registrar\Domain\Model\EventDispatcher;
use Cemetery\Registrar\Domain\Model\Exception;
use Cemetery\Registrar\Domain\Model\Organization\SoleProprietor\SoleProprietorCreated;
use Cemetery\Registrar\Domain\Model\Organization\SoleProprietor\SoleProprietorFactory;
use Cemetery\Registrar\Domain\Model\Organization\SoleProprietor\SoleProprietorRepositoryInterface;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class CreateSoleProprietorService extends SoleProprietorService
{
    public function __construct(
        CreateSoleProprietorRequestValidator $requestValidator,
        SoleProprietorRepositoryInterface    $soleProprietorRepo,
        EventDispatcher                      $eventDispatcher,
        private SoleProprietorFactory        $juristicPersonFactory,
    ) {
        parent::__construct($requestValidator, $soleProprietorRepo, $eventDispatcher);
    }

    /**
     * @throws Exception  when there was any issue within the domain
     * @throws \Throwable when any error occurred while processing the request
     */
    public function execute(ApplicationRequest $request): ApplicationSuccessResponse
    {
        /** @var CreateSoleProprietorRequest $request */
        $juristicPerson = $this->juristicPersonFactory->create(
            $request->name,
            $request->inn,
            $request->ogrnip,
            $request->okpo,
            $request->okved,
            $request->registrationAddress,
            $request->actualLocationAddress,
            $request->bankDetailsBankName,
            $request->bankDetailsBik,
            $request->bankDetailsCorrespondentAccount,
            $request->bankDetailsCurrentAccount,
            $request->phone,
            $request->phoneAdditional,
            $request->fax,
            $request->email,
            $request->website,
        );
        $this->soleProprietorRepo->save($juristicPerson);
        $this->eventDispatcher->dispatch(new SoleProprietorCreated(
            $juristicPerson->id(),
            $juristicPerson->name(),
            $juristicPerson->inn(),
        ));

        return new CreateSoleProprietorResponse(
            $juristicPerson->id()->value(),
        );
    }

    protected function supportedRequestClassName(): string
    {
        return CreateSoleProprietorRequest::class;
    }
}
