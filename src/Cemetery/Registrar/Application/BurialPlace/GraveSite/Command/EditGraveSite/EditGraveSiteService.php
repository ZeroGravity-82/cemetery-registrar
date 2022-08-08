<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\BurialPlace\GraveSite\Command\EditGraveSite;

use Cemetery\Registrar\Application\ApplicationRequest;
use Cemetery\Registrar\Application\ApplicationSuccessResponse;
use Cemetery\Registrar\Application\BurialPlace\GraveSite\Command\GraveSiteService;
use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\CemeteryBlockId;
use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\GraveSiteEdited;
use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\GraveSiteRepository;
use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\GraveSiteSize;
use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\PositionInRow;
use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\RowInBlock;
use Cemetery\Registrar\Domain\Model\EventDispatcher;
use Cemetery\Registrar\Domain\Model\Exception;
use Cemetery\Registrar\Domain\Model\NotFoundException;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class EditGraveSiteService extends GraveSiteService
{
    public function __construct(
        GraveSiteRepository           $graveSiteRepo,
        EventDispatcher               $eventDispatcher,
        EditGraveSiteRequestValidator $requestValidator,
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
        /** @var EditGraveSiteRequest $request */
        $graveSite = $this->getGraveSite($request->id);
        $graveSite->setCemeteryBlockId(new CemeteryBlockId($request->cemeteryBlockId));
        $graveSite->setRowInBlock(new RowInBlock($request->rowInBlock));
        $graveSite->setPositionInRow($request->positionInRow ? new PositionInRow($request->positionInRow) : null);
//        $graveSite->setGeoPosition(new GraveSiteName($request->ge));
        $graveSite->setSize($request->size ? new GraveSiteSize($request->size) : null);
        $this->graveSiteRepo->save($graveSite);
        $this->eventDispatcher->dispatch(new GraveSiteEdited(
            $graveSite->id(),
            $graveSite->cemeteryBlockId(),
            $graveSite->rowInBlock(),
            $graveSite->positionInRow(),
        ));

        return new EditGraveSiteResponse(
            $graveSite->id()->value(),
        );
    }

    protected function supportedRequestClassName(): string
    {
        return EditGraveSiteRequest::class;
    }
}
