<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\Burial\CreateBurial;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class CreateBurial
{


    public function execute(CreateBurialRequest $request): CreateBurialResponse
    {
        $deceasedDetails = \json_decode($request->deceasedDetails);
        if (!isset($deceasedDetails->naturalPersonId)) {

        }
        $deceased        = $this->deceasedFactory->create(

        );


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
