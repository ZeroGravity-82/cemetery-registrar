<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\JuristicPerson\Command\RemoveJuristicPerson;

use Cemetery\Registrar\Application\ApplicationService;
use Cemetery\Registrar\Domain\Model\EventDispatcher;
use Cemetery\Registrar\Domain\Model\Organization\JuristicPerson\JuristicPerson;
use Cemetery\Registrar\Domain\Model\Organization\JuristicPerson\JuristicPersonId;
use Cemetery\Registrar\Domain\Model\Organization\JuristicPerson\JuristicPersonRemoved;
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
     * @param EventDispatcher          $eventDispatcher
     */
    public function __construct(
        private readonly JuristicPersonRepository $juristicPersonRepo,
        private readonly JuristicPersonRemover    $juristicPersonRemover,
        private readonly EventDispatcher          $eventDispatcher,
    ) {}

    /**
     * {@inheritdoc}
     */
    public function supportedRequestClassName(): string
    {
        return RemoveJuristicPersonRequest::class;
    }

    /**
     * @param RemoveJuristicPersonRequest $request
     */
    public function execute($request): void
    {
        $this->assertSupportedRequestClass($request);
        $juristicPerson = $this->getJuristicPerson($request->id);
        $this->juristicPersonRemover->remove($juristicPerson);
        $this->eventDispatcher->dispatch(new JuristicPersonRemoved(
            $juristicPerson->id(),
        ));
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
