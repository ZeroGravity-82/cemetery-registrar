<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\Burial\CreateCustomer;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class CreateCustomerResponse
{
    /**
     * @param string $customerType
     * @param string $customerId
     */
    public function __construct(
        public string $customerType,
        public string $customerId,
    ) {}
}
