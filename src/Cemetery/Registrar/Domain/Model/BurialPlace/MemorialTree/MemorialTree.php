<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Model\BurialPlace\MemorialTree;

use Cemetery\Registrar\Domain\Model\GeoPosition\GeoPosition;
use Cemetery\Registrar\Domain\Model\AggregateRoot;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class MemorialTree extends AggregateRoot
{
    public const CLASS_SHORTCUT = 'MEMORIAL_TREE';
    public const CLASS_LABEL    = 'памятное дерево';

    private ?GeoPosition $geoPosition = null;

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

    public function geoPosition(): ?GeoPosition
    {
        return $this->geoPosition;
    }

    public function setGeoPosition(?GeoPosition $geoPosition): self
    {
        $this->geoPosition = $geoPosition;

        return $this;
    }
}
