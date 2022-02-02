<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\Burial\CreateCustomer;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class CreateCustomerRequest
{
    /**
     * @param string                  $customerType
     * @param string|null             $customerNaturalPersonFullName
     * @param \DateTimeImmutable|null $customerNaturalPersonBornAt
     * @param string|null             $customerSoleProprietorFullName
     * @param string|null             $customerSoleProprietorInn
     * @param string|null             $customerJuristicPersonName
     * @param string|null             $customerJuristicPersonInn
     * @param string|null             $customerJuristicPersonKpp
     */
    public function __construct(
        public string              $customerType,
        public ?string             $customerNaturalPersonFullName,
        public ?\DateTimeImmutable $customerNaturalPersonBornAt,
        public ?string             $customerSoleProprietorFullName,
        public ?string             $customerSoleProprietorInn,
        public ?string             $customerJuristicPersonName,
        public ?string             $customerJuristicPersonInn,
        public ?string             $customerJuristicPersonKpp,
    ) {}
}
