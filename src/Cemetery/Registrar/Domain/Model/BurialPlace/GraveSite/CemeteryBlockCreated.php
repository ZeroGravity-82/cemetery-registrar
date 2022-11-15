<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite;

use Cemetery\Registrar\Domain\Model\AbstractEvent;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class CemeteryBlockCreated extends AbstractEvent
{
    public function __construct(
        private CemeteryBlockId   $id,
        private CemeteryBlockName $name,
    ) {
        parent::__construct();
    }

    public function id(): CemeteryBlockId
    {
        return $this->id;
    }

    public function name(): CemeteryBlockName
    {
        return $this->name;
    }
}
