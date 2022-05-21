<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Burial;

use Cemetery\Registrar\Domain\Burial\Burial;
use Cemetery\Registrar\Domain\Burial\BurialChainId;
use Cemetery\Registrar\Domain\Burial\BurialCode;
use Cemetery\Registrar\Domain\Burial\BurialId;
use Cemetery\Registrar\Domain\Burial\BurialPlaceId;
use Cemetery\Registrar\Domain\Burial\BurialType;
use Cemetery\Registrar\Domain\Burial\CustomerId;
use Cemetery\Registrar\Domain\Burial\FuneralCompanyId;
use Cemetery\Registrar\Domain\BurialContainer\BurialContainer;
use Cemetery\Registrar\Domain\BurialContainer\Coffin;
use Cemetery\Registrar\Domain\BurialContainer\CoffinShape;
use Cemetery\Registrar\Domain\BurialContainer\CoffinSize;
use Cemetery\Registrar\Domain\BurialContainer\Urn;
use Cemetery\Registrar\Domain\BurialPlace\ColumbariumNiche\ColumbariumNicheId;
use Cemetery\Registrar\Domain\BurialPlace\GraveSite\GraveSiteId;
use Cemetery\Registrar\Domain\BurialPlace\MemorialTree\MemorialTreeId;
use Cemetery\Registrar\Domain\Deceased\DeceasedId;
use Cemetery\Registrar\Domain\NaturalPerson\NaturalPersonId;
use Cemetery\Registrar\Domain\Organization\JuristicPerson\JuristicPersonId;
use Cemetery\Registrar\Domain\Organization\SoleProprietor\SoleProprietorId;
use Cemetery\Tests\Registrar\Domain\AggregateRootTest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class BurialTest extends AggregateRootTest
{
    private const BURIAL_TYPE_COFFIN_IN_GRAVE_SITE_LABEL      = 'гробом в могилу';
    private const BURIAL_TYPE_URN_IN_GRAVE_SITE_LABEL         = 'урной в могилу';
    private const BURIAL_TYPE_URN_IN_COLUMBARIUM_NICHE_LABEL  = 'урной в колумбарную нишу';
    private const BURIAL_TYPE_ASHES_UNDER_MEMORIAL_TREE_LABEL = 'прахом под деревом';
    private const BURIAL_PLACE_GRAVE_SITE_LABEL               = 'могила';
    private const BURIAL_PLACE_COLUMBARIUM_NICHE_LABEL        = 'колумбарная ниша';
    private const BURIAL_PLACE_MEMORIAL_TREE_LABEL            = 'памятное дерево';

    private Burial $burial;

    public function setUp(): void
    {
        $id           = new BurialId('B001');
        $code         = new BurialCode('BC001');
        $type         = BurialType::coffinInGraveSite();
        $deceasedId   = new DeceasedId('D001');
        $this->burial = new Burial($id, $code, $type, $deceasedId);
        $this->entity = $this->burial;
    }

    public function testItSuccessfullyCreated(): void
    {
        $this->assertInstanceOf(BurialId::class, $this->burial->id());
        $this->assertSame('B001', (string) $this->burial->id());
        $this->assertInstanceOf(BurialCode::class, $this->burial->code());
        $this->assertSame('BC001', (string) $this->burial->code());
        $this->assertInstanceOf(BurialType::class, $this->burial->type());
        $this->assertInstanceOf(DeceasedId::class, $this->burial->deceasedId());
        $this->assertSame('D001', (string) $this->burial->deceasedId());
        $this->assertTrue($this->burial->type()->isCoffinInGraveSite());
        $this->assertNull($this->burial->customerId());
        $this->assertNull($this->burial->burialPlaceId());
        $this->assertNull($this->burial->burialPlaceOwnerId());
        $this->assertNull($this->burial->funeralCompanyId());
        $this->assertNull($this->burial->burialContainer());
        $this->assertNull($this->burial->buriedAt());
        $this->assertNull($this->burial->burialChainId());
    }

    public function testItSetsBurialType(): void
    {
        $burialType = BurialType::urnInColumbariumNiche();
        $this->burial->setType($burialType);
        $this->assertInstanceOf(BurialType::class, $this->burial->type());
        $this->assertTrue($this->burial->type()->isEqual($burialType));
    }

    public function testItSetsDeceasedId(): void
    {
        $deceasedId = new DeceasedId('D001');
        $this->burial->setDeceasedId($deceasedId);
        $this->assertInstanceOf(DeceasedId::class, $this->burial->deceasedId());
        $this->assertTrue($this->burial->deceasedId()->isEqual($deceasedId));
    }

    public function testItSetsCustomerId(): void
    {
        $naturalPersonId = new NaturalPersonId('NP001');
        $customerId      = new CustomerId($naturalPersonId);
        $this->burial->setCustomerId($customerId);
        $this->assertInstanceOf(CustomerId::class, $this->burial->customerId());
        $this->assertTrue($this->burial->customerId()->isEqual($customerId));

        $juristicPersonId = new JuristicPersonId('JP001');
        $customerId       = new CustomerId($juristicPersonId);
        $this->burial->setCustomerId($customerId);
        $this->assertInstanceOf(CustomerId::class, $this->burial->customerId());
        $this->assertTrue($this->burial->customerId()->isEqual($customerId));

        $soleProprietorId = new SoleProprietorId('SP001');
        $customerId       = new CustomerId($soleProprietorId);
        $this->burial->setCustomerId($customerId);
        $this->assertInstanceOf(CustomerId::class, $this->burial->customerId());
        $this->assertTrue($this->burial->customerId()->isEqual($customerId));

        $this->burial->setCustomerId(null);
        $this->assertNull($this->burial->customerId());
    }

    public function testItSetsBurialPlaceId(): void
    {
        $this->burial->setType(BurialType::coffinInGraveSite());
        $graveSiteId   = new GraveSiteId('GS001');
        $burialPlaceId = new BurialPlaceId($graveSiteId);
        $this->burial->setBurialPlaceId($burialPlaceId);
        $this->assertInstanceOf(BurialPlaceId::class, $this->burial->burialPlaceId());
        $this->assertTrue($this->burial->burialPlaceId()->isEqual($burialPlaceId));

        $this->burial->setType(BurialType::urnInColumbariumNiche());
        $columbariumNicheId = new ColumbariumNicheId('CN001');
        $burialPlaceId      = new BurialPlaceId($columbariumNicheId);
        $this->burial->setBurialPlaceId($burialPlaceId);
        $this->assertInstanceOf(BurialPlaceId::class, $this->burial->burialPlaceId());
        $this->assertTrue($this->burial->burialPlaceId()->isEqual($burialPlaceId));

        $this->burial->setType(BurialType::ashesUnderMemorialTree());
        $memorialTreeId = new MemorialTreeId('MT001');
        $burialPlaceId  = new BurialPlaceId($memorialTreeId);
        $this->burial->setBurialPlaceId($burialPlaceId);
        $this->assertInstanceOf(BurialPlaceId::class, $this->burial->burialPlaceId());
        $this->assertTrue($this->burial->burialPlaceId()->isEqual($burialPlaceId));

        $this->burial->setBurialPlaceId(null);
        $this->assertNull($this->burial->burialPlaceId());
    }

    public function testItSetsBurialPlaceOwnerId(): void
    {
        $burialPlaceOwnerId = new NaturalPersonId('NP002');
        $this->burial->setBurialPlaceOwnerId($burialPlaceOwnerId);
        $this->assertInstanceOf(NaturalPersonId::class, $this->burial->burialPlaceOwnerId());
        $this->assertTrue($this->burial->burialPlaceOwnerId()->isEqual($burialPlaceOwnerId));

        $this->burial->setBurialPlaceOwnerId(null);
        $this->assertNull($this->burial->burialPlaceOwnerId());
    }
    
    public function testItSetsFuneralCompanyId(): void
    {
        $juristicPersonId = new JuristicPersonId('JP001');
        $funeralCompanyId = new FuneralCompanyId($juristicPersonId);
        $this->burial->setFuneralCompanyId($funeralCompanyId);
        $this->assertInstanceOf(FuneralCompanyId::class, $this->burial->funeralCompanyId());
        $this->assertTrue($this->burial->funeralCompanyId()->isEqual($funeralCompanyId));

        $soleProprietorId = new SoleProprietorId('SP001');
        $funeralCompanyId = new FuneralCompanyId($soleProprietorId);
        $this->burial->setFuneralCompanyId($funeralCompanyId);
        $this->assertInstanceOf(FuneralCompanyId::class, $this->burial->funeralCompanyId());
        $this->assertTrue($this->burial->funeralCompanyId()->isEqual($funeralCompanyId));

        $this->burial->setFuneralCompanyId(null);
        $this->assertNull($this->burial->funeralCompanyId());
    }

    public function testItSetsBurialContainer(): void
    {
        $burialContainer = new BurialContainer(new Coffin(new CoffinSize(180), CoffinShape::american(), false));
        $this->burial->setBurialContainer($burialContainer);
        $this->assertInstanceOf(BurialContainer::class, $this->burial->burialContainer());
        $this->assertTrue($this->burial->burialContainer()->isEqual($burialContainer));

        $this->burial->setBurialContainer(null);
        $this->assertNull($this->burial->burialContainer());
    }
    
    public function testItSetsBuriedAt(): void
    {
        $buriedAt = new \DateTimeImmutable('2022-01-01 01:01:01');
        $this->burial->setBuriedAt($buriedAt);
        $this->assertSame('2022-01-01 01:01:01', $this->burial->buriedAt()->format('Y-m-d H:i:s'));

        $this->burial->setBuriedAt(null);
        $this->assertNull($this->burial->buriedAt());
    }

    public function testItSetsBurialChainId(): void
    {
        $burialChainId = new BurialChainId('BCH001');
        $this->burial->setBurialChainId($burialChainId);
        $this->assertInstanceOf(BurialChainId::class, $this->burial->burialChainId());
        $this->assertTrue($this->burial->burialChainId()->isEqual($burialChainId));

        $this->burial->setBurialChainId(null);
        $this->assertNull($this->burial->burialChainId());
    }

    // ------------------------------ "BurialType <-> BurialPlace" invariant testing ------------------------------

    public function testItFailsWhenSettingColumbariumNicheForCoffinInGraveSiteBurialType(): void
    {
        $this->burial->setType(BurialType::coffinInGraveSite());

        $this->expectExceptionForBurialPlaceNotMatchingTheBurialType(
            self::BURIAL_PLACE_COLUMBARIUM_NICHE_LABEL,
            self::BURIAL_TYPE_COFFIN_IN_GRAVE_SITE_LABEL,
        );
        $columbariumNicheId = new ColumbariumNicheId('CN001');
        $burialPlaceId      = new BurialPlaceId($columbariumNicheId);
        $this->burial->setBurialPlaceId($burialPlaceId);
    }

    public function testItFailsWhenSettingMemorialTreeForCoffinInGraveSiteBurialType(): void
    {
        $this->burial->setType(BurialType::coffinInGraveSite());

        $this->expectExceptionForBurialPlaceNotMatchingTheBurialType(
            self::BURIAL_PLACE_MEMORIAL_TREE_LABEL,
            self::BURIAL_TYPE_COFFIN_IN_GRAVE_SITE_LABEL,
        );
        $memorialTreeId = new MemorialTreeId('MT001');
        $burialPlaceId  = new BurialPlaceId($memorialTreeId);
        $this->burial->setBurialPlaceId($burialPlaceId);
    }

    public function testItFailsWhenSettingColumbariumNicheForUrnInGraveSiteBurialType(): void
    {
        $this->burial->setType(BurialType::urnInGraveSite());

        $this->expectExceptionForBurialPlaceNotMatchingTheBurialType(
            self::BURIAL_PLACE_COLUMBARIUM_NICHE_LABEL,
            self::BURIAL_TYPE_URN_IN_GRAVE_SITE_LABEL,
        );
        $columbariumNicheId = new ColumbariumNicheId('CN001');
        $burialPlaceId      = new BurialPlaceId($columbariumNicheId);
        $this->burial->setBurialPlaceId($burialPlaceId);
    }

    public function testItFailsWhenSettingMemorialTreeForUrnInGraveSiteBurialType(): void
    {
        $this->burial->setType(BurialType::urnInGraveSite());

        $this->expectExceptionForBurialPlaceNotMatchingTheBurialType(
            self::BURIAL_PLACE_MEMORIAL_TREE_LABEL,
            self::BURIAL_TYPE_URN_IN_GRAVE_SITE_LABEL,
        );
        $memorialTreeId = new MemorialTreeId('MT001');
        $burialPlaceId  = new BurialPlaceId($memorialTreeId);
        $this->burial->setBurialPlaceId($burialPlaceId);
    }

    public function testItFailsWhenSettingGraveSiteForUrnInColumbariumNicheBurialType(): void
    {
        $this->burial->setType(BurialType::urnInColumbariumNiche());

        $this->expectExceptionForBurialPlaceNotMatchingTheBurialType(
            self::BURIAL_PLACE_GRAVE_SITE_LABEL,
            self::BURIAL_TYPE_URN_IN_COLUMBARIUM_NICHE_LABEL,
        );
        $graveSiteId   = new GraveSiteId('GS001');
        $burialPlaceId = new BurialPlaceId($graveSiteId);
        $this->burial->setBurialPlaceId($burialPlaceId);
    }

    public function testItFailsWhenSettingMemorialTreeForUrnInColumbariumNicheBurialType(): void
    {
        $this->burial->setType(BurialType::urnInColumbariumNiche());

        $this->expectExceptionForBurialPlaceNotMatchingTheBurialType(
            self::BURIAL_PLACE_MEMORIAL_TREE_LABEL,
            self::BURIAL_TYPE_URN_IN_COLUMBARIUM_NICHE_LABEL,
        );
        $memorialTreeId = new MemorialTreeId('MT001');
        $burialPlaceId  = new BurialPlaceId($memorialTreeId);
        $this->burial->setBurialPlaceId($burialPlaceId);
    }

    public function testItFailsWhenSettingGraveSiteForAshesUnderMemorialTreeBurialType(): void
    {
        $this->burial->setType(BurialType::ashesUnderMemorialTree());

        $this->expectExceptionForBurialPlaceNotMatchingTheBurialType(
            self::BURIAL_PLACE_GRAVE_SITE_LABEL,
            self::BURIAL_TYPE_ASHES_UNDER_MEMORIAL_TREE_LABEL,
        );
        $graveSiteId   = new GraveSiteId('GS001');
        $burialPlaceId = new BurialPlaceId($graveSiteId);
        $this->burial->setBurialPlaceId($burialPlaceId);
    }

    public function testItFailsWhenSettingColumbariumNicheForAshesUnderMemorialTreeBurialType(): void
    {
        $this->burial->setType(BurialType::ashesUnderMemorialTree());

        $this->expectExceptionForBurialPlaceNotMatchingTheBurialType(
            self::BURIAL_PLACE_COLUMBARIUM_NICHE_LABEL,
            self::BURIAL_TYPE_ASHES_UNDER_MEMORIAL_TREE_LABEL,
        );
        $columbariumNicheId = new ColumbariumNicheId('CN001');
        $burialPlaceId      = new BurialPlaceId($columbariumNicheId);
        $this->burial->setBurialPlaceId($burialPlaceId);
    }

    // ------------------------------ "BurialType <-> BurialContainer" invariant testing ------------------------------

    public function testItFailsWhenSettingCoffinForUrnInGraveSiteBurialType(): void
    {
        $this->burial->setType(BurialType::urnInGraveSite());

        $coffin = new Coffin(new CoffinSize(180), CoffinShape::american(), false);
        $this->expectExceptionForBurialContainerNotMatchingTheBurialType(
            (string) $coffin,
            self::BURIAL_TYPE_URN_IN_GRAVE_SITE_LABEL,
        );
        $burialContainer = new BurialContainer($coffin);
        $this->burial->setBurialContainer($burialContainer);
    }

    public function testItFailsWhenSettingCoffinForUrnInColumbariumNicheBurialType(): void
    {
        $this->burial->setType(BurialType::urnInColumbariumNiche());

        $coffin = new Coffin(new CoffinSize(180), CoffinShape::american(), false);
        $this->expectExceptionForBurialContainerNotMatchingTheBurialType(
            (string) $coffin,
            self::BURIAL_TYPE_URN_IN_COLUMBARIUM_NICHE_LABEL,
        );
        $burialContainer = new BurialContainer($coffin);
        $this->burial->setBurialContainer($burialContainer);
    }

    public function testItFailsWhenSettingCoffinForAshesUnderMemorialTreeBurialType(): void
    {
        $this->burial->setType(BurialType::ashesUnderMemorialTree());

        $coffin = new Coffin(new CoffinSize(180), CoffinShape::american(), false);
        $this->expectExceptionForBurialContainerNotMatchingTheBurialType(
            (string) $coffin,
            self::BURIAL_TYPE_ASHES_UNDER_MEMORIAL_TREE_LABEL,
        );
        $burialContainer = new BurialContainer($coffin);
        $this->burial->setBurialContainer($burialContainer);
    }

    public function testItFailsWhenSettingUrnForCoffinInGraveSiteBurialType(): void
    {
        $this->burial->setType(BurialType::coffinInGraveSite());

        $urn = new Urn();
        $this->expectExceptionForBurialContainerNotMatchingTheBurialType(
            (string) $urn,
            self::BURIAL_TYPE_COFFIN_IN_GRAVE_SITE_LABEL,
        );
        $burialContainer = new BurialContainer($urn);
        $this->burial->setBurialContainer($burialContainer);
    }

    public function testItFailsWhenSettingUrnForAshesUnderMemorialTreeBurialType(): void
    {
        $this->burial->setType(BurialType::ashesUnderMemorialTree());

        $urn = new Urn();
        $this->expectExceptionForBurialContainerNotMatchingTheBurialType(
            (string) $urn,
            self::BURIAL_TYPE_ASHES_UNDER_MEMORIAL_TREE_LABEL,
        );
        $burialContainer = new BurialContainer($urn);
        $this->burial->setBurialContainer($burialContainer);
    }

    private function expectExceptionForBurialPlaceNotMatchingTheBurialType(
        string $burialPlace,
        string $burialType,
    ): void {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(\sprintf(
            'Место захоронения "%s" не соответствует типу захороненния "%s".',
            $burialPlace,
            $burialType,
        ));
    }

    private function expectExceptionForBurialContainerNotMatchingTheBurialType(
        string $burialContainer,
        string $burialType,
    ): void {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(\sprintf(
            'Контейнер захоронения "%s" не соответствует типу захороненния "%s".',
            $burialContainer,
            $burialType,
        ));
    }
}
