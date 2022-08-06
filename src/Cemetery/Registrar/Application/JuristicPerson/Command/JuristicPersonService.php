<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\JuristicPerson\Command;

use Cemetery\Registrar\Application\ApplicationService;
use Cemetery\Registrar\Application\JuristicPerson\JuristicPersonRequestValidator;
use Cemetery\Registrar\Domain\Model\EventDispatcher;
use Cemetery\Registrar\Domain\Model\Exception;
use Cemetery\Registrar\Domain\Model\NotFoundException;
use Cemetery\Registrar\Domain\Model\Organization\JuristicPerson\JuristicPerson;
use Cemetery\Registrar\Domain\Model\Organization\JuristicPerson\JuristicPersonId;
use Cemetery\Registrar\Domain\Model\Organization\JuristicPerson\JuristicPersonRepository;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
abstract class JuristicPersonService extends ApplicationService
{
    public function __construct(
        protected readonly JuristicPersonRepository $juristicPersonRepo,
        protected readonly EventDispatcher          $eventDispatcher,
        JuristicPersonRequestValidator              $requestValidator,
    ) {
        parent::__construct($requestValidator);
    }

    /**
     * @throws Exception         when the ID is invalid
     * @throws NotFoundException when the juristic person is not found
     */
    protected function getJuristicPerson(string $id): JuristicPerson
    {
        $id = new JuristicPersonId($id);
        /** @var JuristicPerson $juristicPerson */
        $juristicPerson = $this->juristicPersonRepo->findById($id);
        if ($juristicPerson === null) {
            throw new NotFoundException(\sprintf('Юрлицо с ID "%s" не найдено.', $id->value()));
        }

        return $juristicPerson;
    }
}
