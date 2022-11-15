<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Model\FuneralCompany;

use Cemetery\Registrar\Domain\Model\FuneralCompany\FuneralCompany;
use Cemetery\Registrar\Domain\Model\FuneralCompany\FuneralCompanyId;
use Cemetery\Registrar\Domain\Model\FuneralCompany\FuneralCompanyName;
use Cemetery\Registrar\Domain\Model\FuneralCompany\FuneralCompanyNote;
use Cemetery\Tests\Registrar\Domain\Model\AbstractAggregateRootTest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class FuneralCompanyTest extends AbstractAggregateRootTest
{
    private FuneralCompanyId   $funeralCompanyIdA;
    private FuneralCompanyName $funeralCompanyName;
    private FuneralCompany     $funeralCompanyA;
    
    public function setUp(): void
    {
        $this->funeralCompanyIdA  = new FuneralCompanyId('FC001');
        $this->funeralCompanyName = new FuneralCompanyName('Апостол');
        $this->funeralCompanyA    = new FuneralCompany($this->funeralCompanyIdA, $this->funeralCompanyName);
        $this->entity             = $this->funeralCompanyA;
    }

    public function testItSuccessfullyCreated(): void
    {
        $this->assertInstanceOf(FuneralCompanyId::class, $this->funeralCompanyA->id());
        $this->assertTrue($this->funeralCompanyA->id()->isEqual($this->funeralCompanyIdA));
        $this->assertInstanceOf(FuneralCompanyName::class, $this->funeralCompanyA->name());
        $this->assertTrue($this->funeralCompanyA->name()->isEqual($this->funeralCompanyName));
        $this->assertNull($this->funeralCompanyA->note());
    }

    public function testItSetsName(): void
    {
        $name = new FuneralCompanyName('Мемориал');
        $this->funeralCompanyA->setName($name);
        $this->assertInstanceOf(FuneralCompanyName::class, $this->funeralCompanyA->name());
        $this->assertTrue($this->funeralCompanyA->name()->isEqual($name));
    }

    public function testItSetsNote(): void
    {
        $note = new FuneralCompanyNote('Примечание 1');
        $this->funeralCompanyA->setNote($note);
        $this->assertInstanceOf(FuneralCompanyNote::class, $this->funeralCompanyA->note());
        $this->assertTrue($this->funeralCompanyA->note()->isEqual($note));

        $this->funeralCompanyA->setNote(null);
        $this->assertNull($this->funeralCompanyA->note());
    }
}
