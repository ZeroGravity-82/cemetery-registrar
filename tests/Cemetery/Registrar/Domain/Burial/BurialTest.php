<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Burial;

use Cemetery\Registrar\Domain\Burial\Burial;
use Cemetery\Registrar\Domain\Burial\BurialCode;
use Cemetery\Registrar\Domain\Burial\BurialContainerId;
use Cemetery\Registrar\Domain\Burial\BurialContainerType;
use Cemetery\Registrar\Domain\Burial\BurialId;
use Cemetery\Registrar\Domain\Burial\BurialPlaceId;
use Cemetery\Registrar\Domain\Burial\BurialPlaceType;
use Cemetery\Registrar\Domain\Burial\CustomerId;
use Cemetery\Registrar\Domain\Burial\CustomerType;
use Cemetery\Registrar\Domain\Deceased\DeceasedId;
use Cemetery\Registrar\Domain\FuneralCompany\FuneralCompanyId;
use Cemetery\Registrar\Domain\FuneralCompany\FuneralCompanyType;
use Cemetery\Registrar\Domain\NaturalPerson\NaturalPersonId;
use PHPUnit\Framework\TestCase;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class BurialTest extends TestCase
{
    private Burial $burial;

    public function setUp(): void
    {
        $id           = new BurialId('B001');
        $burialCode   = new BurialCode('BC001');
        $deceasedId   = new DeceasedId('D001');
        $this->burial = new Burial($id, $burialCode, $deceasedId);
    }

    public function testItSuccessfullyCreated(): void
    {
        $this->assertInstanceOf(BurialId::class, $this->burial->getId());
        $this->assertSame('B001', (string) $this->burial->getId());
        $this->assertInstanceOf(BurialCode::class, $this->burial->getCode());
        $this->assertSame('BC001', (string) $this->burial->getCode());
        $this->assertInstanceOf(DeceasedId::class, $this->burial->getDeceasedId());
        $this->assertSame('D001', (string) $this->burial->getDeceasedId());
        $this->assertNull($this->burial->getCustomerId());
        $this->assertNull($this->burial->getBurialPlaceId());
        $this->assertNull($this->burial->getBurialPlaceOwnerId());
        $this->assertNull($this->burial->getFuneralCompanyId());
        $this->assertNull($this->burial->getBurialContainerId());
        $this->assertNull($this->burial->getBuriedAt());
        $this->assertInstanceOf(\DateTimeImmutable::class, $this->burial->getCreatedAt());
        $this->assertLessThan(new \DateTimeImmutable(), $this->burial->getCreatedAt());
        $this->assertInstanceOf(\DateTimeImmutable::class, $this->burial->getUpdatedAt());
        $this->assertLessThan(new \DateTimeImmutable(), $this->burial->getUpdatedAt());
    }

    public function testItSetsCustomerId(): void
    {
        $customerId = new CustomerId('C001', CustomerType::naturalPerson());
        $this->burial->setCustomerId($customerId);
        $this->assertInstanceOf(CustomerId::class, $this->burial->getCustomerId());
        $this->assertSame('C001', $this->burial->getCustomerId()->getValue());
        $this->assertSame(CustomerType::NATURAL_PERSON, (string) $this->burial->getCustomerId()->getType());
    }

    public function testItSetsBurialPlaceId(): void
    {
        $burialPlaceId = new BurialPlaceId('BP001', BurialPlaceType::graveSite());
        $this->burial->setBurialPlaceId($burialPlaceId);
        $this->assertInstanceOf(BurialPlaceId::class, $this->burial->getBurialPlaceId());
        $this->assertSame('BP001', $this->burial->getBurialPlaceId()->getValue());
        $this->assertSame(BurialPlaceType::GRAVE_SITE, (string) $this->burial->getBurialPlaceId()->getType());
    }

    public function testItSetsBurialPlaceOwnerId(): void
    {
        $burialPlaceOwnerId = new NaturalPersonId('NP002');
        $this->burial->setBurialPlaceOwnerId($burialPlaceOwnerId);
        $this->assertInstanceOf(NaturalPersonId::class, $this->burial->getBurialPlaceOwnerId());
        $this->assertSame('NP002', (string) $this->burial->getBurialPlaceOwnerId());
    }
    
    public function testItSetsFuneralCompanyId(): void
    {
        $funeralCompanyId = new FuneralCompanyId('FC001', FuneralCompanyType::juristicPerson());
        $this->burial->setFuneralCompanyId($funeralCompanyId);
        $this->assertInstanceOf(FuneralCompanyId::class, $this->burial->getFuneralCompanyId());
        $this->assertSame('FC001', $this->burial->getFuneralCompanyId()->getValue());
    }

    public function testItSetsBurialContainerId(): void
    {
        $burialContainerId = new BurialContainerId('CT001', BurialContainerType::coffin());
        $this->burial->setBurialContainerId($burialContainerId);
        $this->assertInstanceOf(BurialContainerId::class, $this->burial->getBurialContainerId());
        $this->assertSame('CT001', $this->burial->getBurialContainerId()->getValue());
    }
    
    public function testItSetsBuriedAt(): void
    {
        $buriedAt = new \DateTimeImmutable('2022-01-01 01:01:01');
        $this->burial->setBuriedAt($buriedAt);
        $this->assertSame('2022-01-01 01:01:01', $this->burial->getBuriedAt()->format('Y-m-d H:i:s'));
    }
}
