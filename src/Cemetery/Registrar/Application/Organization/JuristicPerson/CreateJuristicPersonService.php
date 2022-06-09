<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\Organization\JuristicPerson;

use Cemetery\Registrar\Domain\EventDispatcher;
use Cemetery\Registrar\Domain\Organization\JuristicPerson\JuristicPersonCreated;
use Cemetery\Registrar\Domain\Organization\JuristicPerson\JuristicPersonFactory;
use Cemetery\Registrar\Domain\Organization\JuristicPerson\JuristicPersonRepository;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class CreateJuristicPersonService extends JuristicPersonService
{
    /**
     * @param JuristicPersonFactory    $juristicPersonFactory
     * @param EventDispatcher          $eventDispatcher
     * @param JuristicPersonRepository $juristicPersonRepo
     */
    public function __construct(
        private readonly JuristicPersonFactory $juristicPersonFactory,
        private readonly EventDispatcher       $eventDispatcher,
        JuristicPersonRepository               $juristicPersonRepo,
    ) {
        parent::__construct($juristicPersonRepo);
    }

    /**
     * @param CreateJuristicPersonRequest $request
     *
     * @return CreateJuristicPersonResponse
     */
    public function execute($request): CreateJuristicPersonResponse
    {
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