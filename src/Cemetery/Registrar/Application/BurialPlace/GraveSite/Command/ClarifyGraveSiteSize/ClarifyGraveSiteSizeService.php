<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\BurialPlace\GraveSite\Command\ClarifyGraveSiteSize;

use Cemetery\Registrar\Application\ApplicationRequest;
use Cemetery\Registrar\Application\ApplicationSuccessResponse;
use Cemetery\Registrar\Application\BurialPlace\GraveSite\Command\GraveSiteService;
use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\CemeteryBlockRepository;
use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\GraveSiteRepository;
use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\GraveSiteSize;
use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\GraveSiteSizeClarified;
use Cemetery\Registrar\Domain\Model\EventDispatcher;
use Cemetery\Registrar\Domain\Model\Exception;
use Cemetery\Registrar\Domain\Model\NotFoundException;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class ClarifyGraveSiteSizeService extends GraveSiteService
{
    public function __construct(
        GraveSiteRepository                  $graveSiteRepo,
        CemeteryBlockRepository              $cemeteryBlockRepo,
        EventDispatcher                      $eventDispatcher,
        ClarifyGraveSiteSizeRequestValidator $requestValidator,
    ) {
        parent::__construct($graveSiteRepo, $cemeteryBlockRepo, $eventDispatcher, $requestValidator);
    }

    /**
     * @throws NotFoundException when the grave site is not found
     * @throws Exception         when there was any issue within the domain
     * @throws \Throwable        when any error occurred while processing the request
     */
    public function execute(ApplicationRequest $request): ApplicationSuccessResponse
    {
        $isClarified = false;

        /** @var ClarifyGraveSiteSizeRequest $request */
        $graveSite = $this->getGraveSite($request->id);
        if ($graveSite->size()?->value() !== $request->size) {
            $graveSite->setSize($this->buildSize($request));
            $isClarified = true;
        }
        if ($isClarified) {
            $this->graveSiteRepo->save($graveSite);
            $this->eventDispatcher->dispatch(new GraveSiteSizeClarified(
                $graveSite->id(),
                $graveSite->size(),
            ));
        }

        return new ClarifyGraveSiteSizeResponse(
            $graveSite->id()->value(),
        );
    }

    protected function supportedRequestClassName(): string
    {
        return ClarifyGraveSiteSizeRequest::class;
    }

    /**
     * @throws Exception when the size has invalid value
     */
    private function buildSize(ApplicationRequest $request): ?GraveSiteSize
    {
        /** @var ClarifyGraveSiteSizeRequest $request */
        return $request->size ? new GraveSiteSize($request->size) : null;
    }
}
