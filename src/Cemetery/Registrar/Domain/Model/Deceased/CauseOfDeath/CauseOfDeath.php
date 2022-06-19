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
     * @param CauseOfDeathId   $id
     * @param CauseOfDeathName $name
     */
    public function __construct(
        private readonly CauseOfDeathId $id,
        private CauseOfDeathName        $name,
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
     * @return CauseOfDeathName
     */
    public function name(): CauseOfDeathName
    {
        return $this->name;
    }

    /**
     * @param CauseOfDeathName $name
     *
     * @return $this
     */
    public function setName(CauseOfDeathName $name): self
    {
        $this->name = $name;

        return $this;
    }
}
