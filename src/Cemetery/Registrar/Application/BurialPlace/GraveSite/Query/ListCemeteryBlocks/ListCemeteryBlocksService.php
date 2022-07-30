<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\BurialPlace\GraveSite\Query\ListCemeteryBlocks;

use Cemetery\Registrar\Application\ApplicationRequest;
use Cemetery\Registrar\Application\ApplicationSuccessResponse;
use Cemetery\Registrar\Application\ApplicationService;
use Cemetery\Registrar\Application\Notification;
use Cemetery\Registrar\Domain\View\BurialPlace\GraveSite\CemeteryBlockFetcher;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class ListCemeteryBlocksService extends ApplicationService
{
    public function __construct(
        private readonly CemeteryBlockFetcher $cemeteryBlockFetcher,
    ) {}

    /**
     * @param ListCemeteryBlocksRequest $request
     *
     * @return Notification
     */
    public function validate(ApplicationRequest $request): Notification
    {
        // TODO: Implement validate() method.
    }

    /**
     * @param ListCemeteryBlocksRequest $request
     *
     * @return ApplicationSuccessResponse
     */
    public function execute(ApplicationRequest $request): ApplicationSuccessResponse
    {
        return new ListCemeteryBlocksResponse($this->cemeteryBlockFetcher->findAll(1));
    }

    /**
     * {@inheritdoc}
     */
    protected function supportedRequestClassName(): string
    {
        return ListCemeteryBlocksRequest::class;
    }
}
