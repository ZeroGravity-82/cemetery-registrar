<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Burial;

use Cemetery\Registrar\Domain\BurialPlace\ColumbariumNiche\ColumbariumNicheId;
use Cemetery\Registrar\Domain\BurialPlace\GraveSite\GraveSiteId;
use Cemetery\Registrar\Domain\BurialPlace\MemorialTree\MemorialTreeId;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class BurialPlaceIdFactory
{
    /**
     * @param string|null $id
     *
     * @return BurialPlaceId
     */
    public function createForGraveSite(?string $id): BurialPlaceId
    {
        return new BurialPlaceId(new GraveSiteId((string) $id));
    }

    /**
     * @param string|null $id
     *
     * @return BurialPlaceId
     */
    public function createForColumbariumNiche(?string $id): BurialPlaceId
    {
        return new BurialPlaceId(new ColumbariumNicheId((string) $id));
    }

    /**
     * @param string|null $id
     *
     * @return BurialPlaceId
     */
    public function createForMemorialTree(?string $id): BurialPlaceId
    {
        return new BurialPlaceId(new MemorialTreeId((string) $id));
    }
}
