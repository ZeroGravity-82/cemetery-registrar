<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\NaturalPerson\Command;

use Cemetery\Registrar\Application\AbstractApplicationService;
use Cemetery\Registrar\Application\NaturalPerson\AbstractNaturalPersonRequestValidator;
use Cemetery\Registrar\Domain\Model\EventDispatcher;
use Cemetery\Registrar\Domain\Model\Exception;
use Cemetery\Registrar\Domain\Model\NaturalPerson\NaturalPerson;
use Cemetery\Registrar\Domain\Model\NaturalPerson\NaturalPersonId;
use Cemetery\Registrar\Domain\Model\NaturalPerson\NaturalPersonRepositoryInterface;
use Cemetery\Registrar\Domain\Model\NotFoundException;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
abstract class AbstractNaturalPersonService extends AbstractApplicationService
{
    public function __construct(
        AbstractNaturalPersonRequestValidator      $requestValidator,
        protected NaturalPersonRepositoryInterface $naturalPersonRepo,
        protected EventDispatcher                  $eventDispatcher,
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
