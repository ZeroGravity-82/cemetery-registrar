<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\Command\JuristicPerson\CreateJuristicPerson;

use Cemetery\Registrar\Application\ApplicationService;
use Cemetery\Registrar\Domain\Model\EventDispatcher;
use Cemetery\Registrar\Domain\Model\Organization\JuristicPerson\JuristicPersonCreated;
use Cemetery\Registrar\Domain\Model\Organization\JuristicPerson\JuristicPersonFactory;
use Cemetery\Registrar\Domain\Model\Organization\JuristicPerson\JuristicPersonRepository;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class CreateJuristicPersonService extends ApplicationService
{
    /**
     * @param JuristicPersonRepository $juristicPersonRepo
     * @param JuristicPersonFactory    $juristicPersonFactory
     * @param EventDispatcher          $eventDispatcher
     */
    public function __construct(
        private readonly JuristicPersonRepository $juristicPersonRepo,
        private readonly JuristicPersonFactory    $juristicPersonFactory,
        private readonly EventDispatcher          $eventDispatcher,
    ) {}

    /**
     * {@inheritdoc}
     */
    public function supportedRequestClassName(): string
    {
        return CreateJuristicPersonRequest::class;
    }

    /**
     * @param CreateJuristicPersonRequest $request
     *
     * @return CreateJuristicPersonResponse
     */
    public function execute($request): CreateJuristicPersonResponse
    {
        $this->assertSupportedRequestClass($request);

        // TODO add uniqueness check
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

        return new CreateJuristicPersonResponse((string) $juristicPerson->id());
    }
}
