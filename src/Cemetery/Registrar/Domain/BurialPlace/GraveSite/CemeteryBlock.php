<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\BurialPlace\GraveSite;

use Cemetery\Registrar\Domain\AggregateRoot;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 *
 * @final
 */
class CemeteryBlock extends AggregateRoot
{
    /**
     * @param CemeteryBlockId   $id
     * @param CemeteryBlockName $name
     */
    public function __construct(
        private readonly CemeteryBlockId $id,
        private CemeteryBlockName        $name,
    ) {
        parent::__construct();
    }

    /**
     * @return CemeteryBlockId
     */
    public function id(): CemeteryBlockId
    {
        return $this->id;
    }

    /**
     * @return CemeteryBlockName
     */
    public function name(): CemeteryBlockName
    {
        return $this->name;
    }

    /**
     * @param CemeteryBlockName $name
     *
     * @return $this
     */
    public function setName(CemeteryBlockName $name): self
    {
        $this->name = $name;

        return $this;
    }
}
