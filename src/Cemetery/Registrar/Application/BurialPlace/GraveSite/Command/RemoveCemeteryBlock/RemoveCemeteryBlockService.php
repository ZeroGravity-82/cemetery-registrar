<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\BurialPlace\GraveSite\Command\RemoveCemeteryBlock;

use Cemetery\Registrar\Application\AbstractApplicationRequest;
use Cemetery\Registrar\Application\ApplicationSuccessResponse;
use Cemetery\Registrar\Application\BurialPlace\GraveSite\Command\AbstractCemeteryBlockService;
use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\CemeteryBlockRemoved;
use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\CemeteryBlockRepositoryInterface;
use Cemetery\Registrar\Domain\Model\EventDispatcher;
use Cemetery\Registrar\Domain\Model\Exception;
use Cemetery\Registrar\Domain\Model\NotFoundException;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class RemoveCemeteryBlockService extends AbstractCemeteryBlockService
{
    public function __construct(
        RemoveCemeteryBlockRequestValidator $requestValidator,
        CemeteryBlockRepositoryInterface    $cemeteryBlockRepo,
        EventDispatcher                     $eventDispatcher,
    ) {
        parent::__construct($requestValidator, $cemeteryBlockRepo, $eventDispatcher);
    }

    /**
     * @throws NotFoundException when the cemetery block is not found
     * @throws Exception         when there was any issue within the domain
     * @throws \Throwable        when any error occurred while processing the request
     */
    public function execute(AbstractApplicationRequest $request): ApplicationSuccessResponse
    {
        /** @var RemoveCemeteryBlockRequest $request */
        $cemeteryBlock = $this->getCemeteryBlock($request->id);
        $this->cemeteryBlockRepo->remove($cemeteryBlock);
        $this->eventDispatcher->dispatch(new CemeteryBlockRemoved($cemeteryBlock->id()));

        return new ApplicationSuccessResponse();
    }

    protected function supportedRequestClassName(): string
    {
        return RemoveCemeteryBlockRequest::class;
    }
}
