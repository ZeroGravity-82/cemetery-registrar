<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\JuristicPerson\Command\CreateJuristicPerson;

use Cemetery\Registrar\Application\ApplicationRequest;
use Cemetery\Registrar\Application\ApplicationSuccessResponse;
use Cemetery\Registrar\Application\JuristicPerson\Command\JuristicPersonService;
use Cemetery\Registrar\Domain\Model\EventDispatcher;
use Cemetery\Registrar\Domain\Model\Exception;
use Cemetery\Registrar\Domain\Model\Organization\JuristicPerson\JuristicPersonCreated;
use Cemetery\Registrar\Domain\Model\Organization\JuristicPerson\JuristicPersonFactory;
use Cemetery\Registrar\Domain\Model\Organization\JuristicPerson\JuristicPersonRepositoryInterface;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class CreateJuristicPersonService extends JuristicPersonService
{
    public function __construct(
        CreateJuristicPersonRequestValidator $requestValidator,
        JuristicPersonRepositoryInterface    $juristicPersonRepo,
        EventDispatcher                      $eventDispatcher,
        private JuristicPersonFactory        $juristicPersonFactory,
    ) {
        parent::__construct($requestValidator, $juristicPersonRepo, $eventDispatcher);
    }

    /**
     * @throws Exception  when there was any issue within the domain
     * @throws \Throwable when any error occurred while processing the request
     */
    public function execute(ApplicationRequest $request): ApplicationSuccessResponse
    {
        /** @var CreateJuristicPersonRequest $request */
        $juristicPerson = $this->juristicPersonFactory->create(
            $request->name,
            $request->inn,
            $request->kpp,
            $request->ogrn,
            $request->okpo,
            $request->okved,
            $request->legalAddress,
            $request->postalAddress,
            $request->bankDetailsBankName,
            $request->bankDetailsBik,
            $request->bankDetailsCorrespondentAccount,
            $request->bankDetailsCurrentAccount,
            $request->phone,
            $request->phoneAdditional,
            $request->fax,
            $request->generalDirector,
            $request->email,
            $request->website,
        );
        $this->juristicPersonRepo->save($juristicPerson);
        $this->eventDispatcher->dispatch(new JuristicPersonCreated(
            $juristicPerson->id(),
            $juristicPerson->name(),
            $juristicPerson->inn(),
        ));

        return new CreateJuristicPersonResponse(
            $juristicPerson->id()->value(),
        );
    }

    protected function supportedRequestClassName(): string
    {
        return CreateJuristicPersonRequest::class;
    }
}
