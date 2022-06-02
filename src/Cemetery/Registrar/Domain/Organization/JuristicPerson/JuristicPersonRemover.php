<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Organization\JuristicPerson;

use Cemetery\Registrar\Domain\Burial\BurialRepository;
use Cemetery\Registrar\Domain\Burial\CustomerId;
use Cemetery\Registrar\Domain\EventDispatcher;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class JuristicPersonRemover
{
    /**
     * @param BurialRepository         $burialRepo
     * @param JuristicPersonRepository $juristicPersonRepo
     * @param EventDispatcher          $eventDispatcher
     */
    public function __construct(
        private readonly BurialRepository         $burialRepo,
        private readonly JuristicPersonRepository $juristicPersonRepo,
        private readonly EventDispatcher          $eventDispatcher,
    ) {}

    /**
     * @param JuristicPerson $juristicPerson
     */
    public function remove(JuristicPerson $juristicPerson): void
    {
        $burialCount = $this->burialRepo->countByFuneralCompanyId(new FuneralCompanyId($juristicPerson->id()));
        if ($burialCount > 0) {
            throw new \RuntimeException(\sprintf(
                'Юридическое лицо не может быть удалено, т.к. оно указано как похоронная фирма для %d захоронений.',
                $burialCount,
            ));
        }
        $burialCount = $this->burialRepo->countByCustomerId(new CustomerId($juristicPerson->id()));
        if ($burialCount > 0) {
            throw new \RuntimeException(\sprintf(
                'Юридическое лицо не может быть удалено, т.к. оно указано как заказчик для %d захоронений.',
                $burialCount,
            ));
        }
        $this->juristicPersonRepo->remove($juristicPerson);
        $this->eventDispatcher->dispatch(new JuristicPersonRemoved($juristicPerson->id()));
    }
}
