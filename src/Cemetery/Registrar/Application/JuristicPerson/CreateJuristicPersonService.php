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
final class CreateJuristicPersonService extends JuristicPersonService
{
    /**
     * @param JuristicPersonFactory             $juristicPersonFactory
     * @param EventDispatcherInterface          $eventDispatcher
     * @param JuristicPersonRepositoryInterface $juristicPersonRepo
     */
    public function __construct(
        private JuristicPersonFactory     $juristicPersonFactory,
        private EventDispatcherInterface  $eventDispatcher,
        JuristicPersonRepositoryInterface $juristicPersonRepo,
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
            $request->juristicPersonName,
            $request->juristicPersonInn,
            $request->juristicPersonKpp,
            $request->juristicPersonOgrn,
            $request->juristicPersonOkpo,
            $request->juristicPersonOkved,
            $request->juristicPersonLegalAddress,
            $request->juristicPersonPostalAddress,
            $request->juristicPersonBankName,
            $request->juristicPersonBik,
            $request->juristicPersonCorrespondentAccount,
            $request->juristicPersonCurrentAccount,
            $request->juristicPersonPhone,
            $request->juristicPersonPhoneAdditional,
            $request->juristicPersonFax,
            $request->juristicPersonGeneralDirector,
            $request->juristicPersonEmail,
            $request->juristicPersonWebsite,
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
