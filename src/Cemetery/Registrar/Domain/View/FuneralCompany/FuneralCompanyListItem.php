<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\View\FuneralCompany;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class FuneralCompanyListItem
{
    public function __construct(
        public string  $id,
        public string  $name,
        public ?string $note,
    ) {}
}
