<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\JuristicPerson\Command\RemoveJuristicPerson;

use Cemetery\Registrar\Application\ApplicationRequest;
use Cemetery\Registrar\Application\ApplicationSuccessResponse;
use Cemetery\Registrar\Application\ApplicationService;
use Cemetery\Registrar\Application\Notification;
use Cemetery\Registrar\Domain\Model\EventDispatcher;
use Cemetery\Registrar\Domain\Model\Exception;
use Cemetery\Registrar\Domain\Model\NotFoundException;
use Cemetery\Registrar\Domain\Model\Organization\JuristicPerson\JuristicPerson;
use Cemetery\Registrar\Domain\Model\Organization\JuristicPerson\JuristicPersonId;
use Cemetery\Registrar\Domain\Model\Organization\JuristicPerson\JuristicPersonRemoved;
use Cemetery\Registrar\Domain\Model\Organization\JuristicPerson\JuristicPersonRepository;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class RemoveJuristicPersonService extends ApplicationService
{
    public function __construct(
        private readonly JuristicPersonRepository $juristicPersonRepo,
        private readonly EventDispatcher          $eventDispatcher,
    ) {}

    /**
     * @throws \InvalidArgumentException when the request is not an instance of the supported class
     */
    public function validate(ApplicationRequest $request): Notification
    {
        $this->assertSupportedRequestClass($request);

        /** @var RemoveJuristicPersonRequest $request */
        return $this->requestValidator->validate($request);
    }

    /**
     * @throws Exception  when there was any issue within the domain
     * @throws \Throwable when any error occurred while processing the request
     */
    public function execute(ApplicationRequest $request): ApplicationSuccessResponse
    {
        $juristicPerson = $this->getJuristicPerson($request->id);
        $this->juristicPersonRepo->remove($juristicPerson);
        $this->eventDispatcher->dispatch(new JuristicPersonRemoved(
            $juristicPerson->id(),
        ));
    }

    protected function supportedRequestClassName(): string
    {
        return RemoveJuristicPersonRequest::class;
    }

    /**
     * @throws Exception         when the ID is invalid
     * @throws NotFoundException when the juristic person is not found
     */
    private function getJuristicPerson(string $id): JuristicPerson
    {
        $id = new JuristicPersonId($id);
        /** @var JuristicPerson $juristicPerson */
        $juristicPerson = $this->juristicPersonRepo->findById($id);
        if (!$juristicPerson) {
            throw new NotFoundException(\sprintf('Юридическое лицо с ID "%s" не найдено.', $id));
        }

        return $juristicPerson;
    }
}
