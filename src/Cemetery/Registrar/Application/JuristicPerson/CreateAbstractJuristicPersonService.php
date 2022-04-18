<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\JuristicPerson;

use Cemetery\Registrar\Domain\EventDispatcherInterface;
use Cemetery\Registrar\Domain\Organization\JuristicPerson\JuristicPersonCreated;
use Cemetery\Registrar\Domain\Organization\JuristicPerson\JuristicPersonFactory;
use Cemetery\Registrar\Domain\Organization\JuristicPerson\JuristicPersonRepositoryInterface;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class CreateAbstractJuristicPersonService extends AbstractJuristicPersonService
{
    /**
     * @param JuristicPersonFactory             $juristicPersonFactory
     * @param EventDispatcherInterface          $eventDispatcher
     * @param JuristicPersonRepositoryInterface $juristicPersonRepo
     */
    public function __construct(
        private readonly JuristicPersonFactory    $juristicPersonFactory,
        private readonly EventDispatcherInterface $eventDispatcher,
        JuristicPersonRepositoryInterface         $juristicPersonRepo,
    ) {
        parent::__construct($juristicPersonRepo);
    }

    /**
     * @param CreateJuristicPersonRequest $request
     *
     * @return CreateJuristicPersonResponse
     */
    public function execute(CreateJuristicPersonRequest $request): CreateJuristicPersonResponse
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
            $request->bankName,
            $request->bik,
            $request->correspondentAccount,
            $request->currentAccount,
            $request->phone,
            $request->phoneAdditional,
            $request->fax,
            $request->generalDirector,
            $request->email,
            $request->website,
        );
        $this->juristicPersonRepo->save($juristicPerson);
        $this->eventDispatcher->dispatch(new JuristicPersonCreated(
            $juristicPerson->getId(),
            $juristicPerson->getName(),
            $juristicPerson->getInn(),
        ));

        return new CreateJuristicPersonResponse((string) $juristicPerson->getId());
    }
}
