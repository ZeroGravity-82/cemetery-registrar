<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\FuneralCompany;

use Cemetery\Registrar\Domain\AbstractAggregateRoot;
use Cemetery\Registrar\Domain\Organization\OrganizationId;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class FuneralCompany extends AbstractAggregateRoot
{
    /**
     * @var string|null
     */
    private ?string $note = null;

    /**
     * @param FuneralCompanyId $id
     * @param OrganizationId   $organizationId
     */
    public function __construct(
        private FuneralCompanyId $id,
        private OrganizationId   $organizationId,
    ) {
        parent::__construct();
    }

    /**
     * @return FuneralCompanyId
     */
    public function getId(): FuneralCompanyId
    {
        return $this->id;
    }

    /**
     * @return OrganizationId
     */
    public function getOrganizationId(): OrganizationId
    {
        return $this->organizationId;
    }

    /**
     * @return string|null
     */
    public function getNote(): ?string
    {
        return $this->note;
    }

    /**
     * @param string|null $note
     *
     * @return $this
     */
    public function setNote(?string $note): self
    {
        $this->note = $note;

        return $this;
    }
}
