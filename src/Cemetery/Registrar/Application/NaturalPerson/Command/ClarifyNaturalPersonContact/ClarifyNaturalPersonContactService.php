<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\NaturalPerson\Command\ClarifyNaturalPersonContact;

use Cemetery\Registrar\Application\ApplicationRequest;
use Cemetery\Registrar\Application\ApplicationSuccessResponse;
use Cemetery\Registrar\Application\NaturalPerson\Command\NaturalPersonService;
use Cemetery\Registrar\Domain\Model\Contact\Address;
use Cemetery\Registrar\Domain\Model\Contact\Email;
use Cemetery\Registrar\Domain\Model\Contact\PhoneNumber;
use Cemetery\Registrar\Domain\Model\EventDispatcher;
use Cemetery\Registrar\Domain\Model\Exception;
use Cemetery\Registrar\Domain\Model\NaturalPerson\NaturalPersonContactClarified;
use Cemetery\Registrar\Domain\Model\NaturalPerson\NaturalPersonRepository;
use Cemetery\Registrar\Domain\Model\NotFoundException;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class ClarifyNaturalPersonContactService extends NaturalPersonService
{
    public function __construct(
        NaturalPersonRepository                     $naturalPersonRepo,
        EventDispatcher                             $eventDispatcher,
        ClarifyNaturalPersonContactRequestValidator $requestValidator,
    ) {
        parent::__construct($naturalPersonRepo, $eventDispatcher, $requestValidator);
    }

    /**
     * @throws NotFoundException when the natural person is not found
     * @throws Exception         when there was any issue within the domain
     * @throws \Throwable        when any error occurred while processing the request
     */
    public function execute(ApplicationRequest $request): ApplicationSuccessResponse
    {
        $isClarified = false;

        /** @var ClarifyNaturalPersonContactRequest $request */
        $naturalPerson = $this->getNaturalPerson($request->id);
        if ($naturalPerson->phone()?->value() !== $request->phone) {
            $naturalPerson->setPhone($this->buildPhone($request));
            $isClarified = true;
        }
        if ($naturalPerson->phoneAdditional()?->value() !== $request->phoneAdditional) {
            $naturalPerson->setPhoneAdditional($this->buildPhoneAdditional($request));
            $isClarified = true;
        }
        if ($naturalPerson->address()?->value() !== $request->address) {
            $naturalPerson->setAddress($this->buildAddress($request));
            $isClarified = true;
        }
        if ($naturalPerson->email()?->value() !== $request->email) {
            $naturalPerson->setEmail($this->buildEmail($request));
            $isClarified = true;
        }
        if ($isClarified) {
            $this->naturalPersonRepo->save($naturalPerson);
            $this->eventDispatcher->dispatch(new NaturalPersonContactClarified(
                $naturalPerson->id(),
                $naturalPerson->phone(),
                $naturalPerson->phoneAdditional(),
                $naturalPerson->address(),
                $naturalPerson->email(),
            ));
        }

        return new ClarifyNaturalPersonContactResponse(
            $naturalPerson->id()->value(),
        );
    }

    protected function supportedRequestClassName(): string
    {
        return ClarifyNaturalPersonContactRequest::class;
    }

    /**
     * @throws Exception when the phone number has invalid value
     */
    private function buildPhone(ApplicationRequest $request): ?PhoneNumber
    {
        /** @var ClarifyNaturalPersonContactRequest $request */
        return $request->phone !== null ? new PhoneNumber($request->phone) : null;
    }

    /**
     * @throws Exception when the additional phone number has invalid value
     */
    private function buildPhoneAdditional(ApplicationRequest $request): ?PhoneNumber
    {
        /** @var ClarifyNaturalPersonContactRequest $request */
        return $request->phoneAdditional !== null ? new PhoneNumber($request->phoneAdditional) : null;
    }

    /**
     * @throws Exception when the address has invalid value
     */
    private function buildAddress(ApplicationRequest $request): ?Address
    {
        /** @var ClarifyNaturalPersonContactRequest $request */
        return $request->address !== null ? new Address($request->address) : null;
    }

    /**
     * @throws Exception when the email has invalid value
     */
    private function buildEmail(ApplicationRequest $request): ?Email
    {
        /** @var ClarifyNaturalPersonContactRequest $request */
        return $request->email !== null ? new Email($request->email) : null;
    }
}
