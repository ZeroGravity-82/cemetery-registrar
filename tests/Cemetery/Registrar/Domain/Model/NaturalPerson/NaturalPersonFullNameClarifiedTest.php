<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Model\NaturalPerson;

use Cemetery\Registrar\Domain\Model\NaturalPerson\FullName;
use Cemetery\Registrar\Domain\Model\NaturalPerson\NaturalPersonFullNameClarified;
use Cemetery\Registrar\Domain\Model\NaturalPerson\NaturalPersonId;
use Cemetery\Tests\Registrar\Domain\Model\EventTest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class NaturalPersonFullNameClarifiedTest extends EventTest
{
    private NaturalPersonId $naturalPersonId;
    private FullName        $fullName;

    public function setUp(): void
    {
        $this->naturalPersonId = new NaturalPersonId('NP001');
        $this->fullName        = new FullName('Иванов Иван Петрович');
        $this->event           = new NaturalPersonFullNameClarified(
            $this->naturalPersonId,
            $this->fullName,
        );
    }

    public function testItSuccessfullyCreated(): void
    {
        $this->assertTrue($this->naturalPersonId->isEqual($this->event->naturalPersonId()));
        $this->assertTrue($this->fullName->isEqual($this->event->fullName()));
    }
}
