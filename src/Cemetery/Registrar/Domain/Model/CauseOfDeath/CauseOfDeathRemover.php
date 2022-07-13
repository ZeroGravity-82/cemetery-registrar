<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Model\CauseOfDeath;

use Cemetery\Registrar\Domain\Model\NaturalPerson\NaturalPersonRepository;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class CauseOfDeathRemover
{
    /**
     * @param NaturalPersonRepository $naturalPersonRepo
     * @param CauseOfDeathRepository  $causeOfDeathRepo
     */
    public function __construct(
        private readonly NaturalPersonRepository $naturalPersonRepo,
        private readonly CauseOfDeathRepository  $causeOfDeathRepo,
    ) {}

    /**
     * @param CauseOfDeath $causeOfDeath
     */
    public function remove(CauseOfDeath $causeOfDeath): void
    {
        $naturalPersonCount = $this->naturalPersonRepo->countByCauseOfDeathId($causeOfDeath->id());
        if ($naturalPersonCount > 0) {
            throw new \RuntimeException(\sprintf(
                'Причина смерти не может быть удалена, т.к. она указана для %d умерших.',
                $naturalPersonCount,
            ));
        }
        $this->causeOfDeathRepo->remove($causeOfDeath);
    }
}
