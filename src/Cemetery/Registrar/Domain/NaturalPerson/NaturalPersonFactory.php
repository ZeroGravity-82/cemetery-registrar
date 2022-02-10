<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\NaturalPerson;

use Cemetery\Registrar\Domain\AbstractEntityFactory;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class NaturalPersonFactory extends AbstractEntityFactory
{
    /**
     * Creates an object for the natural person.
     *
     * @param FullName                $fullName
     * @param \DateTimeImmutable|null $bornAt
     *
     * @return NaturalPerson
     */
    public function create(FullName $fullName, ?\DateTimeImmutable $bornAt): NaturalPerson
    {
        $nextId = new NaturalPersonId($this->identityGenerator->getNextIdentity());

        return new NaturalPerson($nextId, $fullName, $bornAt);
    }
}
