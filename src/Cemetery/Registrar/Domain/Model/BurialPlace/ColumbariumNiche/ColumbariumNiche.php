<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Model\BurialPlace\ColumbariumNiche;

use Cemetery\Registrar\Domain\Model\BurialPlace\AbstractBurialPlace;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class ColumbariumNiche extends AbstractBurialPlace
{
    public const CLASS_SHORTCUT = 'COLUMBARIUM_NICHE';
    public const CLASS_LABEL    = 'колумбарная ниша';

    public function __construct(
        private ColumbariumNicheId     $id,
        private ColumbariumId          $columbariumId,
        private RowInColumbarium       $rowInColumbarium,
        private ColumbariumNicheNumber $nicheNumber,
    ) {
        parent::__construct();
    }

    public function id(): ColumbariumNicheId
    {
        return $this->id;
    }

    public function columbariumId(): ColumbariumId
    {
        return $this->columbariumId;
    }

    public function setColumbarium(Columbarium $columbarium): self
    {
        $this->columbariumId = $columbarium->id();

        return $this;
    }

    public function rowInColumbarium(): RowInColumbarium
    {
        return $this->rowInColumbarium;
    }

    public function setRowInColumbarium(RowInColumbarium $rowInColumbarium): self
    {
        $this->rowInColumbarium = $rowInColumbarium;

        return $this;
    }

    public function nicheNumber(): ColumbariumNicheNumber
    {
        return $this->nicheNumber;
    }

    public function setNicheNumber(ColumbariumNicheNumber $nicheNumber): self
    {
        $this->nicheNumber = $nicheNumber;

        return $this;
    }
}
