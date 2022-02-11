<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Deceased;

use Cemetery\Registrar\Domain\AbstractEntityFactory;
use Cemetery\Registrar\Domain\NaturalPerson\NaturalPersonId;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class DeceasedFactory extends AbstractEntityFactory
{
    /**
     * Creates an object for the deceased.
     *
     * @param NaturalPersonId    $naturalPersonId
     * @param \DateTimeImmutable $diedAt
     *
     * @return Deceased
     */
    public function create(
        NaturalPersonId    $naturalPersonId,
        \DateTimeImmutable $diedAt,
    ): Deceased {
        $nextId = new DeceasedId($this->identityGenerator->getNextIdentity());

        return new Deceased($nextId, $naturalPersonId, $diedAt);
    }
}
