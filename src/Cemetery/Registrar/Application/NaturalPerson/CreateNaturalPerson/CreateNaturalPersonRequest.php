<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\NaturalPerson\CreateNaturalPerson;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class CreateNaturalPersonRequest
{
    /**
     * @param string                  $fullName
     * @param \DateTimeImmutable|null $bornAt
     */
    public function __construct(
        public string              $fullName,
        public ?\DateTimeImmutable $bornAt,
    ) {}
}
