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
     * @param NaturalPersonId         $id
     * @param FullName                $fullName
     * @param \DateTimeImmutable|null $bornAt
     */
    public function __construct(
        private NaturalPersonId     $id,
        private FullName            $fullName,
        private ?\DateTimeImmutable $bornAt,
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
     */
    public function setFullName(FullName $fullName): void
    {
        $this->fullName = $fullName;
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
     */
    public function setBornAt(?\DateTimeImmutable $bornAt): void
    {
        $this->bornAt = $bornAt;
    }
}
