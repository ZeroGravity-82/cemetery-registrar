<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\BurialPlace\GraveSite\Command\CreateGraveSite;

use Cemetery\Registrar\Application\ApplicationRequest;
use Cemetery\Registrar\Application\ApplicationSuccessResponse;
use Cemetery\Registrar\Application\BurialPlace\GraveSite\Command\GraveSiteService;
use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\GraveSiteCreated;
use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\GraveSiteFactory;
use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\GraveSiteRepository;
use Cemetery\Registrar\Domain\Model\EventDispatcher;
use Cemetery\Registrar\Domain\Model\Exception;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class CreateGraveSiteService extends GraveSiteService
{
    public function __construct(
        private readonly GraveSiteFactory $graveSiteFactory,
        GraveSiteRepository               $graveSiteRepo,
        EventDispatcher                   $eventDispatcher,
        CreateGraveSiteRequestValidator   $requestValidator,
    ) {
        parent::__construct($graveSiteRepo, $eventDispatcher, $requestValidator);
    }

    /**
     * @throws Exception  when there was any issue within the domain
     * @throws \Throwable when any error occurred while processing the request
     */
    public function execute(ApplicationRequest $request): ApplicationSuccessResponse
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
