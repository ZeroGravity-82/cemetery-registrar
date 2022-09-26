<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\BurialPlace\GraveSite\Command\ReplaceGraveSitePersonInCharge;

use Cemetery\Registrar\Application\ApplicationRequest;
use Cemetery\Registrar\Application\ApplicationSuccessResponse;
use Cemetery\Registrar\Application\BurialPlace\GraveSite\Command\GraveSiteService;
use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\CemeteryBlock;
use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\CemeteryBlockRepository;
use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\GraveSite;
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
class ReplaceGraveSitePersonInChargeService extends GraveSiteService
{
    public function __construct(
        GraveSiteRepository                            $graveSiteRepo,
        CemeteryBlockRepository                        $cemeteryBlockRepo,
        EventDispatcher                                $eventDispatcher,
        ReplaceGraveSitePersonInChargeRequestValidator $requestValidator,
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

        /** @var ReplaceGraveSitePersonInChargeRequest $request */
        $graveSite     = $this->getGraveSite($request->id);
        $cemeteryBlock = $this->getCemeteryBlock($request->cemeteryBlockId);
        $rowInBlock    = $this->buildRowInBlock($request);
        $positionInRow = $this->buildPositionInRow($request);
        if (!$this->isSameCemeteryBlock($cemeteryBlock, $graveSite)) {
            $graveSite->assignCemeteryBlock($cemeteryBlock);
            $isClarified = true;
        }
        if (!$this->isSameRowInBlock($rowInBlock, $graveSite)) {
            $graveSite->setRowInBlock($rowInBlock);
            $isClarified = true;
        }
        if (!$this->isSamePositionInRow($positionInRow, $graveSite)) {
            $graveSite->setPositionInRow($positionInRow);
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
        return ReplaceGraveSitePersonInChargeRequest::class;
    }

    private function isSameCemeteryBlock(CemeteryBlock $cemeteryBlock, GraveSite $graveSite): bool
    {
        return $cemeteryBlock->id()->isEqual($graveSite->cemeteryBlockId());
    }

    /**
     * @throws Exception when the row in block has invalid value
     */
    private function buildRowInBlock(ApplicationRequest $request): RowInBlock
    {
        /** @var ReplaceGraveSitePersonInChargeRequest $request */
        return new RowInBlock($request->rowInBlock);
    }

    private function isSameRowInBlock(RowInBlock $rowInBlock, GraveSite $graveSite): bool
    {
        return $rowInBlock->isEqual($graveSite->rowInBlock());
    }

    /**
     * @throws Exception when the position in row has invalid value
     */
    private function buildPositionInRow(ApplicationRequest $request): ?PositionInRow
    {
        /** @var ReplaceGraveSitePersonInChargeRequest $request */
        return $request->positionInRow ? new PositionInRow($request->positionInRow) : null;
    }

    private function isSamePositionInRow(?PositionInRow $positionInRow, GraveSite $graveSite): bool
    {
        return $positionInRow !== null && $graveSite->positionInRow() !== null
            ? $positionInRow->isEqual($graveSite->positionInRow())
            : $positionInRow === null && $graveSite->positionInRow() === null;
    }
}
