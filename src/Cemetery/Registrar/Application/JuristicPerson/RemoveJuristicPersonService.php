<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\JuristicPerson;

use Cemetery\Registrar\Domain\Organization\JuristicPerson\JuristicPersonRemover;
use Cemetery\Registrar\Domain\Organization\JuristicPerson\JuristicPersonRepository;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class RemoveJuristicPersonService extends JuristicPersonService
{
    /**
     * @param JuristicPersonRemover    $juristicPersonRemover
     * @param JuristicPersonRepository $juristicPersonRepo
     */
    public function __construct(
        private readonly JuristicPersonRemover $juristicPersonRemover,
        JuristicPersonRepository               $juristicPersonRepo,
    ) {
        parent::__construct($juristicPersonRepo);
    }

    /**
     * @param RemoveJuristicPersonRequest $request
     */
    public function execute($request): void
    {
        $juristicPerson = $this->getJuristicPerson($request->id);
        $this->juristicPersonRemover->remove($juristicPerson);
    }
}
