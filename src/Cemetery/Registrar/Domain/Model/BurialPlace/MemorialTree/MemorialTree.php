<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Model\BurialPlace\MemorialTree;

use Cemetery\Registrar\Domain\Model\BurialPlace\BurialPlace;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class MemorialTree extends BurialPlace
{
    public const CLASS_SHORTCUT = 'MEMORIAL_TREE';
    public const CLASS_LABEL    = 'памятное дерево';

    public function __construct(
        private MemorialTreeId     $id,
        private MemorialTreeNumber $treeNumber,
    ) {
        parent::__construct();
    }

    public function id(): MemorialTreeId
    {
        return $this->id;
    }

    public function treeNumber(): MemorialTreeNumber
    {
        return $this->treeNumber;
    }

    public function setTreeNumber(MemorialTreeNumber $treeNumber): self
    {
        $this->treeNumber = $treeNumber;

        return $this;
    }
}
