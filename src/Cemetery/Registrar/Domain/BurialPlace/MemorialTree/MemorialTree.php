<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\BurialPlace\MemorialTree;

use Cemetery\Registrar\Domain\AggregateRoot;
use Cemetery\Registrar\Domain\GeoPosition\GeoPosition;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class MemorialTree extends AggregateRoot
{
    public const LABEL = 'памятное дерево';

    /**
     * @var GeoPosition|null
     */
    private ?GeoPosition $geoPosition = null;

    /**
     * @param MemorialTreeId     $id
     * @param MemorialTreeNumber $treeNumber
     */
    public function __construct(
        private readonly MemorialTreeId $id,
        private MemorialTreeNumber      $treeNumber,
    ) {
        parent::__construct();
    }

    /**
     * @return MemorialTreeId
     */
    public function id(): MemorialTreeId
    {
        return $this->id;
    }

    /**
     * @return MemorialTreeNumber
     */
    public function treeNumber(): MemorialTreeNumber
    {
        return $this->treeNumber;
    }

    /**
     * @param MemorialTreeNumber $treeNumber
     *
     * @return $this
     */
    public function setTreeNumber(MemorialTreeNumber $treeNumber): self
    {
        $this->treeNumber = $treeNumber;

        return $this;
    }

    /**
     * @return GeoPosition|null
     */
    public function geoPosition(): ?GeoPosition
    {
        return $this->geoPosition;
    }

    /**
     * @param GeoPosition|null $geoPosition
     *
     * @return $this
     */
    public function setGeoPosition(?GeoPosition $geoPosition): self
    {
        $this->geoPosition = $geoPosition;

        return $this;
    }
}
