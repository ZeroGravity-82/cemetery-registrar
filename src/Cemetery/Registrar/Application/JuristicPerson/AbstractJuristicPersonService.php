<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\JuristicPerson;

use Cemetery\Registrar\Domain\Organization\JuristicPerson\JuristicPerson;
use Cemetery\Registrar\Domain\Organization\JuristicPerson\JuristicPersonId;
use Cemetery\Registrar\Domain\Organization\JuristicPerson\JuristicPersonRepository;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
abstract class AbstractJuristicPersonService
{
    /**
     * @param JuristicPersonRepository $juristicPersonRepo
     */
    public function __construct(
        protected readonly JuristicPersonRepository $juristicPersonRepo,
    ) {}

    /**
     * @param string $id
     *
     * @return JuristicPerson
     *
     * @throws \RuntimeException when the juristic person does not exist
     */
    protected function getJuristicPerson(string $id): JuristicPerson
    {
        $id             = new JuristicPersonId($id);
        $juristicPerson = $this->juristicPersonRepo->findById($id);
        if (!$juristicPerson) {
            throw new \RuntimeException(\sprintf('Юридическое лицо с ID "%s" не найдено.', $id));
        }

        return $juristicPerson;
    }
}
