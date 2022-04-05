<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\JuristicPerson;

use Cemetery\Registrar\Domain\Organization\JuristicPerson\JuristicPerson;
use Cemetery\Registrar\Domain\Organization\JuristicPerson\JuristicPersonId;
use Cemetery\Registrar\Domain\Organization\JuristicPerson\JuristicPersonRepositoryInterface;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
abstract class JuristicPersonService
{
    /**
     * @param JuristicPersonRepositoryInterface $juristicPersonRepo
     */
    public function __construct(
        protected JuristicPersonRepositoryInterface $juristicPersonRepo,
    ) {}

    /**
     * @param string $juristicPersonId
     *
     * @return JuristicPerson
     *
     * @throws \RuntimeException when the juristic person does not exist
     */
    protected function getJuristicPerson(string $juristicPersonId): JuristicPerson
    {
        $juristicPersonId = new JuristicPersonId($juristicPersonId);
        $juristicPerson   = $this->juristicPersonRepo->findById($juristicPersonId);
        if (!$juristicPerson) {
            throw new \RuntimeException('Юридическое лицо с ID "%s" не найдено.');
        }

        return $juristicPerson;
    }
}
