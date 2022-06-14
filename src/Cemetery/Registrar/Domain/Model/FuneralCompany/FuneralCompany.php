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
    /**
     * @var FuneralCompanyNote|null
     */
    private ?FuneralCompanyNote $note = null;

    /**
     * @param FuneralCompanyId $id
     * @param OrganizationId   $organizationId
     */
    public function __construct(
        private readonly FuneralCompanyId $id,
        private readonly OrganizationId   $organizationId,
    ) {
        parent::__construct();
    }

    /**
     * @return FuneralCompanyId
     */
    public function id(): FuneralCompanyId
    {
        return $this->id;
    }

    /**
     * @return OrganizationId
     */
    public function organizationId(): OrganizationId
    {
        return $this->organizationId;
    }

    /**
     * @return FuneralCompanyNote|null
     */
    public function note(): ?FuneralCompanyNote
    {
        return $this->note;
    }

    /**
     * @param FuneralCompanyNote|null $note
     *
     * @return $this
     */
    public function setNote(?FuneralCompanyNote $note): self
    {
        $this->note = $note;

        return $this;
    }
}
