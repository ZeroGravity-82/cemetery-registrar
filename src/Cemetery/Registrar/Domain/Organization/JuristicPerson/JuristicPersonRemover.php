<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Organization\JuristicPerson;

use Cemetery\Registrar\Domain\Burial\BurialRepositoryInterface;
use Cemetery\Registrar\Domain\Burial\CustomerId;
use Cemetery\Registrar\Domain\Burial\FuneralCompanyId;
use Cemetery\Registrar\Domain\EventDispatcherInterface;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class JuristicPersonRemover
{
    /**
     * @param BurialRepositoryInterface         $burialRepo
     * @param JuristicPersonRepositoryInterface $juristicPersonRepo
     * @param EventDispatcherInterface          $eventDispatcher
     */
    public function __construct(
        private readonly BurialRepositoryInterface         $burialRepo,
        private readonly JuristicPersonRepositoryInterface $juristicPersonRepo,
        private readonly EventDispatcherInterface          $eventDispatcher,
    ) {}

    /**
     * @param JuristicPerson $juristicPerson
     */
    public function remove(JuristicPerson $juristicPerson): void
    {
        $burialCount = $this->burialRepo->countByFuneralCompanyId(new FuneralCompanyId($juristicPerson->getId()));
        if ($burialCount > 0) {
            throw new \RuntimeException(\sprintf(
                'Юридическое лицо не может быть удалено, т.к. оно указано как похоронная фирма для %d захоронений.',
                $burialCount,
            ));
        }
        $burialCount = $this->burialRepo->countByCustomerId(new CustomerId($juristicPerson->getId()));
        if ($burialCount > 0) {
            throw new \RuntimeException(\sprintf(
                'Юридическое лицо не может быть удалено, т.к. оно указано как заказчик для %d захоронений.',
                $burialCount,
            ));
        }
        $this->juristicPersonRepo->remove($juristicPerson);
        $this->eventDispatcher->dispatch(new JuristicPersonRemoved($juristicPerson->getId()));
    }
}
