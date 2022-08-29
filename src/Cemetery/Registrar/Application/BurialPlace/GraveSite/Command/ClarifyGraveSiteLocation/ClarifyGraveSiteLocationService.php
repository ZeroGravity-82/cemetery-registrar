<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\BurialPlace\GraveSite\Command\ClarifyGraveSiteLocation;

use Cemetery\Registrar\Application\ApplicationRequest;
use Cemetery\Registrar\Application\ApplicationSuccessResponse;
use Cemetery\Registrar\Application\BurialPlace\GraveSite\Command\GraveSiteService;
use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\CemeteryBlockRepository;
use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\GraveSiteLocationClarified;
use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\GraveSiteRepository;
use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\PositionInRow;
use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\RowInBlock;
use Cemetery\Registrar\Domain\Model\EventDispatcher;
use Cemetery\Registrar\Domain\Model\Exception;
use Cemetery\Registrar\Domain\Model\NotFoundException;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class ClarifyGraveSiteLocationService extends GraveSiteService
{
    public function __construct(
        GraveSiteRepository                      $graveSiteRepo,
        CemeteryBlockRepository                  $cemeteryBlockRepo,
        EventDispatcher                          $eventDispatcher,
        ClarifyGraveSiteLocationRequestValidator $requestValidator,
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

        /** @var ClarifyGraveSiteLocationRequest $request */
        $graveSite = $this->getGraveSite($request->id);
        if ($graveSite->cemeteryBlockId()->value() !== $request->cemeteryBlockId) {
            $cemeteryBlock = $this->getCemeteryBlock($request->cemeteryBlockId);
            $graveSite->assignCemeteryBlock($cemeteryBlock);
            $isClarified = true;
        }
        if ($graveSite->rowInBlock()->value() !== $request->rowInBlock) {
            $graveSite->setRowInBlock($this->buildRowInBlocks($request));
            $isClarified = true;
        }
        if ($graveSite->positionInRow()?->value() !== $request->positionInRow) {
            $graveSite->setPositionInRow($this->buildPositionInRow($request));
            $isClarified = true;
        }
        if ($isClarified) {
            $this->graveSiteRepo->save($graveSite);
            $this->eventDispatcher->dispatch(new GraveSiteLocationClarified(
                $graveSite->id(),
                $graveSite->cemeteryBlockId(),
                $graveSite->rowInBlock(),
                $graveSite->positionInRow(),
            ));
        }

        return new ClarifyGraveSiteLocationResponse(
            $graveSite->id()->value(),
        );
    }

    protected function supportedRequestClassName(): string
    {
        return ClarifyGraveSiteLocationRequest::class;
    }

    /**
     * @throws Exception when the row in block has invalid value
     */
    private function buildRowInBlocks(ApplicationRequest $request): RowInBlock
    {
        /** @var ClarifyGraveSiteLocationRequest $request */
        return new RowInBlock($request->rowInBlock);
    }

    /**
     * @throws Exception when the position in row has invalid value
     */
    private function buildPositionInRow(ApplicationRequest $request): ?PositionInRow
    {
        /** @var ClarifyGraveSiteLocationRequest $request */
        return $request->positionInRow ? new PositionInRow($request->positionInRow) : null;
    }
}
