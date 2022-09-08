<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\View\NaturalPerson;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class NaturalPersonSimpleListItem
{
    public function __construct(
        public string  $id,
        public string  $fullName,
        public ?string $bornAt,
        public ?string $diedAt,
    ) {}
}
