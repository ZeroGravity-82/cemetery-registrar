<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\BurialPlace\GraveSite\Command\RemoveGraveSite;

use Cemetery\Registrar\Application\ApplicationRequest;
use Cemetery\Registrar\Application\ApplicationSuccessResponse;
use Cemetery\Registrar\Application\BurialPlace\GraveSite\Command\GraveSiteService;
use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\GraveSiteRemoved;
use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\GraveSiteRepository;
use Cemetery\Registrar\Domain\Model\EventDispatcher;
use Cemetery\Registrar\Domain\Model\Exception;
use Cemetery\Registrar\Domain\Model\NotFoundException;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class RemoveGraveSiteService extends GraveSiteService
{
    public function __construct(
        GraveSiteRepository             $graveSiteRepo,
        EventDispatcher                 $eventDispatcher,
        RemoveGraveSiteRequestValidator $requestValidator,
    ) {
        parent::__construct($graveSiteRepo, $eventDispatcher, $requestValidator);
    }

    /**
     * @throws NotFoundException when the grave site is not found
     * @throws Exception         when there was any issue within the domain
     * @throws \Throwable        when any error occurred while processing the request
     */
    public function execute(ApplicationRequest $request): ApplicationSuccessResponse
    {
        /** @var RemoveGraveSiteRequest $request */
        $graveSite = $this->getGraveSite($request->id);
        $this->graveSiteRepo->remove($graveSite);
        $this->eventDispatcher->dispatch(new GraveSiteRemoved(
            $graveSite->id(),
            $graveSite->cemeteryBlockId(),
            $graveSite->rowInBlock(),
            $graveSite->positionInRow(),
        ));

        return new ApplicationSuccessResponse();
    }

    protected function supportedRequestClassName(): string
    {
        return RemoveGraveSiteRequest::class;
    }
}
