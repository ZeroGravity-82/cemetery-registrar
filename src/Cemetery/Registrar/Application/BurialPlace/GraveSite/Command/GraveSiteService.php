<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\BurialPlace\GraveSite\Command;

use Cemetery\Registrar\Application\ApplicationService;
use Cemetery\Registrar\Application\BurialPlace\GraveSite\GraveSiteRequestValidator;
use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\GraveSite;
use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\GraveSiteId;
use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\GraveSiteRepository;
use Cemetery\Registrar\Domain\Model\EventDispatcher;
use Cemetery\Registrar\Domain\Model\Exception;
use Cemetery\Registrar\Domain\Model\NotFoundException;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
abstract class GraveSiteService extends ApplicationService
{
    public function __construct(
        protected readonly GraveSiteRepository $graveSiteRepo,
        protected readonly EventDispatcher     $eventDispatcher,
        GraveSiteRequestValidator              $requestValidator,
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
}
