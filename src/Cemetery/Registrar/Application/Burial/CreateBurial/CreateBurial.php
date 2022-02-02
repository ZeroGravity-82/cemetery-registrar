<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\Burial\CreateBurial;

use Cemetery\Registrar\Domain\Burial\CustomerType;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class CreateBurial
{


    public function execute(CreateBurialRequest $request): CreateBurialResponse
    {
        $customerId                 = $request->customerId   ?? null;
        $customerType               = $request->customerType ?? null;
        $isCustomerCreationRequired = \is_null($customerId) && !\is_null($customerType);
        if ($isCustomerCreationRequired) {
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
            };
            $customerId = (string) $customer->getId();
        }
        if ($customerId && $customerType) {
            $this->burialBuilder->addCustomerId($customerId, $customerType);
        }





        $this->burialBuilder->addDeceased();
        $this->burialBuilder->addBurialPlace();
        $this->burialBuilder->addBurialPlaceOwner();
        $this->burialBuilder->addFuneralCompany();
        $this->burialBuilder->addBurialContainer();
        $this->burialBuilder->addBuriedAt();

        $burial = $this->burialBuilder->getResult();
        $this->burailRepo->save($burial);

        return new CreateBurialResponse((string) $burial->getId());















        $deceasedDetails = \json_decode($request->deceasedDetails);
        if (!isset($deceasedDetails->naturalPersonId)) {
            $naturalPerson = $this->naturalPersonFactory()->create(
                $deceasedDetails->fullName,
                $deceasedDetails->bornAt,
            );
            $this->naturalPersonFactory->save($naturalPerson);
            $naturalPersonId = $naturalPerson->getId();
        } else {
            $naturalPersonId = $deceasedDetails->naturalPersonId;
        }
        $deceased = $this->deceasedFactory->create(
            $naturalPersonId,
            $deceasedDetails->diedAt,
            $deceasedDetails->deathCertificateId,
            $deceasedDetails->causeOfDeath,
        );
        $this->deceasedRepo->save($deceased);


        $customerDetails = $request->customerDetails ?: \json_decode($request->customerDetails);


        $burialPlaceDetails = $request->burialPlaceDetails ?: \json_decode($request->burialPlaceDetails);


        $burialPlaceOwnerDetails = $request->burialPlaceOwnerDetails ?: \json_decode($request->burialPlaceOwnerDetails);


        $funeralCompanyDetails = $request->funeralCompanyDetails ?: \json_decode($request->funeralCompanyDetails);


        $burialContainerDetails = $request->burialContainerDetails ?: \json_decode($request->burialContainerDetails);


        $buriedAt = $request->buriedAt;



        $burial = $this->burialFactory->create(
            $deceasedId,
            $customerId,
            $burialPlaceId,
            $burialPlaceOwnerId,
            $funeralCompanyId,
            $burialContainerId,
            $buriedAt,
        );
        $this->burailRepo->save($burial);

        return new CreateBurialResponse((string) $burial->getId());
    }
}
