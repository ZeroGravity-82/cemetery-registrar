<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Model\Organization\SoleProprietor;

use Cemetery\Registrar\Domain\Model\Organization\Name;
use Cemetery\Registrar\Domain\Model\Organization\SoleProprietor\Inn;
use Cemetery\Registrar\Domain\Model\Organization\SoleProprietor\SoleProprietorCreated;
use Cemetery\Registrar\Domain\Model\Organization\SoleProprietor\SoleProprietorId;
use Cemetery\Tests\Registrar\Domain\Model\EventTest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class SoleProprietorCreatedTest extends EventTest
{
    private SoleProprietorId $soleProprietorId;
    private Name             $soleProprietorName;
    private Inn              $soleProprietorInn;

    public function setUp(): void
    {
        $this->soleProprietorId   = new SoleProprietorId('888');
        $this->soleProprietorName = new Name('ИП Иванов Иван Иванович');
        $this->soleProprietorInn  = new Inn('772208786091');
        $this->event              = new SoleProprietorCreated(
            $this->soleProprietorId,
            $this->soleProprietorName,
            $this->soleProprietorInn,
        );
    }

    public function testItSuccessfullyCreated(): void
    {
        $this->assertSame($this->soleProprietorId, $this->event->soleProprietorId());
        $this->assertSame($this->soleProprietorName, $this->event->soleProprietorName());
        $this->assertSame($this->soleProprietorInn, $this->event->soleProprietorInn());
    }
}
