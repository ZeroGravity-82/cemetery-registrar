<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\BurialPlace\GraveSite\Command\CreateGraveSite;

use Cemetery\Registrar\Application\AbstractApplicationRequest;
use Cemetery\Registrar\Application\ApplicationSuccessResponse;
use Cemetery\Registrar\Application\BurialPlace\GraveSite\Command\AbstractGraveSiteService;
use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\CemeteryBlockRepositoryInterface;
use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\GraveSiteCreated;
use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\GraveSiteFactory;
use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\GraveSiteRepositoryInterface;
use Cemetery\Registrar\Domain\Model\EventDispatcher;
use Cemetery\Registrar\Domain\Model\Exception;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class CreateGraveSiteService extends AbstractGraveSiteService
{
    public function __construct(
        GraveSiteRepositoryInterface     $graveSiteRepo,
        CemeteryBlockRepositoryInterface $cemeteryBlockRepo,
        EventDispatcher                  $eventDispatcher,
        CreateGraveSiteRequestValidator  $requestValidator,
        private GraveSiteFactory         $graveSiteFactory,
    ) {
        parent::__construct($requestValidator, $graveSiteRepo, $cemeteryBlockRepo, $eventDispatcher);
    }

    /**
     * @throws Exception  when there was any issue within the domain
     * @throws \Throwable when any error occurred while processing the request
     */
    public function execute(AbstractApplicationRequest $request): ApplicationSuccessResponse
    {
        /** @var CreateGraveSiteRequest $request */
        $graveSite = $this->graveSiteFactory->create(
            $request->cemeteryBlockId,
            $request->rowInBlock,
            $request->positionInRow,
            $request->geoPositionLatitude,
            $request->geoPositionLongitude,
            $request->geoPositionError,
            $request->size,
            $request->personInChargeId,
        );
        $this->graveSiteRepo->save($graveSite);
        $this->eventDispatcher->dispatch(new GraveSiteCreated(
            $graveSite->id(),
            $graveSite->cemeteryBlockId(),
            $graveSite->rowInBlock(),
            $graveSite->positionInRow(),
        ));

        return new CreateGraveSiteResponse(
            $graveSite->id()->value(),
        );
    }

    protected function supportedRequestClassName(): string
    {
        return CreateGraveSiteRequest::class;
    }
}
