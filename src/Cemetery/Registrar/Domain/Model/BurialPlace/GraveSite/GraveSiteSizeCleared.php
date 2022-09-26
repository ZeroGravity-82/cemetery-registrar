<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite;

use Cemetery\Registrar\Domain\Model\Event;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class GraveSiteSizeCleared extends Event
{
    public function __construct(
        private GraveSiteId   $id,
    ) {
        parent::__construct();
    }

    public function id(): GraveSiteId
    {
        return $this->id;
    }
}
