<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Model\Burial;

use Cemetery\Registrar\Domain\Model\Burial\Burial;
use Cemetery\Registrar\Domain\Model\Burial\BurialChainId;
use Cemetery\Registrar\Domain\Model\Burial\BurialCode;
use Cemetery\Registrar\Domain\Model\Burial\BurialContainer\BurialContainer;
use Cemetery\Registrar\Domain\Model\Burial\BurialContainer\Coffin;
use Cemetery\Registrar\Domain\Model\Burial\BurialContainer\CoffinShape;
use Cemetery\Registrar\Domain\Model\Burial\BurialContainer\CoffinSize;
use Cemetery\Registrar\Domain\Model\Burial\BurialContainer\Urn;
use Cemetery\Registrar\Domain\Model\Burial\BurialId;
use Cemetery\Registrar\Domain\Model\Burial\BurialPlaceId;
use Cemetery\Registrar\Domain\Model\Burial\BurialType;
use Cemetery\Registrar\Domain\Model\Burial\CustomerId;
use Cemetery\Registrar\Domain\Model\BurialPlace\ColumbariumNiche\ColumbariumNicheId;
use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\GraveSiteId;
use Cemetery\Registrar\Domain\Model\BurialPlace\MemorialTree\MemorialTreeId;
use Cemetery\Registrar\Domain\Model\Exception;
use Cemetery\Registrar\Domain\Model\FuneralCompany\FuneralCompanyId;
use Cemetery\Registrar\Domain\Model\NaturalPerson\NaturalPersonId;
use Cemetery\Registrar\Domain\Model\Organization\JuristicPerson\JuristicPersonId;
use Cemetery\Registrar\Domain\Model\Organization\SoleProprietor\SoleProprietorId;
use Cemetery\Tests\Registrar\Domain\Model\AggregateRootTest;

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
        $code         = new BurialCode('10001');
        $type         = BurialType::coffinInGraveSite();
        $deceasedId   = new NaturalPersonId('NP001');
        $this->burial = new Burial($id, $code, $type, $deceasedId);
        $this->entity = $this->burial;
    }

    public function testItSuccessfullyCreated(): void
    {
        $this->assertInstanceOf(BurialId::class, $this->burial->id());
        $this->assertSame('B001', $this->burial->id()->value());
        $this->assertInstanceOf(BurialCode::class, $this->burial->code());
        $this->assertSame('10001', $this->burial->code()->value());
        $this->assertInstanceOf(BurialType::class, $this->burial->type());
        $this->assertInstanceOf(NaturalPersonId::class, $this->burial->deceasedId());
        $this->assertSame('NP001', $this->burial->deceasedId()->value());
        $this->assertTrue($this->burial->type()->isCoffinInGraveSite());
        $this->assertNull($this->burial->customerId());
        $this->assertNull($this->burial->burialPlaceId());
        $this->assertNull($this->burial->personInChargeId());
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
        $deceasedId = new NaturalPersonId('NP002');
        $this->burial->setDeceasedId($deceasedId);
        $this->assertInstanceOf(NaturalPersonId::class, $this->burial->deceasedId());
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

    public function testItSetsPersonInChargeId(): void
    {
        $personInChargeId = new NaturalPersonId('NP002');
        $this->burial->setPersonInChargeId($personInChargeId);
        $this->assertInstanceOf(NaturalPersonId::class, $this->burial->personInChargeId());
        $this->assertTrue($this->burial->personInChargeId()->isEqual($personInChargeId));

        $this->burial->setPersonInChargeId(null);
        $this->assertNull($this->burial->personInChargeId());
    }
    
    public function testItSetsFuneralCompanyId(): void
    {
        $funeralCompanyId = new FuneralCompanyId('FC001');
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
        // Prepare entity for testing
        $this->burial->setType(BurialType::coffinInGraveSite());

        // Testing itself
        $this->expectExceptionForBurialPlaceNotMatchingBurialType(
            self::BURIAL_PLACE_COLUMBARIUM_NICHE_LABEL,
            self::BURIAL_TYPE_COFFIN_IN_GRAVE_SITE_LABEL,
        );
        $columbariumNicheId = new ColumbariumNicheId('CN001');
        $burialPlaceId      = new BurialPlaceId($columbariumNicheId);
        $this->burial->setBurialPlaceId($burialPlaceId);
    }

    public function testItFailsWhenSettingMemorialTreeForCoffinInGraveSiteBurialType(): void
    {
        // Prepare entity for testing
        $this->burial->setType(BurialType::coffinInGraveSite());

        // Testing itself
        $this->expectExceptionForBurialPlaceNotMatchingBurialType(
            self::BURIAL_PLACE_MEMORIAL_TREE_LABEL,
            self::BURIAL_TYPE_COFFIN_IN_GRAVE_SITE_LABEL,
        );
        $memorialTreeId = new MemorialTreeId('MT001');
        $burialPlaceId  = new BurialPlaceId($memorialTreeId);
        $this->burial->setBurialPlaceId($burialPlaceId);
    }

    public function testItFailsWhenSettingColumbariumNicheForUrnInGraveSiteBurialType(): void
    {
        // Prepare entity for testing
        $this->burial->setType(BurialType::urnInGraveSite());

        // Testing itself
        $this->expectExceptionForBurialPlaceNotMatchingBurialType(
            self::BURIAL_PLACE_COLUMBARIUM_NICHE_LABEL,
            self::BURIAL_TYPE_URN_IN_GRAVE_SITE_LABEL,
        );
        $columbariumNicheId = new ColumbariumNicheId('CN001');
        $burialPlaceId      = new BurialPlaceId($columbariumNicheId);
        $this->burial->setBurialPlaceId($burialPlaceId);
    }

    public function testItFailsWhenSettingMemorialTreeForUrnInGraveSiteBurialType(): void
    {
        // Prepare entity for testing
        $this->burial->setType(BurialType::urnInGraveSite());

        // Testing itself
        $this->expectExceptionForBurialPlaceNotMatchingBurialType(
            self::BURIAL_PLACE_MEMORIAL_TREE_LABEL,
            self::BURIAL_TYPE_URN_IN_GRAVE_SITE_LABEL,
        );
        $memorialTreeId = new MemorialTreeId('MT001');
        $burialPlaceId  = new BurialPlaceId($memorialTreeId);
        $this->burial->setBurialPlaceId($burialPlaceId);
    }

    public function testItFailsWhenSettingGraveSiteForUrnInColumbariumNicheBurialType(): void
    {
        // Prepare entity for testing
        $this->burial->setType(BurialType::urnInColumbariumNiche());

        // Testing itself
        $this->expectExceptionForBurialPlaceNotMatchingBurialType(
            self::BURIAL_PLACE_GRAVE_SITE_LABEL,
            self::BURIAL_TYPE_URN_IN_COLUMBARIUM_NICHE_LABEL,
        );
        $graveSiteId   = new GraveSiteId('GS001');
        $burialPlaceId = new BurialPlaceId($graveSiteId);
        $this->burial->setBurialPlaceId($burialPlaceId);
    }

    public function testItFailsWhenSettingMemorialTreeForUrnInColumbariumNicheBurialType(): void
    {
        // Prepare entity for testing
        $this->burial->setType(BurialType::urnInColumbariumNiche());

        // Testing itself
        $this->expectExceptionForBurialPlaceNotMatchingBurialType(
            self::BURIAL_PLACE_MEMORIAL_TREE_LABEL,
            self::BURIAL_TYPE_URN_IN_COLUMBARIUM_NICHE_LABEL,
        );
        $memorialTreeId = new MemorialTreeId('MT001');
        $burialPlaceId  = new BurialPlaceId($memorialTreeId);
        $this->burial->setBurialPlaceId($burialPlaceId);
    }

    public function testItFailsWhenSettingGraveSiteForAshesUnderMemorialTreeBurialType(): void
    {
        // Prepare entity for testing
        $this->burial->setType(BurialType::ashesUnderMemorialTree());

        // Testing itself
        $this->expectExceptionForBurialPlaceNotMatchingBurialType(
            self::BURIAL_PLACE_GRAVE_SITE_LABEL,
            self::BURIAL_TYPE_ASHES_UNDER_MEMORIAL_TREE_LABEL,
        );
        $graveSiteId   = new GraveSiteId('GS001');
        $burialPlaceId = new BurialPlaceId($graveSiteId);
        $this->burial->setBurialPlaceId($burialPlaceId);
    }

    public function testItFailsWhenSettingColumbariumNicheForAshesUnderMemorialTreeBurialType(): void
    {
        // Prepare entity for testing
        $this->burial->setType(BurialType::ashesUnderMemorialTree());

        // Testing itself
        $this->expectExceptionForBurialPlaceNotMatchingBurialType(
            self::BURIAL_PLACE_COLUMBARIUM_NICHE_LABEL,
            self::BURIAL_TYPE_ASHES_UNDER_MEMORIAL_TREE_LABEL,
        );
        $columbariumNicheId = new ColumbariumNicheId('CN001');
        $burialPlaceId      = new BurialPlaceId($columbariumNicheId);
        $this->burial->setBurialPlaceId($burialPlaceId);
    }

    public function testItDiscardsBurialPlaceIdAfterChangingBurialTypeA(): void
    {
        // Prepare entity for testing
        $this->burial->setType(BurialType::coffinInGraveSite());
        $this->burial->setBurialPlaceId(new BurialPlaceId(new GraveSiteId('GS001')));
        $this->assertNotNull($this->burial->burialPlaceId());

        // Testing itself
        $this->burial->setType(BurialType::urnInGraveSite());
        $this->assertNull($this->burial->burialPlaceId());
    }

    public function testItDiscardsBurialPlaceIdAfterChangingBurialTypeB(): void
    {
        // Prepare entity for testing
        $this->burial->setType(BurialType::urnInColumbariumNiche());
        $this->burial->setBurialPlaceId(new BurialPlaceId(new ColumbariumNicheId('CN001')));
        $this->assertNotNull($this->burial->burialPlaceId());

        // Testing itself
        $this->burial->setType(BurialType::ashesUnderMemorialTree());
        $this->assertNull($this->burial->burialPlaceId());
    }

    public function testItDiscardsBurialPlaceIdAfterChangingBurialTypeC(): void
    {
        // Prepare entity for testing
        $this->burial->setType(BurialType::ashesUnderMemorialTree());
        $this->burial->setBurialPlaceId(new BurialPlaceId(new MemorialTreeId('MT001')));
        $this->assertNotNull($this->burial->burialPlaceId());

        // Testing itself
        $this->burial->setType(BurialType::urnInGraveSite());
        $this->assertNull($this->burial->burialPlaceId());
    }

    // ------------------------------ "BurialType <-> BurialContainer" invariant testing ------------------------------

    public function testItFailsWhenSettingCoffinForUrnInGraveSiteBurialType(): void
    {
        // Prepare entity for testing
        $this->burial->setType(BurialType::urnInGraveSite());

        // Testing itself
        $this->expectExceptionForBurialContainerNotMatchingBurialType(
            Coffin::CLASS_LABEL,
            self::BURIAL_TYPE_URN_IN_GRAVE_SITE_LABEL,
        );
        $coffin = new Coffin(new CoffinSize(180), CoffinShape::american(), false);
        $burialContainer = new BurialContainer($coffin);
        $this->burial->setBurialContainer($burialContainer);
    }

    public function testItFailsWhenSettingCoffinForUrnInColumbariumNicheBurialType(): void
    {
        // Prepare entity for testing
        $this->burial->setType(BurialType::urnInColumbariumNiche());

        // Testing itself
        $this->expectExceptionForBurialContainerNotMatchingBurialType(
            Coffin::CLASS_LABEL,
            self::BURIAL_TYPE_URN_IN_COLUMBARIUM_NICHE_LABEL,
        );
        $coffin = new Coffin(new CoffinSize(180), CoffinShape::american(), false);
        $burialContainer = new BurialContainer($coffin);
        $this->burial->setBurialContainer($burialContainer);
    }

    public function testItFailsWhenSettingCoffinForAshesUnderMemorialTreeBurialType(): void
    {
        // Prepare entity for testing
        $this->burial->setType(BurialType::ashesUnderMemorialTree());

        // Testing itself
        $this->expectExceptionForBurialContainerNotMatchingBurialType(
            Coffin::CLASS_LABEL,
            self::BURIAL_TYPE_ASHES_UNDER_MEMORIAL_TREE_LABEL,
        );
        $coffin = new Coffin(new CoffinSize(180), CoffinShape::american(), false);
        $burialContainer = new BurialContainer($coffin);
        $this->burial->setBurialContainer($burialContainer);
    }

    public function testItFailsWhenSettingUrnForCoffinInGraveSiteBurialType(): void
    {
        // Prepare entity for testing
        $this->burial->setType(BurialType::coffinInGraveSite());

        // Testing itself
        $this->expectExceptionForBurialContainerNotMatchingBurialType(
            Urn::CLASS_LABEL,
            self::BURIAL_TYPE_COFFIN_IN_GRAVE_SITE_LABEL,
        );
        $urn = new Urn();
        $burialContainer = new BurialContainer($urn);
        $this->burial->setBurialContainer($burialContainer);
    }

    public function testItFailsWhenSettingUrnForAshesUnderMemorialTreeBurialType(): void
    {
        // Prepare entity for testing
        $this->burial->setType(BurialType::ashesUnderMemorialTree());

        // Testing itself
        $this->expectExceptionForBurialContainerNotMatchingBurialType(
            Urn::CLASS_LABEL,
            self::BURIAL_TYPE_ASHES_UNDER_MEMORIAL_TREE_LABEL,
        );
        $urn = new Urn();
        $burialContainer = new BurialContainer($urn);
        $this->burial->setBurialContainer($burialContainer);
    }

    public function testItDiscardsBurialContainerAfterChangingBurialTypeA(): void
    {
        // Prepare entity for testing
        $this->burial->setType(BurialType::coffinInGraveSite());
        $this->burial->setBurialContainer(
            new BurialContainer(new Coffin(new CoffinSize(180), CoffinShape::greekWithHandles(), false))
        );
        $this->assertNotNull($this->burial->burialContainer());

        // Testing itself
        $this->burial->setType(BurialType::urnInGraveSite());
        $this->assertNull($this->burial->burialContainer());
    }

    public function testItDiscardsBurialContainerAfterChangingBurialTypeB(): void
    {
        // Prepare entity for testing
        $this->burial->setType(BurialType::urnInGraveSite());
        $this->burial->setBurialContainer(new BurialContainer(new Urn));
        $this->assertNotNull($this->burial->burialContainer());

        // Testing itself
        $this->burial->setType(BurialType::ashesUnderMemorialTree());
        $this->assertNull($this->burial->burialContainer());
    }

    public function testItDiscardsBurialContainerAfterChangingBurialTypeC(): void
    {
        // Prepare entity for testing
        $this->burial->setType(BurialType::urnInColumbariumNiche());
        $this->burial->setBurialContainer(new BurialContainer(new Urn));
        $this->assertNotNull($this->burial->burialContainer());

        // Testing itself
        $this->burial->setType(BurialType::coffinInGraveSite());
        $this->assertNull($this->burial->burialContainer());
    }

    // ------------------------------ "BurialType <-> FuneralCompanyId" invariant testing ------------------------------

    public function testItFailsWhenSettingFuneralCompanyIdForUrnInGraveSiteBurialType(): void
    {
        // Prepare entity for testing
        $this->burial->setType(BurialType::urnInGraveSite());

        // Testing itself
        $this->expectExceptionForFuneralCompanyIdNotNotAllowedForBurialType(
            self::BURIAL_TYPE_URN_IN_GRAVE_SITE_LABEL,
        );
        $funeralCompanyId = new FuneralCompanyId('FC001');
        $this->burial->setFuneralCompanyId($funeralCompanyId);
    }

    public function testItFailsWhenSettingFuneralCompanyIdForUrnInColumbariumNicheBurialType(): void
    {
        // Prepare entity for testing
        $this->burial->setType(BurialType::urnInColumbariumNiche());

        // Testing itself
        $this->expectExceptionForFuneralCompanyIdNotNotAllowedForBurialType(
            self::BURIAL_TYPE_URN_IN_COLUMBARIUM_NICHE_LABEL,
        );
        $funeralCompanyId = new FuneralCompanyId('FC001');
        $this->burial->setFuneralCompanyId($funeralCompanyId);
    }

    public function testItFailsWhenSettingFuneralCompanyIdForAshesUnderMemorialTreeBurialType(): void
    {
        // Prepare entity for testing
        $this->burial->setType(BurialType::ashesUnderMemorialTree());

        // Testing itself
        $this->expectExceptionForFuneralCompanyIdNotNotAllowedForBurialType(
            self::BURIAL_TYPE_ASHES_UNDER_MEMORIAL_TREE_LABEL,
        );
        $funeralCompanyId = new FuneralCompanyId('FC001');
        $this->burial->setFuneralCompanyId($funeralCompanyId);
    }

    public function testItDiscardsFuneralCompanyIdAfterChangingBurialTypeA(): void
    {
        // Prepare entity for testing
        $this->burial->setType(BurialType::coffinInGraveSite());
        $this->burial->setFuneralCompanyId(new FuneralCompanyId('FC001'));
        $this->assertNotNull($this->burial->funeralCompanyId());

        // Testing itself
        $this->burial->setType(BurialType::urnInGraveSite());
        $this->assertNull($this->burial->funeralCompanyId());
    }

    public function testItDiscardsFuneralCompanyIdAfterChangingBurialTypeB(): void
    {
        // Prepare entity for testing
        $this->burial->setType(BurialType::coffinInGraveSite());
        $this->burial->setFuneralCompanyId(new FuneralCompanyId('FC001'));
        $this->assertNotNull($this->burial->funeralCompanyId());

        // Testing itself
        $this->burial->setType(BurialType::urnInColumbariumNiche());
        $this->assertNull($this->burial->funeralCompanyId());
    }

    public function testItDiscardsFuneralCompanyIdAfterChangingBurialTypeC(): void
    {
        // Prepare entity for testing
        $this->burial->setType(BurialType::coffinInGraveSite());
        $this->burial->setFuneralCompanyId(new FuneralCompanyId('FC001'));
        $this->assertNotNull($this->burial->funeralCompanyId());

        // Testing itself
        $this->burial->setType(BurialType::ashesUnderMemorialTree());
        $this->assertNull($this->burial->funeralCompanyId());
    }

    private function expectExceptionForBurialPlaceNotMatchingBurialType(
        string $burialPlace,
        string $burialType,
    ): void {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage(\sprintf(
            'Место захоронения "%s" не соответствует типу захороненния "%s".',
            $burialPlace,
            $burialType,
        ));
    }

    private function expectExceptionForBurialContainerNotMatchingBurialType(
        string $burialContainer,
        string $burialType,
    ): void {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage(\sprintf(
            'Контейнер захоронения "%s" не соответствует типу захороненния "%s".',
            $burialContainer,
            $burialType,
        ));
    }

    private function expectExceptionForFuneralCompanyIdNotNotAllowedForBurialType(
        string $burialType,
    ): void {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage(\sprintf(
            'Похоронная фирма не может быть задана для типа захороненния "%s".',
            $burialType,
        ));
    }
}
