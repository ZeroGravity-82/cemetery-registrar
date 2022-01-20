<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Burial;

use Cemetery\Registrar\Domain\Burial\Burial;
use Cemetery\Registrar\Domain\Burial\BurialCode;
use Cemetery\Registrar\Domain\Burial\BurialId;
use Cemetery\Registrar\Domain\Burial\CustomerId;
use Cemetery\Registrar\Domain\Burial\CustomerType;
use Cemetery\Registrar\Domain\Burial\FuneralCompanyId;
use Cemetery\Registrar\Domain\Burial\FuneralCompanyType;
use Cemetery\Registrar\Domain\NaturalPerson\NaturalPersonId;
use Cemetery\Registrar\Domain\Site\SiteId;
use PHPUnit\Framework\TestCase;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class BurialTest extends TestCase
{
    public function testItSuccessfullyCreated(): void
    {
        $burialId         = new BurialId('B001');
        $burialCode       = new BurialCode('BC001');
        $deceasedId       = new NaturalPersonId('NP001');
        $siteId           = new SiteId('S001');
        $customerId       = new CustomerId('C001', CustomerType::naturalPerson());
        $siteOwnerId      = new NaturalPersonId('NP002');
        $funeralCompanyId = new FuneralCompanyId('FC001', FuneralCompanyType::juristicPerson());
        $burial           = new Burial(
            $burialId,
            $burialCode,
            $deceasedId,
            $siteId,
            $customerId,
            $siteOwnerId,
            $funeralCompanyId,
        );

        $this->assertInstanceOf(BurialId::class, $burial->getId());
        $this->assertSame('B001', (string) $burial->getId());
        $this->assertInstanceOf(BurialCode::class, $burial->getCode());
        $this->assertSame('BC001', (string) $burial->getCode());
        $this->assertInstanceOf(NaturalPersonId::class, $burial->getDeceasedId());
        $this->assertSame('NP001', (string) $burial->getDeceasedId());
        $this->assertInstanceOf(SiteId::class, $burial->getSiteId());
        $this->assertSame('S001', (string) $burial->getSiteId());
        $this->assertInstanceOf(CustomerId::class, $burial->getCustomerId());
        $this->assertSame('C001', $burial->getCustomerId()->getValue());
        $this->assertSame(CustomerType::NATURAL_PERSON, (string) $burial->getCustomerId()->getType());
        $this->assertInstanceOf(NaturalPersonId::class, $burial->getSiteOwnerId());
        $this->assertSame('NP002', (string) $burial->getSiteOwnerId());
        $this->assertInstanceOf(FuneralCompanyId::class, $burial->getFuneralCompanyId());
        $this->assertSame('FC001', $burial->getFuneralCompanyId()->getValue());
    }

    public function testItSuccessfullyCreatedWithoutOptionalFields(): void
    {
        $burialId   = new BurialId('B001');
        $burialCode = new BurialCode('BC001');
        $deceasedId = new NaturalPersonId('NP001');
        $siteId     = new SiteId('S001');
        $burial     = new Burial($burialId, $burialCode, $deceasedId, $siteId, null, null, null);

        $this->assertInstanceOf(BurialId::class, $burial->getId());
        $this->assertSame('B001', (string) $burial->getId());
        $this->assertInstanceOf(BurialCode::class, $burial->getCode());
        $this->assertSame('BC001', (string) $burial->getCode());
        $this->assertInstanceOf(NaturalPersonId::class, $burial->getDeceasedId());
        $this->assertSame('NP001', (string) $burial->getDeceasedId());
        $this->assertInstanceOf(SiteId::class, $burial->getSiteId());
        $this->assertSame('S001', (string) $burial->getSiteId());
        $this->assertNull($burial->getCustomerId());
        $this->assertNull($burial->getSiteOwnerId());
        $this->assertNull($burial->getFuneralCompanyId());
    }
}
