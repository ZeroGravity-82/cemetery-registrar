<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\BurialPlace\GraveSite\Command\DiscardGraveSitePersonInCharge;

use Cemetery\Registrar\Application\AbstractApplicationRequest;
use Cemetery\Registrar\Application\ApplicationSuccessResponse;
use Cemetery\Registrar\Application\BurialPlace\GraveSite\Command\AbstractGraveSiteService;
use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\CemeteryBlockRepositoryInterface;
use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\GraveSitePersonInChargeDiscarded;
use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\GraveSiteRepositoryInterface;
use Cemetery\Registrar\Domain\Model\EventDispatcher;
use Cemetery\Registrar\Domain\Model\Exception;
use Cemetery\Registrar\Domain\Model\NotFoundException;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class DiscardGraveSitePersonInChargeService extends AbstractGraveSiteService
{
    public function __construct(
        DiscardGraveSitePersonInChargeRequestValidator $requestValidator,
        GraveSiteRepositoryInterface                   $graveSiteRepo,
        CemeteryBlockRepositoryInterface               $cemeteryBlockRepo,
        EventDispatcher                                $eventDispatcher,
    ) {
        parent::__construct($requestValidator, $graveSiteRepo, $cemeteryBlockRepo, $eventDispatcher);
    }

    /**
     * @throws NotFoundException when the grave site is not found
     * @throws Exception         when there was any issue within the domain
     * @throws \Throwable        when any error occurred while processing the request
     */
    public function execute(AbstractApplicationRequest $request): ApplicationSuccessResponse
    {
        $isDiscarded = false;

        /** @var DiscardGraveSitePersonInChargeRequest $request */
        $graveSite = $this->getGraveSite($request->id);
        if ($graveSite->personInChargeId() !== null) {
            $graveSite->discardPersonInCharge();
            $isDiscarded = true;
        }
        if ($isDiscarded) {
            $this->graveSiteRepo->save($graveSite);
            $this->eventDispatcher->dispatch(new GraveSitePersonInChargeDiscarded(
                $graveSite->id(),
            ));
        }

        return new DiscardGraveSitePersonInChargeResponse(
            $graveSite->id()->value(),
        );
    }

    protected function supportedRequestClassName(): string
    {
        return DiscardGraveSitePersonInChargeRequest::class;
    }
}
