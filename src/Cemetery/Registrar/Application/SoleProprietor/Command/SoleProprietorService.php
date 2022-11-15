<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\SoleProprietor\Command;

use Cemetery\Registrar\Application\ApplicationService;
use Cemetery\Registrar\Application\SoleProprietor\SoleProprietorRequestValidator;
use Cemetery\Registrar\Domain\Model\EventDispatcher;
use Cemetery\Registrar\Domain\Model\Exception;
use Cemetery\Registrar\Domain\Model\NotFoundException;
use Cemetery\Registrar\Domain\Model\Organization\SoleProprietor\SoleProprietor;
use Cemetery\Registrar\Domain\Model\Organization\SoleProprietor\SoleProprietorId;
use Cemetery\Registrar\Domain\Model\Organization\SoleProprietor\SoleProprietorRepository;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
abstract class SoleProprietorService extends ApplicationService
{
    public function __construct(
        protected SoleProprietorRepository $soleProprietorRepo,
        protected EventDispatcher          $eventDispatcher,
        SoleProprietorRequestValidator     $requestValidator,
    ) {
        parent::__construct($requestValidator);
    }

    /**
     * @throws Exception         when the ID is invalid
     * @throws NotFoundException when the juristic person is not found
     */
    protected function getSoleProprietor(string $id): SoleProprietor
    {
        $id = new SoleProprietorId($id);
        /** @var SoleProprietor $soleProprietor */
        $soleProprietor = $this->soleProprietorRepo->findById($id);
        if ($soleProprietor === null) {
            throw new NotFoundException(\sprintf('ИП с ID "%s" не найден.', $id->value()));
        }

        return $soleProprietor;
    }
}
