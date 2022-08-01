<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Model\FuneralCompany;

use Cemetery\Registrar\Domain\Model\AggregateRoot;
use Cemetery\Registrar\Domain\Model\Organization\OrganizationId;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class FuneralCompany extends AggregateRoot
{
    private ?FuneralCompanyNote $note = null;

    public function __construct(
        private FuneralCompanyId $id,
        private OrganizationId   $organizationId,
    ) {
        parent::__construct();
    }

    public function id(): FuneralCompanyId
    {
        return $this->id;
    }

    public function organizationId(): OrganizationId
    {
        return $this->organizationId;
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
