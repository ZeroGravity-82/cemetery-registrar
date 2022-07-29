<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\CauseOfDeath\Command;

use Cemetery\Registrar\Application\ApplicationService;
use Cemetery\Registrar\Domain\Model\CauseOfDeath\CauseOfDeath;
use Cemetery\Registrar\Domain\Model\CauseOfDeath\CauseOfDeathId;
use Cemetery\Registrar\Domain\Model\CauseOfDeath\CauseOfDeathRepository;
use Cemetery\Registrar\Domain\Model\EventDispatcher;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
abstract class CauseOfDeathService extends ApplicationService
{
    public function __construct(
        protected readonly CauseOfDeathRepository $causeOfDeathRepo,
        protected readonly EventDispatcher        $eventDispatcher,
    ) {}

    /**
     * @param string $id
     *
     * @return CauseOfDeath
     *
     * @throws \RuntimeException when the cause of death is not found
     */
    protected function getCauseOfDeath(string $id): CauseOfDeath
    {
        $id = new CauseOfDeathId($id);
        /** @var CauseOfDeath $causeOfDeath */
        $causeOfDeath = $this->causeOfDeathRepo->findById($id);
        if ($causeOfDeath === null) {
            throw new \RuntimeException(\sprintf('Причина смерти с ID "%s" не найдена.', $id->value()));
        }

        return $causeOfDeath;
    }
}
