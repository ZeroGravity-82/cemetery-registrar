<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Model\NaturalPerson;

use Cemetery\Registrar\Domain\Model\Contact\Address;
use Cemetery\Registrar\Domain\Model\Contact\Email;
use Cemetery\Registrar\Domain\Model\Contact\PhoneNumber;
use Cemetery\Registrar\Domain\Model\NaturalPerson\NaturalPersonContactClarified;
use Cemetery\Registrar\Domain\Model\NaturalPerson\NaturalPersonId;
use Cemetery\Tests\Registrar\Domain\Model\AbstractEventTest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class NaturalPersonContactClarifiedTest extends AbstractEventTest
{
    private NaturalPersonId $id;
    private PhoneNumber     $phone;
    private PhoneNumber     $phoneAdditional;
    private Address         $address;
    private Email           $email;

    public function setUp(): void
    {
        $this->id              = new NaturalPersonId('NP001');
        $this->phone           = new PhoneNumber('+7-111-111-11-11');
        $this->phoneAdditional = new PhoneNumber('+7-222-222-22-22');
        $this->address         = new Address('Новосибирск, Ленина 1');
        $this->email           = new Email('some@email.com');
        $this->event           = new NaturalPersonContactClarified(
            $this->id,
            $this->phone,
            $this->phoneAdditional,
            $this->address,
            $this->email,
        );
    }

    public function testItSuccessfullyCreated(): void
    {
        $this->assertTrue($this->id->isEqual($this->event->id()));
        $this->assertTrue($this->phone->isEqual($this->event->phone()));
        $this->assertTrue($this->phoneAdditional->isEqual($this->event->phoneAdditional()));
        $this->assertTrue($this->address->isEqual($this->event->address()));
        $this->assertTrue($this->email->isEqual($this->event->email()));
    }

    public function testItSuccessfullyCreatedWithoutPhone(): void
    {
        $this->event = new NaturalPersonContactClarified(
            $this->id,
            null,
            $this->phoneAdditional,
            $this->address,
            $this->email,
        );

        $this->assertTrue($this->id->isEqual($this->event->id()));
        $this->assertNull($this->event->phone());
        $this->assertTrue($this->phoneAdditional->isEqual($this->event->phoneAdditional()));
        $this->assertTrue($this->address->isEqual($this->event->address()));
        $this->assertTrue($this->email->isEqual($this->event->email()));
    }

    public function testItSuccessfullyCreatedWithoutPhoneAdditional(): void
    {
        $this->event = new NaturalPersonContactClarified(
            $this->id,
            $this->phone,
            null,
            $this->address,
            $this->email,
        );

        $this->assertTrue($this->id->isEqual($this->event->id()));
        $this->assertTrue($this->phone->isEqual($this->event->phone()));
        $this->assertNull($this->event->phoneAdditional());
        $this->assertTrue($this->address->isEqual($this->event->address()));
        $this->assertTrue($this->email->isEqual($this->event->email()));
    }

    public function testItSuccessfullyCreatedWithoutAddress(): void
    {
        $this->event = new NaturalPersonContactClarified(
            $this->id,
            $this->phone,
            $this->phoneAdditional,
            null,
            $this->email,
        );

        $this->assertTrue($this->id->isEqual($this->event->id()));
        $this->assertTrue($this->phone->isEqual($this->event->phone()));
        $this->assertTrue($this->phoneAdditional->isEqual($this->event->phoneAdditional()));
        $this->assertNull($this->event->address());
        $this->assertTrue($this->email->isEqual($this->event->email()));
    }

    public function testItSuccessfullyCreatedWithoutEmail(): void
    {
        $this->event = new NaturalPersonContactClarified(
            $this->id,
            $this->phone,
            $this->phoneAdditional,
            $this->address,
            null,
        );

        $this->assertTrue($this->id->isEqual($this->event->id()));
        $this->assertTrue($this->phone->isEqual($this->event->phone()));
        $this->assertTrue($this->phoneAdditional->isEqual($this->event->phoneAdditional()));
        $this->assertTrue($this->address->isEqual($this->event->address()));
        $this->assertNull($this->event->email());
    }
}
