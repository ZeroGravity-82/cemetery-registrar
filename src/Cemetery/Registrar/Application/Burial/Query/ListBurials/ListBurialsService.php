<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\Burial\Query\ListBurials;

use Cemetery\Registrar\Application\ApplicationRequest;
use Cemetery\Registrar\Application\ApplicationSuccessResponse;
use Cemetery\Registrar\Application\ApplicationService;
use Cemetery\Registrar\Domain\View\Burial\BurialContainer\CoffinShapeFetcher;
use Cemetery\Registrar\Domain\View\Burial\BurialFetcher;
use Cemetery\Registrar\Domain\View\BurialPlace\GraveSite\CemeteryBlockFetcher;
use Cemetery\Registrar\Domain\View\FuneralCompany\FuneralCompanyFetcher;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class ListBurialsService extends ApplicationService
{
    public function __construct(
        ListBurialsRequestValidator   $requestValidator,
        private BurialFetcher         $burialFetcher,
        private FuneralCompanyFetcher $funeralCompanyFetcher,
        private CemeteryBlockFetcher  $cemeteryBlockFetcher,
        private CoffinShapeFetcher    $coffinShapeFetcher,
    ) {
        parent::__construct($requestValidator);
    }

    /**
     * @throws \Throwable when any error occurred while processing the request
     */
    public function execute(ApplicationRequest $request): ApplicationSuccessResponse
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
