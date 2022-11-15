<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\BurialPlace\GraveSite\Command\ClarifyGraveSiteSize;

use Cemetery\Registrar\Application\ApplicationRequest;
use Cemetery\Registrar\Application\ApplicationSuccessResponse;
use Cemetery\Registrar\Application\BurialPlace\GraveSite\Command\GraveSiteService;
use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\CemeteryBlockRepository;
use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\GraveSite;
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
        ClarifyGraveSiteSizeRequestValidator $requestValidator,
        GraveSiteRepository                  $graveSiteRepo,
        CemeteryBlockRepository              $cemeteryBlockRepo,
        EventDispatcher                      $eventDispatcher,
    ) {
        parent::__construct($requestValidator, $graveSiteRepo, $cemeteryBlockRepo, $eventDispatcher);
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
        $size      = $this->buildSize($request);
        if (!$this->isSameSize($size, $graveSite)) {
            $graveSite->setSize($size);
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
    private function buildSize(ApplicationRequest $request): GraveSiteSize
    {
        /** @var ClarifyGraveSiteSizeRequest $request */
        return new GraveSiteSize($request->size);
    }

    private function isSameSize(GraveSiteSize $size, GraveSite $graveSite): bool
    {
        return $graveSite->size() !== null &&
               $size->isEqual($graveSite->size());
    }
}
