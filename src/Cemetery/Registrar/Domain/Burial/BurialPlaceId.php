<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Burial;

use Cemetery\Registrar\Domain\BurialPlace\ColumbariumNiche\ColumbariumNiche;
use Cemetery\Registrar\Domain\BurialPlace\ColumbariumNiche\ColumbariumNicheId;
use Cemetery\Registrar\Domain\BurialPlace\GraveSite\GraveSite;
use Cemetery\Registrar\Domain\BurialPlace\GraveSite\GraveSiteId;
use Cemetery\Registrar\Domain\BurialPlace\MemorialTree\MemorialTree;
use Cemetery\Registrar\Domain\BurialPlace\MemorialTree\MemorialTreeId;
use Cemetery\Registrar\Domain\EntityMaskingId;

/**
 * Wrapper class for burial place ID value objects.
 *
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class BurialPlaceId extends EntityMaskingId
{
    /**
     * @param GraveSiteId|ColumbariumNicheId|MemorialTreeId $id
     */
    public function __construct(
        GraveSiteId|ColumbariumNicheId|MemorialTreeId $id
    ) {
        parent::__construct($id);
    }

    /**
     * {@inheritdoc}
     */
    public function idClassShortcut(): string
    {
        return match (\get_class($this->id())) {
            GraveSiteId::class        => GraveSite::CLASS_SHORTCUT,
            ColumbariumNicheId::class => ColumbariumNiche::CLASS_SHORTCUT,
            MemorialTreeId::class     => MemorialTree::CLASS_SHORTCUT,
        };
    }
}
