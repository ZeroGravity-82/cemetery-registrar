<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Model\BurialPlace;

use Cemetery\Registrar\Domain\Model\BurialPlace\BurialPlace;
use Cemetery\Registrar\Domain\Model\NaturalPerson\NaturalPersonId;
use Cemetery\Tests\Registrar\Domain\Model\AggregateRootTest;
use DataFixtures\NaturalPerson\NaturalPersonProvider;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
abstract class BurialPlaceTest extends AggregateRootTest
{
    protected BurialPlace $burialPlace;

    public function testItSetsPersonInChargeId(): void
    {
        $personInCharge = NaturalPersonProvider::getNaturalPersonB();
        $this->burialPlace->setPersonInCharge($personInCharge);
        $this->assertInstanceOf(NaturalPersonId::class, $this->burialPlace->personInChargeId());
        $this->assertTrue($this->burialPlace->personInChargeId()->isEqual($personInCharge->id()));

        $this->burialPlace->setPersonInCharge(null);
        $this->assertNull($this->burialPlace->personInChargeId());
    }
}
