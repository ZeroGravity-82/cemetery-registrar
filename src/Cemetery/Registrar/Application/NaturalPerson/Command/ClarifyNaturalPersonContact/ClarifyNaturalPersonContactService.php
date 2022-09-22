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
use Cemetery\Registrar\Domain\Model\NaturalPerson\NaturalPerson;
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
        $naturalPerson   = $this->getNaturalPerson($request->id);
        $phone           = $this->buildPhone($request);
        $phoneAdditional = $this->buildPhoneAdditional($request);
        $address         = $this->buildAddress($request);
        $email           = $this->buildEmail($request);
        if (!$this->isSamePhone($phone, $naturalPerson)) {
            $naturalPerson->setPhone($phone);
            $isClarified = true;
        }
        if (!$this->isSamePhoneAdditional($phoneAdditional, $naturalPerson)) {
            $naturalPerson->setPhoneAdditional($phoneAdditional);
            $isClarified = true;
        }
        if (!$this->isSameAddress($address, $naturalPerson)) {
            $naturalPerson->setAddress($address);
            $isClarified = true;
        }
        if (!$this->isSameEmail($email, $naturalPerson)) {
            $naturalPerson->setEmail($email);
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

    private function isSamePhone(?PhoneNumber $phone, NaturalPerson $naturalPerson): bool
    {
        return $phone !== null && $naturalPerson->phone() !== null
            ? $phone->isEqual($naturalPerson->phone())
            : $phone === null && $naturalPerson->phone() === null;
    }

    /**
     * @throws Exception when the additional phone number has invalid value
     */
    private function buildPhoneAdditional(ApplicationRequest $request): ?PhoneNumber
    {
        /** @var ClarifyNaturalPersonContactRequest $request */
        return $request->phoneAdditional !== null ? new PhoneNumber($request->phoneAdditional) : null;
    }

    private function isSamePhoneAdditional(?PhoneNumber $phoneAdditional, NaturalPerson $naturalPerson): bool
    {
        return $phoneAdditional !== null && $naturalPerson->phoneAdditional() !== null
            ? $phoneAdditional->isEqual($naturalPerson->phoneAdditional())
            : $phoneAdditional === null && $naturalPerson->phoneAdditional() === null;
    }

    /**
     * @throws Exception when the address has invalid value
     */
    private function buildAddress(ApplicationRequest $request): ?Address
    {
        /** @var ClarifyNaturalPersonContactRequest $request */
        return $request->address !== null ? new Address($request->address) : null;
    }

    private function isSameAddress(?Address $address, NaturalPerson $naturalPerson): bool
    {
        return $address !== null && $naturalPerson->address() !== null
            ? $address->isEqual($naturalPerson->address())
            : $address === null && $naturalPerson->address() === null;
    }

    /**
     * @throws Exception when the email has invalid value
     */
    private function buildEmail(ApplicationRequest $request): ?Email
    {
        /** @var ClarifyNaturalPersonContactRequest $request */
        return $request->email !== null ? new Email($request->email) : null;
    }

    private function isSameEmail(?Email $email, NaturalPerson $naturalPerson): bool
    {
        return $email !== null && $naturalPerson->email() !== null
            ? $email->isEqual($naturalPerson->email())
            : $email === null && $naturalPerson->email() === null;
    }
}
