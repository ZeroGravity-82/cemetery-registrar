<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\BurialPlace\GraveSite\Command;

use Cemetery\Registrar\Application\AbstractApplicationService;
use Cemetery\Registrar\Application\BurialPlace\GraveSite\AbstractGraveSiteRequestValidator;
use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\CemeteryBlock;
use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\CemeteryBlockId;
use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\CemeteryBlockRepositoryInterface;
use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\GraveSite;
use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\GraveSiteId;
use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\GraveSiteRepositoryInterface;
use Cemetery\Registrar\Domain\Model\EventDispatcher;
use Cemetery\Registrar\Domain\Model\Exception;
use Cemetery\Registrar\Domain\Model\NotFoundException;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
abstract class AbstractGraveSiteService extends AbstractApplicationService
{
    public function __construct(
        AbstractGraveSiteRequestValidator          $requestValidator,
        protected GraveSiteRepositoryInterface     $graveSiteRepo,
        protected CemeteryBlockRepositoryInterface $cemeteryBlockRepo,
        protected EventDispatcher                  $eventDispatcher,
    ) {
        parent::__construct($requestValidator);
    }

    /**
     * @throws Exception         when the ID is invalid
     * @throws NotFoundException when the grave site is not found
     */
    protected function getGraveSite(string $id): GraveSite
    {
        $id = new GraveSiteId($id);
        /** @var GraveSite $graveSite */
        $graveSite = $this->graveSiteRepo->findById($id);
        if ($graveSite === null) {
            throw new NotFoundException(\sprintf('Участок с ID "%s" не найден.', $id->value()));
        }

        return $graveSite;
    }

    /**
     * @throws Exception         when the ID is invalid
     * @throws NotFoundException when the cemetery block is not found
     */
    protected function getCemeteryBlock(string $id): CemeteryBlock
    {
        $id = new CemeteryBlockId($id);
        /** @var CemeteryBlock $cemeteryBlock */
        $cemeteryBlock = $this->cemeteryBlockRepo->findById($id);
        if ($cemeteryBlock === null) {
            throw new NotFoundException(\sprintf('Квартал с ID "%s" не найден.', $id->value()));
        }

        return $cemeteryBlock;
    }
}
