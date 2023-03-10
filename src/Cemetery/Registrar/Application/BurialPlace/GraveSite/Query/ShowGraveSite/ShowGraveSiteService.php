<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\BurialPlace\GraveSite\Query\ShowGraveSite;

use Cemetery\Registrar\Application\AbstractApplicationRequest;
use Cemetery\Registrar\Application\ApplicationSuccessResponse;
use Cemetery\Registrar\Application\AbstractApplicationService;
use Cemetery\Registrar\Domain\Model\NotFoundException;
use Cemetery\Registrar\Domain\View\BurialPlace\GraveSite\GraveSiteFetcherInterface;
use Cemetery\Registrar\Domain\View\BurialPlace\GraveSite\GraveSiteView;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class ShowGraveSiteService extends AbstractApplicationService
{
    public function __construct(
        ShowGraveSiteRequestValidator     $requestValidator,
        private GraveSiteFetcherInterface $graveSiteFetcher,
    ) {
        parent::__construct($requestValidator);
    }

    /**
     * @param ShowGraveSiteRequest $request
     *
     * @throws NotFoundException when the grave site is not found
     * @throws \Throwable        when any error occurred while processing the request
     */
    public function execute(AbstractApplicationRequest $request): ApplicationSuccessResponse
    {
        return new ShowGraveSiteResponse(
            $this->getGraveSiteView($request->id),
        );
    }

    protected function supportedRequestClassName(): string
    {
        return ShowGraveSiteRequest::class;
    }

    /**
     * @throws NotFoundException when the grave site is not found
     */
    private function getGraveSiteView(string $id): GraveSiteView
    {
        $view = $this->graveSiteFetcher->findViewById($id);
        if ($view === null) {
            throw new NotFoundException(\sprintf('Участок с ID "%s" не найден.', $id));
        }

        return $view;
    }
}
