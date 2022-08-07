<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\BurialPlace\GraveSite\Command\EditCemeteryBlock;

use Cemetery\Registrar\Application\ApplicationRequest;
use Cemetery\Registrar\Application\ApplicationSuccessResponse;
use Cemetery\Registrar\Application\BurialPlace\GraveSite\Command\CemeteryBlockService;
use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\CemeteryBlockEdited;
use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\CemeteryBlockName;
use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\CemeteryBlockRepository;
use Cemetery\Registrar\Domain\Model\EventDispatcher;
use Cemetery\Registrar\Domain\Model\Exception;
use Cemetery\Registrar\Domain\Model\NotFoundException;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class EditCemeteryBlockService extends CemeteryBlockService
{
    public function __construct(
        CemeteryBlockRepository           $cemeteryBlockRepo,
        EventDispatcher                   $eventDispatcher,
        EditCemeteryBlockRequestValidator $requestValidator,
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
        /** @var EditCemeteryBlockRequest $request */
        $cemeteryBlock = $this->getCemeteryBlock($request->id);
        $cemeteryBlock->setName(new CemeteryBlockName($request->name));
        $this->cemeteryBlockRepo->save($cemeteryBlock);
        $this->eventDispatcher->dispatch(new CemeteryBlockEdited(
            $cemeteryBlock->id(),
            $cemeteryBlock->name(),
        ));

        return new EditCemeteryBlockResponse(
            $cemeteryBlock->id()->value(),
        );
    }

    protected function supportedRequestClassName(): string
    {
        return EditCemeteryBlockRequest::class;
    }
}
