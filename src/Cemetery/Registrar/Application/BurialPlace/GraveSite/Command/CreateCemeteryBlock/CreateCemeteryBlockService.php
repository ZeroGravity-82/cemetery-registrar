<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\BurialPlace\GraveSite\Command\CreateCemeteryBlock;

use Cemetery\Registrar\Application\ApplicationRequest;
use Cemetery\Registrar\Application\ApplicationSuccessResponse;
use Cemetery\Registrar\Application\BurialPlace\GraveSite\Command\CemeteryBlockService;
use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\CemeteryBlockCreated;
use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\CemeteryBlockFactory;
use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\CemeteryBlockRepository;
use Cemetery\Registrar\Domain\Model\EventDispatcher;
use Cemetery\Registrar\Domain\Model\Exception;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class CreateCemeteryBlockService extends CemeteryBlockService
{
    public function __construct(
        CreateCemeteryBlockRequestValidator $requestValidator,
        CemeteryBlockRepository             $cemeteryBlockRepo,
        EventDispatcher                     $eventDispatcher,
        private CemeteryBlockFactory        $cemeteryBlockFactory,
    ) {
        parent::__construct($requestValidator, $cemeteryBlockRepo, $eventDispatcher);
    }

    /**
     * @throws Exception  when there was any issue within the domain
     * @throws \Throwable when any error occurred while processing the request
     */
    public function execute(ApplicationRequest $request): ApplicationSuccessResponse
    {
        /** @var CreateCemeteryBlockRequest $request */
        $cemeteryBlock = $this->cemeteryBlockFactory->create(
            $request->name,
        );
        $this->cemeteryBlockRepo->save($cemeteryBlock);
        $this->eventDispatcher->dispatch(new CemeteryBlockCreated(
            $cemeteryBlock->id(),
            $cemeteryBlock->name(),
        ));

        return new CreateCemeteryBlockResponse(
            $cemeteryBlock->id()->value(),
        );
    }

    protected function supportedRequestClassName(): string
    {
        return CreateCemeteryBlockRequest::class;
    }
}
