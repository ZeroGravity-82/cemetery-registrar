<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\Burial\CreateCustomer;

use Cemetery\Registrar\Domain\Burial\CustomerType;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class CreateCustomer
{
    /**
     * @param CreateCustomerRequest $request
     *
     * @return CreateCustomerResponse
     */
    public function execute(CreateCustomerRequest $request): CreateCustomerResponse
    {
        $customerType = $request->customerType ?? null;
        switch ($customerType) {
            case CustomerType::NATURAL_PERSON:
                $customer = $this->naturalPersonFactory->create(
                    $request->customerNaturalPersonFullName ?? null,
                    $request->customerNaturalPersonBornAt ?? null,
                );
                $this->naturalPersonRepo->save($customer);
                break;
            case CustomerType::SOLE_PROPRIETOR:
                $customer = $this->soleProprietorFactory->create(
                    $request->customerSoleProprietorFullName ?? null,
                    $request->customerSoleProprietorInn ?? null,
                );
                $this->soleProprietorRepo->save($customer);
                break;
            case CustomerType::JURISTIC_PERSON:
                $customer = $this->juristicPersonFactory->create(
                    $request->customerJuristicPersonName ?? null,
                    $request->customerJuristicPersonInn ?? null,
                    $request->customerJuristicPersonKpp ?? null,
                );
                $this->juristicPersonRepo->save($customer);
                break;
            default:
                throw new \InvalidArgumentException(\sprintf('Invalid customer type "%s".', $customerType));
        }

        return new CreateCustomerResponse($customerType, (string) $customer->getId());
    }
}
