<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite;

use Cemetery\Registrar\Domain\Model\Event;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class GraveSiteSizeClarified extends Event
{
    public function __construct(
        private GraveSiteId   $graveSiteId,
        private GraveSiteSize $size,
    ) {
        parent::__construct();
    }

    public function graveSiteId(): GraveSiteId
    {
        return $this->graveSiteId;
    }

    public function size(): GraveSiteSize
    {
        return $this->size;
    }
}
