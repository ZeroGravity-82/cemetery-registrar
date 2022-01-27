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
use Cemetery\Registrar\Domain\Burial\FuneralCompanyId;
use Cemetery\Registrar\Domain\Burial\FuneralCompanyType;
use Cemetery\Registrar\Domain\NaturalPerson\NaturalPersonId;
use PHPUnit\Framework\TestCase;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class BurialTest extends TestCase
{
    public function testItSuccessfullyCreated(): void
    {
        $id                 = new BurialId('B001');
        $burialCode         = new BurialCode('BC001');
        $deceasedId         = new NaturalPersonId('NP001');
        $customerId         = new CustomerId('C001', CustomerType::naturalPerson());
        $burialPlaceId      = new BurialPlaceId('BP001', BurialPlaceType::graveSite());
        $burialPlaceOwnerId = new NaturalPersonId('NP002');
        $funeralCompanyId   = new FuneralCompanyId('FC001', FuneralCompanyType::juristicPerson());
        $burialContainerId  = new BurialContainerId('CT001', BurialContainerType::coffin());
        $buriedAt           = new \DateTimeImmutable('2022-01-01 01:01:01');
        $burial             = new Burial(
            $id,
            $burialCode,
            $deceasedId,
            $customerId,
            $burialPlaceId,
            $burialPlaceOwnerId,
            $funeralCompanyId,
            $burialContainerId,
            $buriedAt,
        );

        $this->assertInstanceOf(BurialId::class, $burial->getId());
        $this->assertSame('B001', (string) $burial->getId());
        $this->assertInstanceOf(BurialCode::class, $burial->getCode());
        $this->assertSame('BC001', (string) $burial->getCode());
        $this->assertInstanceOf(NaturalPersonId::class, $burial->getDeceasedId());
        $this->assertSame('NP001', (string) $burial->getDeceasedId());
        $this->assertInstanceOf(CustomerId::class, $burial->getCustomerId());
        $this->assertSame('C001', $burial->getCustomerId()->getValue());
        $this->assertSame(CustomerType::NATURAL_PERSON, (string) $burial->getCustomerId()->getType());
        $this->assertInstanceOf(BurialPlaceId::class, $burial->getBurialPlaceId());
        $this->assertSame('BP001', $burial->getBurialPlaceId()->getValue());
        $this->assertSame(BurialPlaceType::GRAVE_SITE, (string) $burial->getBurialPlaceId()->getType());
        $this->assertInstanceOf(NaturalPersonId::class, $burial->getBurialPlaceOwnerId());
        $this->assertSame('NP002', (string) $burial->getBurialPlaceOwnerId());
        $this->assertInstanceOf(FuneralCompanyId::class, $burial->getFuneralCompanyId());
        $this->assertSame('FC001', $burial->getFuneralCompanyId()->getValue());
        $this->assertInstanceOf(BurialContainerId::class, $burial->getBurialContainerId());
        $this->assertSame('CT001', $burial->getBurialContainerId()->getValue());
        $this->assertSame('2022-01-01 01:01:01', $burial->getBuriedAt()->format('Y-m-d H:i:s'));
        $this->assertInstanceOf(\DateTimeImmutable::class, $burial->getCreatedAt());
        $this->assertLessThan(new \DateTimeImmutable(), $burial->getCreatedAt());
        $this->assertInstanceOf(\DateTimeImmutable::class, $burial->getUpdatedAt());
        $this->assertLessThan(new \DateTimeImmutable(), $burial->getUpdatedAt());
    }

    public function testItSuccessfullyCreatedWithoutOptionalFields(): void
    {
        $id         = new BurialId('B001');
        $burialCode = new BurialCode('BC001');
        $deceasedId = new NaturalPersonId('NP001');
        $burial     = new Burial($id, $burialCode, $deceasedId, null, null, null, null, null, null);

        $this->assertInstanceOf(BurialId::class, $burial->getId());
        $this->assertSame('B001', (string) $burial->getId());
        $this->assertInstanceOf(BurialCode::class, $burial->getCode());
        $this->assertSame('BC001', (string) $burial->getCode());
        $this->assertInstanceOf(NaturalPersonId::class, $burial->getDeceasedId());
        $this->assertSame('NP001', (string) $burial->getDeceasedId());
        $this->assertNull($burial->getCustomerId());
        $this->assertNull($burial->getBurialPlaceId());
        $this->assertNull($burial->getBurialPlaceOwnerId());
        $this->assertNull($burial->getFuneralCompanyId());
        $this->assertNull($burial->getBurialContainerId());
        $this->assertNull($burial->getBuriedAt());
        $this->assertInstanceOf(\DateTimeImmutable::class, $burial->getCreatedAt());
        $this->assertLessThan(new \DateTimeImmutable(), $burial->getCreatedAt());
        $this->assertInstanceOf(\DateTimeImmutable::class, $burial->getUpdatedAt());
        $this->assertLessThan(new \DateTimeImmutable(), $burial->getUpdatedAt());
    }
}
