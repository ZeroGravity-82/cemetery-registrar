<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\NaturalPerson;

use Cemetery\Registrar\Domain\AbstractAggregateRoot;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class NaturalPerson extends AbstractAggregateRoot
{
    /**
     * @var \DateTimeImmutable|null
     */
    private ?\DateTimeImmutable $bornAt = null;

    /**
     * @param NaturalPersonId $id
     * @param FullName        $fullName
     */
    public function __construct(
        private NaturalPersonId $id,
        private FullName        $fullName,
    ) {
        parent::__construct();
    }

    /**
     * @return NaturalPersonId
     */
    public function getId(): NaturalPersonId
    {
        return $this->id;
    }

    /**
     * @return FullName
     */
    public function getFullName(): FullName
    {
        return $this->fullName;
    }

    /**
     * @param FullName $fullName
     *
     * @return $this
     */
    public function setFullName(FullName $fullName): self
    {
        $this->fullName = $fullName;

        return $this;
    }

    /**
     * @return \DateTimeImmutable|null
     */
    public function getBornAt(): ?\DateTimeImmutable
    {
        return $this->bornAt;
    }

    /**
     * @param \DateTimeImmutable|null $bornAt
     *
     * @return $this
     */
    public function setBornAt(?\DateTimeImmutable $bornAt): self
    {
        $this->bornAt = $bornAt;

        return $this;
    }
}
