<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Organization\SoleProprietor;

use Cemetery\Registrar\Domain\Organization\Name;
use Cemetery\Registrar\Domain\Organization\SoleProprietor\SoleProprietor;
use Cemetery\Registrar\Domain\Organization\SoleProprietor\SoleProprietorId;
use PHPUnit\Framework\TestCase;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class SoleProprietorTest extends TestCase
{
    private SoleProprietor $soleProprietor;

    public function setUp(): void
    {
        $soleProprietorId       = new SoleProprietorId('777');
        $soleProprietorFullName = new Name('ИП Иванов Иван Иванович');
        $this->soleProprietor   = new SoleProprietor($soleProprietorId, $soleProprietorFullName);
    }

    public function testItSuccessfullyCreated(): void
    {
        $this->assertInstanceOf(SoleProprietorId::class, $this->soleProprietor->getId());
        $this->assertSame('777', (string) $this->soleProprietor->getId());
        $this->assertInstanceOf(Name::class, $this->soleProprietor->getFullName());
        $this->assertSame('ИП Иванов Иван Иванович', (string) $this->soleProprietor->getName());
        $this->assertNull($this->soleProprietor->getInn());
        $this->assertNull($this->soleProprietor->getOgrnip());
        $this->assertNull($this->soleProprietor->getRegistrationAddress());
        $this->assertNull($this->soleProprietor->getActualLocationAddress());
        $this->assertNull($this->soleProprietor->getBankDetails());
        $this->assertNull($this->soleProprietor->getPhone());
        $this->assertNull($this->soleProprietor->getPhoneAdditional());
        $this->assertNull($this->soleProprietor->getFax());
        $this->assertNull($this->soleProprietor->getEmail());
        $this->assertNull($this->soleProprietor->getWebsite());
        $this->assertInstanceOf(\DateTimeImmutable::class, $this->soleProprietor->getCreatedAt());
        $this->assertLessThan(new \DateTimeImmutable(), $this->soleProprietor->getCreatedAt());
        $this->assertInstanceOf(\DateTimeImmutable::class, $this->soleProprietor->getUpdatedAt());
        $this->assertLessThan(new \DateTimeImmutable(), $this->soleProprietor->getUpdatedAt());
    }

    public function testItSetsInn(): void
    {

    }

    public function testItSetsOgrnip(): void
    {

    }

    public function testItSetsRegistrationAddress(): void
    {

    }

    public function testItSetsActualLocationAddress(): void
    {

    }

    public function testItSetsBankDetails(): void
    {

    }

    public function testItSetsPhone(): void
    {

    }

    public function testItSetsPhoneAdditional(): void
    {

    }

    public function testItSetsFax(): void
    {

    }

    public function testItSetsEmail(): void
    {

    }

    public function testItSetsWebsite(): void
    {

    }
}
