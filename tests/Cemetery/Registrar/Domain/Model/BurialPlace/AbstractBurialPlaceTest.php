<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Model\BurialPlace;

use Cemetery\Registrar\Domain\Model\BurialPlace\AbstractBurialPlace;
use Cemetery\Tests\Registrar\Domain\Model\AbstractAggregateRootTest;
use DataFixtures\NaturalPerson\NaturalPersonProvider;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
abstract class AbstractBurialPlaceTest extends AbstractAggregateRootTest
{
    protected AbstractBurialPlace $burialPlace;

    public function testItAssignsPersonInCharge(): void
    {
        $personInCharge = NaturalPersonProvider::getNaturalPersonG();
        $this->burialPlace->assignPersonInCharge($personInCharge);
        $this->assertTrue($this->burialPlace->personInChargeId()->isEqual($personInCharge->id()));
    }

    public function testItReplacesPersonInCharge(): void
    {
        // Prepare entity for testing
        $personInCharge = NaturalPersonProvider::getNaturalPersonG();
        $this->burialPlace->assignPersonInCharge($personInCharge);

        // Testing itself
        $newPersonInCharge = NaturalPersonProvider::getNaturalPersonH();
        $this->burialPlace->assignPersonInCharge($newPersonInCharge);
        $this->assertTrue($this->burialPlace->personInChargeId()->isEqual($newPersonInCharge->id()));
    }

    public function testItFailsToAssignDeadPersonInCharge(): void
    {
        // Prepare entity for testing
        $personInCharge = NaturalPersonProvider::getNaturalPersonG();
        $this->burialPlace->assignPersonInCharge($personInCharge);

        // Testing itself
        $newPersonInCharge = NaturalPersonProvider::getNaturalPersonA();
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage(\sprintf(
            'Невозможно назначить умершего с ID "%s" в качестве ответственного для места захоронения с ID "%s" и типом "%s".',
            $newPersonInCharge->id()->value(),
            $this->burialPlace->id()->value(),
            BurialPlace::CLASS_LABEL,
        ));
        $this->burialPlace->assignPersonInCharge($newPersonInCharge);
    }

    public function testItDiscardsPersonInCharge(): void
    {
        // Prepare entity for testing
        $personInCharge = NaturalPersonProvider::getNaturalPersonG();
        $this->burialPlace->assignPersonInCharge($personInCharge);

        // Testing itself
        $this->burialPlace->discardPersonInCharge();
        $this->assertNull($this->burialPlace->personInChargeId());
    }

    public function testItFailsToDiscardPersonInChargeWhenNobodyAssigned(): void
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage(\sprintf(
            'Невозможно удалить ответственного для места захоронения с ID "%s" и типом "%s", т.к. никто не назначен ответственным.',
            $this->burialPlace->id()->value(),
            BurialPlace::CLASS_LABEL,
        ));
        $this->burialPlace->discardPersonInCharge();
    }
}
