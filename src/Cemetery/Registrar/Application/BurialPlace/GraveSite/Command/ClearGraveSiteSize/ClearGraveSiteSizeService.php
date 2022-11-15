<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\BurialPlace\GraveSite\Command\ClearGraveSiteSize;

use Cemetery\Registrar\Application\AbstractApplicationRequest;
use Cemetery\Registrar\Application\ApplicationSuccessResponse;
use Cemetery\Registrar\Application\BurialPlace\GraveSite\Command\AbstractGraveSiteService;
use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\CemeteryBlockRepositoryInterface;
use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\GraveSiteRepositoryInterface;
use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\GraveSiteSizeCleared;
use Cemetery\Registrar\Domain\Model\EventDispatcher;
use Cemetery\Registrar\Domain\Model\Exception;
use Cemetery\Registrar\Domain\Model\NotFoundException;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class ClearGraveSiteSizeService extends AbstractGraveSiteService
{
    public function __construct(
        ClearGraveSiteSizeRequestValidator $requestValidator,
        GraveSiteRepositoryInterface       $graveSiteRepo,
        CemeteryBlockRepositoryInterface   $cemeteryBlockRepo,
        EventDispatcher                    $eventDispatcher,
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
        $isCleared = false;

        /** @var ClearGraveSiteSizeRequest $request */
        $graveSite = $this->getGraveSite($request->id);
        if ($graveSite->size() !== null) {
            $graveSite->setSize(null);
            $isCleared = true;
        }
        if ($isCleared) {
            $this->graveSiteRepo->save($graveSite);
            $this->eventDispatcher->dispatch(new GraveSiteSizeCleared(
                $graveSite->id(),
            ));
        }

        return new ClearGraveSiteSizeResponse(
            $graveSite->id()->value(),
        );
    }

    protected function supportedRequestClassName(): string
    {
        return ClearGraveSiteSizeRequest::class;
    }
}
