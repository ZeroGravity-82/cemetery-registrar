<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\JuristicPerson;

use Cemetery\Registrar\Domain\Organization\JuristicPerson\JuristicPersonRemover;
use Cemetery\Registrar\Domain\Organization\JuristicPerson\JuristicPersonRepositoryInterface;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class RemoveAbstractJuristicPersonService extends AbstractJuristicPersonService
{
    /**
     * @param JuristicPersonRemover             $juristicPersonRemover
     * @param JuristicPersonRepositoryInterface $juristicPersonRepo
     */
    public function __construct(
        private JuristicPersonRemover     $juristicPersonRemover,
        JuristicPersonRepositoryInterface $juristicPersonRepo,
    ) {
        parent::__construct($juristicPersonRepo);
    }

    /**
     * @param RemoveJuristicPersonRequest $request
     */
    public function execute(RemoveJuristicPersonRequest $request): void
    {
        $juristicPerson = $this->getJuristicPerson($request->id);
        $this->juristicPersonRemover->remove($juristicPerson);
    }
}
