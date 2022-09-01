<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite;

use Cemetery\Registrar\Domain\Model\Event;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class GraveSiteRemoved extends Event
{
    public function __construct(
        private GraveSiteId     $graveSiteId,
    ) {
        parent::__construct();
    }

    public function graveSiteId(): GraveSiteId
    {
        return $this->graveSiteId;
    }
}
