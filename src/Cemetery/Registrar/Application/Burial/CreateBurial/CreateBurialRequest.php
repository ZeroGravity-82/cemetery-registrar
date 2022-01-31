<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\Burial\CreateBurial;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class CreateBurialRequest
{
    /**
     * @param string                  $deceasedDetails
     * @param string|null             $customerDetails
     * @param string|null             $burialPlaceDetails
     * @param string|null             $burialPlaceOwnerDetails
     * @param string|null             $funeralCompanyDetails
     * @param string|null             $burialContainerDetails
     * @param \DateTimeImmutable|null $buriedAt
     */
    public function __construct(
        public string              $deceasedDetails,
        public ?string             $customerDetails,
        public ?string             $burialPlaceDetails,
        public ?string             $burialPlaceOwnerDetails,
        public ?string             $funeralCompanyDetails,
        public ?string             $burialContainerDetails,
        public ?\DateTimeImmutable $buriedAt,
    ) {}
}
