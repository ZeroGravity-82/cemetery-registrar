<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\FuneralCompany;

use Cemetery\Registrar\Domain\Burial\BurialRepositoryInterface;
use Cemetery\Registrar\Domain\EventDispatcherInterface;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class FuneralCompanyDeleter
{
    /**
     * @param BurialRepositoryInterface         $burialRepo
     * @param FuneralCompanyRepositoryInterface $funeralCompanyRepo
     * @param EventDispatcherInterface          $eventDispatcher
     */
    public function __construct(
        private BurialRepositoryInterface         $burialRepo,
        private FuneralCompanyRepositoryInterface $funeralCompanyRepo,
        private EventDispatcherInterface          $eventDispatcher,
    ) {}

    /**
     * @param FuneralCompany $funeralCompany
     */
    public function delete(FuneralCompany $funeralCompany): void
    {
        $burialCount = $this->burialRepo->countByFuneralCompanyId($funeralCompany->getId());
        if ($burialCount > 0) {
            throw new \RuntimeException(\sprintf(
                'Похоронная компания не может быть удалена, т.к. она связана с %d захоронениями.',
                $burialCount,
            ));
        }
        $funeralCompany->delete();
        $this->funeralCompanyRepo->save($funeralCompany);
        $this->eventDispatcher->dispatch(new FuneralCompanyDeleted($funeralCompany->getId()));
    }
}
