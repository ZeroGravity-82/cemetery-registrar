<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Burial;

use Cemetery\Registrar\Domain\Burial\Burial;
use Cemetery\Registrar\Domain\Burial\BurialBuilder;
use Cemetery\Registrar\Domain\Burial\BurialCode;
use Cemetery\Registrar\Domain\Burial\BurialCodeGeneratorInterface;
use Cemetery\Registrar\Domain\Burial\BurialContainerId;
use Cemetery\Registrar\Domain\Burial\BurialContainerType;
use Cemetery\Registrar\Domain\Burial\BurialId;
use Cemetery\Registrar\Domain\Burial\BurialPlaceId;
use Cemetery\Registrar\Domain\Burial\BurialPlaceType;
use Cemetery\Registrar\Domain\Burial\CustomerId;
use Cemetery\Registrar\Domain\Burial\CustomerType;
use Cemetery\Registrar\Domain\Burial\FuneralCompanyId;
use Cemetery\Registrar\Domain\Burial\FuneralCompanyType;
use Cemetery\Registrar\Domain\Deceased\DeceasedId;
use Cemetery\Registrar\Domain\IdentityGeneratorInterface;
use Cemetery\Registrar\Domain\NaturalPerson\NaturalPersonId;
use PHPUnit\Framework\TestCase;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class BurialBuilderTest extends TestCase
{
    private IdentityGeneratorInterface   $mockIdentityGenerator;
    private BurialCodeGeneratorInterface $mockBurialCodeGenerator;
    private BurialBuilder                $burialBuilder;

    public function setUp(): void
    {
        $this->mockIdentityGenerator   = $this->createMock(IdentityGeneratorInterface::class);
        $this->mockBurialCodeGenerator = $this->createMock(BurialCodeGeneratorInterface::class);
        $this->mockIdentityGenerator->method('getNextIdentity')->willReturn('555');
        $this->mockBurialCodeGenerator->method('getNextCode')->willReturn('001');

        $this->burialBuilder = new BurialBuilder($this->mockIdentityGenerator, $this->mockBurialCodeGenerator);
        $this->burialBuilder->initializeBurial(new DeceasedId('777'));
    }

    public function testItInitiatesABurialWithADeceasedId(): void
    {
        $burial = $this->burialBuilder->getBurial();

        $this->assertInstanceOf(BurialId::class, $burial->getId());
        $this->assertSame('555', (string) $burial->getId());
        $this->assertInstanceOf(BurialCode::class, $burial->getCode());
        $this->assertSame('001', (string) $burial->getCode());
        $this->assertInstanceOf(DeceasedId::class, $burial->getDeceasedId());
        $this->assertSame('777', (string) $burial->getDeceasedId());
        $this->assertNull($burial->getCustomerId());
        $this->assertNull($burial->getBurialPlaceId());
        $this->assertNull($burial->getBurialPlaceOwnerId());
        $this->assertNull($burial->getFuneralCompanyId());
        $this->assertNull($burial->getBurialContainerId());
        $this->assertNull($burial->getBuriedAt());
    }

    public function testItFailsToBuildABurialWithoutDeceasedId(): void
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('The burial is not initialized.');

        $burialBuilder = new BurialBuilder($this->mockIdentityGenerator, $this->mockBurialCodeGenerator);
        $burialBuilder->getBurial();
    }

    public function testItAddsACustomerId(): void
    {
        $customerId = new CustomerId('777', CustomerType::soleProprietor());
        $burial     = $this->burialBuilder->addCustomerId($customerId)->getBurial();
        $this->assertInstanceOf(CustomerId::class, $burial->getCustomerId());
        $this->assertSame('777', $burial->getCustomerId()->getValue());
        $this->assertSame(CustomerType::SOLE_PROPRIETOR, (string) $burial->getCustomerId()->getType());
    }

    public function testItAddsABurialPlaceId(): void
    {
        $burialPlaceId = new BurialPlaceId('888', BurialPlaceType::graveSite());
        $burial        = $this->burialBuilder->addBurialPlaceId($burialPlaceId)->getBurial();
        $this->assertInstanceOf(BurialPlaceId::class, $burial->getBurialPlaceId());
        $this->assertSame('888', $burial->getBurialPlaceId()->getValue());
        $this->assertSame(BurialPlaceType::GRAVE_SITE, (string) $burial->getBurialPlaceId()->getType());
    }

    public function testItAddsABurialPlaceOwnerId(): void
    {
        $burialPlaceOwnerId = new NaturalPersonId('999');
        $burial             = $this->burialBuilder->addBurialPlaceOwnerId($burialPlaceOwnerId)->getBurial();
        $this->assertInstanceOf(NaturalPersonId::class, $burial->getBurialPlaceOwnerId());
        $this->assertSame('999', (string) $burial->getBurialPlaceOwnerId());
    }

    public function testItAddsAFuneralCompanyId(): void
    {
        $funeralCompanyId = new FuneralCompanyId('333', FuneralCompanyType::juristicPerson());
        $burial           = $this->burialBuilder->addFuneralCompanyId($funeralCompanyId)->getBurial();
        $this->assertInstanceOf(FuneralCompanyId::class, $burial->getFuneralCompanyId());
        $this->assertSame('333', $burial->getFuneralCompanyId()->getValue());
        $this->assertSame(FuneralCompanyType::JURISTIC_PERSON, (string) $burial->getFuneralCompanyId()->getType());
    }

    public function testItAddsABurialContainerId(): void
    {
        $burialContainerId = new BurialContainerId('444', BurialContainerType::coffin());
        $burial            = $this->burialBuilder->addBurialContainerId($burialContainerId)->getBurial();
        $this->assertInstanceOf(BurialContainerId::class, $burial->getBurialContainerId());
        $this->assertSame('444', $burial->getBurialContainerId()->getValue());
        $this->assertSame(BurialContainerType::COFFIN, (string) $burial->getBurialContainerId()->getType());
    }

    public function testItAddsABuriedAt(): void
    {
        $buriedAt = new \DateTimeImmutable('2022-02-10 10:33:12');
        $burial   = $this->burialBuilder->setBuriedAt($buriedAt)->getBurial();
        $this->assertInstanceOf(\DateTimeImmutable::class, $burial->getBuriedAt());
        $this->assertSame('2022-02-10 10:33:12', $burial->getBuriedAt()->format('Y-m-d H:i:s'));
    }
}
