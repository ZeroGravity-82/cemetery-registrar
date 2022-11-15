<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\CauseOfDeath\Command;

use Cemetery\Registrar\Application\AbstractApplicationService;
use Cemetery\Registrar\Application\CauseOfDeath\AbstractCauseOfDeathRequestValidator;
use Cemetery\Registrar\Domain\Model\CauseOfDeath\CauseOfDeath;
use Cemetery\Registrar\Domain\Model\CauseOfDeath\CauseOfDeathId;
use Cemetery\Registrar\Domain\Model\CauseOfDeath\CauseOfDeathRepositoryInterface;
use Cemetery\Registrar\Domain\Model\EventDispatcher;
use Cemetery\Registrar\Domain\Model\Exception;
use Cemetery\Registrar\Domain\Model\NotFoundException;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
abstract class AbstractCauseOfDeathService extends AbstractApplicationService
{
    public function __construct(
        AbstractCauseOfDeathRequestValidator      $requestValidator,
        protected CauseOfDeathRepositoryInterface $causeOfDeathRepo,
        protected EventDispatcher                 $eventDispatcher,
    ) {
        parent::__construct($requestValidator);
    }

    /**
     * @throws Exception         when the ID is invalid
     * @throws NotFoundException when the cause of death is not found
     */
    protected function getCauseOfDeath(string $id): CauseOfDeath
    {
        $id = new CauseOfDeathId($id);
        /** @var CauseOfDeath $causeOfDeath */
        $causeOfDeath = $this->causeOfDeathRepo->findById($id);
        if ($causeOfDeath === null) {
            throw new NotFoundException(\sprintf('Причина смерти с ID "%s" не найдена.', $id->value()));
        }

        return $causeOfDeath;
    }
}
