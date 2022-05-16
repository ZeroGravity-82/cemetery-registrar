<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\Burial;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class BurialView
{
    public function __construct(
        public readonly string  $id,
        public readonly string  $code,
        public readonly string  $deceasedFullName,
        public readonly string  $deceasedDiedAt,
        public readonly ?string $deceasedBornAt,
        public readonly ?string $deceasedBuriedAt,
        public readonly ?int    $deceasedAge,
        public readonly ?string $burialPlace,
        public readonly ?string $customerName,
        public readonly ?string $customerAddress,
        public readonly ?string $customerPhone,
    ) {}
}
