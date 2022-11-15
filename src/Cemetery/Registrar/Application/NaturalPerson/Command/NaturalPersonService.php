<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\NaturalPerson\Command;

use Cemetery\Registrar\Application\ApplicationService;
use Cemetery\Registrar\Application\NaturalPerson\NaturalPersonRequestValidator;
use Cemetery\Registrar\Domain\Model\EventDispatcher;
use Cemetery\Registrar\Domain\Model\Exception;
use Cemetery\Registrar\Domain\Model\NaturalPerson\NaturalPerson;
use Cemetery\Registrar\Domain\Model\NaturalPerson\NaturalPersonId;
use Cemetery\Registrar\Domain\Model\NaturalPerson\NaturalPersonRepository;
use Cemetery\Registrar\Domain\Model\NotFoundException;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
abstract class NaturalPersonService extends ApplicationService
{
    public function __construct(
        protected NaturalPersonRepository $naturalPersonRepo,
        protected EventDispatcher         $eventDispatcher,
        NaturalPersonRequestValidator     $requestValidator,
    ) {
        parent::__construct($requestValidator);
    }

    /**
     * @throws Exception         when the ID is invalid
     * @throws NotFoundException when the natural person is not found
     */
    protected function getNaturalPerson(string $id): NaturalPerson
    {
        $id = new NaturalPersonId($id);
        /** @var NaturalPerson $naturalPerson */
        $naturalPerson = $this->naturalPersonRepo->findById($id);
        if ($naturalPerson === null) {
            throw new NotFoundException(\sprintf('Физлицо с ID "%s" не найдено.', $id->value()));
        }

        return $naturalPerson;
    }
}
