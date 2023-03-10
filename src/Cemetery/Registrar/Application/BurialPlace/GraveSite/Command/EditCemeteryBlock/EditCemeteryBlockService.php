<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\BurialPlace\GraveSite\Command\EditCemeteryBlock;

use Cemetery\Registrar\Application\AbstractApplicationRequest;
use Cemetery\Registrar\Application\ApplicationSuccessResponse;
use Cemetery\Registrar\Application\BurialPlace\GraveSite\Command\AbstractCemeteryBlockService;
use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\CemeteryBlockEdited;
use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\CemeteryBlockName;
use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\CemeteryBlockRepositoryInterface;
use Cemetery\Registrar\Domain\Model\EventDispatcher;
use Cemetery\Registrar\Domain\Model\Exception;
use Cemetery\Registrar\Domain\Model\NotFoundException;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class EditCemeteryBlockService extends AbstractCemeteryBlockService
{
    public function __construct(
        EditCemeteryBlockRequestValidator $requestValidator,
        CemeteryBlockRepositoryInterface  $cemeteryBlockRepo,
        EventDispatcher                   $eventDispatcher,
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
        /** @var EditCemeteryBlockRequest $request */
        $cemeteryBlock = $this->getCemeteryBlock($request->id);
        $cemeteryBlock->setName($this->buildName($request));
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

    /**
     * @throws Exception when the name has invalid value
     */
    private function buildName(AbstractApplicationRequest $request): CemeteryBlockName
    {
        /** @var EditCemeteryBlockRequest $request */
        return new CemeteryBlockName($request->name);
    }
}
