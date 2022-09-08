<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\View\NaturalPerson;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class NaturalPersonSimpleList
{
    public function __construct(
        public array $items,
    ) {}
}
