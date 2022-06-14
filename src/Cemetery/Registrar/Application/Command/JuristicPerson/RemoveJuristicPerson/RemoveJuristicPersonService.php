<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\Command\JuristicPerson\RemoveJuristicPerson;

use Cemetery\Registrar\Application\ApplicationService;
use Cemetery\Registrar\Domain\Model\Organization\JuristicPerson\JuristicPerson;
use Cemetery\Registrar\Domain\Model\Organization\JuristicPerson\JuristicPersonId;
use Cemetery\Registrar\Domain\Model\Organization\JuristicPerson\JuristicPersonRemover;
use Cemetery\Registrar\Domain\Model\Organization\JuristicPerson\JuristicPersonRepository;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class RemoveJuristicPersonService extends ApplicationService
{
    /**
     * @param JuristicPersonRepository $juristicPersonRepo
     * @param JuristicPersonRemover    $juristicPersonRemover
     */
    public function __construct(
        private readonly JuristicPersonRepository $juristicPersonRepo,
        private readonly JuristicPersonRemover    $juristicPersonRemover,
    ) {}

    /**
     * @param RemoveJuristicPersonRequest $request
     */
    public function execute($request): void
    {
        $this->assertInstanceOf($request, RemoveJuristicPersonRequest::class);
        $juristicPerson = $this->getJuristicPerson($request->id);
        $this->juristicPersonRemover->remove($juristicPerson);
    }

        /**
     * @param string $id
     *
     * @return JuristicPerson
     *
     * @throws \RuntimeException when the juristic person does not exist
     */
    private function getJuristicPerson(string $id): JuristicPerson
    {
        $id             = new JuristicPersonId($id);
        $juristicPerson = $this->juristicPersonRepo->findById($id);
        if (!$juristicPerson) {
            throw new \RuntimeException(\sprintf('Юридическое лицо с ID "%s" не найдено.', $id));
        }

        return $juristicPerson;
    }
}
