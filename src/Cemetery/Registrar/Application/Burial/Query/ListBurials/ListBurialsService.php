<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\Burial\Query\ListBurials;

use Cemetery\Registrar\Application\AbstractApplicationRequest;
use Cemetery\Registrar\Application\ApplicationSuccessResponse;
use Cemetery\Registrar\Application\AbstractApplicationService;
use Cemetery\Registrar\Domain\View\Burial\BurialContainer\CoffinShapeFetcher;
use Cemetery\Registrar\Domain\View\Burial\BurialFetcherInterface;
use Cemetery\Registrar\Domain\View\BurialPlace\GraveSite\CemeteryBlockFetcherInterface;
use Cemetery\Registrar\Domain\View\FuneralCompany\FuneralCompanyFetcherInterface;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class ListBurialsService extends AbstractApplicationService
{
    public function __construct(
        ListBurialsRequestValidator            $requestValidator,
        private BurialFetcherInterface         $burialFetcher,
        private FuneralCompanyFetcherInterface $funeralCompanyFetcher,
        private CemeteryBlockFetcherInterface  $cemeteryBlockFetcher,
        private CoffinShapeFetcher             $coffinShapeFetcher,
    ) {
        parent::__construct($requestValidator);
    }

    /**
     * @throws \Throwable when any error occurred while processing the request
     */
    public function execute(AbstractApplicationRequest $request): ApplicationSuccessResponse
    {
        return new ListBurialsResponse(
            $this->burialFetcher->paginate(1),
            $this->burialFetcher->countTotal(),
            $this->funeralCompanyFetcher->paginate(1),
            $this->cemeteryBlockFetcher->paginate(1),
            $this->coffinShapeFetcher->findAll(1),
        );
    }

    protected function supportedRequestClassName(): string
    {
        return ListBurialsRequest::class;
    }
}
