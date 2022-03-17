<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\FuneralCompany\DeleteFuneralCompany;

use Cemetery\Registrar\Domain\Burial\BurialRepositoryInterface;
use Cemetery\Registrar\Domain\EventDispatcherInterface;
use Cemetery\Registrar\Domain\FuneralCompany\FuneralCompany;
use Cemetery\Registrar\Domain\FuneralCompany\FuneralCompanyId;
use Cemetery\Registrar\Domain\FuneralCompany\FuneralCompanyRepositoryInterface;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class DeleteFuneralCompany
{
    /**
     * @param FuneralCompanyRepositoryInterface $funeralCompanyRepo
     * @param BurialRepositoryInterface         $burialRepo
     * @param EventDispatcherInterface          $eventDispatcher
     */
    public function __construct(
        private FuneralCompanyRepositoryInterface $funeralCompanyRepo,
        private BurialRepositoryInterface         $burialRepo,
        private EventDispatcherInterface          $eventDispatcher,
    ) {}

    /**
     * @param DeleteFuneralCompanyRequest $request
     */
    public function execute(DeleteFuneralCompanyRequest $request): void
    {
        $funeralCompany = $this->getFuneralCompany($request->funeralCompanyId);
        $burialCount    = $this->burialRepo->countByFuneralCompanyId($funeralCompany->getId());
        if ($burialCount > 0) {
            throw new \RuntimeException(\sprintf(
                'Похоронная компания не может быть удалена, т.к. она связана с %d захоронениями.',
                $burialCount,
            ));
        }
        $this->funeralCompanyRepo->remove($funeralCompany);
        $this->eventDispatcher->dispatch(...$funeralCompany->releaseRecordedEvents());
    }

    /**
     * @param string $funeralCompanyId
     *
     * @return FuneralCompany
     *
     * @throws \RuntimeException when the funeral company does not exist
     */
    private function getFuneralCompany(string $funeralCompanyId): FuneralCompany
    {
        $funeralCompanyId = new FuneralCompanyId($funeralCompanyId);
        $funeralCompany   = $this->funeralCompanyRepo->findById($funeralCompanyId);
        if (!$funeralCompany) {
            throw new \RuntimeException('Похоронная компания с ID "%s" не найдена.');
        }

        return $funeralCompany;
    }
}
