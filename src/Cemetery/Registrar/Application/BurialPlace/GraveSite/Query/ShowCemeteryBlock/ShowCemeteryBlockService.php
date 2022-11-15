<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\BurialPlace\GraveSite\Query\ShowCemeteryBlock;

use Cemetery\Registrar\Application\ApplicationRequest;
use Cemetery\Registrar\Application\ApplicationSuccessResponse;
use Cemetery\Registrar\Application\ApplicationService;
use Cemetery\Registrar\Domain\Model\NotFoundException;
use Cemetery\Registrar\Domain\View\BurialPlace\GraveSite\CemeteryBlockFetcher;
use Cemetery\Registrar\Domain\View\BurialPlace\GraveSite\CemeteryBlockView;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class ShowCemeteryBlockService extends ApplicationService
{
    public function __construct(
        private CemeteryBlockFetcher      $cemeteryBlockFetcher,
        ShowCemeteryBlockRequestValidator $requestValidator,
    ) {
        parent::__construct($requestValidator);
    }

    /**
     * @param ShowCemeteryBlockRequest $request
     *
     * @throws NotFoundException when the cemetery block is not found
     * @throws \Throwable        when any error occurred while processing the request
     */
    public function execute(ApplicationRequest $request): ApplicationSuccessResponse
    {
        return new ShowCemeteryBlockResponse(
            $this->getCemeteryBlockView($request->id),
        );
    }

    protected function supportedRequestClassName(): string
    {
        return ShowCemeteryBlockRequest::class;
    }

    /**
     * @throws NotFoundException when the cemetery block is not found
     */
    private function getCemeteryBlockView(string $id): CemeteryBlockView
    {
        $view = $this->cemeteryBlockFetcher->findViewById($id);
        if ($view === null) {
            throw new NotFoundException(\sprintf('Квартал с ID "%s" не найден.', $id));
        }

        return $view;
    }
}
