<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Model\FuneralCompany;

use Cemetery\Registrar\Domain\Model\AbstractAggregateRoot;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class FuneralCompany extends AbstractAggregateRoot
{
    private ?FuneralCompanyNote $note = null;

    public function __construct(
        private FuneralCompanyId   $id,
        private FuneralCompanyName $name,
    ) {
        parent::__construct();
    }

    public function id(): FuneralCompanyId
    {
        return $this->id;
    }

    public function name(): FuneralCompanyName
    {
        return $this->name;
    }

    public function setName(FuneralCompanyName $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function note(): ?FuneralCompanyNote
    {
        return $this->note;
    }

    public function setNote(?FuneralCompanyNote $note): self
    {
        $this->note = $note;

        return $this;
    }
}
