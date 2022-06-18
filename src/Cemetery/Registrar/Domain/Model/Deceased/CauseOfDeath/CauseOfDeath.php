<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Model\Deceased\CauseOfDeath;

use Cemetery\Registrar\Domain\Model\AggregateRoot;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class CauseOfDeath extends AggregateRoot
{
    /**
     * @param CauseOfDeathId          $id
     * @param CauseOfDeathDescription $description
     */
    public function __construct(
        private readonly CauseOfDeathId $id,
        private CauseOfDeathDescription $description,
    ) {
        parent::__construct();
    }

    /**
     * @return CauseOfDeathId
     */
    public function id(): CauseOfDeathId
    {
        return $this->id;
    }
    /**
     * @return CauseOfDeathDescription
     */
    public function description(): CauseOfDeathDescription
    {
        return $this->description;
    }

    /**
     * @param CauseOfDeathDescription $description
     *
     * @return $this
     */
    public function setDescription(CauseOfDeathDescription $description): self
    {
        $this->description = $description;

        return $this;
    }
}
