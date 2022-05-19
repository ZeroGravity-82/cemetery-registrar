<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\Burial;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class BurialViewListItem
{
    public function __construct(
        public readonly string  $id,
        public readonly string  $code,
        public readonly ?string $deceasedNaturalPersonFullName,
        public readonly ?string $deceasedNaturalPersonBornAt,
        public readonly ?string $deceasedDiedAt,
        public readonly ?int    $deceasedAge,
        public readonly ?string $buriedAt,
        public readonly ?string $burialPlace,
        public readonly ?string $customerName,
        public readonly ?string $customerAddress,
        public readonly ?string $customerPhone,
    ) {}
}
