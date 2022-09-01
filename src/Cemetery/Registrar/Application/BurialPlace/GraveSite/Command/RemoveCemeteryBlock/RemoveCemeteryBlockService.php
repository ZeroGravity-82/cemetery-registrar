<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\BurialPlace\GraveSite\Command\RemoveCemeteryBlock;

use Cemetery\Registrar\Application\ApplicationRequest;
use Cemetery\Registrar\Application\ApplicationSuccessResponse;
use Cemetery\Registrar\Application\BurialPlace\GraveSite\Command\CemeteryBlockService;
use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\CemeteryBlockRemoved;
use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\CemeteryBlockRepository;
use Cemetery\Registrar\Domain\Model\EventDispatcher;
use Cemetery\Registrar\Domain\Model\Exception;
use Cemetery\Registrar\Domain\Model\NotFoundException;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class RemoveCemeteryBlockService extends CemeteryBlockService
{
    public function __construct(
        CemeteryBlockRepository             $cemeteryBlockRepo,
        EventDispatcher                     $eventDispatcher,
        RemoveCemeteryBlockRequestValidator $requestValidator,
    ) {
        parent::__construct($cemeteryBlockRepo, $eventDispatcher, $requestValidator);
    }

    /**
     * @throws NotFoundException when the cemetery block is not found
     * @throws Exception         when there was any issue within the domain
     * @throws \Throwable        when any error occurred while processing the request
     */
    public function execute(ApplicationRequest $request): ApplicationSuccessResponse
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
