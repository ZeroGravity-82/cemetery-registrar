<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\BurialPlace\GraveSite\Command\CreateCemeteryBlock;

use Cemetery\Registrar\Application\AbstractApplicationRequest;
use Cemetery\Registrar\Application\ApplicationSuccessResponse;
use Cemetery\Registrar\Application\BurialPlace\GraveSite\Command\AbstractCemeteryBlockService;
use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\CemeteryBlockCreated;
use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\CemeteryBlockFactory;
use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\CemeteryBlockRepositoryInterface;
use Cemetery\Registrar\Domain\Model\EventDispatcher;
use Cemetery\Registrar\Domain\Model\Exception;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class CreateCemeteryBlockService extends AbstractCemeteryBlockService
{
    public function __construct(
        CreateCemeteryBlockRequestValidator $requestValidator,
        CemeteryBlockRepositoryInterface    $cemeteryBlockRepo,
        EventDispatcher                     $eventDispatcher,
        private CemeteryBlockFactory        $cemeteryBlockFactory,
    ) {
        parent::__construct($requestValidator, $cemeteryBlockRepo, $eventDispatcher);
    }

    /**
     * @throws Exception  when there was any issue within the domain
     * @throws \Throwable when any error occurred while processing the request
     */
    public function execute(AbstractApplicationRequest $request): ApplicationSuccessResponse
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
